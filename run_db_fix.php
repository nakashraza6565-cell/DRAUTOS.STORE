<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== DATABASE DIAGNOSTIC SYSTEM ===\n\n";

// Bootstrap Laravel
define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // 1. Revert Order ID 1297 if it is still pointing to User ID 80
    echo "--- Checking Order ID 1297 ---\n";
    $order1297 = \App\Models\Order::find(1297);
    if ($order1297) {
        echo "  Order 1297 current owner ID: {$order1297->user_id}\n";
        if ($order1297->user_id == 80) {
            $oldUser = \App\User::find(540);
            if ($oldUser) {
                $order1297->user_id = 540;
                $order1297->first_name = $oldUser->name;
                $order1297->last_name = '';
                $order1297->save();
                \App\Models\Cart::where('order_id', 1297)->update(['user_id' => 540]);
                echo "  Successfully reverted Order ID 1297 back to User ID 540 ({$oldUser->name}).\n";
            }
        }
    } else {
        echo "  Order ID 1297 not found.\n";
    }

    // 2. Find all orders for Makkah Autos (User ID 64)
    echo "\n--- All Orders for Makkah Autos (User ID 64) ---\n";
    $orders64 = \App\Models\Order::where('user_id', 64)->orderBy('id', 'desc')->get();
    echo "Found " . $orders64->count() . " orders:\n";
    foreach ($orders64 as $o) {
        echo "  ID: {$o->id} | Number: {$o->order_number} | Total: {$o->total_amount} | Date: {$o->created_at}\n";
    }

    // 3. Find all orders created on June 28, 2026 (today)
    echo "\n--- All Orders Created Today (2026-06-28) ---\n";
    $ordersToday = \App\Models\Order::whereDate('created_at', '2026-06-28')->get();
    echo "Found " . $ordersToday->count() . " orders:\n";
    foreach ($ordersToday as $o) {
        echo "  ID: {$o->id} | Number: {$o->order_number} | User ID: {$o->user_id} | Total: {$o->total_amount} | Date: {$o->created_at}\n";
    }

    // 4. Output a summary of last 10 orders
    echo "\n--- Last 10 Orders in Database ---\n";
    $last10 = \App\Models\Order::orderBy('id', 'desc')->limit(10)->get();
    foreach ($last10 as $o) {
        echo "  ID: {$o->id} | Number: {$o->order_number} | User ID: {$o->user_id} | Total: {$o->total_amount} | Date: {$o->created_at}\n";
    }

} catch (\Exception $e) {
    echo "\n❌ Database Error: " . $e->getMessage() . "\n";
}
