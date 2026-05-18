<?php
/**
 * 🧹 Extreme Cache Cleaner & Ledger Retro-Synchronizer for Laravel on Hostinger
 */

define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h1>🧹 Danyal Autos - Cache Cleaner & Ledger Sync</h1>";

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

    echo "<h2>🔄 Retroactive Packaging Purchase Ledger Sync</h2>";
    try {
        $purchases = \App\Models\PackagingPurchase::whereNotNull('supplier_id')->get();
        
        // Group by base invoice_no (stripping trailing -1, -2, etc. suffixes)
        $groupedPurchases = [];
        foreach ($purchases as $p) {
            $baseInvoice = $p->invoice_no;
            if (preg_match('/^(PKG-[A-Z0-9]+)-\d+$/', $baseInvoice, $matches)) {
                $baseInvoice = $matches[1];
            } elseif (preg_match('/^(.+)-\d+$/', $baseInvoice, $matches)) {
                $baseInvoice = $matches[1];
            }
            $groupedPurchases[$baseInvoice][] = $p;
        }

        echo "Found " . count($groupedPurchases) . " unique invoices associated with suppliers.<br>";
        echo "<ul>";
        $postedCount = 0;
        foreach ($groupedPurchases as $invoiceNo => $items) {
            $firstItem = $items[0];
            $supplierId = $firstItem->supplier_id;
            $supplier = \App\Models\Supplier::find($supplierId);
            if (!$supplier) {
                echo "<li>⚠️ Skipping invoice <b>{$invoiceNo}</b>: Supplier ID {$supplierId} not found.</li>";
                continue;
            }
            
            $purchaseDate = $firstItem->purchase_date;
            $totalCost = 0;
            foreach ($items as $item) {
                $totalCost += $item->total_price;
            }
            
            // Check if ledger entry already exists
            $exists = \App\Models\SupplierLedger::where('supplier_id', $supplierId)
                ->where('description', 'like', "%Invoice #{$invoiceNo}%")
                ->exists();
                
            if ($exists) {
                echo "<li>✅ Invoice <b>{$invoiceNo}</b> for supplier <b>{$supplier->name}</b> is already recorded in the ledger.</li>";
            } else {
                // Record entry
                \App\Models\SupplierLedger::record(
                    $supplierId,
                    $purchaseDate,
                    'debit',
                    'purchase',
                    "Packaging Material Purchase: Invoice #{$invoiceNo}",
                    $totalCost
                );
                echo "<li style='color: green;'>🚀 <b>Posted Ledger Entry:</b> Invoice <b>{$invoiceNo}</b> for supplier <b>{$supplier->name}</b> of Rs. " . number_format($totalCost, 2) . " on {$purchaseDate}</li>";
                $postedCount++;
            }
        }
        echo "</ul>";
        if ($postedCount > 0) {
            echo "<h4 style='color: green;'>Successfully posted {$postedCount} missing ledger entries retroactively!</h4>";
        } else {
            echo "<h4>No missing ledger entries found to post. All are fully synchronized!</h4>";
        }
    } catch (\Exception $dbEx) {
        echo "<span style='color: red;'>❌ DB Sync Error: " . $dbEx->getMessage() . "</span><br>";
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
