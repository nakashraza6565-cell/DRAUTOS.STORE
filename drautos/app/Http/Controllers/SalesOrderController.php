<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Product;
use App\User;
use Illuminate\Support\Str;
use DB;

class SalesOrderController extends Controller
{
    public function index(Request $request)
    {
        $city     = $request->get('city');
        $search   = trim($request->get('search'));
        $staffId  = $request->get('staff_id');
        $status   = $request->get('status');

        $salesOrders = SalesOrder::with(['user', 'items', 'staff'])
            ->join('users', 'sales_orders.user_id', '=', 'users.id')
            ->when($city,    fn($q) => $q->where('users.city', $city))
            ->when($staffId, fn($q) => $q->where('sales_orders.staff_id', $staffId))
            ->when($status,  fn($q) => $q->where('sales_orders.status', $status))
            ->when($search, function($q) use ($search) {
                $q->where(function($sub) use ($search) {
                    $sub->where('sales_orders.order_number', 'like', "%{$search}%")
                        ->orWhere('users.name', 'like', "%{$search}%")
                        ->orWhere('users.phone', 'like', "%{$search}%");
                });
            })
            ->select('sales_orders.*')
            ->orderBy('sales_orders.is_priority', 'DESC')
            ->orderBy('sales_orders.created_at', 'DESC')
            ->paginate(20)
            ->appends($request->only(['city', 'search', 'staff_id', 'status']));

        $cities   = User::whereNotNull('city')->where('city', '!=', '')->distinct()->pluck('city')->sort()->values();
        $allStaff = User::whereIn('role', ['admin', 'staff', 'manager'])->orderBy('name')->get();

        return view('backend.sales-orders.index', compact('salesOrders', 'cities', 'city', 'search', 'allStaff', 'staffId', 'status'));
    }

    public function togglePriority($id)
    {
        $salesOrder = SalesOrder::findOrFail($id);
        $salesOrder->update(['is_priority' => !$salesOrder->is_priority]);
        return back()->with('success', $salesOrder->is_priority ? 'Marked as Priority' : 'Priority removed');
    }

    public function create(Request $request)
    {
        $customers = User::where('role', 'user')->orderBy('name', 'ASC')->get();
        $products = Product::with('brand')->where('status', 'active')->orderBy('title', 'ASC')->get();
        $categories = \App\Models\Category::where('status', 'active')->get();
        $brands = \App\Models\Brand::where('status', 'active')->get();
        $product_models = \App\Models\ProductModel::all();
        $suppliers = \App\Models\Supplier::where('status', 'active')->get();
        $units = \App\Models\Unit::orderBy('name')->get();
        
        $cities = User::whereNotNull('city')->where('city', '!=', '')->distinct()->pluck('city')->sort()->values();
        
        $selectedUserId = $request->get('user_id');
        
        return view('backend.sales-orders.create', compact('customers', 'products', 'categories', 'brands', 'product_models', 'suppliers', 'units', 'selectedUserId', 'cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $salesOrder = SalesOrder::create([
                'order_number' => 'SO-' . strtoupper(Str::random(8)),
                'user_id' => $request->user_id,
                'staff_id' => auth()->id(),
                'total_amount' => collect($request->items)->sum(function($item) {
                    return $item['quantity'] * $item['price'];
                }),
                'note' => $request->note,
                'status' => 'pending'
            ]);

            $allMergedOrderNumbers = [];
            foreach ($request->items as $item) {
                SalesOrderItem::create([
                    'sales_order_id' => $salesOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'status' => 'pending'
                ]);

                // Update Price Memory for Customer
                \App\Models\CustomerProductPrice::updateOrCreate(
                    ['customer_id' => $request->user_id, 'product_id' => $item['product_id']],
                    ['last_sold_price' => $item['price']]
                );

                // Collect order numbers for consolidated items
                $itemMergedOrders = SalesOrder::where('user_id', $request->user_id)
                    ->whereIn('status', ['pending', 'partially_delivered'])
                    ->where('id', '!=', $salesOrder->id)
                    ->whereHas('items', function($q) use ($item) {
                        $q->where('product_id', $item['product_id'])->where('status', 'pending');
                    })
                    ->pluck('order_number')
                    ->toArray();
                
                $allMergedOrderNumbers = array_merge($allMergedOrderNumbers, $itemMergedOrders);

                // Mark previous pending items for this product as "merged"
                SalesOrderItem::where('product_id', $item['product_id'])
                    ->whereHas('salesOrder', function($q) use ($request, $salesOrder) {
                        $q->where('user_id', $request->user_id)
                          ->whereIn('status', ['pending', 'partially_delivered'])
                          ->where('id', '!=', $salesOrder->id);
                    })
                    ->update(['status' => 'merged']);
            }

            // Update new order note with consolidated references once
            $uniqueMergedOrders = array_unique($allMergedOrderNumbers);
            if (!empty($uniqueMergedOrders)) {
                $consolidationNote = "\n[System: Consolidated items from " . implode(', ', $uniqueMergedOrders) . "]";
                $salesOrder->update(['note' => $salesOrder->note . $consolidationNote]);
            }

            // Automatic Fulfillment Logic for previous orders
            $previousOrders = SalesOrder::where('user_id', $request->user_id)
                ->where('status', '!=', 'delivered')
                ->where('status', '!=', 'merged')
                ->where('id', '!=', $salesOrder->id)
                ->with('items')
                ->get();

            foreach ($previousOrders as $oldOrder) {
                $isMerged = true;
                foreach ($oldOrder->items as $oldItem) {
                    if ($oldItem->status != 'delivered' && $oldItem->status != 'merged') {
                        $isMerged = false;
                        break;
                    }
                }
                if ($isMerged) {
                    $oldOrder->update(['status' => 'merged']);
                }
            }

            DB::commit();
            return redirect()->route('sales-orders.index')->with('success', 'Sales Order created successfully. Previous orders updated and completed if fulfilled.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error creating Sales Order: ' . $e->getMessage());
        }
    }

    /**
     * Get pending items for a customer (AJAX)
     */
    public function getPendingItems($userId)
    {
        $pendingItems = SalesOrderItem::whereHas('salesOrder', function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->whereIn('status', ['pending', 'partially_delivered']);
        })
        ->where('status', 'pending')
        ->whereColumn('delivered_quantity', '<', 'quantity')
        ->with('product')
        ->get();
        
        $data = $pendingItems->map(function($item) {
            return [
                'product_id' => $item->product_id,
                'product_title' => $item->product->title . ($item->product->sku ? ' ('.$item->product->sku.')' : ''),
                'quantity' => $item->quantity - $item->delivered_quantity,
                'price' => $item->price
            ];
        });

        return response()->json($data);
    }

    public function show($id)
    {
        $salesOrder  = SalesOrder::with(['items.product.brand', 'user', 'staff'])->findOrFail($id);
        $allStaff    = User::whereIn('role', ['admin', 'staff'])->get();
        $products    = Product::with('brand')->where('status', 'active')->orderBy('title')->get();
        $categories  = \App\Models\Category::where('status', 'active')->get();
        $brands      = \App\Models\Brand::where('status', 'active')->get();
        $product_models = \App\Models\ProductModel::all();
        $suppliers   = \App\Models\Supplier::where('status', 'active')->get();
        $units       = \App\Models\Unit::orderBy('name')->get();
        return view('backend.sales-orders.show', compact('salesOrder', 'allStaff', 'products', 'categories', 'brands', 'product_models', 'suppliers', 'units'));
    }

    public function fulfill(Request $request, $id)
    {
        $salesOrder = SalesOrder::with(['items.product.brand'])->findOrFail($id);
        
        $selectedIds = $request->input('selected_items', []);
        $deliverQtys = $request->input('deliver', []);

        if (empty($selectedIds)) {
            return back()->with('error', 'Please select at least one item to deliver');
        }

        $payload = [];
        foreach ($selectedIds as $itemId) {
            $soItem = $salesOrder->items->find($itemId);
            if (!$soItem) continue;

            $qtyRequested = $deliverQtys[$itemId] ?? 0;
            if ($qtyRequested <= 0) continue;

            $deliveryQty = (float)$qtyRequested;

            if ($deliveryQty > 0) {
                $payload[] = [
                    'id' => $soItem->product_id,
                    'type' => 'product',
                    'title' => $soItem->product->title,
                    'qty' => $deliveryQty,
                    'price' => (float)$soItem->price,
                    'unit' => $soItem->product->unit,
                    'brand' => $soItem->product->brand ? $soItem->product->brand->title : '',
                    'model' => $soItem->product->model,
                    'so_item_id' => $soItem->id
                ];
            }
        }

        if (empty($payload)) {
            return back()->with('error', 'No valid quantities selected for delivery');
        }

        // Store in session and redirect to POS
        session(['pos_payload' => [
            'items' => $payload,
            'customer_id' => $salesOrder->user_id,
            'sales_order_id' => $salesOrder->id
        ]]);

        return redirect()->route('admin.pos', ['from_so' => 1]);
    }

    public function thermalPrint($id)
    {
        $salesOrder = SalesOrder::with(['items.product', 'user', 'staff'])->findOrFail($id);
        return view('backend.sales-orders.thermal', compact('salesOrder'));
    }

    public function assignStaff(Request $request, $id)
    {
        $request->validate(['staff_id' => 'required|exists:users,id']);
        $salesOrder = SalesOrder::findOrFail($id);
        $salesOrder->update(['staff_id' => $request->staff_id]);
        return back()->with('success', 'Staff assigned successfully');
    }

    public function addItem(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|numeric|min:0.01',
            'price'      => 'required|numeric|min:0',
        ]);

        $salesOrder = SalesOrder::findOrFail($id);

        // Check if product already exists in order
        $existing = $salesOrder->items()->where('product_id', $request->product_id)->first();
        if ($existing) {
            $existing->increment('quantity', $request->quantity);
        } else {
            SalesOrderItem::create([
                'sales_order_id' => $salesOrder->id,
                'product_id'     => $request->product_id,
                'quantity'       => $request->quantity,
                'price'          => $request->price,
                'status'         => 'pending',
            ]);
        }

        // Recalculate total
        $salesOrder->total_amount = $salesOrder->items()->sum(DB::raw('quantity * price'));
        $salesOrder->save();

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Item added to order',
                'total_amount' => $salesOrder->total_amount,
                'order' => $salesOrder->load('items.product')
            ]);
        }

        return back()->with('success', 'Item added to order');
    }

    public function removeItem($id, $itemId)
    {
        $salesOrder = SalesOrder::findOrFail($id);
        $item = SalesOrderItem::where('sales_order_id', $id)->findOrFail($itemId);
        $item->delete();

        // Recalculate total
        $salesOrder->total_amount = $salesOrder->items()->sum(DB::raw('quantity * price'));
        $salesOrder->save();

        return back()->with('success', 'Item removed from order');
    }

    public function destroy($id)
    {
        $salesOrder = SalesOrder::findOrFail($id);
        $salesOrder->delete();
        return back()->with('success', 'Sales Order deleted');
    }

    /**
     * Get the historical or default price for a customer and product
     */
    public function getCustomerPrice(Request $request)
    {
        $customerId = $request->customer_id;
        $productId = $request->product_id;

        if (!$customerId || !$productId) {
            return response()->json(['success' => false, 'message' => 'Missing data'], 400);
        }

        // Try to find historical price
        $historical = \App\Models\CustomerProductPrice::where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->first();

        if ($historical) {
            return response()->json([
                'success' => true,
                'price' => $historical->last_sold_price,
                'source' => 'history'
            ]);
        }

        // Fallback to product selling price
        $product = \App\Models\Product::find($productId);
        return response()->json([
            'success' => true,
            'price' => $product->price ?? 0,
            'source' => 'default'
        ]);
    }
}
