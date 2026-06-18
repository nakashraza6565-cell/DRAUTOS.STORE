<?php
header('Content-Type: text/html; charset=utf-8');
require 'drautos/vendor/autoload.php';
$app = require_once 'drautos/bootstrap/app.php';

// Bootstrap web request context
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

try {
    // Login as admin
    $user = \App\User::find(1);
    \Auth::login($user);
    
    // Call controller edit action directly
    $controller = $app->make(\App\Http\Controllers\OrderController::class);
    $view = $controller->edit(534);
    
    // Render the view to HTML
    $html = $view->render();
    
    // Output the HTML
    echo $html;
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
