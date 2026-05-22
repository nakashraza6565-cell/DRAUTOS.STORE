<?php

// Basic diagnostic script to test Gemini API Key and endpoints on the live server
header('Content-Type: text/plain; charset=utf-8');

echo "=== Danyal Autos Gemini API Diagnostic ===\n\n";

// 1. Load the active key
$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath)) {
    echo "❌ Error: .env file not found at " . realpath($envPath) . "\n";
    exit;
}

$envContent = file_get_contents($envPath);
$key = null;
if (preg_match('/^GEMINI_API_KEY\s*=\s*[\'"]?([^\'"\r\n]+)/m', $envContent, $matches)) {
    $key = trim($matches[1]);
}

if (!$key) {
    echo "❌ Error: GEMINI_API_KEY is not defined in your .env file.\n";
    exit;
}

$maskedKey = substr($key, 0, 8) . '...' . substr($key, -6);
echo "🔑 Loaded API Key (Masked): $maskedKey\n";
echo "🔑 Key Length: " . strlen($key) . " characters\n\n";

// 2. Test model list endpoint
$listUrl = "https://generativelanguage.googleapis.com/v1beta/models?key=" . $key;
echo "🌐 Testing Model Discovery Endpoint (v1beta):\nURL: $listUrl\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $listUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$listResponse = curl_exec($ch);
$listHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "📥 HTTP Response Code: $listHttpCode\n";
echo "📥 Raw Discovery Response:\n" . json_encode(json_decode($listResponse), JSON_PRETTY_PRINT) . "\n\n";

// 3. Test generateContent on gemini-1.5-flash
$generateUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $key;
echo "🌐 Testing Content Generation (v1beta / gemini-1.5-flash):\nURL: $generateUrl\n";

$payload = [
    'contents' => [
        [
            'parts' => [
                ['text' => 'Hello, please say "Danyal Autos AI is ready" in one short sentence.']
            ]
        ]
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $generateUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$genResponse = curl_exec($ch);
$genHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "📥 HTTP Response Code: $genHttpCode\n";
echo "📥 Raw Generate Content Response:\n" . json_encode(json_decode($genResponse), JSON_PRETTY_PRINT) . "\n\n";

echo "=== Diagnostic Complete ===";
