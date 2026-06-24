<?php
header('Content-Type: text/plain; charset=utf-8');
require 'drautos/vendor/autoload.php';
$app = require_once 'drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Find products with sales, incoming items, and returns
$products = \DB::table('products')
    ->select('id', 'title')
    ->where('status', 'active')
    ->get();

echo "Product ID | Title | Sales (Delivered) | Incoming (Purchases) | Returns (Approved)\n";
echo str_repeat("-", 90) . "\n";

foreach ($products as $p) {
    $salesCount = \DB::table('carts')
        ->join('orders', 'carts.order_id', '=', 'orders.id')
        ->where('carts.product_id', $p->id)
        ->where('orders.status', 'delivered')
        ->count();

    $incomingCount = \DB::table('inventory_incoming_items')
        ->where('product_id', $p->id)
        ->count();

    $returnsCount = \DB::table('sale_return_items')
        ->join('sale_returns', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')
        ->where('sale_return_items.product_id', $p->id)
        ->where('sale_returns.status', 'approved')
        ->count();

    if ($salesCount > 0 || $incomingCount > 0 || $returnsCount > 0) {
        printf("%-10d | %-30s | %-17d | %-20d | %-16d\n", $p->id, substr($p->title, 0, 30), $salesCount, $incomingCount, $returnsCount);
    }
}

