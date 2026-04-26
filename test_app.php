<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Laravel Boot Debugger</h1>";

echo "Checking autoloader... ";
if (file_exists(__DIR__.'/drautos/vendor/autoload.php')) {
    echo "✅ Found!<br>";
    try {
        require __DIR__.'/drautos/vendor/autoload.php';
        echo "Autoloader loaded successfully! ✅<br>";
    } catch (\Throwable $e) {
        die("❌ Autoloader failed: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    }
} else {
    die("❌ Autoloader NOT FOUND at " . __DIR__.'/drautos/vendor/autoload.php');
}

echo "Checking bootstrap... ";
if (file_exists(__DIR__.'/drautos/bootstrap/app.php')) {
    echo "✅ Found!<br>";
    try {
        $app = require_once __DIR__.'/drautos/bootstrap/app.php';
        echo "Bootstrap loaded successfully! ✅<br>";
    } catch (\Throwable $e) {
        die("❌ Bootstrap failed: " . $e->getMessage());
    }
} else {
    die("❌ Bootstrap NOT FOUND!");
}

echo "<h2>🚀 Environment is OK. If you see this, the issue is inside your Routes or Providers.</h2>";
