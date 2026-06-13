<?php
$file = 'routes/web.php';
$content = file_get_contents($file);

if (strpos($content, '/fix-missing-ledgers-8822') === false) {
    $route = "\nRoute::get('/fix-missing-ledgers-8822', function() {
    \$purchases = \App\Models\RawMaterialPurchase::with('items')->get();
    \$restored = 0;
    foreach (\$purchases as \$purchase) {
        \$exists = \App\Models\SupplierLedger::where('category', 'purchase')
            ->where('reference_id', \$purchase->manufacturing_bill_id)
            ->where('supplier_id', \$purchase->supplier_id)
            ->where('amount', \$purchase->total_amount)
            ->exists();
        if (!\$exists) {
            \$descriptions = [];
            foreach (\$purchase->items as \$item) {
                \$descriptions[] = \$item->quantity . ' pcs of ' . \$item->item_name;
            }
            \$description = 'Purchased (Invoice: ' . \$purchase->invoice_number . '): ' . implode(', ', \$descriptions);
            \App\Models\SupplierLedger::record(
                \$purchase->supplier_id, \$purchase->purchase_date, 'debit', 'purchase',
                \$description, \$purchase->total_amount, \$purchase->manufacturing_bill_id
            );
            \$restored++;
        }
    }
    return 'Successfully restored ' . \$restored . ' missing Raw Material Purchase ledger entries! You can now check the Supplier Ledger.';
});\n";

    file_put_contents($file, $route, FILE_APPEND);
    echo "Route appended!\n";
} else {
    echo "Route already exists.\n";
}
