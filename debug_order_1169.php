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
    $customers = User::where('name', 'like', '%Khurram Shahzad%')->get(['id', 'name', 'phone', 'current_balance']);
    echo "Khurram Customers:\n";
    foreach ($customers as $c) {
        echo " - ID: {$c->id}, Bal: {$c->current_balance}\n";
    }

    $order = Order::find(1169);
    if (!$order) {
        $order = Order::where('order_number', '2606133430')->first();
    }

    if ($order) {
        echo "Order: ID={$order->id}, User={$order->user_id}, Total={$order->total_amount}, Paid={$order->amount_paid}\n";
        
        $ledgers = CustomerLedger::where('reference_id', $order->id)
            ->orWhere('description', 'like', '%' . $order->order_number . '%')
            ->get();
        echo "Ledger Entries:\n";
        foreach ($ledgers as $l) {
            $arr = [
                'id' => $l->id,
                'user_id' => $l->user_id,
                'type' => $l->type,
                'category' => $l->category,
                'amount' => $l->amount,
                'balance' => $l->balance
            ];
            echo "Entry: " . json_encode($arr) . "\n";
        }
    } else {
        echo "Order not found!\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
