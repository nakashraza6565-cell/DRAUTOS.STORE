<?php
header('Content-Type: text/plain');
echo "=== DIRECT FILE READ SYSTEM ===\n\n";

$files = [
    'manifest.json',
    'assetlinks.php',
    '.well-known/assetlinks.json'
];

foreach ($files as $file) {
    echo "Checking: $file\n";
    if (file_exists($file)) {
        echo "Status: EXISTS\n";
        echo "Size: " . filesize($file) . " bytes\n";
        echo "Last Modified: " . date("Y-m-d H:i:s", filemtime($file)) . "\n";
        echo "Content:\n" . file_get_contents($file) . "\n";
    } else {
        echo "Status: NOT FOUND\n";
    }
    echo str_repeat("-", 40) . "\n\n";
}
