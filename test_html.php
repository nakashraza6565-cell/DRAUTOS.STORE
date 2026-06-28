<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$admin = \App\User::where('role', 'admin')->first();
auth()->login($admin);

$request = \Illuminate\Http\Request::create('/admin/reports/product-analysis', 'GET', [
    'product_id' => 18688,
    'start_date' => '2026-03-01',
    'end_date' => '2026-06-28'
]);

try {
    $controller = app()->make(\App\Http\Controllers\ReportController::class);
    $response = $controller->productAnalysis($request);
    if ($response instanceof \Illuminate\View\View) {
        $html = $response->render();
        // Dump everything from <script> to </script> near the bottom
        preg_match_all('/<script\b[^>]*>(.*?)<\/script>/is', $html, $matches);
        echo "FOUND SCRIPTS: " . count($matches[0]) . "\n\n";
        foreach ($matches[0] as $i => $script) {
            if (str_contains($script, 'Chart') || str_contains($script, 'salesVelocityChart') || str_contains($script, 'customerSalesBarChart')) {
                echo "--- Script $i ---\n";
                echo $script . "\n\n";
            }
        }
    } else {
        echo "RESPONSE IS NOT View: " . get_class($response) . "\n";
    }
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
