<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Order;
use App\Models\Cheque;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AIChatController extends Controller
{
    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';
    
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Main Chat Entry Point
     */
    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);
        
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return response()->json(['reply' => '⚠️ Gemini API Key missing in .env file. Please add GEMINI_API_KEY.']);
        }

        $userMessage = $request->input('message');
        $chatHistory = $request->input('history', []); 

        return $this->runAgenticLoop($userMessage, $chatHistory, $apiKey);
    }

    private function runAgenticLoop($message, $history, $apiKey)
    {
        $systemPrompt = "You are the 'Danyal Autos AI Manager'. You have full access to the business data via tools. 
        RULES:
        1. Use tools to look up info. Don't guess.
        2. If you need to update a price or stock, ask for confirmation first.
        3. Be professional, concise, and helpful. 
        4. You can speak English, Urdu, and Roman Urdu.
        5. NEVER perform destructive actions (deleting) unless a specific tool allows it.
        6. Today's date is: " . date('Y-m-d') . ".";

        $messages = [];
        // Convert history to Gemini format
        foreach ($history as $turn) {
            $role = ($turn['role'] === 'user') ? 'user' : 'model';
            $messages[] = ['role' => $role, 'parts' => [['text' => $turn['content']]]];
        }
        $messages[] = ['role' => 'user', 'parts' => [['text' => $message]]];

        try {
            $response = $this->callGemini($messages, $systemPrompt, $apiKey);
            
            $candidate = $response['candidates'][0] ?? null;
            if (!$candidate) throw new \Exception("Empty AI response");

            $content = $candidate['content'];
            $parts = $content['parts'] ?? [];
            
            // Check for tool calls
            foreach ($parts as $part) {
                if (isset($part['functionCall'])) {
                    return $this->handleFunctionCall($part['functionCall'], $messages, $systemPrompt, $apiKey, $history, $message);
                }
            }

            // Normal text response
            $aiText = $parts[0]['text'] ?? "I'm not sure how to answer that.";
            return response()->json([
                'reply' => $aiText,
                'history' => array_merge($history, [
                    ['role' => 'user', 'content' => $message],
                    ['role' => 'model', 'content' => $aiText]
                ])
            ]);

        } catch (\Exception $e) {
            Log::error("Gemini Error: " . $e->getMessage());
            return response()->json(['reply' => "⚠️ Brain Error: " . $e->getMessage()]);
        }
    }

    private function handleFunctionCall($functionCall, $messages, $systemPrompt, $apiKey, $history, $originalMessage)
    {
        $name = $functionCall['name'];
        $args = $functionCall['args'] ?? [];
        
        $result = ['error' => 'Tool not found'];
        $redirect = null;

        switch ($name) {
            case 'search_products':
                $result = $this->tool_search_products($args['query'] ?? '');
                break;
            case 'get_supplier_info':
                $result = $this->tool_get_supplier_info($args['id_or_name'] ?? '');
                break;
            case 'get_recent_orders':
                $result = $this->tool_get_recent_orders($args['limit'] ?? 5);
                break;
            case 'get_pending_cheques':
                $result = $this->tool_get_pending_cheques();
                break;
            case 'update_product_price':
                $result = $this->tool_update_price($args['id'], $args['new_price']);
                break;
            case 'update_product_stock':
                $result = $this->tool_update_stock($args['id'], $args['new_quantity']);
                break;
            case 'download_price_list':
                $redirect = route('product.price-list.pdf');
                $result = "Success: Redirecting to PDF download.";
                break;
        }

        // Send tool result back to AI
        $messages[] = ['role' => 'model', 'parts' => [['functionCall' => $functionCall]]];
        $messages[] = [
            'role' => 'function', 
            'parts' => [[
                'functionResponse' => [
                    'name' => $name,
                    'response' => ['name' => $name, 'content' => $result]
                ]
            ]]
        ];

        $finalResponse = $this->callGemini($messages, $systemPrompt, $apiKey);
        $finalText = $finalResponse['candidates'][0]['content']['parts'][0]['text'] ?? "Done.";

        return response()->json([
            'reply' => $finalText,
            'redirect' => $redirect,
            'history' => array_merge($history, [
                ['role' => 'user', 'content' => $originalMessage],
                ['role' => 'model', 'content' => $finalText]
            ])
        ]);
    }

    private function callGemini($messages, $systemPrompt, $apiKey)
    {
        $url = "{$this->apiUrl}?key={$apiKey}";
        $body = [
            'contents' => $messages,
            'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
            'tools' => [[ 'function_declarations' => $this->getToolsDefinition() ]],
            'generationConfig' => [ 'temperature' => 0.4, 'maxOutputTokens' => 800 ]
        ];

        $response = Http::post($url, $body);
        if (!$response->successful()) throw new \Exception($response->body());
        return $response->json();
    }

    private function getToolsDefinition()
    {
        return [
            [
                'name' => 'search_products',
                'description' => 'Search products by name/SKU to check price and stock.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [ 'query' => ['type' => 'STRING'] ],
                    'required' => ['query']
                ]
            ],
            [
                'name' => 'get_supplier_info',
                'description' => 'Get supplier details and current ledger balance.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [ 'id_or_name' => ['type' => 'STRING'] ],
                    'required' => ['id_or_name']
                ]
            ],
            [
                'name' => 'get_recent_orders',
                'description' => 'Get a list of the most recent customer orders.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [ 'limit' => ['type' => 'NUMBER'] ]
                ]
            ],
            [
                'name' => 'get_pending_cheques',
                'description' => 'List all pending customer cheques.',
                'parameters' => ['type' => 'OBJECT', 'properties' => []]
            ],
            [
                'name' => 'update_product_price',
                'description' => 'Update a product price. Ask confirmation first.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [ 'id' => ['type' => 'NUMBER'], 'new_price' => ['type' => 'NUMBER'] ],
                    'required' => ['id', 'new_price']
                ]
            ],
            [
                'name' => 'update_product_stock',
                'description' => 'Set product stock quantity. Ask confirmation first.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [ 'id' => ['type' => 'NUMBER'], 'new_quantity' => ['type' => 'NUMBER'] ],
                    'required' => ['id', 'new_quantity']
                ]
            ],
            [
                'name' => 'download_price_list',
                'description' => 'Generate and download the full price list PDF.',
                'parameters' => ['type' => 'OBJECT', 'properties' => []]
            ]
        ];
    }

    private function tool_search_products($query)
    {
        return Product::where('title', 'like', "%$query%")->orWhere('sku', 'like', "%$query%")->limit(10)->get(['id', 'title', 'sku', 'price', 'stock'])->toArray();
    }

    private function tool_get_supplier_info($query)
    {
        $s = Supplier::where('name', 'like', "%$query%")->orWhere('id', $query)->first();
        return $s ? $s->only(['id', 'name', 'current_balance', 'status']) : "Supplier not found.";
    }

    private function tool_get_recent_orders($limit)
    {
        return Order::latest()->limit($limit)->get(['order_number', 'total_amount', 'status'])->toArray();
    }

    private function tool_get_pending_cheques()
    {
        return Cheque::where('status', 'pending')->limit(15)->get(['cheque_number', 'amount', 'bank_name', 'cheque_date'])->toArray();
    }

    private function tool_update_price($id, $price)
    {
        $p = Product::find($id);
        if (!$p) return "Product not found.";
        $old = $p->price;
        $p->update(['price' => $price]);
        ActivityLog::log('product', 'AI Price Update', "Updated {$p->title} price from $old to $price", route('product.index'));
        return "Price updated successfully.";
    }

    private function tool_update_stock($id, $qty)
    {
        $p = Product::find($id);
        if (!$p) return "Product not found.";
        $old = $p->stock;
        $p->update(['stock' => $qty]);
        ActivityLog::log('product', 'AI Stock Update', "Updated {$p->title} stock from $old to $qty", route('product.index'));
        return "Stock updated successfully.";
    }
}
