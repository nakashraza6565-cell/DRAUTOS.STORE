<?php
header('Content-Type: text/plain; charset=utf-8');
require 'drautos/vendor/autoload.php';
$app = require_once 'drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$cartItems = \App\Models\Cart::where('product_id', 1544)->get();
echo "Found " . $cartItems->count() . " cart items with product_id 1544:\n";
foreach ($cartItems as $item) {
    echo "  Cart ID: {$item->id}, Order ID: {$item->order_id}, Price: {$item->price}, Qty: {$item->quantity}\n";
    if ($item->order) {
        echo "    Order: {$item->order->order_number}, Customer: {$item->order->first_name} {$item->order->last_name}\n";
    } else {
        echo "    Order relation is NULL!\n";
    }
}
