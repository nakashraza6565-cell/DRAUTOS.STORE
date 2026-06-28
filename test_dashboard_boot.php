<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== LARAVEL DASHBOARD BOOT TEST ===\n\n";

// Bootstrap Laravel
define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Laravel booted successfully.\n";
    
    // Simulate Admin login if not authenticated
    $admin = \App\User::where('role', 'admin')->first();
    if ($admin) {
        auth()->login($admin);
        echo "Logged in as Admin: {$admin->name}\n";
    } else {
        echo "Warning: No Admin user found in database.\n";
    }
    
    echo "Rendering backend.index view...\n";
    
    // Renders the view and outputs it or catches error
    $view = view('backend.index');
    // Force compile
    $html = $view->render();
    echo "SUCCESS: Dashboard view rendered successfully! Length: " . strlen($html) . " bytes.\n";
    echo "First 500 characters of HTML:\n";
    echo substr($html, 0, 500) . "\n";
    
} catch (\Throwable $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
