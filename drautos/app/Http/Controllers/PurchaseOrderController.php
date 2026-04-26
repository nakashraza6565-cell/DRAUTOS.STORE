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
            'expected_delivery_date' => 'nullable|date',
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric|min:1',
            'unit_price' => 'required|array',
            'unit_price.*' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $po_number = 'PO-' . strtoupper(uniqid());
            
            $total_amount = 0;
            foreach ($request->quantity as $key => $qty) {
                $total_amount += $qty * $request->unit_price[$key];
            }

            $paid_amount = $request->paid_amount ?: 0;

            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $request->supplier_id,
                'po_number' => $po_number,
                'order_date' => $request->order_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'status' => 'pending',
                'total_amount' => $total_amount,
                'paid_amount' => $paid_amount,
                'notes' => $request->notes,
            ]);

            foreach ($request->product_id as $key => $pid) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $pid,
                    'quantity' => $request->quantity[$key],
                    'unit_price' => $request->unit_price[$key],
                    'subtotal' => $request->quantity[$key] * $request->unit_price[$key],
                ]);
            }

            // --- Handle Payment Reminder for Supplier ---
            $pending_amount = $total_amount - $paid_amount;
            if ($pending_amount > 0) {
                \App\Models\PaymentReminder::create([
                    'type' => 'payable',
                    'party_type' => 'App\\Models\\Supplier',
                    'party_id' => $request->supplier_id,
                    'reference_number' => $po_number,
                    'amount' => $pending_amount,
                    'due_date' => $request->due_date ?: now()->addDays(7),
                    'status' => 'pending',
                    'notes' => 'Generated from Purchase Order ' . $po_number
                ]);

                // Update supplier current balance
                $supplier = Supplier::find($request->supplier_id);
                if ($supplier) {
                    $supplier->current_balance += $pending_amount;
                    $supplier->save();

                    // Record in Ledger
                    \App\Models\SupplierLedger::record(
                        $supplier->id,
                        $request->order_date,
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
            'paid_amount' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date'
        ]);

        $old_paid = $purchaseOrder->paid_amount;
        $new_paid = $request->paid_amount ?? $old_paid;

        $purchaseOrder->status = $request->status;
        $purchaseOrder->paid_amount = $new_paid;
        $purchaseOrder->save();

        // Sync Reminder
        $reminder = \App\Models\PaymentReminder::where('reference_number', $purchaseOrder->po_number)->first();
        $total_amount = $purchaseOrder->total_amount;
        $new_pending = $total_amount - $new_paid;

        if ($new_pending > 0) {
            if (!$reminder) {
                $reminder = new \App\Models\PaymentReminder();
                $reminder->type = 'payable';
                $reminder->party_type = 'App\\Models\\Supplier';
                $reminder->party_id = $purchaseOrder->supplier_id;
                $reminder->reference_number = $purchaseOrder->po_number;
                $reminder->status = 'pending';
            }
            
            $old_pending = $reminder->amount;
            $reminder->amount = $new_pending;
            if ($request->due_date) {
                $reminder->due_date = $request->due_date;
            }
            $reminder->save();

            // Update Supplier Balance Difference
            $diff = $new_pending - $old_pending;
            if ($diff != 0) {
                $supplier = Supplier::find($purchaseOrder->supplier_id);
                if ($supplier) {
                    $supplier->current_balance += $diff;
                    $supplier->save();
                }
            }
        } elseif ($reminder) {
            // Paid in full now?
            $old_pending = $reminder->amount;
            $reminder->delete();

            $supplier = Supplier::find($purchaseOrder->supplier_id);
            if ($supplier) {
                $supplier->current_balance -= $old_pending;
                $supplier->save();
            }
        }

        request()->session()->flash('success', 'Purchase Order updated successfully');
        return redirect()->route('purchase-orders.index');
    }

    public function destroy($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $purchaseOrder->delete();
        request()->session()->flash('success', 'Purchase Order deleted');
        return redirect()->route('purchase-orders.index');
    }
}
