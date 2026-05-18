<?php
require __DIR__ . '/drautos/vendor/autoload.php';
$app = require_once __DIR__ . '/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Supplier;
use App\Models\SupplierLedger;

$supplier = Supplier::where('name', 'like', '%amjad%')->first();
if (!$supplier) {
    echo "No supplier found with name like amjad\n";
    exit;
}

echo "Supplier found: ID: {$supplier->id}, Name: {$supplier->name}, Balance: {$supplier->current_balance}\n";

$ledgers = SupplierLedger::where('supplier_id', $supplier->id)->get();
echo "Ledger entries count: " . count($ledgers) . "\n";
foreach ($ledgers as $l) {
    echo "ID: {$l->id}, Date: {$l->transaction_date}, Type: {$l->type}, Cat: {$l->category}, Desc: {$l->description}, Amount: {$l->amount}, Ref: {$l->reference_id}\n";
}
