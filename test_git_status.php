<?php
header('Content-Type: text/plain');
echo "=== Live File Checker ===\n";
$filePath = __DIR__ . '/drautos/routes/web.php';
if (file_exists($filePath)) {
    echo "File exists!\n";
    echo "Modified: " . date("Y-m-d H:i:s", filemtime($filePath)) . "\n";
    $content = file_get_contents($filePath);
    if (strpos($content, '/ping-recent-activity') !== false) {
        echo "FOUND: /ping-recent-activity in the file!\n";
    } else {
        echo "NOT FOUND: /ping-recent-activity in the file!\n";
    }
} else {
    echo "File not found at: $filePath\n";
}
