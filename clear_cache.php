<?php
/**
 * 🧹 Extreme Cache Cleaner for Laravel on Hostinger
 */

define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h1>🧹 Danyal Autos - Cache Cleaner</h1>";

try {
    echo "Clearing View Cache... ";
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "✅<br>";

    echo "Clearing Route Cache... ";
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    echo "✅<br>";

    echo "Clearing Config Cache... ";
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "✅<br>";

    echo "Clearing Application Cache... ";
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "✅<br>";

    echo "Optimizing... ";
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    echo "✅<br>";

    if(function_exists('opcache_reset')) {
        echo "Resetting PHP OPcache... ";
        opcache_reset();
        echo "✅<br>";
    }

    echo "<h2>📂 Git Deployment Diagnostics</h2>";
    echo "<b>Current Directory:</b> " . __DIR__ . "<br>";
    echo "<b>Git Status:</b><pre>" . shell_exec('git status 2>&1') . "</pre>";
    echo "<b>Git Log (Last 3):</b><pre>" . shell_exec('git log -n 3 2>&1') . "</pre>";

    echo "<h2>📊 Recent Database Records</h2>";
    try {
        $purchases = \App\Models\PackagingPurchase::with('supplier', 'packagingItem')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();
            
        echo "<h3>Recent Packaging Purchases</h3>";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Invoice No</th><th>Item</th><th>Supplier</th><th>Qty</th><th>Price</th><th>Total</th><th>Date</th></tr>";
        foreach ($purchases as $p) {
            $supplierName = $p->supplier ? $p->supplier->name : 'N/A';
            $itemName = $p->packagingItem ? $p->packagingItem->name : 'N/A';
            echo "<tr><td>{$p->id}</td><td>{$p->invoice_no}</td><td>{$itemName}</td><td>{$supplierName} (ID: {$p->supplier_id})</td><td>{$p->quantity}</td><td>{$p->price}</td><td>{$p->total_price}</td><td>{$p->purchase_date}</td></tr>";
        }
        echo "</table>";

        $ledgers = \App\Models\SupplierLedger::with('supplier')
            ->orderBy('id', 'desc')
            ->limit(8)
            ->get();
            
        echo "<h3>Recent Supplier Ledger Entries</h3>";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Supplier</th><th>Type</th><th>Category</th><th>Description</th><th>Amount</th><th>Balance</th><th>Date</th></tr>";
        foreach ($ledgers as $l) {
            $supplierName = $l->supplier ? $l->supplier->name : 'N/A';
            echo "<tr><td>{$l->id}</td><td>{$supplierName} (ID: {$l->supplier_id})</td><td>{$l->type}</td><td>{$l->category}</td><td>{$l->description}</td><td>{$l->amount}</td><td>{$l->balance}</td><td>" . ($l->transaction_date ? $l->transaction_date->format('Y-m-d') : 'N/A') . "</td></tr>";
        }
        echo "</table>";
    } catch (\Exception $dbEx) {
        echo "❌ DB Error: " . $dbEx->getMessage() . "<br>";
    }

    echo "<h2>🚀 All caches cleared!</h2>";
    echo "<p>Please refresh your site now. You should see the <b>Red Bar</b> if the files were updated.</p>";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
