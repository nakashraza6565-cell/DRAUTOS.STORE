<?php

namespace App\Http\Controllers;

use App\Models\InventoryIncoming;
use App\Models\InventoryIncomingItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\BarcodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventoryIncomingController extends Controller
{
    protected $barcodeService;

    public function __construct(BarcodeService $barcodeService)
    {
        $this->barcodeService = $barcodeService;
    }

    /**
     * Display incoming goods list
     */
    public function index()
    {
        $incoming = InventoryIncoming::with(['supplier', 'warehouse', 'receiver', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(5000);

        return view('backend.inventory.incoming.index', compact('incoming'));
    }

    /**
     * Show form for creating new incoming goods entry
     */
    public function create()
    {
        $suppliers = Supplier::where('status', 'active')->get();
        $warehouses = Warehouse::where('status', 'active')->get();
        $products = Product::where('status', 'active')->get();
        $packaging_items = \App\Models\PackagingItem::all();

        return view('backend.inventory.incoming.create', compact('suppliers', 'warehouses', 'products', 'packaging_items'));
    }

    /**
     * Store new incoming goods entry
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'shipping_cost' => 'nullable|numeric|min:0',
            'received_date' => 'required|date',
            'invoice_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.batch_number' => 'nullable|string',
            'items.*.packaging_item_id' => 'nullable|exists:packaging_items,id',
            'items.*.packaging_quantity' => 'nullable|numeric|min:0',
            'items.*.packaging_cost' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Generate reference number
            $refNumber = 'INC-' . date('Ymd') . '-' . str_pad(InventoryIncoming::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            // Create incoming record
            $incoming = InventoryIncoming::create([
                'reference_number' => $refNumber,
                'supplier_id' => $request->supplier_id,
                'warehouse_id' => $request->warehouse_id,
                'shipping_cost' => $request->shipping_cost ?? 0,
                'received_date' => $request->received_date,
                'invoice_number' => $request->invoice_number,
                'received_by' => Auth::id(),
                'notes' => $request->notes,
                'status' => 'pending',
            ]);

            // Add items and update stock
            foreach ($request->items as $item) {
                $pkgMaterialCostTotal = 0;
                if (!empty($item['packaging_item_id'])) {
                    $pkgItem = \App\Models\PackagingItem::find($item['packaging_item_id']);
                    if ($pkgItem) {
                        $pkgMaterialCostTotal = ($pkgItem->cost * ($item['packaging_quantity'] ?? 0));
                    }
                }

                $itemTotalCost = ($item['quantity'] * $item['unit_cost']) + ($item['packaging_cost'] ?? 0) + $pkgMaterialCostTotal;

                InventoryIncomingItem::create([
                    'inventory_incoming_id' => $incoming->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $itemTotalCost,
                    'packaging_item_id' => $item['packaging_item_id'] ?? null,
                    'packaging_quantity' => $item['packaging_quantity'] ?? 0,
                    'packaging_cost' => $item['packaging_cost'] ?? 0,
                    'batch_number' => $item['batch_number'] ?? null,
                    'barcode_printed' => false,
                ]);

                // Update product stock and purchase price
                $product = Product::find($item['product_id']);
                $product->stock += $item['quantity'];
                $product->purchase_price = $item['unit_cost'];
                
                if ($request->warehouse_id) {
                    $product->warehouse_id = $request->warehouse_id;
                }
                $product->save();

                // Deduct packaging stock
                if (!empty($item['packaging_item_id']) && !empty($item['packaging_quantity'])) {
                    $pkgItem = \App\Models\PackagingItem::find($item['packaging_item_id']);
                    if ($pkgItem) {
                        $pkgItem->stock -= $item['packaging_quantity'];
                        $pkgItem->save();
                    }
                }
            }

            // Auto-Post to Ledger if requested
            if ($request->has('post_to_ledger') && $incoming->supplier_id) {
                $this->performVerification($incoming);
            }

            DB::commit();

            // Activity Log
            \App\Models\ActivityLog::log('inventory', 'Incoming Goods Received', Auth::user()->name . ' received incoming goods #' . $incoming->reference_number . ' (' . count($request->items) . ' items)', route('inventory-incoming.show', $incoming->id));

            session()->flash('success', 'Incoming goods entry created successfully.' . ($request->has('post_to_ledger') ? ' Posted to supplier ledger.' : ''));
            return redirect()->route('inventory-incoming.show', $incoming->id);

        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Error creating entry: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Refactored verification logic
     */
    protected function performVerification(InventoryIncoming $inventoryIncoming)
    {
        $inventoryIncoming->update(['status' => 'verified']);

        // Record in Supplier Ledger
        if ($inventoryIncoming->supplier_id && $inventoryIncoming->total_cost > 0) {
            $exists = \App\Models\SupplierLedger::where('supplier_id', $inventoryIncoming->supplier_id)
                ->where('reference_id', $inventoryIncoming->id)
                ->where('category', 'purchase')
                ->exists();
            
            if (!$exists) {
                \App\Models\SupplierLedger::record(
                    $inventoryIncoming->supplier_id,
                    $inventoryIncoming->received_date,
                    'debit',
                    'purchase',
                    'Incoming Goods Record #' . $inventoryIncoming->reference_number . ( $inventoryIncoming->invoice_number ? ' (Inv: '.$inventoryIncoming->invoice_number.')' : '' ),
                    $inventoryIncoming->total_cost,
                    $inventoryIncoming->id
                );
            }
        }
    }

    /**
     * Verify incoming goods (change status to verified) via button
     */
    public function verify(InventoryIncoming $inventoryIncoming)
    {
        DB::beginTransaction();
        try {
            $this->performVerification($inventoryIncoming);
            DB::commit();
            session()->flash('success', 'Incoming goods verified successfully and recorded in supplier ledger');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Error verifying: ' . $e->getMessage());
        }
        
        return back();
    }

    /**
     * Display incoming goods details
     */
    public function show(InventoryIncoming $inventoryIncoming)
    {
        $inventoryIncoming->load(['supplier', 'warehouse', 'receiver', 'items.product']);
        return view('backend.inventory.incoming.show', compact('inventoryIncoming'));
    }

    /**
     * Generate barcode stickers for incoming items
     */
    public function printBarcodes(InventoryIncoming $inventoryIncoming)
    {
        $inventoryIncoming->load('items.product');
        
        // Generate barcodes for all items
        $barcodes = [];
        foreach ($inventoryIncoming->items as $item) {
            $product = $item->product;
            $barcodeCode = $this->barcodeService->getProductBarcodeCode($product);
            $barcodeImage = $this->barcodeService->generatePNG($barcodeCode);

            // Generate multiple stickers based on quantity
            for ($i = 0; $i < $item->quantity; $i++) {
                $barcodes[] = [
                    'product_name' => $product->title,
                    'sku' => $product->sku,
                    'barcode_code' => $barcodeCode,
                    'barcode_image' => $barcodeImage,
                    'box_quantity' => $product->box_quantity,
                    'price' => $product->price,
                ];
            }
        }

        return view('backend.inventory.incoming.barcode-stickers', compact('barcodes', 'inventoryIncoming'));
    }

    /**
     * Mark barcode as printed for an item
     */
    public function markBarcodePrinted(Request $request, InventoryIncomingItem $item)
    {
        $item->update(['barcode_printed' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Barcode marked as printed'
        ]);
    }

    /**
     * Complete incoming goods (change status to completed)
     */
    public function complete(InventoryIncoming $inventoryIncoming)
    {
        $inventoryIncoming->update(['status' => 'completed']);

        session()->flash('success', 'Incoming goods completed successfully');
        return back();
    }

    /**
     * Search products for quick entry (AJAX)
     */
    public function searchProducts(Request $request)
    {
        $query = $request->get('q');

        $products = Product::where('status', 'active')
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhere('barcode', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'title', 'sku', 'purchase_price', 'stock']);

        return response()->json($products);
    }

    /**
     * Thermal print for incoming goods
     */
    public function thermalPrint(InventoryIncoming $inventoryIncoming)
    {
        $inventoryIncoming->load(['supplier', 'items.product', 'receiver']);
        
        $ledger = [];
        if ($inventoryIncoming->supplier_id) {
            $ledger = \App\Models\SupplierLedger::where('supplier_id', $inventoryIncoming->supplier_id)
                ->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get()
                ->reverse();
        }
        
        return view('backend.inventory.incoming.thermal', compact('inventoryIncoming', 'ledger'));
    }

    /**
     * Update item quantity or cost (AJAX)
     */
    public function updateItem(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            $item = InventoryIncomingItem::findOrFail($id);
            $oldQty = $item->quantity;
            $newQty = $request->quantity;
            
            // 1. Update Product Stock (Difference)
            $product = Product::find($item->product_id);
            if ($product) {
                $qtyDiff = $newQty - $oldQty;
                $product->stock += $qtyDiff;
                $product->purchase_price = $request->unit_cost; // Update product's cost price
                $product->save();
            }

            // 2. Update Item record
            $item->quantity = $newQty;
            $item->unit_cost = $request->unit_cost;
            
            // Recalculate item total cost (including packaging if any)
            $pkgMaterialCostTotal = 0;
            if ($item->packaging_item_id) {
                $pkgItem = \App\Models\PackagingItem::find($item->packaging_item_id);
                if ($pkgItem) {
                    $pkgMaterialCostTotal = ($pkgItem->cost * ($item->packaging_quantity ?? 0));
                }
            }
            $item->total_cost = ($newQty * $request->unit_cost) + ($item->packaging_cost ?? 0) + $pkgMaterialCostTotal;
            $item->save();

            // 3. Sync with Supplier Ledger if parent is verified
            $incoming = $item->inventoryIncoming;
            if ($incoming->status == 'verified' || $incoming->status == 'completed') {
                $ledger = \App\Models\SupplierLedger::where('supplier_id', $incoming->supplier_id)
                    ->where('reference_id', $incoming->id)
                    ->where('category', 'purchase')
                    ->first();
                
                if ($ledger) {
                    $ledger->amount = $incoming->total_cost;
                    $ledger->save();
                    \App\Models\SupplierLedger::updateBalance($incoming->supplier_id);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => 'Item updated successfully and product cost price updated.',
                'new_total' => number_format($item->total_cost, 2),
                'grand_total' => number_format($incoming->total_cost, 2)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
