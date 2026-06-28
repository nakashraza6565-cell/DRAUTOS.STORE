<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== LARAVEL DASHBOARD CONTROLLER TEST ===\n\n";

// Bootstrap Laravel
define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Laravel booted successfully.\n";
    
    // Simulate Admin login
    $admin = \App\User::where('role', 'admin')->first();
    if ($admin) {
        auth()->login($admin);
        echo "Logged in as Admin: {$admin->name}\n";
    } else {
        echo "Warning: No Admin user found in database.\n";
    }
    
    // Share $errors variable
    view()->share('errors', new \Illuminate\Support\ViewErrorBag());
    
    echo "Executing AdminController@index method...\n";
    
    // Setup request
    $request = \Illuminate\Http\Request::create('/admin', 'GET');
    $app->instance('request', $request);
    
    $controller = $app->make(\App\Http\Controllers\AdminController::class);
    
    // Call controller index method
    $response = $controller->index($request);
    
    if (is_object($response) && method_exists($response, 'render')) {
        echo "Controller returned a view. Rendering...\n";
        $html = $response->render();
        echo "SUCCESS: Controller view rendered successfully! Length: " . strlen($html) . " bytes.\n";
        echo "First 500 characters of HTML:\n";
        echo substr($html, 0, 500) . "\n";
    } else {
        echo "SUCCESS: Controller returned response: " . get_class($response) . "\n";
        if (method_exists($response, 'getContent')) {
            $content = $response->getContent();
            echo "Response Content Length: " . strlen($content) . " bytes.\n";
            echo "First 500 characters:\n" . substr($content, 0, 500) . "\n";
        }
    }
    
} catch (\Throwable $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
