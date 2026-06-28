<?php
// Bootstrap Laravel
define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Force login as Admin
$admin = \App\User::where('role', 'admin')->first();
if (!$admin) {
    echo "ERROR: Admin user not found.\n";
    exit(1);
}
auth()->login($admin);

// Create request with custom filters
$request = \Illuminate\Http\Request::create('/admin/reports/cash-flow', 'GET', [
    'start_date' => date('Y-01-01'),
    'end_date' => date('Y-12-31'),
    'group_by' => 'monthly'
]);

try {
    echo "=== TESTING CASH FLOW REPORT ===\n";
    $controller = app()->make(\App\Http\Controllers\ReportController::class);
    $response = $controller->cashFlow($request);
    
    // If it's a View, render it to verify compile
    if ($response instanceof \Illuminate\View\View) {
        $html = $response->render();
        echo "SUCCESS: Cash Flow Report rendered successfully! Length: " . strlen($html) . " bytes\n";
    } else {
        echo "SUCCESS: Cash Flow response type: " . get_class($response) . "\n";
    }
} catch (\Throwable $e) {
    echo "FAILED: Cash Flow Report threw exception:\n" . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}

try {
    echo "\n=== TESTING SALES & PURCHASES COMPARISON REPORT ===\n";
    $request2 = \Illuminate\Http\Request::create('/admin/reports/sales-purchases', 'GET', [
        'start_date' => date('Y-01-01'),
        'end_date' => date('Y-12-31'),
        'group_by' => 'monthly'
    ]);
    $response2 = $controller->salesPurchases($request2);
    
    if ($response2 instanceof \Illuminate\View\View) {
        $html2 = $response2->render();
        echo "SUCCESS: Sales & Purchases Report rendered successfully! Length: " . strlen($html2) . " bytes\n";
    } else {
        echo "SUCCESS: Sales & Purchases response type: " . get_class($response2) . "\n";
    }
} catch (\Throwable $e) {
    echo "FAILED: Sales & Purchases Report threw exception:\n" . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
