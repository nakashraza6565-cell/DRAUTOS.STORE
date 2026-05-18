<?php
// Temporary database inspector script
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h1>📊 Danyal Autos - DB Inspector</h1>";

try {
    // 1. Get recent packaging purchases
    $purchases = \App\Models\PackagingPurchase::with('supplier', 'packagingItem')
        ->orderBy('id', 'desc')
        ->limit(10)
        ->get();
        
    echo "<h3>Recent Packaging Purchases (Last 10)</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Invoice No</th><th>Item</th><th>Supplier</th><th>Qty</th><th>Price</th><th>Total</th><th>Date</th><th>Created At</th></tr>";
    foreach ($purchases as $p) {
        $supplierName = $p->supplier ? $p->supplier->name : 'N/A';
        $itemName = $p->packagingItem ? $p->packagingItem->name : 'N/A';
        echo "<tr>";
        echo "<td>{$p->id}</td>";
        echo "<td>{$p->invoice_no}</td>";
        echo "<td>{$itemName}</td>";
        echo "<td>{$supplierName} (ID: {$p->supplier_id})</td>";
        echo "<td>{$p->quantity}</td>";
        echo "<td>{$p->price}</td>";
        echo "<td>{$p->total_price}</td>";
        echo "<td>{$p->purchase_date}</td>";
        echo "<td>{$p->created_at}</td>";
        echo "</tr>";
    }
    echo "</table>";

    // 2. Get recent Supplier Ledger entries
    $ledgers = \App\Models\SupplierLedger::with('supplier')
        ->orderBy('id', 'desc')
        ->limit(15)
        ->get();
        
    echo "<h3>Recent Supplier Ledger Entries (Last 15)</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Supplier</th><th>Type</th><th>Category</th><th>Description</th><th>Amount</th><th>Balance</th><th>Date</th><th>Created At</th></tr>";
    foreach ($ledgers as $l) {
        $supplierName = $l->supplier ? $l->supplier->name : 'N/A';
        echo "<tr>";
        echo "<td>{$l->id}</td>";
        echo "<td>{$supplierName} (ID: {$l->supplier_id})</td>";
        echo "<td>{$l->type}</td>";
        echo "<td>{$l->category}</td>";
        echo "<td>{$l->description}</td>";
        echo "<td>{$l->amount}</td>";
        echo "<td>{$l->balance}</td>";
        echo "<td>" . ($l->transaction_date ? $l->transaction_date->format('Y-m-d') : 'N/A') . "</td>";
        echo "<td>{$l->created_at}</td>";
        echo "</tr>";
    }
    echo "</table>";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
