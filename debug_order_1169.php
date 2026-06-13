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
use App\Models\PaymentReminder;
use App\User;

try {
    // 1. Search users matching "Khurram" or "Shahzad"
    $users = User::where('name', 'like', '%Khurram%')
        ->orWhere('name', 'like', '%Shahzad%')
        ->get(['id', 'name', 'phone', 'email', 'current_balance', 'created_at']);
    
    echo "Matching Customers:\n";
    foreach ($users as $u) {
        $ordersCount = Order::where('user_id', $u->id)->count();
        $ledgerCount = CustomerLedger::where('user_id', $u->id)->count();
        echo " - ID: {$u->id} | Name: {$u->name} | Phone: {$u->phone} | Email: {$u->email} | Bal: {$u->current_balance} | Created: {$u->created_at} | Orders: {$ordersCount} | Ledgers: {$ledgerCount}\n";
    }

    // 2. Find the Order
    $order = Order::find(1169);
    if (!$order) {
        $order = Order::where('order_number', '2606133430')->first();
    }

    if ($order) {
        echo "\nOrder Details:\n";
        echo " - ID: {$order->id}\n";
        echo " - Order Number: {$order->order_number}\n";
        echo " - User ID: {$order->user_id}\n";
        echo " - Name: {$order->first_name} {$order->last_name}\n";
        echo " - Total: {$order->total_amount} | Paid: {$order->amount_paid} | Method: {$order->payment_method} | Status: {$order->payment_status}\n";
        echo " - Created: {$order->created_at}\n";
        
        // 3. Find Ledger Entries by reference_id (Indexed and fast)
        echo "\nLedger Entries by reference_id:\n";
        $ledgers = CustomerLedger::where('reference_id', $order->id)->get();
        foreach ($ledgers as $l) {
            echo " - L_ID={$l->id} | User={$l->user_id} | Type={$l->type} | Cat={$l->category} | Amt={$l->amount} | Bal={$l->balance} | Acc={$l->financial_account_id}\n";
        }
        
        // 4. Find Payment Reminders by reference_number
        echo "\nPayment Reminders:\n";
        $reminders = PaymentReminder::where('reference_number', $order->order_number)->get();
        foreach ($reminders as $r) {
            echo " - R_ID={$r->id} | Party_ID={$r->party_id} | Party_Type={$r->party_type} | Amt={$r->amount} | Status={$r->status} | Due={$r->due_date}\n";
        }
    } else {
        echo "Order not found!\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
