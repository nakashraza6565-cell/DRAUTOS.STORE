<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Order;
use App\Models\ActivityLog;

class AIChatController extends Controller
{
    // ============================================================
    // SAFETY RULES — These are HARDCODED and cannot be changed
    // ============================================================
    private $forbiddenKeywords = [
        'delete', 'remove', 'drop', 'destroy', 'erase', 'truncate', 'wipe', 'clear all'
    ];

    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    // ============================================================
    // Main Chat Endpoint
    // ============================================================
    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:500']);

        $userMessage   = trim($request->input('message'));
        $pendingAction = $request->input('pending_action'); // For confirmation flow

        // --- SAFETY: Block any delete-related commands ---
        foreach ($this->forbiddenKeywords as $keyword) {
            if (stripos($userMessage, $keyword) !== false) {
                return response()->json([
                    'reply' => '🚫 **Safety Block:** I am not allowed to delete anything. Please do this manually from the admin panel if needed.',
                    'action' => null,
                ]);
            }
        }

        // --- CONFIRMATION FLOW: If user says YES to a pending action ---
        if ($pendingAction && strtolower(trim($userMessage)) === 'yes') {
            return $this->executeConfirmedAction($pendingAction);
        }

        // --- CANCEL FLOW ---
        if ($pendingAction && in_array(strtolower(trim($userMessage)), ['no', 'cancel', 'stop'])) {
            return response()->json([
                'reply' => '✅ Action cancelled. No changes were made.',
                'action' => null,
            ]);
        }

        // --- Parse intent using Gemini ---
        return $this->parseAndRespond($userMessage);
    }

    // ============================================================
    // Parse the user message with Gemini AI
    // ============================================================
    private function parseAndRespond($message)
    {
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'reply' => '⚠️ AI is not configured. Please add GEMINI_API_KEY to your .env file.',
                'action' => null,
            ]);
        }

        // Get real context from database for the AI
        $topProducts   = Product::orderBy('price', 'desc')->take(10)->get(['id', 'title', 'price', 'stock', 'sku']);
        $recentOrders  = Order::with('user')->latest()->take(5)->get(['id', 'order_number', 'total_amount', 'status', 'user_id']);

        $contextData = "Current Products (top 10 by price):\n";
        foreach ($topProducts as $p) {
            $contextData .= "- ID:{$p->id} | {$p->title} | Price: Rs.{$p->price} | Stock: {$p->stock}\n";
        }
        $contextData .= "\nRecent Orders:\n";
        foreach ($recentOrders as $o) {
            $contextData .= "- Order#{$o->order_number} | Rs.{$o->total_amount} | Status: {$o->status}\n";
        }

        $systemPrompt = <<<PROMPT
You are a helpful, professional AI assistant for "Danyal Autos" auto parts store in Pakistan.
You help the admin manage the store. You have access to the following real-time data:

{$contextData}

IMPORTANT RULES:
1. You CANNOT delete anything. If asked to delete, refuse politely.
2. For any change (price update, stock update), you must first CONFIRM with the admin.
3. You can answer questions about products, orders, stock, and business.
4. You can generate reports or summaries from the data above.
5. When you want to UPDATE something, respond with a JSON block like:
   ACTION_JSON:{"type":"update_price","product_id":123,"product_name":"TR Boot","old_price":420,"new_price":450}
6. When you want to DOWNLOAD a PDF, respond with:
   ACTION_JSON:{"type":"download_pdf","url":"/admin/price-list/pdf"}
7. For informational answers, just reply normally with clear text.
8. Keep replies short, professional, and in English.
9. Use emojis sparingly to make it friendly.

Admin's message: {$message}
PROMPT;

        try {
            $response = Http::timeout(15)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}",
                [
                    'contents' => [['parts' => [['text' => $systemPrompt]]]],
                    'generationConfig' => [
                        'temperature' => 0.4,
                        'maxOutputTokens' => 400,
                    ]
                ]
            );

            if ($response->successful()) {
                $result = $response->json();
                $aiText = trim($result['candidates'][0]['content']['parts'][0]['text'] ?? '');

                // Check if AI wants to take an action
                if (str_contains($aiText, 'ACTION_JSON:')) {
                    return $this->handleActionIntent($aiText);
                }

                return response()->json(['reply' => $aiText, 'action' => null]);
            } else {
                Log::warning('AI Chat API Error: ' . $response->body());
                return response()->json([
                    'reply' => '⚠️ I had trouble connecting to the AI. Please try again in a moment.',
                    'action' => null,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('AI Chat Exception: ' . $e->getMessage());
            return response()->json([
                'reply' => '⚠️ Connection error: ' . $e->getMessage(),
                'action' => null,
            ]);
        }
    }

    // ============================================================
    // Handle Action — Show confirmation before executing
    // ============================================================
    private function handleActionIntent($aiText)
    {
        // Extract JSON from the AI response
        preg_match('/ACTION_JSON:(\{.*?\})/s', $aiText, $matches);
        if (!$matches) {
            $cleanText = str_replace(strstr($aiText, 'ACTION_JSON:'), '', $aiText);
            return response()->json(['reply' => trim($cleanText) ?: 'I need more details to help you.', 'action' => null]);
        }

        $actionData = json_decode($matches[1], true);
        if (!$actionData) {
            return response()->json(['reply' => 'I understood your request but could not process it. Please try rephrasing.', 'action' => null]);
        }

        // Build confirmation message
        $confirmMsg = $this->buildConfirmationMessage($actionData);

        return response()->json([
            'reply'          => $confirmMsg,
            'action'         => $actionData,
            'needs_confirm'  => true,
        ]);
    }

    // ============================================================
    // Build Human-Readable Confirmation Message
    // ============================================================
    private function buildConfirmationMessage($action)
    {
        switch ($action['type'] ?? '') {
            case 'update_price':
                return "⚠️ **Confirm Price Update:**\n\n" .
                       "Product: **{$action['product_name']}**\n" .
                       "Current Price: **Rs. {$action['old_price']}**\n" .
                       "New Price: **Rs. {$action['new_price']}**\n\n" .
                       "Is this correct? Type **YES** to confirm or **NO** to cancel.";

            case 'update_stock':
                return "⚠️ **Confirm Stock Update:**\n\n" .
                       "Product: **{$action['product_name']}**\n" .
                       "New Stock: **{$action['new_stock']} units**\n\n" .
                       "Is this correct? Type **YES** to confirm or **NO** to cancel.";

            case 'download_pdf':
                return "📄 **Download Price List PDF?**\n\n" .
                       "I will open the PDF download link for you.\n\n" .
                       "Type **YES** to proceed or **NO** to cancel.";

            default:
                return "❓ I need confirmation to proceed. Type **YES** to confirm or **NO** to cancel.";
        }
    }

    // ============================================================
    // Execute Confirmed Action
    // ============================================================
    private function executeConfirmedAction($pendingAction)
    {
        $action = is_array($pendingAction) ? $pendingAction : json_decode($pendingAction, true);

        switch ($action['type'] ?? '') {
            case 'update_price':
                return $this->updateProductPrice($action);

            case 'update_stock':
                return $this->updateProductStock($action);

            case 'download_pdf':
                return response()->json([
                    'reply'       => '📄 Opening PDF download...',
                    'action'      => null,
                    'redirect'    => $action['url'] ?? '/admin/price-list/pdf',
                ]);

            default:
                return response()->json([
                    'reply'  => '❓ Unknown action. No changes were made.',
                    'action' => null,
                ]);
        }
    }

    // ============================================================
    // Safe Price Update
    // ============================================================
    private function updateProductPrice($action)
    {
        try {
            $product = Product::findOrFail($action['product_id']);
            $oldPrice = $product->price;
            $product->price = (float) $action['new_price'];
            $product->save();

            // Log it
            ActivityLog::log(
                'product',
                'Price Updated via AI',
                'AI Assistant updated price of ' . $product->title . ' from Rs.' . $oldPrice . ' to Rs.' . $product->price,
                route('product.index')
            );

            return response()->json([
                'reply'  => "✅ **Done!** Price of **{$product->title}** updated from Rs.{$oldPrice} to Rs.{$product->price}.",
                'action' => null,
            ]);
        } catch (\Exception $e) {
            Log::error('AI Price Update Error: ' . $e->getMessage());
            return response()->json([
                'reply'  => '❌ Update failed: ' . $e->getMessage(),
                'action' => null,
            ]);
        }
    }

    // ============================================================
    // Safe Stock Update
    // ============================================================
    private function updateProductStock($action)
    {
        try {
            $product = Product::findOrFail($action['product_id']);
            $oldStock = $product->stock;
            $product->stock = (int) $action['new_stock'];
            $product->save();

            ActivityLog::log(
                'product',
                'Stock Updated via AI',
                'AI Assistant updated stock of ' . $product->title . ' from ' . $oldStock . ' to ' . $product->stock . ' units.',
                route('product.index')
            );

            return response()->json([
                'reply'  => "✅ **Done!** Stock of **{$product->title}** updated from {$oldStock} to {$product->stock} units.",
                'action' => null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'reply'  => '❌ Update failed: ' . $e->getMessage(),
                'action' => null,
            ]);
        }
    }
}
