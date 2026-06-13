<?php
$order = \App\Models\Order::where('order_number', '2606137382')->first();
if ($order) {
    echo "Order total: " . $order->total_amount . "\n";
    foreach ($order->cart_info as $cart) {
        echo "Cart ID: " . $cart->id . " Price: " . $cart->price . " Amount: " . $cart->amount . "\n";
    }
} else {
    echo "Order not found\n";
}
