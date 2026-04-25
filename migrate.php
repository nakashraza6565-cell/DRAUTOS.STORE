<?php
/**
 * 🚀 Database Migration Script for Hostinger
 * This script runs 'php artisan migrate' directly from your browser.
 */

define('LARAVEL_START', microtime(true));

// 1. Load the AutoLoader
require __DIR__ . '/drautos/vendor/autoload.php';

// 2. Start the Laravel App
$app = require_once __DIR__ . '/drautos/bootstrap/app.php';

// 3. Run the Command
use Illuminate\Support\Facades\Artisan;

echo "⏳ <b>Database Migration Started...</b><br>";

try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->handle(Illuminate\Http\Request::capture());

    // Run migration with --force (required in production)
    Artisan::call('migrate', ['--force' => true]);
    
    echo "✅ <b>Success!</b> The database tables have been updated.<br>";
    echo "📜 <b>Output:</b><br><pre>" . Artisan::output() . "</pre>";
    echo "<br>⚠️ <i>Reminder: Delete this migrate.php file now for security.</i>";

} catch (\Exception $e) {
    echo "❌ <b>Error:</b> " . $e->getMessage();
}
?>
