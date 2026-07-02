<?php
// Temporary database inspector script for incoming goods
header('Content-Type: text/html; charset=utf-8');
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<h1>📊 Danyal Autos - Incoming Goods Inspector</h1>";

try {
    $batchNumbers = [208, 209];
    
    // If request contains delete param
    if (isset($_GET['delete_id'])) {
        $deleteId = intval($_GET['delete_id']);
        
        DB::beginTransaction();
        try {
            $incoming = \App\Models\InventoryIncoming::with('items')->find($deleteId);
            if ($incoming) {
                $supplierId = $incoming->supplier_id;
                
                // 1. Revert product stock
                foreach ($incoming->items as $item) {
                    $product = \App\Models\Product::find($item->product_id);
                    if ($product) {
                        $product->stock -= $item->quantity;
                        $product->save();
                        echo "✅ Reverted stock for product '{$product->title}': subtracted {$item->quantity} pcs.<br>";
                    }
                    
                    // Adjust packaging item stock if any
                    if ($item->packaging_item_id && $item->packaging_quantity) {
                        $pkgItem = \App\Models\PackagingItem::find($item->packaging_item_id);
                        if ($pkgItem) {
                            $pkgItem->stock += $item->packaging_quantity;
                            $pkgItem->save();
                            echo "✅ Reverted packaging stock for '{$pkgItem->name}': added back {$item->packaging_quantity} pcs.<br>";
                        }
                    }
                }
                
                // 2. Delete related Supplier Ledger entry
                $ledger = \App\Models\SupplierLedger::where('reference_id', $deleteId)
                    ->where('category', 'purchase')
                    ->first();
                    
                if ($ledger) {
                    \App\Models\AccountTransaction::where(function($query) {
                        $query->where('reference_type', 'SupplierLedger')
                              ->orWhere('reference_type', 'App\Models\SupplierLedger')
                              ->orWhere('reference_type', 'App\SupplierLedger');
                    })->where('reference_id', $ledger->id)->delete();
                    
                    $ledger->delete();
                    echo "✅ Deleted associated Supplier Ledger entry and cashbook transactions.<br>";
                }
                
                // 3. Delete items then the incoming record
                $incoming->items()->delete();
                $incoming->delete();
                
                // 4. Recalculate Supplier Balance
                if ($supplierId) {
                    \App\Models\SupplierLedger::updateBalance($supplierId);
                    echo "✅ Recalculated supplier balance.<br>";
                }
                
                DB::commit();
                echo "<p style='color: green; font-weight: bold; font-size:18px;'>SUCCESS: Batch ID {$deleteId} deleted and all records cleaned up!</p>";
            } else {
                DB::rollBack();
                echo "<p style='color: red;'>InventoryIncoming ID: {$deleteId} not found.</p>";
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            echo "<p style='color:red;'>ERROR during delete: " . $ex->getMessage() . "</p>";
        }
    }

    $incomings = \App\Models\InventoryIncoming::with(['supplier', 'items'])
        ->whereIn('batch_number', $batchNumbers)
        ->orderBy('id', 'asc')
        ->get();
        
    echo "<h3>Incoming Goods - Batch 208 and 209</h3>";
    echo "<table border='1' cellpadding='8' style='border-collapse: collapse; font-family: sans-serif;'>";
    echo "<tr style='background:#333;color:#fff'><th>ID</th><th>Batch #</th><th>Supplier</th><th>Total Cost</th><th>Status</th><th>Received Date</th><th>Created At</th><th>Items</th><th>Action</th></tr>";
    
    $first = true;
    foreach ($incomings as $inc) {
        $supplierName = $inc->supplier ? $inc->supplier->name : 'N/A';
        $bg = $first ? '#efffef' : '#fff0f0';
        echo "<tr style='background:{$bg};'>";
        echo "<td>{$inc->id}</td>";
        echo "<td>{$inc->batch_number}</td>";
        echo "<td>{$supplierName}</td>";
        echo "<td>Rs. " . number_format($inc->total_cost, 0) . "</td>";
        echo "<td>{$inc->status}</td>";
        echo "<td>{$inc->received_date}</td>";
        echo "<td>{$inc->created_at}</td>";
        
        $itemsList = [];
        foreach ($inc->items as $item) {
            $itemsList[] = $item->item_name . " (qty: {$item->quantity})";
        }
        echo "<td>" . implode('<br>', $itemsList) . "</td>";
        
        if ($first) {
            echo "<td style='color:gray'>ORIGINAL - keep</td>";
        } else {
            echo "<td><a href='?delete_id={$inc->id}' onclick='return confirm(\"DELETE Batch {$inc->id}? This will:\\n- Revert product stock\\n- Delete supplier ledger entry\\n- Delete cashbook transactions\\n\\nContinue?\");' style='color: red; font-weight: bold; padding: 5px 10px; background: #fee; border: 1px solid red; text-decoration: none;'>🗑 Delete Duplicate</a></td>";
        }
        echo "</tr>";
        $first = false;
    }
    echo "</table>";
    
    if ($incomings->count() === 0) {
        echo "<p style='color:orange;'>No incoming goods found with batch numbers 208 or 209.</p>";
    }

} catch (\Exception $e) {
    echo "❌ Error: " . htmlspecialchars($e->getMessage());
}
