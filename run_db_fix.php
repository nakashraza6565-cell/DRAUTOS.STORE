<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== MAKKAH AUTOS ORDERS LIST ===\n\n";

// Bootstrap Laravel
define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // 1. Get all orders containing "Makkah" in name or matching phone numbers
    $orders = \App\Models\Order::where('first_name', 'like', '%Makkah%')
        ->orWhere('last_name', 'like', '%Makkah%')
        ->orWhere('phone', 'like', '%03118834066%')
        ->orWhere('phone', 'like', '%03009581335%')
        ->get();
        
    echo "Found " . $orders->count() . " orders in total:\n";
    foreach ($orders as $o) {
        $first = preg_replace('/[^a-zA-Z0-9\s()]/', '', $o->first_name);
        $last = preg_replace('/[^a-zA-Z0-9\s()]/', '', $o->last_name);
        echo "  ID: {$o->id} | Num: {$o->order_number} | User ID: {$o->user_id} | Name: {$first} {$last} | Phone: {$o->phone} | Total: {$o->total_amount}\n";
    }

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
