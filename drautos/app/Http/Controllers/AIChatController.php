<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use DB;

class AIChatController extends Controller
{
    private $apiKey;
    private $redirectUrl = null;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        
        // --- AUTO-MEMORY PERSISTENCE (Self-Healing) ---
        try {
            if (!Schema::hasTable('ai_chat_messages')) {
                Schema::create('ai_chat_messages', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id');
                    $table->string('role');
                    $table->text('content');
                    $table->json('tool_calls')->nullable();
                    $table->json('tool_results')->nullable();
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {
            Log::error("AI Chat Migration Error: " . $e->getMessage());
        }
    }

    public function chat(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'reply' => '❌ Error: You are not authenticated. Please log in.',
                    'history' => []
                ]);
            }

            $input = $request->input('message');
            if (empty($input)) {
                return response()->json([
                    'reply' => '❌ Error: Message cannot be empty.',
                    'history' => []
                ]);
            }

            if (empty($this->apiKey)) {
                return response()->json([
                    'reply' => '❌ Error: Gemini API Key (GEMINI_API_KEY) is not set in the server environment (.env file). Please update your .env config.',
                    'history' => []
                ]);
            }

            // Ensure table exists on the fly (Self-Healing Check)
            if (!Schema::hasTable('ai_chat_messages')) {
                try {
                    Schema::create('ai_chat_messages', function (Blueprint $table) {
                        $table->id();
                        $table->unsignedBigInteger('user_id');
                        $table->string('role');
                        $table->text('content');
                        $table->json('tool_calls')->nullable();
                        $table->json('tool_results')->nullable();
                        $table->timestamps();
                    });
                } catch (\Throwable $migrationError) {
                    throw new \Exception("Database schema error (failed to auto-create 'ai_chat_messages' table): " . $migrationError->getMessage());
                }
            }

            // Save User Message
            DB::table('ai_chat_messages')->insert([
                'user_id' => $user->id,
                'role' => 'user',
                'content' => $input,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Load Last 50 Messages for context
            $historyData = DB::table('ai_chat_messages')
                ->where('user_id', $user->id)
                ->orderBy('id', 'asc') // Load in chronological order
                ->limit(50)
                ->get();

            $messages = [];
            foreach ($historyData as $msg) {
                $messages[] = ['role' => ($msg->role === 'user' ? 'user' : 'model'), 'parts' => [['text' => $msg->content]]];
            }

            $userMemory = "";
            try {
                $memoryPath = storage_path("app/ai_memory_{$user->id}.json");
                if (file_exists($memoryPath)) {
                    $userMemory = file_get_contents($memoryPath);
                }
            } catch (\Throwable $e) {}

            // Dynamic Model Discovery
            $models = ['User'];
            $path = app_path('Models');
            if (is_dir($path)) {
                foreach (scandir($path) as $file) {
                    if (strpos($file, '.php') !== false) $models[] = str_replace('.php', '', $file);
                }
            }
            $modelList = implode(', ', $models);

            $systemPrompt = "You are the 'Danyal Autos AI Manager'. You are a highly intelligent executive assistant currently talking to {$user->name}.
            
            LONG-TERM MEMORY: {$userMemory}

            DATABASE MODELS: {$modelList}

            CRITICAL OPERATING RULES:
            1. PERSISTENCE: Use the provided context to remember what was just discussed.
            2. EXHAUSTIVE SEARCH: If a customer/item is not found, use global_search or try fuzzy variations of the name. NEVER say 'Not found' without checking Customers, Suppliers, and Products.
            3. NO EXCUSES: If you need an ID (like for activity logs), use read_database to find it yourself first. Do NOT ask the user for IDs.
            4. TOOL CHAINING: You can call multiple tools in a row. If one tool output suggests you need another, call it immediately.
            5. LANGUAGE: Match the user's language (English/Urdu/Roman Urdu).
            6. WHATSAPP: Use open_whatsapp with generated URLs for PDFs/Receipts.
            7. ANALYTICS: Use get_analytics for totals, top items, and sales reports.
            
            Today is: " . date('Y-m-d') . ".";

            $finalResponse = $this->executeRecursiveLoop($messages, $systemPrompt);

            // Save Assistant Response
            DB::table('ai_chat_messages')->insert([
                'user_id' => $user->id,
                'role' => 'assistant',
                'content' => $finalResponse,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'reply' => $finalResponse,
                'history' => [],
                'redirect' => $this->redirectUrl
            ]);

        } catch (\Throwable $e) {
            Log::error("AI Chat Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'reply' => '❌ AI Chat Error: ' . $e->getMessage() . '. Please verify your database connection and environment keys.',
                'history' => []
            ]);
        }
    }

    private function executeRecursiveLoop($messages, $systemPrompt, $depth = 0)
    {
        if ($depth > 10) return "Error: Thinking too deep. Please simplify your request.";

        $response = $this->callGemini($messages, $systemPrompt);
        
        $candidate = $response['candidates'][0] ?? null;
        if (!$candidate) throw new \Exception("Empty response from Gemini.");

        $content = $candidate['content'];
        $messages[] = $content;

        $hasToolCall = false;
        $toolResultsParts = [];

        foreach ($content['parts'] as $part) {
            if (isset($part['functionCall'])) {
                $hasToolCall = true;
                $name = $part['functionCall']['name'];
                $args = $part['functionCall']['args'] ?? [];
                
                try {
                    $result = $this->handleFunctionCall($name, $args);
                } catch (\Throwable $e) {
                    $result = "Error: " . $e->getMessage();
                }

                $toolResultsParts[] = [
                    'functionResponse' => [
                        'name' => $name,
                        'response' => ['name' => $name, 'content' => $result]
                    ]
                ];
            }
        }

        if ($hasToolCall) {
            $messages[] = ['role' => 'user', 'parts' => $toolResultsParts];
            return $this->executeRecursiveLoop($messages, $systemPrompt, $depth + 1);
        }

        // Return final text
        $finalText = "";
        foreach ($content['parts'] as $part) {
            if (isset($part['text'])) $finalText .= $part['text'];
        }
        return $finalText;
    }

    private function callGemini($messages, $systemPrompt)
    {
        // Try latest stable production models (gemini-2.5-flash and gemini-2.0-flash)
        $endpoints = [
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $this->apiKey,
            "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key=" . $this->apiKey,
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $this->apiKey,
            "https://generativelanguage.googleapis.com/v1/models/gemini-2.0-flash:generateContent?key=" . $this->apiKey,
        ];

        $lastException = null;
        foreach ($endpoints as $url) {
            try {
                $body = [
                    'contents' => $messages,
                    'systemInstruction' => ['parts' => [['text' => $systemPrompt]]],
                    'tools' => [['functionDeclarations' => $this->getTools()]]
                ];

                $response = Http::timeout(45)->post($url, $body);
                if ($response->successful()) {
                    return $response->json();
                }
                
                $responseBody = $response->body();
                // If it's a clear invalid key error, throw it immediately to avoid looping uselessly
                if (strpos($responseBody, 'API_KEY_INVALID') !== false || strpos($responseBody, 'API Key not found') !== false) {
                    throw new \Exception("Invalid API Key: Please verify the GEMINI_API_KEY in your .env file.");
                }

                $lastException = new \Exception("Gemini API Error (" . parse_url($url, PHP_URL_PATH) . "): " . $responseBody);
            } catch (\Throwable $e) {
                $lastException = $e;
                // If it's an explicit invalid key exception, bubble it up directly
                if (strpos($e->getMessage(), 'Invalid API Key') !== false) {
                    throw $e;
                }
            }
        }

        throw $lastException ?: new \Exception("All Gemini endpoints failed.");
    }

    private function handleFunctionCall($name, $args)
    {
        switch ($name) {
            case 'read_database':
                return $this->tool_read_database($args);
            case 'global_search':
                return $this->tool_global_search($args);
            case 'get_analytics':
                return $this->tool_get_analytics($args);
            case 'open_whatsapp':
                return $this->tool_open_whatsapp($args);
            case 'update_memory':
                return $this->tool_update_memory($args);
            case 'open_ledger':
                return $this->tool_open_ledger($args);
            default:
                return "Error: Tool {$name} not implemented.";
        }
    }

    private function tool_read_database($args)
    {
        $model = $args['model_name'];
        $modelClass = strpos($model, '\\') !== false ? $model : "App\\Models\\" . $model;
        if (!class_exists($modelClass)) {
             $modelClass = "App\\" . $model;
             if (!class_exists($modelClass)) return "Error: Model {$model} not found.";
        }

        try {
            $query = $modelClass::query();
            foreach (['user', 'customer', 'party', 'supplier'] as $rel) {
                if (method_exists($modelClass, $rel)) $query->with($rel);
            }

            if (!empty($args['search_column']) && !empty($args['search_value'])) {
                $query->where($args['search_column'], 'like', "%{$args['search_value']}%");
            }
            return $query->orderBy('id', 'desc')->limit(10)->get()->toArray();
        } catch (\Throwable $e) {
            return "DB Error: " . $e->getMessage();
        }
    }

    private function tool_global_search($args)
    {
        $q = $args['query'];
        return [
            'customers' => \App\User::where('name', 'like', "%$q%")->orWhere('phone', 'like', "%$q%")->limit(5)->get()->toArray(),
            'suppliers' => \App\Models\Supplier::where('name', 'like', "%$q%")->limit(5)->get()->toArray(),
            'products' => \App\Models\Product::where('title', 'like', "%$q%")->orWhere('sku', 'like', "%$q%")->limit(5)->get()->toArray()
        ];
    }

    private function tool_get_analytics($args)
    {
        $type = $args['type'] ?? 'sales_summary';
        if ($type === 'top_selling_products') {
            return \App\Models\Cart::whereNotNull('order_id')
                ->whereMonth('created_at', date('m'))
                ->with('product:id,title')
                ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(amount) as total_amount')
                ->groupBy('product_id')
                ->orderBy('total_amount', 'desc')
                ->limit(10)->get()->toArray();
        }
        return \App\Models\Order::whereMonth('created_at', date('m'))->selectRaw('COUNT(*) as count, SUM(total_amount) as total')->first()->toArray();
    }

    private function tool_open_whatsapp($args)
    {
        $phone = preg_replace('/[^0-9]/', '', $args['phone'] ?? '');
        if (strlen($phone) == 10) $phone = '92' . $phone;
        return "Success: Opening WhatsApp for {$phone}";
    }

    private function tool_update_memory($args)
    {
        $user = auth()->user();
        file_put_contents(storage_path("app/ai_memory_{$user->id}.json"), $args['memory_content']);
        return "Success: Memory updated.";
    }

    private function tool_open_ledger($args)
    {
        $type = $args['type'] ?? '';
        $id = $args['id'] ?? null;

        if (!$id) {
            return "Error: Database ID is required to open the ledger.";
        }

        if ($type === 'customer') {
            $this->redirectUrl = route('admin.customer-ledger.show', $id);
            return "Success: Generating automatic redirect to Customer Ledger for ID {$id}.";
        } elseif ($type === 'supplier') {
            $this->redirectUrl = route('admin.supplier-ledger.show', $id);
            return "Success: Generating automatic redirect to Supplier Ledger for ID {$id}.";
        }

        return "Error: Invalid ledger type. Supported types are 'customer' or 'supplier'.";
    }

    private function getTools()
    {
        return [
            [
                'name' => 'read_database',
                'description' => 'Read data from any table.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'model_name' => ['type' => 'STRING'],
                        'search_column' => ['type' => 'STRING'],
                        'search_value' => ['type' => 'STRING']
                    ],
                    'required' => ['model_name']
                ]
            ],
            [
                'name' => 'global_search',
                'description' => 'Search Customers, Suppliers, and Products at once.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => ['query' => ['type' => 'STRING']],
                    'required' => ['query']
                ]
            ],
            [
                'name' => 'get_analytics',
                'description' => 'Get sales reports and top items.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'type' => ['type' => 'STRING'],
                        'period' => ['type' => 'STRING']
                    ]
                ]
            ],
            [
                'name' => 'open_whatsapp',
                'description' => 'Open a chat window.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'phone' => ['type' => 'STRING'],
                        'message' => ['type' => 'STRING']
                    ]
                ]
            ],
            [
                'name' => 'open_ledger',
                'description' => 'Open or redirect browser to a Customer or Supplier ledger page.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'type' => ['type' => 'STRING', 'description' => 'Either "customer" or "supplier"'],
                        'id' => ['type' => 'INTEGER', 'description' => 'The database ID of the user or supplier']
                    ],
                    'required' => ['type', 'id']
                ]
            ]
        ];
    }
}
