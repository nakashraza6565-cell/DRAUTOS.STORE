<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Running specific migrations for Raw Material Purchases...<br>";
    \Illuminate\Support\Facades\Artisan::call('migrate', [
        '--path' => 'database/migrations/2026_06_09_024424_create_raw_material_purchases_table.php',
        '--force' => true
    ]);
    echo nl2br(\Illuminate\Support\Facades\Artisan::output());

    \Illuminate\Support\Facades\Artisan::call('migrate', [
        '--path' => 'database/migrations/2026_06_09_024425_create_raw_material_purchase_items_table.php',
        '--force' => true
    ]);
    echo nl2br(\Illuminate\Support\Facades\Artisan::output());

    echo "<br>✅ Success! Tables created.";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
