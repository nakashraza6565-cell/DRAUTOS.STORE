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
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AIChatController extends Controller
{
    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';
    
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1500']);
        
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return response()->json(['reply' => '⚠️ Gemini API Key missing. Please add GEMINI_API_KEY to your .env file.']);
        }

        $userMessage = $request->input('message');
        $chatHistory = $request->input('history', []); 

        return $this->runAgenticLoop($userMessage, $chatHistory, $apiKey);
    }

    private function runAgenticLoop($message, $history, $apiKey)
    {
        $systemPrompt = "You are the 'Danyal Autos AI Manager'. You are a high-level executive assistant.
        YOUR POWERS:
        - You can check stock, prices, orders, and ledgers.
        - You can update prices and stock.
        - You can add cheques and ledger entries.
        - You can update order statuses.

        RULES:
        1. ALWAYS confirm with the user before making a change. Show a summary of what you are about to do.
        2. SLOT FILLING: If the user gives an incomplete command (e.g. 'Add a cheque' but no amount), ASK for the missing info.
        3. Be fast, professional, and concise.
        4. Understand Urdu, Roman Urdu, and English.
        5. NEVER delete data.
        6. Today is: " . date('Y-m-d') . ". Use this for relative dates like 'today', 'tomorrow', 'yesterday'.";

        $messages = [];
        foreach ($history as $turn) {
            $role = ($turn['role'] === 'user') ? 'user' : 'model';
            $messages[] = ['role' => $role, 'parts' => [['text' => $turn['content']]]];
        }
        $messages[] = ['role' => 'user', 'parts' => [['text' => $message]]];

        try {
            $response = $this->callGemini($messages, $systemPrompt, $apiKey);
            $candidate = $response['candidates'][0] ?? null;
            if (!$candidate) throw new \Exception("Empty response from Gemini.");

            $content = $candidate['content'];
            $parts = $content['parts'] ?? [];
            
            foreach ($parts as $part) {
                if (isset($part['functionCall'])) {
                    return $this->handleFunctionCall($part['functionCall'], $messages, $systemPrompt, $apiKey, $history, $message);
                }
            }

            $aiText = $parts[0]['text'] ?? "I'm ready to help. What would you like me to do?";
            return response()->json([
                'reply' => $aiText,
                'history' => array_merge($history, [
                    ['role' => 'user', 'content' => $message],
                    ['role' => 'model', 'content' => $aiText]
                ])
            ]);

        } catch (\Exception $e) {
            Log::error("Gemini Error: " . $e->getMessage());
            return response()->json(['reply' => "⚠️ Error: " . $e->getMessage()]);
        }
    }

    private function handleFunctionCall($functionCall, $messages, $systemPrompt, $apiKey, $history, $originalMessage)
    {
        $name = $functionCall['name'];
        $args = $functionCall['args'] ?? [];
        $result = ['status' => 'error', 'message' => 'Tool not found'];
        $redirect = null;

        try {
            switch ($name) {
                case 'search_products':
                    $result = Product::where('title', 'like', "%{$args['query']}%")->orWhere('sku', 'like', "%{$args['query']}%")->limit(10)->get(['id', 'title', 'sku', 'price', 'stock'])->toArray();
                    break;
                case 'get_supplier_ledger':
                    $s = Supplier::where('name', 'like', "%{$args['name']}%")->first();
                    $result = $s ? $s->only(['id', 'name', 'current_balance']) : "Supplier not found.";
                    break;
                case 'add_customer_cheque':
                    $result = $this->tool_add_cheque($args);
                    break;
                case 'add_supplier_ledger_entry':
                    $result = $this->tool_add_supplier_ledger_entry($args);
                    break;
                case 'update_product_price':
                    $p = Product::find($args['id']);
                    if ($p) {
                        $old = $p->price;
                        $p->update(['price' => $args['new_price']]);
                        ActivityLog::log('product', 'AI Update', "Price of {$p->title} changed from $old to {$args['new_price']}");
                        $result = "Success: Price updated.";
                    } else $result = "Product not found.";
                    break;
                case 'update_order_status':
                    $o = Order::where('order_number', $args['order_number'])->first();
                    if ($o) {
                        $o->update(['status' => $args['status']]);
                        $result = "Success: Order #{$args['order_number']} status changed to {$args['status']}.";
                    } else $result = "Order not found.";
                    break;
                case 'download_price_list':
                    $redirect = route('product.price-list.pdf');
                    $result = "Success: Redirecting to PDF.";
                    break;
            }
        } catch (\Exception $e) { $result = "Error: " . $e->getMessage(); }

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
            'generationConfig' => [ 'temperature' => 0.2, 'maxOutputTokens' => 1000 ]
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
                'description' => 'Search products by name or SKU.',
                'parameters' => [ 'type' => 'OBJECT', 'properties' => [ 'query' => ['type' => 'STRING'] ], 'required' => ['query'] ]
            ],
            [
                'name' => 'get_pending_cheques',
                'description' => 'List all pending customer cheques.',
                'parameters' => [ 'type' => 'OBJECT', 'properties' => new \stdClass() ]
            ],
            [
                'name' => 'get_supplier_ledger',
                'description' => 'Get balance of a supplier.',
                'parameters' => [ 'type' => 'OBJECT', 'properties' => [ 'name' => ['type' => 'STRING'] ], 'required' => ['name'] ]
            ],
            [
                'name' => 'add_customer_cheque',
                'description' => 'Add a new cheque received from a customer.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'customer_name' => ['type' => 'STRING'],
                        'amount' => ['type' => 'NUMBER'],
                        'cheque_number' => ['type' => 'STRING'],
                        'bank_name' => ['type' => 'STRING'],
                        'cheque_date' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD'],
                        'clearing_date' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD']
                    ],
                    'required' => ['customer_name', 'amount', 'cheque_number', 'bank_name', 'cheque_date']
                ]
            ],
            [
                'name' => 'add_supplier_ledger_entry',
                'description' => 'Add a manual transaction (payment/purchase) to a supplier ledger.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'supplier_name' => ['type' => 'STRING'],
                        'amount' => ['type' => 'NUMBER'],
                        'type' => ['type' => 'STRING', 'description' => 'debit or credit'],
                        'category' => ['type' => 'STRING', 'description' => 'payment, purchase, return, etc'],
                        'description' => ['type' => 'STRING']
                    ],
                    'required' => ['supplier_name', 'amount', 'type', 'category']
                ]
            ],
            [
                'name' => 'update_product_price',
                'description' => 'Update product price.',
                'parameters' => [ 'type' => 'OBJECT', 'properties' => [ 'id' => ['type' => 'NUMBER'], 'new_price' => ['type' => 'NUMBER'] ], 'required' => ['id', 'new_price'] ]
            ],
            [
                'name' => 'update_order_status',
                'description' => 'Change status of an order.',
                'parameters' => [ 'type' => 'OBJECT', 'properties' => [ 'order_number' => ['type' => 'STRING'], 'status' => ['type' => 'STRING', 'description' => 'new, process, delivered, cancel'] ], 'required' => ['order_number', 'status'] ]
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
                'name' => 'download_price_list',
                'description' => 'Download price list PDF.',
                'parameters' => [ 'type' => 'OBJECT', 'properties' => new \stdClass() ]
            ]
        ];
    }

    private function tool_add_cheque($args)
    {
        $user = User::where('name', 'like', "%{$args['customer_name']}%")->first();
        if (!$user) return "Error: Customer '{$args['customer_name']}' not found.";

        $cheque = Cheque::create([
            'type' => 'received',
            'cheque_number' => $args['cheque_number'],
            'amount' => $args['amount'],
            'cheque_date' => $args['cheque_date'],
            'clearing_date' => $args['clearing_date'] ?? $args['cheque_date'],
            'bank_name' => $args['bank_name'],
            'party_type' => 'App\User',
            'party_id' => $user->id,
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        ActivityLog::log('cheque', 'AI Add', "Added cheque #{$cheque->cheque_number} for customer {$user->name}");
        return "Success: Cheque added for {$user->name}.";
    }

    private function tool_add_supplier_ledger_entry($args)
    {
        $supplier = Supplier::where('name', 'like', "%{$args['supplier_name']}%")->first();
        if (!$supplier) return "Error: Supplier '{$args['supplier_name']}' not found.";

        SupplierLedger::record(
            $supplier->id,
            date('Y-m-d'),
            $args['type'],
            $args['category'],
            ($args['description'] ?? 'Manual entry via AI'),
            $args['amount']
        );

        return "Success: Entry added to {$supplier->name}'s ledger.";
    }
}
