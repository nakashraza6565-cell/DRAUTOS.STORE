<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Product;
use DB;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchase_orders = PurchaseOrder::with('supplier')->orderBy('id', 'DESC')->get();
        return view('backend.purchase.index')->with('purchase_orders', $purchase_orders);
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 'active')->get();
        $products = Product::where('status', 'active')->get();
        $categories = \App\Models\Category::where('status', 'active')->where('is_parent', 1)->get();
        $brands = \App\Models\Brand::where('status', 'active')->get();
        $units = \App\Models\Unit::all();
        $product_models = \App\Models\ProductModel::all();

        return view('backend.purchase.create')->with([
            'suppliers' => $suppliers,
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'units' => $units,
            'product_models' => $product_models
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric|min:0.1',
        ]);

        try {
            DB::beginTransaction();

            $last_po = PurchaseOrder::orderBy('id', 'desc')->first();
            $po_number = $last_po ? (int)$last_po->po_number + 1 : 1001;
            
            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $request->supplier_id,
                'po_number' => $po_number,
                'order_date' => $request->order_date,
                'status' => 'pending',
                'total_amount' => 0,
                'notes' => $request->notes,
            ]);

            foreach ($request->product_id as $key => $pid) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $pid,
                    'quantity' => $request->quantity[$key],
                    'unit_price' => 0,
                    'subtotal' => 0,
                ]);
            }

            DB::commit();

            // WhatsApp Notification
            try {
                $purchaseOrder->load('supplier', 'items.product');
                $waService = new \App\Services\WhatsAppService();
                $waService->sendPurchaseOrderNotification($purchaseOrder);
            } catch (\Exception $e) {
                \Log::error("Failed to send PO WhatsApp: " . $e->getMessage());
            }

            // Activity Log
            \App\Models\ActivityLog::log('purchase', 'Purchase Order Created', auth()->user()->name . ' created purchase order #' . $po_number, route('purchase-orders.show', $purchaseOrder->id));

            request()->session()->flash('success', 'Purchase Order (Request) created successfully.');
            return redirect()->route('purchase-orders.index');

        } catch (\Exception $e) {
            DB::rollback();
            request()->session()->flash('error', 'Error creating purchase order: ' . $e->getMessage());
            return back();
        }
    }

    public function show($id)
    {
        $purchaseOrder = PurchaseOrder::with(['supplier', 'items.product'])->findOrFail($id);
        return view('backend.purchase.show')->with('purchaseOrder', $purchaseOrder);
    }

    public function edit($id)
    {
        $purchaseOrder = PurchaseOrder::with('items.product')->findOrFail($id);
        $suppliers = Supplier::where('status', 'active')->get();
        $products = Product::where('status', 'active')->get();
        $categories = \App\Models\Category::where('status', 'active')->where('is_parent', 1)->get();
        $brands = \App\Models\Brand::where('status', 'active')->get();
        $units = \App\Models\Unit::all();
        $product_models = \App\Models\ProductModel::all();

        return view('backend.purchase.edit')->with([
            'purchaseOrder' => $purchaseOrder,
            'suppliers' => $suppliers,
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'units' => $units,
            'product_models' => $product_models
        ]);
    }

    public function update(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $this->validate($request, [
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'status' => 'required|in:pending,ordered,received,cancelled',
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric|min:0.1',
        ]);

        try {
            DB::beginTransaction();

            $purchaseOrder->update([
                'supplier_id' => $request->supplier_id,
                'order_date' => $request->order_date,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            // Sync Items
            $purchaseOrder->items()->delete();
            foreach ($request->product_id as $key => $productId) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $productId,
                    'quantity' => $request->quantity[$key],
                    'unit_price' => 0,
                    'total_price' => 0,
                ]);
            }

            DB::commit();
            request()->session()->flash('success', 'Purchase Order updated successfully');
            return redirect()->route('purchase-orders.index');

        } catch (\Exception $e) {
            DB::rollback();
            request()->session()->flash('error', 'Error updating purchase order: ' . $e->getMessage());
            return back();
        }
    }

    public function convert($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        
        // Redirect to Incoming Goods create page with the PO ID
        return redirect()->route('inventory-incoming.create', ['purchase_order_id' => $id]);
    }

    public function thermalPrint($id)
    {
        $purchaseOrder = PurchaseOrder::with(['supplier', 'items.product'])->findOrFail($id);
        return view('backend.purchase.thermal', compact('purchaseOrder'));
    }

    public function destroy($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $purchaseOrder->delete();
        request()->session()->flash('success', 'Purchase Order deleted');
        return redirect()->route('purchase-orders.index');
    }
}
