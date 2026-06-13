<?php
header('Content-Type: application/json');

require __DIR__ . '/drautos/vendor/autoload.php';
$app = require_once __DIR__ . '/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\CustomerLedger;
use App\Models\PaymentReminder;
use App\User;

$result = [];

$user = User::find(512);
if ($user) {
    $result['user'] = [
        'id' => $user->id,
        'name' => $user->name,
        'phone' => $user->phone,
        'email' => $user->email,
        'address' => $user->address,
        'courier_company' => $user->courier_company,
        'courier_number' => $user->courier_number,
        'current_balance' => $user->current_balance
    ];
} else {
    $result['user'] = null;
}

$order = Order::find(1169);
if ($order) {
    $result['order'] = [
        'id' => $order->id,
        'order_number' => $order->order_number,
        'user_id' => $order->user_id,
        'first_name' => $order->first_name,
        'last_name' => $order->last_name,
        'email' => $order->email,
        'phone' => $order->phone,
        'address1' => $order->address1,
        'courier_company' => $order->courier_company,
        'courier_number' => $order->courier_number,
        'total_amount' => $order->total_amount,
        'amount_paid' => $order->amount_paid,
        'payment_method' => $order->payment_method,
        'payment_status' => $order->payment_status,
        'status' => $order->status
    ];
} else {
    $result['order'] = null;
}

$ledger = CustomerLedger::find(2209);
if ($ledger) {
    $result['ledger'] = [
        'id' => $ledger->id,
        'user_id' => $ledger->user_id,
        'type' => $ledger->type,
        'category' => $ledger->category,
        'description' => $ledger->description,
        'amount' => $ledger->amount,
        'balance' => $ledger->balance,
        'reference_id' => $ledger->reference_id,
        'financial_account_id' => $ledger->financial_account_id
    ];
} else {
    $result['ledger'] = null;
}

$reminder = PaymentReminder::find(844);
if ($reminder) {
    $result['reminder'] = [
        'id' => $reminder->id,
        'type' => $reminder->type,
        'party_type' => $reminder->party_type,
        'party_id' => $reminder->party_id,
        'reference_number' => $reminder->reference_number,
        'amount' => $reminder->amount,
        'due_date' => (string)$reminder->due_date,
        'status' => $reminder->status,
        'notes' => $reminder->notes
    ];
} else {
    $result['reminder'] = null;
}

echo json_encode($result, JSON_PRETTY_PRINT);
