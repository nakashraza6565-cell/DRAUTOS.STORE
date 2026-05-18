<?php
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $ledger = \App\Models\SupplierLedger::where('description', 'like', '%BOM-6A0AE3153780B%')->first();
    if ($ledger) {
        $oldAmount = $ledger->amount;
        $ledger->amount = 5200.00;
        $ledger->description = "Labor / Subcontract Service for Completed BOM BOM-6A0AE3153780B (produced 520 units)";
        $ledger->save();

        // Recalculate balance for the supplier
        \App\Models\SupplierLedger::updateBalance($ledger->supplier_id);

        echo "✅ Success! Ledger entry for BOM-6A0AE3153780B successfully rectified from Rs. " . number_format($oldAmount) . " to Rs. 5,200. Subcontractor ledger balance successfully recalculated!";
    } else {
        echo "❌ Ledger entry for BOM-6A0AE3153780B not found in database.";
    }
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
