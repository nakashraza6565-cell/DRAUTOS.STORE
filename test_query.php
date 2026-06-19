<?php
header('Content-Type: text/plain; charset=utf-8');
require 'drautos/vendor/autoload.php';
$app = require_once 'drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$orders = \App\Models\Order::where('phone', '03248653890')->orderBy('id', 'desc')->get();
if ($orders->isEmpty()) {
    echo "No orders found for phone 03248653890. Checking by first_name like Tayyaba...\n";
    $orders = \App\Models\Order::where('first_name', 'like', '%Tayyaba%')->orderBy('id', 'desc')->get();
}

foreach ($orders as $order) {
    echo "Order ID: {$order->id}, Number: {$order->order_number}, Status: {$order->status}, Customer: {$order->first_name} {$order->last_name}\n";
    echo "  cart_info count: " . $order->cart_info()->count() . "\n";
    echo "  cart count: " . $order->cart()->count() . "\n";
    foreach ($order->cart_info as $item) {
        echo "    - Item Type: {$item->item_type}, Product ID: {$item->product_id}, Bundle ID: {$item->bundle_id}, Price: {$item->price}, Qty: {$item->quantity}\n";
        echo "      Product: " . ($item->product ? $item->product->title : 'NULL') . "\n";
        echo "      Bundle: " . ($item->bundle ? $item->bundle->name : 'NULL') . "\n";
    }
    
    // Map just like in edit.blade.php
    $cartData = $order->cart_info->map(function($item) {
        return [
            'id' => $item->product_id,
            'bundle_id' => $item->bundle_id,
            'is_bundle' => $item->bundle_id ? true : false,
            'title' => $item->product ? $item->product->title : ($item->bundle ? $item->bundle->name : 'Unknown Product'),
            'price' => (float)($item->price ?? 0),
            'qty' => (int)($item->quantity ?? 1)
        ];
    })->values();
    
    echo "  Mapped JSON Data: " . json_encode($cartData) . "\n\n";
}
