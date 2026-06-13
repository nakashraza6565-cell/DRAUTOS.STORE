<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RawMaterialPurchase;
use App\Models\SupplierLedger;

$purchases = RawMaterialPurchase::with('items')->get();
$restored = 0;

foreach ($purchases as $purchase) {
    // Check if ledger entry exists
    $exists = SupplierLedger::where('category', 'purchase')
        ->where('reference_id', $purchase->manufacturing_bill_id)
        ->where('supplier_id', $purchase->supplier_id)
        ->where('amount', $purchase->total_amount)
        ->exists();

    if (!$exists) {
        $descriptions = [];
        foreach ($purchase->items as $item) {
            $descriptions[] = $item->quantity . ' pcs of ' . $item->item_name;
        }
        $description = "Purchased (Invoice: {$purchase->invoice_number}): " . implode(', ', $descriptions);

        SupplierLedger::record(
            $purchase->supplier_id,
            $purchase->purchase_date,
            'debit',
            'purchase',
            $description,
            $purchase->total_amount,
            $purchase->manufacturing_bill_id
        );
        $restored++;
        echo "Restored ledger for Invoice: {$purchase->invoice_number}\n";
    }
}

echo "Total restored: {$restored}\n";
