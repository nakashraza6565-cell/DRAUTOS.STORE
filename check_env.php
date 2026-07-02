<?php
// Read what .env is available on the live server
$paths = [
    __DIR__ . '/drautos/.env',
    __DIR__ . '/.env',
    dirname(__DIR__) . '/.env',
    '/home/u909342762/drautos.store/drautos/.env',
    '/home/u704900370/drautos.store/drautos/.env',
];

echo "=== Finding .env on Live Server ===\n";
echo "Script location: " . __DIR__ . "\n\n";

foreach ($paths as $path) {
    echo "Checking: $path\n";
    if (file_exists($path)) {
        echo "  FOUND!\n";
        $content = file_get_contents($path);
        // Show only DB lines for security
        foreach (explode("\n", $content) as $line) {
            if (preg_match('/^DB_/', $line)) {
                echo "  " . $line . "\n";
            }
        }
    } else {
        echo "  Not found.\n";
    }
}

// Also show environment variables
echo "\n=== PHP Environment Variables (DB_*) ===\n";
foreach ($_ENV as $k => $v) {
    if (strpos($k, 'DB_') === 0) {
        echo "  $k=$v\n";
    }
}

// Check if we can get it via getenv
echo "\n=== getenv DB vars ===\n";
foreach (['DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'] as $var) {
    $val = getenv($var);
    echo "  $var=" . ($val !== false ? $val : '(not set)') . "\n";
}
?>
