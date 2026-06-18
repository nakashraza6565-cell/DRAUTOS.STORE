<?php
header('Content-Type: application/json; charset=utf-8');
require 'drautos/vendor/autoload.php';
$app = require_once 'drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$order = \App\Models\Order::find(534);
$cartData = $order->cart_info->map(function($item) {
    return [
        'id' => $item->product_id,
        'bundle_id' => $item->bundle_id,
        'is_bundle' => $item->bundle_id ? true : false,
        'title' => $item->product ? $item->product->title : ($item->bundle ? $item->bundle->name : 'Unknown Product'),
        'price' => (float)($item->price ?? 0),
        'qty' => (int)($item->quantity ?? 1)
    ];
});
echo json_encode($cartData);
