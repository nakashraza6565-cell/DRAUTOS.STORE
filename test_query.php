<?php
header('Content-Type: text/plain; charset=utf-8');
require 'drautos/vendor/autoload.php';
$app = require_once 'drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $orderId = 534;
    $order = \App\Models\Order::find($orderId);
    
    if (!$order) {
        echo "Order {$orderId} not found!\n";
        exit;
    }
    
    echo "===========================================\n";
    echo "ORDER ID: {$order->id} | Number: {$order->order_number}\n";
    echo "Total Amount: {$order->total_amount} | Subtotal: {$order->sub_total}\n";
    echo "===========================================\n";
    
    echo "1. TESTING cart RELATION:\n";
    $cartItems = $order->cart;
    echo "Count of cart: " . count($cartItems) . "\n";
    foreach ($cartItems as $c) {
        echo "  - Cart ID: {$c->id} | Product ID: {$c->product_id} | Bundle ID: {$c->bundle_id} | Price: {$c->price} | Qty: {$c->quantity}\n";
        echo "    Product: " . ($c->product ? $c->product->title : 'NULL') . "\n";
        echo "    Bundle: " . ($c->bundle ? $c->bundle->name : 'NULL') . "\n";
    }
    
    echo "\n2. TESTING cart_info RELATION:\n";
    $cartInfoItems = $order->cart_info;
    echo "Count of cart_info: " . count($cartInfoItems) . "\n";
    foreach ($cartInfoItems as $c) {
        echo "  - Cart ID: {$c->id} | Product ID: {$c->product_id} | Bundle ID: {$c->bundle_id} | Price: {$c->price} | Qty: {$c->quantity}\n";
        echo "    Product: " . ($c->product ? $c->product->title : 'NULL') . "\n";
        echo "    Bundle: " . ($c->bundle ? $c->bundle->name : 'NULL') . "\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
