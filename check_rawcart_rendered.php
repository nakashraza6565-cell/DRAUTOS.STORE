<?php
header('Content-Type: text/plain');
$filePath = __DIR__ . '/rendered_edit_page_nocache.html';
if (file_exists($filePath)) {
    $content = file_get_contents($filePath);
    // Find all occurrences of ORD-
    $offset = 0;
    while (($pos = strpos($content, 'ORD-', $offset)) !== false) {
        echo "Found order number pattern at index $pos: " . substr($content, $pos, 25) . "\n";
        $offset = $pos + 4;
    }
} else {
    echo "File not found: $filePath\n";
}
