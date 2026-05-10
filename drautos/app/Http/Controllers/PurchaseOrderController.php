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
        return view('backend.purchase.create')->with([
            'suppliers' => $suppliers,
            'products' => $products
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

            $po_number = 'PO-' . strtoupper(uniqid());
            
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
                        'debit',
                        'purchase',
                        'Purchase Order: ' . $po_number,
                        $pending_amount,
                        $purchaseOrder->id
                    );
                }
            }

            DB::commit();
            
            // Send WhatsApp Notification to Supplier
            // Send WhatsApp Notification to Supplier
            try {
                $purchaseOrder->load('supplier', 'items.product');
                $waService = new \App\Services\WhatsAppService();
                $waService->sendPurchaseOrderNotification($purchaseOrder);
                \Log::info("WhatsApp PO sent to supplier: {$request->supplier_id}");
            } catch (\Exception $e) {
                \Log::error("Failed to send PO WhatsApp: " . $e->getMessage());
            }
            
            request()->session()->flash('success', 'Purchase Order successfully created');
            return redirect()->route('purchase-orders.index');

        } catch (\Exception $e) {
            DB::rollback();
            request()->session()->flash('error', 'Error occurred: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function show($id)
    {
        $purchaseOrder = PurchaseOrder::with(['supplier', 'items.product'])->findOrFail($id);
        return view('backend.purchase.show')->with('purchaseOrder', $purchaseOrder);
    }

    public function edit($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $suppliers = Supplier::where('status', 'active')->get();
        $products = Product::where('status', 'active')->get();
        return view('backend.purchase.edit')->with([
            'purchaseOrder' => $purchaseOrder,
            'suppliers' => $suppliers,
            'products' => $products
        ]);
    }

    public function update(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $this->validate($request, [
            'status' => 'required|in:pending,ordered,received,cancelled',
        ]);

        $purchaseOrder->status = $request->status;
        $purchaseOrder->notes = $request->notes;
        $purchaseOrder->save();

        request()->session()->flash('success', 'Purchase Order updated successfully');
        return redirect()->route('purchase-orders.index');
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
