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

try {
    $cIds = User::where('name', 'Khurram Shahzad')->pluck('id')->toArray();
    $order = Order::find(1169);
    $lIds = CustomerLedger::where('reference_id', 1169)->pluck('id')->toArray();
    $rIds = PaymentReminder::where('reference_number', '2606133430')->pluck('id')->toArray();

    echo "C:" . implode(',', $cIds) . "\n";
    echo "O:" . ($order ? $order->user_id : 'null') . "\n";
    echo "L:" . implode(',', $lIds) . "\n";
    echo "R:" . implode(',', $rIds) . "\n";
} catch (\Exception $e) {
    echo "ERR:" . $e->getMessage() . "\n";
}
