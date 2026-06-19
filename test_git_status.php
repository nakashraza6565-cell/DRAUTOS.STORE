<?php
header('Content-Type: text/plain');
echo "=== Live File Checker - edit.blade.php script section ===\n";
$filePath = __DIR__ . '/drautos/resources/views/backend/order/edit.blade.php';
if (file_exists($filePath)) {
    $content = file_get_contents($filePath);
    // Find the @push('scripts') section
    $pos = strpos($content, "@push('scripts')");
    if ($pos !== false) {
        echo substr($content, $pos, 1000);
    } else {
        echo "@push('scripts') not found\n";
    }
} else {
    echo "File not found at: $filePath\n";
}
