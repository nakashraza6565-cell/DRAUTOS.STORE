<?php
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CustomerLedger;

echo "=== LEDGER ENTRIES FOR USER 64 (Makkah Autos) ===\n";
$ledgers64 = CustomerLedger::where('user_id', 64)->orderBy('id', 'desc')->limit(10)->get();
foreach ($ledgers64 as $l) {
    echo "ID: {$l->id} | Date: {$l->transaction_date} | Type: {$l->type} | Amount: {$l->amount} | Bal: {$l->balance} | Desc: {$l->description}\n";
}

echo "\n=== LEDGER ENTRIES FOR USER 80 (Makkah Autos (M)) ===\n";
$ledgers80 = CustomerLedger::where('user_id', 80)->orderBy('id', 'desc')->limit(10)->get();
foreach ($ledgers80 as $l) {
    echo "ID: {$l->id} | Date: {$l->transaction_date} | Type: {$l->type} | Amount: {$l->amount} | Bal: {$l->balance} | Desc: {$l->description}\n";
}
