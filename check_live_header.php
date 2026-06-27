<?php
header('Content-Type: text/plain; charset=utf-8');
$file = __DIR__ . '/drautos/resources/views/backend/layouts/header.blade.php';
if (file_exists($file)) {
    echo "File size: " . filesize($file) . "\n";
    echo "File modification time: " . date("Y-m-d H:i:s", filemtime($file)) . "\n";
    echo "First 10 lines:\n";
    $lines = file($file);
    echo implode("", array_slice($lines, 0, 10)) . "\n";
    echo "Lines 90 to 125:\n";
    echo implode("", array_slice($lines, 89, 36)) . "\n";
} else {
    echo "File not found at: " . $file;
}
