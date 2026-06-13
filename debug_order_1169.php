<?php
header('Content-Type: text/plain');

require __DIR__ . '/drautos/vendor/autoload.php';
$app = require_once __DIR__ . '/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\CustomerLedger;
use App\Models\PaymentReminder;
use App\User;

echo "=== DIAGNOSTIC FOR ORDER 1169 ===\n\n";

// 1. Customers named Khurram Shahzad
$customers = User::where('name', 'like', '%Khurram Shahzad%')->get();
echo "Total 'Khurram Shahzad' users found: " . $customers->count() . "\n";
$hasActivity = [];
$noActivity = [];
foreach ($customers as $c) {
    $ordersCount = Order::where('user_id', $c->id)->count();
    $ledgerCount = CustomerLedger::where('user_id', $c->id)->count();
    $info = "ID: {$c->id} | Name: '{$c->name}' | Phone: '{$c->phone}' | Balance: {$c->current_balance} | Created: {$c->created_at} | Orders: {$ordersCount} | Ledgers: {$ledgerCount}";
    if ($ordersCount > 0 || $ledgerCount > 0) {
        $hasActivity[] = $info;
    } else {
        $noActivity[] = $info;
    }
}

echo "\n--- Users with Activity ---\n";
if (empty($hasActivity)) {
    echo "None\n";
} else {
    foreach ($hasActivity as $act) echo $act . "\n";
}

echo "\n--- Users without Activity (Showing first 5) ---\n";
foreach (array_slice($noActivity, 0, 5) as $noAct) {
    echo $noAct . "\n";
}

echo "\n------------------------------------------------\n\n";

// 2. Walk-in Customer
$walkInUser = User::where('email', 'walkin@pos.local')->first();
$walkInId = $walkInUser ? $walkInUser->id : 1;
echo "Walk-in Customer ID: {$walkInId}\n";
if ($walkInUser) {
    $walkInLedgerCount = CustomerLedger::where('user_id', $walkInId)->count();
    echo "Walk-in Ledger Entry Count: {$walkInLedgerCount}\n";
}

echo "\n------------------------------------------------\n\n";

// 3. Find the Order
$order = Order::find(1169);
if (!$order) {
    $order = Order::where('order_number', '2606133430')->first();
}

if (!$order) {
    echo "❌ Order NOT found by ID 1169 or Number 2606133430!\n";
} else {
    echo "✅ Found Order:\n";
    echo " - ID: {$order->id}\n";
    echo " - Order Number: {$order->order_number}\n";
    echo " - User ID: {$order->user_id}\n";
    echo " - Customer Name: '{$order->first_name} {$order->last_name}'\n";
    echo " - Phone: '{$order->phone}'\n";
    echo " - Total: {$order->total_amount} | Paid: {$order->amount_paid} | Payment Method: {$order->payment_method} | Status: {$order->payment_status}\n";
    echo " - Created At: {$order->created_at}\n";
}

echo "\n------------------------------------------------\n\n";

// 4. Find Customer Ledger Entries for this Order
if ($order) {
    echo "Ledger entries matching reference_id = {$order->id}:\n";
    $ledgers = CustomerLedger::where('reference_id', $order->id)->get();
    if ($ledgers->isEmpty()) {
         echo "None\n";
    } else {
        foreach ($ledgers as $l) {
            echo " - Ledger ID: {$l->id} | User ID: {$l->user_id} | Type: {$l->type} | Cat: {$l->category} | Desc: '{$l->description}' | Amt: {$l->amount} | Bal: {$l->balance}\n";
        }
    }

    echo "\nLedger entries matching description like '%{$order->order_number}%':\n";
    $ledgersDesc = CustomerLedger::where('description', 'like', '%' . $order->order_number . '%')->get();
    if ($ledgersDesc->isEmpty()) {
         echo "None\n";
    } else {
        foreach ($ledgersDesc as $l) {
            echo " - Ledger ID: {$l->id} | User ID: {$l->user_id} | Type: {$l->type} | Cat: {$l->category} | Desc: '{$l->description}' | Amt: {$l->amount} | Bal: {$l->balance}\n";
        }
    }
}
