<?php
/**
 * 🧹 Extreme Cache Cleaner for Laravel on Hostinger
 */

define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h1>🧹 Danyal Autos - Cache Cleaner</h1>";

try {
    echo "Clearing View Cache... ";
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "✅<br>";

    echo "Clearing Route Cache... ";
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    echo "✅<br>";

    echo "Clearing Config Cache... ";
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "✅<br>";

    echo "Clearing Application Cache... ";
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "✅<br>";

    echo "Optimizing... ";
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    echo "✅<br>";

    if(function_exists('opcache_reset')) {
        echo "Resetting PHP OPcache... ";
        opcache_reset();
        echo "✅<br>";
    }

    echo "<h2>🚀 All caches cleared!</h2>";
    echo "<p>Please refresh your site now. You should see the <b>Red Bar</b> if the files were updated.</p>";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
