<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Render view with mock variables so it doesn't fail
$products = \App\Models\Product::where('status', 'active')->orderBy('title')->get();
$selectedProduct = \App\Models\Product::find(18688) ?: \App\Models\Product::first();

$stats = [
    'gross_sold' => 10,
    'total_revenue' => 1000,
    'returned_qty' => 0,
    'refunded_revenue' => 0,
    'return_ratio' => 0,
    'net_sold' => 10,
    'net_revenue' => 1000,
    'total_cost' => 500,
    'gross_profit' => 500,
    'margin_loss_returns' => 0,
    'purchased_qty' => 5,
    'total_purchased_cost' => 250
];

$salesHistory = [];
$topProducts = collect();

$chartLabels = ['01 Jun', '02 Jun'];
$chartSalesData = [5, 10];
$chartPurchasesData = [2, 4];
$chartReturnsData = [0, 1];

$chartBarLabels = ['Customer A (01 Jun)', 'Customer B (02 Jun)'];
$chartBarQuantities = [5, 10];
$chartBarDetails = [
    ['customer' => 'Customer A', 'date' => '01 Jun 2026', 'qty' => 5, 'order' => '1001', 'unit' => 'Pc'],
    ['customer' => 'Customer B', 'date' => '02 Jun 2026', 'qty' => 10, 'order' => '1002', 'unit' => 'Pc']
];

$startDate = \Carbon\Carbon::now()->subMonth();
$endDate = \Carbon\Carbon::now();
$isAllTime = false;

try {
    $html = view('backend.reports.product_analysis', compact(
        'products', 'selectedProduct', 'stats', 'salesHistory', 'startDate', 'endDate',
        'chartLabels', 'chartSalesData', 'chartPurchasesData', 'chartReturnsData', 'topProducts', 'isAllTime',
        'chartBarLabels', 'chartBarQuantities', 'chartBarDetails'
    ))->render();

    echo "--- RENDERED HTML SCRIPT BLOCK ---\n";
    // Find the <script> block with 'customerSalesBarChart'
    preg_match_all('/<script\b[^>]*>(.*?)<\/script>/is', $html, $matches);
    foreach ($matches[0] as $script) {
        if (str_contains($script, 'customerSalesBarChart')) {
            echo htmlspecialchars($script) . "\n\n";
        }
    }
} catch (\Throwable $e) {
    echo "RENDER ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
