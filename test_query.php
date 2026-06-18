<?php
header('Content-Type: text/plain; charset=utf-8');
require 'drautos/vendor/autoload.php';
$app = require_once 'drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$orders = \App\Models\Order::orderBy('id', 'desc')->limit(10)->get();
foreach ($orders as $order) {
    echo "Order ID: {$order->id}, Number: {$order->order_number}, Status: {$order->status}\n";
    echo "  cart_info count: " . $order->cart_info()->count() . "\n";
    echo "  cart count: " . $order->cart()->count() . "\n";
    foreach ($order->cart_info as $item) {
        echo "    - Item Type: {$item->item_type}, Product ID: {$item->product_id}, Bundle ID: {$item->bundle_id}, Price: {$item->price}, Qty: {$item->quantity}\n";
        echo "      Product title: " . ($item->product->title ?? 'N/A') . "\n";
        echo "      Bundle name: " . ($item->bundle->name ?? 'N/A') . "\n";
    }
    echo "\n";
}
