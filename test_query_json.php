<?php
header('Content-Type: text/plain; charset=utf-8');
$filePath = __DIR__ . '/drautos/app/Http/Controllers/OrderController.php';

if (!file_exists($filePath)) {
    echo "File not found at: {$filePath}\n";
    exit;
}

echo "File exists! Size: " . filesize($filePath) . " bytes\n";
echo "Last Modified: " . date("Y-m-d H:i:s", filemtime($filePath)) . "\n\n";

$content = file_get_contents($filePath);

// Search for edit method in OrderController.php
$lines = explode("\n", $content);
$foundEdit = false;
$editLines = 0;
foreach ($lines as $num => $line) {
    if (strpos($line, 'public function edit(') !== false) {
        $foundEdit = true;
    }
    if ($foundEdit && $editLines < 15) {
        echo "Line " . ($num + 1) . ": " . trim($line) . "\n";
        $editLines++;
    }
}
