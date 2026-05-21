<?php
header('Content-Type: text/plain');
define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apiKey = env('GEMINI_API_KEY');
if (!$apiKey) {
    echo "ERROR: GEMINI_API_KEY is not defined in the live .env file!\n";
    exit;
}

echo "API Key loaded (length: " . strlen($apiKey) . ", starts with: " . substr($apiKey, 0, 6) . ")\n\n";

$urls = [
    'v1beta Models' => "https://generativelanguage.googleapis.com/v1beta/models?key=" . $apiKey,
    'v1 Models' => "https://generativelanguage.googleapis.com/v1/models?key=" . $apiKey
];

foreach ($urls as $label => $url) {
    echo "=== Fetching {$label} ===\n";
    try {
        $response = \Illuminate\Support\Facades\Http::timeout(10)->get($url);
        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['models'])) {
                foreach ($data['models'] as $m) {
                    echo "  - " . $m['name'] . " (Methods: " . implode(', ', $m['supportedGenerationMethods']) . ")\n";
                }
            } else {
                echo "Success, but no models list in response.\n";
            }
        } else {
            echo "Error status " . $response->status() . ": " . $response->body() . "\n";
        }
    } catch (\Exception $e) {
        echo "Exception: " . $e->getMessage() . "\n";
    }
    echo "\n";
}
