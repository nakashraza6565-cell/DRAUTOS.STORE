<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RawMaterialPurchase;
use App\Models\SupplierLedger;

$purchases = RawMaterialPurchase::whereIn('invoice_number', ['RMP-20260613-0003', 'RMP-20260613-0002'])->get();

foreach ($purchases as $purchase) {
    echo "Purchase: " . $purchase->invoice_number . " Amount: " . $purchase->total_amount . "\n";
    $ledgers = SupplierLedger::where('category', 'purchase')
        ->where('reference_id', $purchase->manufacturing_bill_id)
        ->where('supplier_id', $purchase->supplier_id)
        ->get();
        
    echo "Found ledgers: " . $ledgers->count() . "\n";
    foreach ($ledgers as $l) {
        echo "- Ledger ID: {$l->id}, Amount: {$l->amount}, Desc: {$l->description}\n";
    }
}
