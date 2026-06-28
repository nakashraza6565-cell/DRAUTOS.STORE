<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$admin = \App\User::where('role', 'admin')->first();
auth()->login($admin);

$request = \Illuminate\Http\Request::create('/admin/reports/cash-flow', 'GET', [
    'start_date' => date('Y-01-01'),
    'end_date' => date('Y-12-31'),
    'group_by' => 'monthly'
]);

try {
    $controller = app()->make(\App\Http\Controllers\ReportController::class);
    $response = $controller->cashFlow($request);
    if ($response instanceof \Illuminate\View\View) {
        $html = $response->render();
        echo "RENDER SUCCESS: Length " . strlen($html) . "\n";
    } else {
        echo "RESPONSE SUCCESS: " . get_class($response) . "\n";
    }
} catch (\Throwable $e) {
    echo "ERROR EXCEPTION:\n";
    echo $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo $e->getTraceAsString() . "\n";
}
