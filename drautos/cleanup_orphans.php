<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = 0;
$transactions = \App\Models\AccountTransaction::whereIn('reference_type', ['CustomerLedger', 'App\Models\CustomerLedger', 'App\CustomerLedger'])->get();
foreach($transactions as $t) {
    if(!\App\Models\CustomerLedger::find($t->reference_id)) {
        echo "Deleting orphaned CustomerLedger AccountTransaction {$t->id}\n";
        $t->delete();
        $count++;
    }
}

$transactions2 = \App\Models\AccountTransaction::whereIn('reference_type', ['SupplierLedger', 'App\Models\SupplierLedger', 'App\SupplierLedger'])->get();
foreach($transactions2 as $t) {
    if(!\App\Models\SupplierLedger::find($t->reference_id)) {
        echo "Deleting orphaned SupplierLedger AccountTransaction {$t->id}\n";
        $t->delete();
        $count++;
    }
}

echo "Cleanup complete. Deleted $count orphaned transactions.\n";
