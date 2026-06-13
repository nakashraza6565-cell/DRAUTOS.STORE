<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/plain');

require __DIR__ . '/drautos/vendor/autoload.php';
$app = require_once __DIR__ . '/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\CustomerLedger;
use App\User;

try {
    // Find customer ids
    $customerIds = User::where('name', 'like', '%Khurram Shahzad%')->pluck('id')->toArray();
    echo "Khurram Shahzad IDs: " . implode(', ', $customerIds) . "\n";

    // Walk-in Customer
    $walkInUser = User::where('email', 'walkin@pos.local')->first();
    $walkInId = $walkInUser ? $walkInUser->id : 1;
    echo "Walk-in ID: {$walkInId}\n";

    // Find Order
    $order = Order::find(1169);
    if (!$order) {
        $order = Order::where('order_number', '2606133430')->first();
    }

    if ($order) {
        echo "Order: ID={$order->id}, Num={$order->order_number}, User={$order->user_id}, Name='{$order->first_name} {$order->last_name}', Total={$order->total_amount}, Paid={$order->amount_paid}, Method={$order->payment_method}, Status={$order->payment_status}\n";
        
        // Ledger entries
        $ledgers = CustomerLedger::where('reference_id', $order->id)
            ->orWhere('description', 'like', '%' . $order->order_number . '%')
            ->get();
        echo "Ledger Entries Count: " . $ledgers->count() . "\n";
        foreach ($ledgers as $l) {
            echo "L_ID=" . $l->id . "\n";
            echo "User=" . $l->user_id . "\n";
            echo "Type=" . $l->type . "\n";
            echo "Cat=" . $l->category . "\n";
            echo "Amt=" . $l->amount . "\n";
            echo "Bal=" . $l->balance . "\n";
            echo "Acc=" . $l->financial_account_id . "\n";
        }
    } else {
        echo "Order not found!\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
