<?php
header('Content-Type: application/json');

require __DIR__ . '/drautos/vendor/autoload.php';
$app = require_once __DIR__ . '/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\CustomerLedger;
use App\Models\PaymentReminder;
use Illuminate\Support\Facades\DB;

$response = [
    'success' => false,
    'message' => ''
];

try {
    DB::transaction(function() use (&$response) {
        // 1. Fetch target order
        $order = Order::find(1169);
        if (!$order) {
            throw new Exception("Order 1169 not found!");
        }

        if ($order->order_number != '2606133430') {
            throw new Exception("Order number mismatch: expected 2606133430, found {$order->order_number}");
        }

        if ($order->user_id == 512) {
            throw new Exception("Order is already assigned to Khurram Shahzad (ID 512)!");
        }

        $oldUserId = $order->user_id;

        // 2. Update Order fields
        $order->user_id = 512;
        $order->first_name = 'Khurram';
        $order->last_name = 'Shahzad';
        $order->email = '03009709763_6a2d692c66c7b@local.com';
        $order->phone = '03009709763';
        $order->save();

        // 3. Update Carts
        $cartsUpdated = \App\Models\Cart::where('order_id', 1169)->update(['user_id' => 512]);

        // 4. Update Customer Ledgers matching the order ID
        $ledgersUpdated = CustomerLedger::where('reference_id', 1169)->update(['user_id' => 512]);

        // 5. Update Payment Reminders matching the order number
        $remindersUpdated = PaymentReminder::where('reference_number', '2606133430')->update(['party_id' => 512]);

        // 6. Recalculate ledger balances for both customers
        CustomerLedger::updateBalance($oldUserId);
        CustomerLedger::updateBalance(512);

        $response['success'] = true;
        $response['message'] = "Order 1169 (Order Number 2606133430) shifted successfully from User ID {$oldUserId} to Khurram Shahzad (User ID 512)! Recalculated balances.";
        $response['details'] = [
            'carts_updated' => $cartsUpdated,
            'ledgers_updated' => $ledgersUpdated,
            'reminders_updated' => $remindersUpdated,
            'old_user_id' => $oldUserId,
            'new_user_id' => 512
        ];
    });
} catch (\Exception $e) {
    $response['success'] = false;
    $response['message'] = "Error shifting order: " . $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
