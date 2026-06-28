<?php
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\User;
use App\Models\Order;
use App\Models\CustomerLedger;

echo "=== USER SEARCH ===\n";
$users = User::where('name', 'like', '%Makkah%')->get();
foreach ($users as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Phone: {$u->phone} | Role: {$u->role}\n";
}

echo "\n=== ORDER SEARCH ===\n";
$orderNumber = '2806213043';
$order = Order::where('order_number', 'like', "%{$orderNumber}%")
              ->orWhere('id', 'like', "%{$orderNumber}%")
              ->first();
if ($order) {
    echo "Order ID: {$order->id}\n";
    echo "Order Number: {$order->order_number}\n";
    echo "User ID: {$order->user_id}\n";
    echo "First Name: {$order->first_name}\n";
    echo "Last Name: {$order->last_name}\n";
    echo "Total Amount: {$order->total_amount}\n";
    echo "Created At: {$order->created_at}\n";
} else {
    echo "Order not found with query: {$orderNumber}\n";
    
    echo "Recent orders:\n";
    $recent = Order::orderBy('id', 'desc')->limit(5)->get();
    foreach ($recent as $o) {
        echo "ID: {$o->id} | Num: {$o->order_number} | User ID: {$o->user_id} | Name: {$o->first_name} {$o->last_name} | Total: {$o->total_amount} | Date: {$o->created_at}\n";
    }
}
