<?php

namespace App\Http\Controllers;

use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Order;
use App\Models\PurchaseOrder;
use App\User;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\CustomerLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StatusNotification;

class ReturnsController extends Controller
{
    /**
     * Display sale returns list
     */
    public function saleReturnsIndex(Request $request)
    {
        $returns = SaleReturn::with(['order', 'customer', 'processor', 'items'])
            ->when($request->search, function($q) use ($request) {
                $search = $request->search;
                return $q->where('return_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('customer', function($sq) use ($search) {
                        $sq->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('order', function($sq) use ($search) {
                        $sq->where('order_number', 'LIKE', "%{$search}%")
                          ->orWhere('first_name', 'LIKE', "%{$search}%")
                          ->orWhere('last_name', 'LIKE', "%{$search}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5000);
            
        $orders = Order::whereIn('status', ['delivered', 'process'])->orderBy('id', 'desc')->take(100)->get();

        return view('backend.returns.sale.index', compact('returns', 'orders'));
    }

    /**
     * Create sale return
     */
    public function createSaleReturn(Request $request, Order $order)
    {
        $order->load('cart.product');
        return view('backend.returns.sale.create', compact('order'));
    }

    /**
     * Store sale return
     */
    public function storeSaleReturn(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'return_date' => 'required|date',
            'refund_method' => 'required|in:cash,credit_note,bank_transfer,cheque',
            'refund_reference' => 'nullable|string',
            'reason' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.condition' => 'required|in:good,damaged,defective',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $order = Order::findOrFail($validated['order_id']);
            
            // Generate return number
            $returnNumber = 'SR-' . date('Ymd') . '-' . str_pad(SaleReturn::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            // Calculate total
            $totalReturnAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalReturnAmount += $item['quantity'] * $item['unit_price'];
            }

            // Create return
            $return = SaleReturn::create([
                'return_number' => $returnNumber,
                'order_id' => $order->id,
                'customer_id' => $order->user_id,
                'return_date' => $validated['return_date'],
                'total_return_amount' => $totalReturnAmount,
                'refund_method' => $validated['refund_method'],
                'refund_reference' => $validated['refund_reference'] ?? null,
                'reason' => $validated['reason'] ?? null,
                'status' => 'pending',
                'processed_by' => Auth::id(),
            ]);

            // Add items and update stock
            foreach ($validated['items'] as $item) {
                $totalPrice = $item['quantity'] * $item['unit_price'];

                SaleReturnItem::create([
                    'sale_return_id' => $return->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $totalPrice,
                    'condition' => $item['condition'],
                    'notes' => $item['notes'] ?? null,
                ]);

                // Update product stock (only if condition is good)
                if ($item['condition'] === 'good') {
                    $product = Product::find($item['product_id']);
                    $product->stock += $item['quantity'];
                    $product->save();
                }
            }

            // Notify Admins
            try {
                $admins = User::where('role', 'admin')->get();
                $details = [
                    'title' => 'New Sale Return created by Admin',
                    'actionURL' => route('returns.sale.show', $return->id),
                    'fas' => 'fa-undo-alt'
                ];
                Notification::send($admins, new StatusNotification($details));
            } catch (\Exception $e) {
                \Log::error('Failed to notify admins of admin sale return: ' . $e->getMessage());
            }

            DB::commit();

            session()->flash('success', 'Sale return created successfully');
            return redirect()->route('returns.sale.show', $return->id);

        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Error creating sale return: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Display sale return details
     */
    public function showSaleReturn(SaleReturn $return)
    {
        $return->load(['order', 'customer', 'processor', 'items.product']);
        return view('backend.returns.sale.show', compact('return'));
    }

    /**
     * Approve sale return
     */
    public function approveSaleReturn(SaleReturn $return)
    {
        DB::beginTransaction();
        try {
            $return->update(['status' => 'approved']);

            // Update customer balance if credit_note
            if ($return->refund_method === 'credit_note' && $return->customer) {
                CustomerLedger::record(
                    $return->customer_id,
                    now(),
                    'credit',
                    'return',
                    'Approved ' . ucfirst($return->type ?? 'return') . ' #' . $return->return_number,
                    $return->total_return_amount,
                    $return->id
                );
                
                // Balance update is handled by record()
                // $return->customer->current_balance += $return->total_return_amount;
                // $return->customer->save();
            }

            DB::commit();

            session()->flash('success', 'Sale return approved');
            return back();

        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Error approving return: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Display purchase returns list
     */
    public function purchaseReturnsIndex(Request $request)
    {
        $returns = PurchaseReturn::with(['purchaseOrder', 'supplier', 'processor', 'items'])
            ->when($request->search, function($q) use ($request) {
                $search = $request->search;
                return $q->where('return_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('supplier', function($sq) use ($search) {
                        $sq->where('name', 'LIKE', "%{$search}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5000);

        $purchaseOrders = PurchaseOrder::with('supplier')->orderBy('id', 'desc')->take(100)->get();
        return view('backend.returns.purchase.index', compact('returns', 'purchaseOrders'));
    }

    /**
     * Create purchase return
     */
    public function createPurchaseReturn(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('items.product', 'supplier');
        return view('backend.returns.purchase.create', compact('purchaseOrder'));
    }

    /**
     * Store purchase return
     */
    public function storePurchaseReturn(Request $request)
    {
        $validated = $request->validate([
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'return_date' => 'required|date',
            'refund_method' => 'required|in:cash,credit_note,bank_transfer,cheque',
            'refund_reference' => 'nullable|string',
            'reason' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.condition' => 'required|in:expired,damaged,wrong_item,other',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Generate return number
            $returnNumber = 'PR-' . date('Ymd') . '-' . str_pad(PurchaseReturn::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            // Calculate total
            $totalReturnAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalReturnAmount += $item['quantity'] * $item['unit_cost'];
            }

            // Create return
            $return = PurchaseReturn::create([
                'return_number' => $returnNumber,
                'purchase_order_id' => $validated['purchase_order_id'] ?? null,
                'supplier_id' => $validated['supplier_id'],
                'return_date' => $validated['return_date'],
                'total_return_amount' => $totalReturnAmount,
                'refund_method' => $validated['refund_method'],
                'refund_reference' => $validated['refund_reference'] ?? null,
                'reason' => $validated['reason'] ?? null,
                'status' => 'pending',
                'processed_by' => Auth::id(),
            ]);

            // Add items and update stock
            foreach ($validated['items'] as $item) {
                $totalCost = $item['quantity'] * $item['unit_cost'];

                PurchaseReturnItem::create([
                    'purchase_return_id' => $return->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $totalCost,
                    'condition' => $item['condition'],
                    'notes' => $item['notes'] ?? null,
                ]);

                // Decrease product stock
                $product = Product::find($item['product_id']);
                $product->stock -= $item['quantity'];
                if ($product->stock < 0) {
                    $product->stock = 0;
                }
                $product->save();
            }

            // Notify Admins
            try {
                $admins = User::where('role', 'admin')->get();
                $details = [
                    'title' => 'New Purchase Return created by Admin',
                    'actionURL' => route('returns.purchase.show', $return->id),
                    'fas' => 'fa-truck-loading'
                ];
                Notification::send($admins, new StatusNotification($details));
            } catch (\Exception $e) {
                \Log::error('Failed to notify admins of purchase return: ' . $e->getMessage());
            }

            DB::commit();

            session()->flash('success', 'Purchase return created successfully');
            return redirect()->route('returns.purchase.show', $return->id);

        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Error creating purchase return: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Display purchase return details
     */
    public function showPurchaseReturn(PurchaseReturn $return)
    {
        $return->load(['purchaseOrder', 'supplier', 'processor', 'items.product']);
        return view('backend.returns.purchase.show', compact('return'));
    }

    /**
     * Approve purchase return
     */
    public function approvePurchaseReturn(PurchaseReturn $return)
    {
        DB::beginTransaction();
        try {
            $return->update(['status' => 'approved']);

            // Update supplier balance if credit_note
            if ($return->refund_method === 'credit_note' && $return->supplier) {
                $return->supplier->current_balance -= $return->total_return_amount;
                $return->supplier->save();
            }

            DB::commit();

            session()->flash('success', 'Purchase return approved');
            return back();

        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Error approving return: ' . $e->getMessage());
            return back();
        }
    }
    public function printThermalSale(SaleReturn $return)
    {
        $return->load(['order', 'customer', 'processor', 'items.product']);
        return view('backend.returns.sale.thermal', compact('return'));
    }

    public function printThermalPurchase(PurchaseReturn $return)
    {
        $return->load(['purchaseOrder', 'supplier', 'processor', 'items.product']);
        return view('backend.returns.purchase.thermal', compact('return'));
    }
}
