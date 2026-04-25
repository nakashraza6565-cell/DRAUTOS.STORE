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
            'received_date' => 'required|date',
            'invoice_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
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
                'received_date' => $request->received_date,
                'invoice_number' => $request->invoice_number,
                'received_by' => Auth::id(),
                'notes' => $request->notes,
                'status' => 'pending',
            ]);

            // Add items and update stock
            foreach ($request->items as $item) {
                // Calculate Packaging Material Cost (Base Cost * Quantity Used)
                $pkgMaterialCostTotal = 0;
                if (!empty($item['packaging_item_id'])) {
                    $pkgItem = \App\Models\PackagingItem::find($item['packaging_item_id']);
                    if ($pkgItem) {
                        $pkgMaterialCostTotal = ($pkgItem->cost * ($item['packaging_quantity'] ?? 0));
                    }
                }

                // Total Cost = (Product Qty * Unit Cost) + Additional Packaging Cost + Material Cost
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

                // Update product stock
                $product = Product::find($item['product_id']);
                $product->stock += $item['quantity'];
                $product->purchase_price = $item['unit_cost']; // Update purchase price
                
                if ($request->warehouse_id) {
                    $product->warehouse_id = $request->warehouse_id;
                }
                
                $product->save();

                // DEDUCT PACKAGING STOCK
                if (!empty($item['packaging_item_id']) && !empty($item['packaging_quantity'])) {
                    $pkgItem = \App\Models\PackagingItem::find($item['packaging_item_id']);
                    if ($pkgItem) {
                        $pkgItem->stock -= $item['packaging_quantity'];
                        $pkgItem->save();
                    }
                }
            }

            DB::commit();

            session()->flash('success', 'Incoming goods entry created successfully. Stock adjusted.');
            return redirect()->route('inventory-incoming.show', $incoming->id);

        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Error creating entry: ' . $e->getMessage());
            return back()->withInput();
        }
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
     * Verify incoming goods (change status to verified)
     */
    public function verify(InventoryIncoming $inventoryIncoming)
    {
        $inventoryIncoming->update(['status' => 'verified']);

        session()->flash('success', 'Incoming goods verified successfully');
        return back();
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
}
