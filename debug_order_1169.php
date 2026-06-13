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

// 1. Customers named Khurram Shahzad
$customers = User::where('name', 'like', '%Khurram Shahzad%')->get();
$result['customers_count'] = $customers->count();
$result['customers'] = [];
foreach ($customers as $c) {
    $ordersCount = Order::where('user_id', $c->id)->count();
    $ledgerCount = CustomerLedger::where('user_id', $c->id)->count();
    $result['customers'][] = [
        'id' => $c->id,
        'name' => $c->name,
        'phone' => $c->phone,
        'email' => $c->email,
        'balance' => $c->current_balance,
        'created_at' => (string)$c->created_at,
        'orders_count' => $ordersCount,
        'ledgers_count' => $ledgerCount
    ];
}

// 2. Walk-in Customer
$walkInUser = User::where('email', 'walkin@pos.local')->first();
$walkInId = $walkInUser ? $walkInUser->id : 1;
$result['walkin'] = [
    'id' => $walkInId,
    'email' => $walkInUser ? $walkInUser->email : 'not_found',
    'ledger_count' => $walkInUser ? CustomerLedger::where('user_id', $walkInId)->count() : 0
];

// 3. Find the Order
$order = Order::find(1169);
if (!$order) {
    $order = Order::where('order_number', '2606133430')->first();
}

if (!$order) {
    $result['order'] = null;
} else {
    $result['order'] = [
        'id' => $order->id,
        'order_number' => $order->order_number,
        'user_id' => $order->user_id,
        'first_name' => $order->first_name,
        'last_name' => $order->last_name,
        'phone' => $order->phone,
        'total_amount' => $order->total_amount,
        'amount_paid' => $order->amount_paid,
        'payment_method' => $order->payment_method,
        'payment_status' => $order->payment_status,
        'created_at' => (string)$order->created_at
    ];

    // 4. Find Customer Ledger Entries for this Order
    $result['ledger_by_ref'] = [];
    $ledgers = CustomerLedger::where('reference_id', $order->id)->get();
    foreach ($ledgers as $l) {
        $result['ledger_by_ref'][] = [
            'id' => $l->id,
            'user_id' => $l->user_id,
            'type' => $l->type,
            'category' => $l->category,
            'description' => $l->description,
            'amount' => $l->amount,
            'balance' => $l->balance
        ];
    }

    $result['ledger_by_desc'] = [];
    $ledgersDesc = CustomerLedger::where('description', 'like', '%' . $order->order_number . '%')->get();
    foreach ($ledgersDesc as $l) {
        $result['ledger_by_desc'][] = [
            'id' => $l->id,
            'user_id' => $l->user_id,
            'type' => $l->type,
            'category' => $l->category,
            'description' => $l->description,
            'amount' => $l->amount,
            'balance' => $l->balance
        ];
    }
}

echo json_encode($result, JSON_PRETTY_PRINT);
