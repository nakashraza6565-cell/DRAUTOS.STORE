<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== DETAILED DATABASE SEARCH ===\n\n";

// Bootstrap Laravel
define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // 1. Search for any orders with "Makkah" in name or phone matching
    echo "--- Searching Orders by Name / Phone ---\n";
    $orders = \App\Models\Order::where('first_name', 'like', '%Makkah%')
        ->orWhere('last_name', 'like', '%Makkah%')
        ->orWhere('phone', 'like', '%03118834066%')
        ->orWhere('phone', 'like', '%03009581335%')
        ->get();
        
    echo "Found " . $orders->count() . " orders:\n";
    foreach ($orders as $o) {
        echo "  ID: {$o->id} | Number: {$o->order_number} | User ID: {$o->user_id} | Name: {$o->first_name} {$o->last_name} | Phone: {$o->phone} | Total: {$o->total_amount} | Date: {$o->created_at}\n";
    }

    // 2. Search all Customer Ledger entries with "Makkah"
    echo "\n--- Searching Ledger by Description / User ---\n";
    $ledgers = \App\Models\CustomerLedger::where('description', 'like', '%Makkah%')->get();
    echo "Found " . $ledgers->count() . " entries:\n";
    foreach ($ledgers as $l) {
        echo "  ID: {$l->id} | User ID: {$l->user_id} | Amount: {$l->amount} | Desc: {$l->description}\n";
    }

    // 3. Search all Users with "Makkah" in their name
    echo "\n--- All Users with 'Makkah' ---\n";
    $users = \App\User::where('name', 'like', '%Makkah%')->get();
    foreach ($users as $u) {
        echo "  ID: {$u->id} | Name: {$u->name} | Phone: {$u->phone} | Balance: {$u->current_balance}\n";
    }

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
