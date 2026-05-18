<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Running Migrations...<br>";
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo nl2br(\Illuminate\Support\Facades\Artisan::output());
    echo "<br>✅ Success!";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
