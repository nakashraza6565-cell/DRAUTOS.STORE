<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== PREVIEW SHIFT ORDER ===\n\n";

require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\User;
use App\Models\Order;
use App\Models\Cart;
use App\Models\CustomerLedger;
use App\Models\PaymentReminder;

$orderId = 1297;
$fromUserId = 64;
$toUserId = 80;

$order = Order::find($orderId);
if (!$order) {
    echo "ERROR: Order ID $orderId not found!\n";
    exit;
}

echo "Order Info:\n";
echo "  ID: {$order->id}\n";
echo "  Order Number: {$order->order_number}\n";
echo "  Current User ID: {$order->user_id}\n";
echo "  Current Name in Order: {$order->first_name} {$order->last_name}\n";
echo "  Total Amount: {$order->total_amount}\n";
echo "  Created At: {$order->created_at}\n\n";

$fromUser = User::find($fromUserId);
$toUser = User::find($toUserId);

echo "From User (ID $fromUserId):\n";
if ($fromUser) {
    echo "  Name: {$fromUser->name}\n";
    echo "  Current Balance: {$fromUser->current_balance}\n";
} else {
    echo "  NOT FOUND!\n";
}

echo "\nTo User (ID $toUserId):\n";
if ($toUser) {
    echo "  Name: {$toUser->name}\n";
    echo "  Current Balance: {$toUser->current_balance}\n";
} else {
    echo "  NOT FOUND!\n";
}

echo "\n=== Associated Cart Items ===\n";
$carts = Cart::where('order_id', $orderId)->get();
echo "Found " . $carts->count() . " cart items:\n";
foreach ($carts as $c) {
    echo "  ID: {$c->id} | User ID: {$c->user_id} | Product ID: {$c->product_id} | Qty: {$c->quantity} | Price: {$c->price}\n";
}

echo "\n=== Associated Customer Ledger Entries ===\n";
$ledgers = CustomerLedger::where('reference_id', $orderId)
    ->orWhere('description', 'like', "%{$order->order_number}%")
    ->get();
echo "Found " . $ledgers->count() . " ledger entries:\n";
foreach ($ledgers as $l) {
    echo "  ID: {$l->id} | User ID: {$l->user_id} | Date: {$l->transaction_date->format('Y-m-d')} | Type: {$l->type} | Amount: {$l->amount} | Bal: {$l->balance} | Desc: {$l->description}\n";
}

echo "\n=== Associated Payment Reminders ===\n";
$reminders = PaymentReminder::where('reference_number', $order->order_number)
    ->orWhere('user_id', $fromUserId)
    ->where('reference_number', $order->order_number)
    ->get();
echo "Found " . $reminders->count() . " reminders:\n";
foreach ($reminders as $r) {
    echo "  ID: {$r->id} | User ID: {$r->user_id} | Ref Num: {$r->reference_number} | Amount: {$r->amount} | Description: {$r->description}\n";
}
