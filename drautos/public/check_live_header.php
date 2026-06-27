<?php
header('Content-Type: text/plain; charset=utf-8');
// Try both relative paths to find header.blade.php
$paths = [
    __DIR__ . '/../resources/views/backend/layouts/header.blade.php',
    __DIR__ . '/drautos/resources/views/backend/layouts/header.blade.php',
    __DIR__ . '/../drautos/resources/views/backend/layouts/header.blade.php'
];

foreach ($paths as $file) {
    if (file_exists($file)) {
        echo "Found file at: " . $file . "\n";
        echo "File size: " . filesize($file) . " bytes\n";
        echo "File modification time: " . date("Y-m-d H:i:s", filemtime($file)) . "\n";
        $lines = file($file);
        echo "Lines 75 to 115:\n";
        echo implode("", array_slice($lines, 74, 40)) . "\n";
        exit;
    }
}

echo "ERROR: header.blade.php not found in any path.\n";
