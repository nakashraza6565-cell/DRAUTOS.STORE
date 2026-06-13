<?php
$ledgers = \App\Models\SupplierLedger::where('category', 'purchase')
    ->where('description', 'LIKE', 'Subcontract Service%')
    ->get();
echo "Subcontract ledgers left: " . $ledgers->count() . "\n";
foreach($ledgers as $l) {
    echo "ID: " . $l->id . " Amount: " . $l->amount . " Desc: " . $l->description . "\n";
}
