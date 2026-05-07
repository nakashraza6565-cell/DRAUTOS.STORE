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
    private $forbiddenKeywords = ['delete', 'remove', 'drop', 'destroy', 'erase', 'truncate', 'wipe'];
    private $model = 'google/gemma-4-26b-a4b-it:free';
    private $apiUrl = 'https://openrouter.ai/api/v1/chat/completions';

    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:500']);

        $userMessage   = trim($request->input('message'));
        $pendingAction = $request->input('pending_action');

        // Safety: block delete commands
        foreach ($this->forbiddenKeywords as $word) {
            if (stripos($userMessage, $word) !== false) {
                return $this->reply('🚫 I am not allowed to delete anything. Please use the admin panel for that.');
            }
        }

        // Confirmation flow
        if ($pendingAction) {
            $lower = strtolower(trim($userMessage));
            if (in_array($lower, ['yes', 'y', 'confirm', 'ok', 'haan', 'ہاں'])) {
                return $this->executeAction($pendingAction);
            }
            if (in_array($lower, ['no', 'n', 'cancel', 'nahi', 'نہیں'])) {
                return $this->reply('✅ Action cancelled. No changes were made.');
            }
        }

        return $this->parseAndRespond($userMessage);
    }

    private function parseAndRespond($message)
    {
        $apiKey = env('OPENROUTER_API_KEY');

        if (!$apiKey) {
            return $this->reply('⚠️ AI is not configured. Please add OPENROUTER_API_KEY to your .env file.');
        }

        // Build real-time context from database
        $products     = Product::orderBy('price', 'desc')->take(15)->get(['id', 'title', 'price', 'stock']);
        $recentOrders = Order::with('user')->latest()->take(5)->get(['id', 'order_number', 'total_amount', 'status', 'user_id']);
        $todaySales   = Order::whereDate('created_at', now()->toDateString())->sum('total_amount');
        $pending      = Order::whereIn('status', ['new', 'process'])->count();

        $context = "Current Products (top 15):\n";
        foreach ($products as $p) {
            $context .= "- ID:{$p->id} | {$p->title} | Price: Rs.{$p->price} | Stock: {$p->stock}\n";
        }
        $context .= "\nRecent Orders:\n";
        foreach ($recentOrders as $o) {
            $context .= "- Order#{$o->order_number} | Rs.{$o->total_amount} | {$o->status} | " . ($o->user->name ?? 'Guest') . "\n";
        }
        $context .= "\nToday's Sales: Rs.{$todaySales} | Pending Orders: {$pending}";

        $systemPrompt = "You are a professional AI business assistant for 'Danyal Autos' spare parts store in Pakistan. " .
            "You help the admin manage inventory, orders, and pricing.\n\n" .
            "REAL-TIME BUSINESS DATA:\n{$context}\n\n" .
            "STRICT RULES:\n" .
            "1. NEVER delete anything. Refuse politely if asked.\n" .
            "2. For ANY price or stock update, respond ONLY with this exact JSON format on its own line: ACTION_JSON:{\"type\":\"update_price\",\"product_id\":123,\"product_name\":\"Name\",\"old_price\":100,\"new_price\":200}\n" .
            "3. Answer questions about products, orders, stock naturally.\n" .
            "4. Keep replies short, friendly, and professional.\n" .
            "5. You understand Urdu, Roman Urdu, and English.\n" .
            "6. For PDF download requests respond with: ACTION_JSON:{\"type\":\"download_pdf\"}\n";

        try {
            $response = Http::timeout(20)->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'HTTP-Referer'  => config('app.url'),
                'X-Title'       => 'Danyal Autos AI',
                'Content-Type'  => 'application/json',
            ])->post($this->apiUrl, [
                'model'    => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user',   'content' => $message],
                ],
                'max_tokens'  => 400,
                'temperature' => 0.4,
            ]);

            if ($response->successful()) {
                $aiText = trim($response->json()['choices'][0]['message']['content'] ?? '');

                if (str_contains($aiText, 'ACTION_JSON:')) {
                    return $this->handleActionIntent($aiText);
                }

                return $this->reply($aiText);
            } else {
                Log::warning('OpenRouter Error: ' . $response->body());
                return $this->reply('⚠️ AI connection issue. Please try again. (' . $response->status() . ')');
            }
        } catch (\Exception $e) {
            Log::error('AI Chat Exception: ' . $e->getMessage());
            return $this->reply('⚠️ Connection error: ' . $e->getMessage());
        }
    }

    private function handleActionIntent($aiText)
    {
        preg_match('/ACTION_JSON:(\{.*?\})/s', $aiText, $matches);
        if (!$matches) {
            $clean = str_replace(strstr($aiText, 'ACTION_JSON:'), '', $aiText);
            return $this->reply(trim($clean) ?: 'Please rephrase your request.');
        }

        $action = json_decode($matches[1], true);
        if (!$action) return $this->reply('I understood the request but could not process it. Please rephrase.');

        if (($action['type'] ?? '') === 'download_pdf') {
            return response()->json([
                'reply'    => '📄 Opening Price List PDF...',
                'action'   => null,
                'redirect' => route('admin.price-list.pdf'),
            ]);
        }

        $confirmMsg = "⚠️ **Confirm Price Update:**\n\n" .
            "Product: **{$action['product_name']}**\n" .
            "Current Price: **Rs. {$action['old_price']}**\n" .
            "New Price: **Rs. {$action['new_price']}**\n\n" .
            "Is this correct? Type **YES** to confirm or **NO** to cancel.";

        return response()->json([
            'reply'         => $confirmMsg,
            'action'        => $action,
            'needs_confirm' => true,
        ]);
    }

    private function executeAction($action)
    {
        $a = is_array($action) ? $action : json_decode($action, true);

        if (($a['type'] ?? '') === 'update_price') {
            try {
                $product = Product::findOrFail($a['product_id']);
                $old = $product->price;
                $product->price = (float) $a['new_price'];
                $product->save();
                ActivityLog::log('product', 'Price Updated via AI Chat',
                    "AI updated price of {$product->title} from Rs.{$old} to Rs.{$product->price}",
                    route('product.index'));
                return $this->reply("✅ **Done!** Price of **{$product->title}** updated from Rs.{$old} to Rs.{$product->price}.");
            } catch (\Exception $e) {
                return $this->reply('❌ Update failed: ' . $e->getMessage());
            }
        }

        return $this->reply('❓ Unknown action. No changes made.');
    }

    private function reply(string $text)
    {
        return response()->json(['reply' => $text, 'action' => null]);
    }
}
