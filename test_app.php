<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Laravel Deep Debugger</h1>";

echo "Checking autoloader... ";
require __DIR__.'/drautos/vendor/autoload.php';
echo "✅<br>";

echo "Checking bootstrap... ";
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
echo "✅<br>";

echo "Attempting to create Kernel... ";
try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "✅<br>";
} catch (\Throwable $e) {
    die("❌ Kernel Creation Failed: " . $e->getMessage());
}

echo "Checking Database Connection... ";
try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "✅ Connection Successful!<br>";
} catch (\Throwable $e) {
    echo "❌ Database Connection Failed: " . $e->getMessage() . "<br>";
    echo "<i>Check your .env file credentials!</i><br>";
}

echo "Attempting to boot the app... ";
try {
    $app->boot();
    echo "✅ Booted!<br>";
} catch (\Throwable $e) {
    die("❌ Boot Failed: " . $e->getMessage() . " in " . $e->getFile());
}

echo "<h2>🚀 System seems perfectly healthy. If the main site still fails, it's likely a redirect loop or an .htaccess issue.</h2>";
