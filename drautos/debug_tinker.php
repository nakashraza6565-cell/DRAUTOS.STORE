<?php
$purchases = \App\Models\RawMaterialPurchase::with('items')->get();
foreach ($purchases as $p) {
    echo "Invoice: {$p->invoice_number}, Items: {$p->items->count()}, BOM: {$p->manufacturing_bill_id}\n";
    foreach ($p->items as $item) {
        echo "  - Item: {$item->item_name}\n";
    }
}
$ledgers = \App\Models\SupplierLedger::where('category', 'purchase')->get();
foreach ($ledgers as $l) {
    echo "Ledger: {$l->id}, Desc: {$l->description}, Ref: {$l->reference_id}, Supplier: {$l->supplier_id}, Amount: {$l->amount}\n";
}
