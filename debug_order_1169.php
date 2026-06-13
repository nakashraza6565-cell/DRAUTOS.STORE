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

// 1. Find the target customer "Khurram Shahzad"
echo "Searching for customer 'Khurram Shahzad'...\n";
$customers = User::where('name', 'like', '%Khurram Shahzad%')->get();
if ($customers->isEmpty()) {
    echo "❌ Customer 'Khurram Shahzad' NOT found!\n";
} else {
    foreach ($customers as $c) {
        $ordersCount = Order::where('user_id', $c->id)->count();
        $ledgerCount = CustomerLedger::where('user_id', $c->id)->count();
        echo "✅ ID: {$c->id} | Name: '{$c->name}' | Phone: '{$c->phone}' | Balance: {$c->current_balance} | Created: {$c->created_at} | Orders: {$ordersCount} | Ledgers: {$ledgerCount}\n";
    }
}

echo "\n------------------------------------------------\n\n";

// 2. Find the Order
echo "Searching for Order #1169...\n";
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
    echo " - User ID (Current Customer ID): {$order->user_id}\n";
    echo " - Customer Name in Order: '{$order->first_name} {$order->last_name}'\n";
    echo " - Email: '{$order->email}'\n";
    echo " - Phone: '{$order->phone}'\n";
    echo " - Total Amount: {$order->total_amount}\n";
    echo " - Sub Total: {$order->sub_total}\n";
    echo " - Coupon/Discount: {$order->coupon}\n";
    echo " - Amount Paid: {$order->amount_paid}\n";
    echo " - Payment Method: '{$order->payment_method}'\n";
    echo " - Payment Status: '{$order->payment_status}'\n";
    echo " - Order Status: '{$order->status}'\n";
    echo " - Created At: {$order->created_at}\n";
}

echo "\n------------------------------------------------\n\n";

// 3. Find Customer Ledger Entries for this Order
if ($order) {
    echo "Searching for Customer Ledger entries associated with this order (reference_id = {$order->id} or description containing order number)...\n";
    $ledgers = CustomerLedger::where('reference_id', $order->id)
        ->orWhere('description', 'like', '%' . $order->order_number . '%')
        ->get();
    
    if ($ledgers->isEmpty()) {
        echo "ℹ️ No Customer Ledger entries found matching this order.\n";
    } else {
        echo "Found " . count($ledgers) . " ledger entry/entries:\n";
        foreach ($ledgers as $l) {
            echo " - ID: {$l->id}, User ID: {$l->user_id}, Date: {$l->transaction_date->format('Y-m-d')}, Type: '{$l->type}', Category: '{$l->category}', Desc: '{$l->description}', Amount: {$l->amount}, Balance: {$l->balance}, Ref ID: {$l->reference_id}, Financial Account ID: {$l->financial_account_id}\n";
        }
    }
}

echo "\n------------------------------------------------\n\n";

// 4. Find any Payment Reminders
if ($order) {
    echo "Searching for Payment Reminders with reference_number = '{$order->order_number}'...\n";
    $reminders = PaymentReminder::where('reference_number', $order->order_number)->get();
    if ($reminders->isEmpty()) {
        echo "ℹ️ No Payment Reminders found for this order.\n";
    } else {
        foreach ($reminders as $r) {
            echo " - ID: {$r->id}, Type: '{$r->type}', Party ID: {$r->party_id}, Amount: {$r->amount}, Status: '{$r->status}', Due Date: {$r->due_date}\n";
        }
    }
}
