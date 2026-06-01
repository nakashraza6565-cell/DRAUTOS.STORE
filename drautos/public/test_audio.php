<?php
$dir = __DIR__ . '/storage/chat_audio';
if (!is_dir($dir)) {
    echo "Directory not found: $dir\n";
    
    // Check if storage is a symlink
    $storageDir = __DIR__ . '/storage';
    if (is_link($storageDir)) {
        echo "Storage is a symlink to: " . readlink($storageDir) . "\n";
    }
    exit;
}

$files = scandir($dir);
foreach ($files as $file) {
    if ($file == '.' || $file == '..') continue;
    $path = $dir . '/' . $file;
    echo $file . " - Size: " . filesize($path) . " bytes\n";
}
