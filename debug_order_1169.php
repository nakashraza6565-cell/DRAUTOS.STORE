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

$out = "";

try {
    $out .= "=== DIAGNOSTIC FOR ORDER 1169 ===\n\n";

    // 1. Customers named Khurram Shahzad
    $users = User::where('name', 'Khurram Shahzad')->get();
    $out .= "Khurram Shahzad Customers Count: " . $users->count() . "\n";
    foreach ($users as $u) {
        $out .= " - ID: {$u->id} | Name: {$u->name} | Phone: {$u->phone} | Email: {$u->email} | Bal: {$u->current_balance} | Created: {$u->created_at}\n";
    }

    // 2. Find the Order
    $order = Order::find(1169);
    if ($order) {
        $out .= "\nOrder Details:\n";
        $out .= " - ID: {$order->id}\n";
        $out .= " - Order Number: {$order->order_number}\n";
        $out .= " - User ID: {$order->user_id}\n";
        $out .= " - Name: {$order->first_name} {$order->last_name}\n";
        $out .= " - Total: {$order->total_amount} | Paid: {$order->amount_paid} | Method: {$order->payment_method} | Status: {$order->payment_status}\n";
        $out .= " - Created: {$order->created_at}\n";
        
        // 3. Find Ledger Entries by reference_id (Indexed and fast)
        $out .= "\nLedger Entries by reference_id:\n";
        $ledgers = CustomerLedger::where('reference_id', $order->id)->get();
        foreach ($ledgers as $l) {
            $out .= " - L_ID={$l->id} | User={$l->user_id} | Type={$l->type} | Cat={$l->category} | Amt={$l->amount} | Bal={$l->balance} | Acc={$l->financial_account_id}\n";
        }
        
        // 4. Find Payment Reminders by reference_number
        $out .= "\nPayment Reminders:\n";
        $reminders = PaymentReminder::where('reference_number', $order->order_number)->get();
        foreach ($reminders as $r) {
            $out .= " - R_ID={$r->id} | Party_ID={$r->party_id} | Party_Type={$r->party_type} | Amt={$r->amount} | Status={$r->status} | Due={$r->due_date}\n";
        }
    } else {
        $out .= "Order not found!\n";
    }
} catch (\Exception $e) {
    $out .= "ERROR: " . $e->getMessage() . "\n";
    $out .= $e->getTraceAsString() . "\n";
}

file_put_contents(__DIR__ . '/debug_out.txt', $out);
echo "SUCCESS: Output written to debug_out.txt\n";
echo $out;
