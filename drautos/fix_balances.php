<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AccountTransaction;
use App\Models\FinancialAccount;

// Find transactions linked to SupplierLedger and fix their types
// In my previous bug, Supplier Payments (Credit) were recorded as 'in'
// They should be 'out'

$transactions = AccountTransaction::where('reference_type', 'SupplierLedger')->get();

foreach ($transactions as $t) {
    // If it was 'in' from a supplier ledger, it was likely a payment and should be 'out'
    if ($t->type == 'in') {
        $t->type = 'out';
        $t->save();
        echo "Fixed transaction #{$t->id} (Amount: {$t->amount}) from 'in' to 'out'\n";
    }
}

// Recalculate balances for all financial accounts
$accounts = FinancialAccount::all();
foreach ($accounts as $acc) {
    FinancialAccount::updateBalance($acc->id);
    echo "Recalculated balance for account: {$acc->name}\n";
}

echo "Cleanup complete!\n";
