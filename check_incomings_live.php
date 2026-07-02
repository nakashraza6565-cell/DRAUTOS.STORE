<?php
// Temporary database inspector script for incoming goods
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
        
        $incoming = \App\Models\InventoryIncoming::find($deleteId);
        if ($incoming) {
            $supplierId = $incoming->supplier_id;
            
            // 1. Revert product stock
            foreach ($incoming->items as $item) {
                $product = \App\Models\Product::find($item->product_id);
                if ($product) {
                    $product->stock -= $item->quantity;
                    $product->save();
                    echo "Reverted stock for product '{$product->title}': subtracted {$item->quantity} pcs.<br>";
                }
                
                // Adjust packaging item stock if any
                if ($item->packaging_item_id && $item->packaging_quantity) {
                    $pkgItem = \App\Models\PackagingItem::find($item->packaging_item_id);
                    if ($pkgItem) {
                        $pkgItem->stock += $item->packaging_quantity;
                        $pkgItem->save();
                        echo "Reverted stock for packaging '{$pkgItem->name}': added back {$item->packaging_quantity} pcs.<br>";
                    }
                }
            }
            
            // 2. Delete related Supplier Ledger entry if exists
            $ledger = \App\Models\SupplierLedger::where('reference_id', $deleteId)
                ->where('category', 'purchase')
                ->first();
                
            if ($ledger) {
                // Delete related account transactions
                \App\Models\AccountTransaction::where(function($query) {
                    $query->where('reference_type', 'SupplierLedger')
                          ->orWhere('reference_type', 'App\Models\SupplierLedger')
                          ->orWhere('reference_type', 'App\SupplierLedger');
                })->where('reference_id', $ledger->id)->delete();
                
                $ledger->delete();
                echo "Deleted associated Supplier Ledger entry and reversed cashbook transactions.<br>";
            }
            
            // 3. Delete items
            $incoming->items()->delete();
            
            // 4. Delete the incoming record itself
            $incoming->delete();
            
            // 5. Recalculate Supplier Balance
            if ($supplierId) {
                \App\Models\SupplierLedger::updateBalance($supplierId);
                echo "Recalculated supplier balance.<br>";
            }
            
            DB::commit();
            echo "<p style='color: green; font-weight: bold;'>Successfully deleted Batch ID: {$deleteId} and cleaned up all stock, ledger and transaction records.</p>";
        } else {
            DB::rollBack();
            echo "<p style='color: red; font-weight: bold;'>InventoryIncoming ID: {$deleteId} not found.</p>";
        }
    }

    $incomings = \App\Models\InventoryIncoming::with('supplier')
        ->whereIn('batch_number', $batchNumbers)
        ->get();
        
    echo "<h3>Incoming Batches 208 and 209</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Batch Number</th><th>Supplier</th><th>Total Cost</th><th>Status</th><th>Received Date</th><th>Created At</th><th>Action</th></tr>";
    
    foreach ($incomings as $inc) {
        $supplierName = $inc->supplier ? $inc->supplier->name : 'N/A';
        echo "<tr>";
        echo "<td>{$inc->id}</td>";
        echo "<td>{$inc->batch_number}</td>";
        echo "<td>{$supplierName} (ID: {$inc->supplier_id})</td>";
        echo "<td>{$inc->total_cost}</td>";
        echo "<td>{$inc->status}</td>";
        echo "<td>{$inc->received_date}</td>";
        echo "<td>{$inc->created_at}</td>";
        echo "<td><a href='?delete_id={$inc->id}' onclick='return confirm(\"Are you sure you want to delete this batch, revert stock, and delete associated ledger entries?\");' style='color: red; font-weight: bold;'>Delete Batch & Clean DB</a></td>";
        echo "</tr>";
        
        echo "<tr><td colspan='8' style='padding-left: 20px; background-color: #f9f9f9;'>";
        echo "<strong>Items inside Batch:</strong><br>";
        foreach ($inc->items as $item) {
            echo "- Item: {$item->item_name}, Qty: {$item->quantity}, Unit Cost: {$item->cost}, Total: " . ($item->quantity * $item->cost) . "<br>";
        }
        echo "</td></tr>";
    }
    echo "</table>";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
