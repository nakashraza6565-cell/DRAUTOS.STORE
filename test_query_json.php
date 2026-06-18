<?php
header('Content-Type: text/plain; charset=utf-8');
$filePath = __DIR__ . '/drautos/resources/views/backend/order/edit.blade.php';

if (!file_exists($filePath)) {
    echo "File not found at: {$filePath}\n";
    exit;
}

echo "File exists! Size: " . filesize($filePath) . " bytes\n";
echo "Last Modified: " . date("Y-m-d H:i:s", filemtime($filePath)) . "\n\n";

$content = file_get_contents($filePath);

// Search for cart mapping line
$lines = explode("\n", $content);
foreach ($lines as $num => $line) {
    if (strpos($line, 'cartData') !== false || strpos($line, '@json($cartData)') !== false) {
        echo "Line " . ($num + 1) . ": " . trim($line) . "\n";
    }
}
