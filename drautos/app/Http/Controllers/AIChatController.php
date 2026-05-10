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
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AIChatController extends Controller
{
    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

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
        $user = Auth::user();
        $memoryFile = "ai_memory_{$user->id}.txt";
        
        $userMemory = 'No preferences saved yet.';
        try {
            if (\Illuminate\Support\Facades\Storage::exists($memoryFile)) {
                $userMemory = \Illuminate\Support\Facades\Storage::get($memoryFile);
            }
        } catch (\Throwable $e) {}

        $systemPrompt = "You are the 'Danyal Autos AI Manager'. You are a highly intelligent executive assistant currently talking to {$user->name}.
        
        LONG-TERM MEMORY FOR {$user->name}:
        {$userMemory}

        YOUR POWERS:
        - You can read ANY table using read_database. 
        **CRITICAL DATABASE MAP (Use these exact model names):**
        - Customers -> model_name: 'User' (Search by 'name')
        - Suppliers -> model_name: 'Supplier' (Search by 'name')
        - Customer Ledgers -> model_name: 'CustomerLedger'
        - Supplier Ledgers -> model_name: 'SupplierLedger'
        - Cheques -> model_name: 'Cheque'
        - Products -> model_name: 'Product'
        - Orders -> model_name: 'Order'

        - You can update prices and stock, add cheques, and ledger entries.
        - You can open/print receipts.
        - You can permanently remember rules or facts using update_memory.

        RULES:
        1. ALWAYS confirm with the user before writing data.
        2. SLOT FILLING: If a command is missing data, ASK the user.
        3. SELF-CORRECTION: If read_database returns an error about invalid columns or missing models, YOU MUST silently retry with the correct model/column. NEVER apologize for technical errors, just fix them in the background!
        4. CONVERSATIONAL PERSONA: Be highly conversational, polite, and professional. Address the user by their name ({$user->name}) naturally. Act like you know them personally as their loyal assistant (e.g. \"Right away, {$user->name}\", \"I've got that done for you, sir.\"). 
        5. LANGUAGE: ALWAYS respond in the same language the user types in (English, Urdu, or Roman Urdu). If they type Roman Urdu, reply in Roman Urdu.
        6. NEVER delete data.
        7. Today is: " . date('Y-m-d') . ".";

        $messages = [];
        if (is_array($history)) {
            foreach ($history as $turn) {
                if (!isset($turn['role'])) continue;
                $role = ($turn['role'] === 'user') ? 'user' : 'model';
                $text = $turn['content'] ?? '';
                $messages[] = ['role' => $role, 'parts' => [['text' => $text]]];
            }
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

        } catch (\Throwable $e) {
            return response()->json(['reply' => "⚠️ System Crash Prevented: " . $e->getMessage() . " on line " . $e->getLine()]);
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
                case 'read_database':
                    $result = $this->tool_read_database($args);
                    break;
                case 'update_memory':
                    $userId = Auth::id();
                    $file = "ai_memory_{$userId}.txt";
                    $currentMemory = \Illuminate\Support\Facades\Storage::exists($file) ? \Illuminate\Support\Facades\Storage::get($file) : '';
                    $newMemory = $currentMemory . "\n- " . $args['fact'];
                    \Illuminate\Support\Facades\Storage::put($file, $newMemory);
                    $result = "Success: Fact permanently remembered for this user.";
                    break;
                case 'print_document':
                    $o = \App\Models\Order::where('order_number', $args['order_number'])->first();
                    if ($o) {
                        $redirect = ($args['document_type'] === 'pdf') ? "/order/pdf/{$o->id}" : "/order/print/{$o->id}";
                        $result = "Success: Opening {$args['document_type']} for order {$args['order_number']}.";
                    } else {
                        $result = "Error: Order not found.";
                    }
                    break;
                case 'get_pending_cheques':
                    $cheques = \App\Models\Cheque::where('status', 'pending')->with('party')->orderBy('clearing_date', 'asc')->limit(10)->get();
                    $result = $cheques->map(function($c) {
                        $arr = $c->toArray();
                        $arr['customer_name'] = $c->party ? $c->party->name : 'Unknown';
                        return $arr;
                    })->toArray();
                    break;
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
                case 'add_customer_ledger_entry':
                    $result = $this->tool_add_customer_ledger_entry($args);
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
        } catch (\Throwable $e) { $result = "Error executing tool: " . $e->getMessage(); }

        if (!isset($functionCall['args']) || (is_array($functionCall['args']) && empty($functionCall['args']))) {
            $functionCall['args'] = new \stdClass();
        }

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
        $nextPart = $finalResponse['candidates'][0]['content']['parts'][0] ?? [];
        
        // If Gemini wants to call ANOTHER tool based on the result, recursively handle it!
        if (isset($nextPart['functionCall'])) {
            return $this->handleFunctionCall($nextPart['functionCall'], $messages, $systemPrompt, $apiKey, $history, $originalMessage);
        }

        $finalText = $nextPart['text'] ?? "I have successfully processed your request.";

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
                'name' => 'read_database',
                'description' => 'A universal reader to query the database. Available models: Product, User, Supplier, Order, Cheque, CustomerLedger, SupplierLedger, Expense. Use this to lookup ANY information before taking an action.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'model_name' => ['type' => 'STRING', 'description' => 'Exact model name (e.g., Product, User, Supplier, Order)'],
                        'search_column' => ['type' => 'STRING', 'description' => 'Column to search (e.g., title, sku, name, order_number). For dates use transaction_date or created_at. Leave empty if you want latest records.'],
                        'search_value' => ['type' => 'STRING', 'description' => 'Value to search for'],
                        'limit' => ['type' => 'NUMBER', 'description' => 'Max results to return (default 5, max 10)']
                    ],
                    'required' => ['model_name']
                ]
            ],
            [
                'name' => 'print_document',
                'description' => 'Control the users browser to open/print a thermal receipt or PDF invoice for an order.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'document_type' => ['type' => 'STRING', 'description' => 'receipt or pdf'],
                        'order_number' => ['type' => 'STRING']
                    ],
                    'required' => ['document_type', 'order_number']
                ]
            ],
            [
                'name' => 'update_memory',
                'description' => 'Use this tool to save a long-term preference or fact about the current user. Use this when the user says "remember that I...", "always do X", or tells you a rule they want you to follow forever.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'fact' => ['type' => 'STRING', 'description' => 'The rule, preference, or fact to remember.']
                    ],
                    'required' => ['fact']
                ]
            ],
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
                'name' => 'add_customer_ledger_entry',
                'description' => 'Add a manual transaction (payment/purchase) to a customer ledger. e.g., cash payment, JazzCash, Easypaisa.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'customer_name' => ['type' => 'STRING'],
                        'amount' => ['type' => 'NUMBER'],
                        'type' => ['type' => 'STRING', 'description' => 'debit or credit'],
                        'category' => ['type' => 'STRING', 'description' => 'payment, sale, return, etc'],
                        'description' => ['type' => 'STRING']
                    ],
                    'required' => ['customer_name', 'amount', 'type', 'category']
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

    private function tool_read_database($args)
    {
        $modelName = $args['model_name'] ?? '';
        $modelClass = '\\App\\Models\\' . $modelName;
        if ($modelName === 'User') $modelClass = '\\App\\User';

        if (!$modelName || !class_exists($modelClass)) {
            $models = ['User'];
            $modelsPath = app_path('Models');
            if (is_dir($modelsPath)) {
                foreach (scandir($modelsPath) as $file) {
                    if (strpos($file, '.php') !== false) {
                        $models[] = str_replace('.php', '', $file);
                    }
                }
            }
            return "Error: Model '{$modelName}' not found. The actual valid models in this system are: " . implode(', ', $models) . ". Please silently retry your search using one of these correct model names.";
        }

        try {
            $query = $modelClass::query();
            if (!empty($args['search_column']) && !empty($args['search_value'])) {
                $query->where($args['search_column'], 'like', "%{$args['search_value']}%");
            }
            $limit = min((int)($args['limit'] ?? 5), 10);
            return $query->orderBy('id', 'desc')->limit($limit)->get()->toArray();
        } catch (\Exception $e) {
            $modelInstance = new $modelClass();
            $columns = \Illuminate\Support\Facades\Schema::getColumnListing($modelInstance->getTable());
            return "Error: Invalid column. The actual valid columns for {$modelName} are: " . implode(', ', $columns) . ". Please try searching again with the correct column name.";
        }
    }

    private function tool_add_customer_ledger_entry($args)
    {
        $user = User::where('name', 'like', "%{$args['customer_name']}%")->first();
        if (!$user) return "Error: Customer '{$args['customer_name']}' not found.";

        \App\Models\CustomerLedger::record(
            $user->id,
            date('Y-m-d'),
            $args['type'],
            $args['category'],
            ($args['description'] ?? 'Manual entry via AI'),
            $args['amount']
        );

        return "Success: Entry added to {$user->name}'s ledger.";
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
