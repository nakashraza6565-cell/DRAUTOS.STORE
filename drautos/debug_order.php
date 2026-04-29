<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$order_id = 258;
$ledgers = App\Models\CustomerLedger::where('reference_id', $order_id)->get();
foreach($ledgers as $l) {
    echo "Type: " . $l->type . " | Amount: " . $l->amount . " | Desc: " . $l->description . "\n";
}

$reminder = App\Models\PaymentReminder::where('reference_number', 'ORD-VCKHO2PRBM')->first();
if($reminder) {
    echo "Reminder Amount: " . $reminder->amount . " | Paid: " . $reminder->paid_amount . " | Status: " . $reminder->status . "\n";
} else {
    echo "No reminder found\n";
}
