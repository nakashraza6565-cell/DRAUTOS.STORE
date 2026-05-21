<?php
header('Content-Type: text/plain');
echo "=== COMPREHENSIVE LIVE DIAGNOSTICS ===\n\n";

echo "Current Directory: " . __DIR__ . "\n";
echo "Server Time: " . date("Y-m-d H:i:s") . "\n\n";

echo "--- Directory Listing ---\n";
if ($handle = opendir(__DIR__)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $isDir = is_dir(__DIR__ . '/' . $entry) ? '[DIR]' : '[FILE]';
            $mtime = date("Y-m-d H:i:s", filemtime(__DIR__ . '/' . $entry));
            echo "{$isDir} {$entry} (Modified: {$mtime})\n";
        }
    }
    closedir($handle);
}
echo "\n";

echo "--- Routing File Diagnostic ---\n";
$routesPath = __DIR__ . '/drautos/routes/web.php';
if (file_exists($routesPath)) {
    echo "Routes file exists at: {$routesPath}\n";
    echo "Modified: " . date("Y-m-d H:i:s", filemtime($routesPath)) . "\n";
    $content = file_get_contents($routesPath);
    if (strpos($content, '/ping-recent-activity') !== false) {
        echo "RESULT: FOUND '/ping-recent-activity' route in file!\n";
    } else {
        echo "RESULT: '/ping-recent-activity' NOT found in file!\n";
    }
} else {
    echo "Routes file not found at: {$routesPath}\n";
}
echo "\n";
