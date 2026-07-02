<?php
header('Content-Type: text/plain; charset=utf-8');
define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CustomerLedger;

echo "=== LEDGER FOR HAMZA SHAHZAD (ID: 320) ===\n\n";

$ledgers = CustomerLedger::where('user_id', 320)
    ->orderBy('transaction_date', 'desc')
    ->orderBy('id', 'desc')
    ->take(15)
    ->get();

foreach ($ledgers as $l) {
    echo "ID: " . $l->id . " | Date: " . $l->transaction_date->format('Y-m-d') . " | Type: " . $l->type . " | Cat: " . $l->category . " | Amt: " . $l->amount . " | Bal: " . $l->balance . " | Ref: " . $l->reference_id . "\n";
    echo "  Desc: " . $l->description . "\n\n";
}
