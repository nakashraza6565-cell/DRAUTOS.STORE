<?php
header('Content-Type: text/plain; charset=utf-8');
define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SaleReturn;
use App\Models\CustomerLedger;
use App\Models\AccountTransaction;
use App\Models\FinancialAccount;

echo "=== DIAGNOSTICS FOR SALE RETURN 10 ===\n\n";

$return = SaleReturn::with('customer')->find(10);
if (!$return) {
    die("Sale Return ID 10 not found!\n");
}

echo "Return ID: " . $return->id . "\n";
echo "Return Number: " . $return->return_number . "\n";
echo "Customer: " . ($return->customer ? $return->customer->name : 'N/A') . " (ID: " . $return->customer_id . ")\n";
echo "Total Amount: " . $return->total_return_amount . "\n";
echo "Refund Method: " . $return->refund_method . "\n";
echo "Status: " . $return->status . "\n\n";

echo "=== CUSTOMER LEDGER ENTRIES ===\n";
$ledgers = CustomerLedger::where('reference_id', 10)->get();
foreach ($ledgers as $l) {
    echo "Ledger ID: " . $l->id . "\n";
    echo "  Date: " . $l->transaction_date->format('Y-m-d') . "\n";
    echo "  Type: " . $l->type . "\n";
    echo "  Category: " . $l->category . "\n";
    echo "  Description: " . $l->description . "\n";
    echo "  Amount: " . $l->amount . "\n";
    echo "  Balance: " . $l->balance . "\n";
    echo "  Financial Account ID: " . $l->financial_account_id . "\n";
    
    // Check associated account transactions
    $accTx = AccountTransaction::where('reference_type', 'CustomerLedger')->where('reference_id', $l->id)->get();
    if ($accTx->count() > 0) {
        echo "  Associated Account Transactions:\n";
        foreach ($accTx as $tx) {
            echo "    TX ID: " . $tx->id . " | Acc: " . ($tx->account ? $tx->account->name : 'N/A') . " (ID: " . $tx->financial_account_id . ") | Amt: " . $tx->amount . " | Type: " . $tx->type . "\n";
        }
    } else {
        echo "  No associated Account Transactions\n";
    }
    echo "\n";
}
