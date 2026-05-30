<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\SalesOrderPhoto;
use App\Models\Product;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StatusNotification;

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
        // Items are optional IF photos are being uploaded
        $hasPhotos = $request->hasFile('order_photos');
        $hasItems  = !empty($request->items);

        if (!$hasItems && !$hasPhotos) {
            return back()->with('error', 'Please add at least one item OR upload at least one photo to create the order.')
                         ->withInput();
        }

        // Validate items only when they exist
        $rules = ['user_id' => 'required|exists:users,id'];
        if ($hasItems) {
            $rules['items']                = 'array';
            $rules['items.*.product_id']   = 'required|exists:products,id';
            $rules['items.*.quantity']     = 'required|numeric|min:0.01';
            $rules['items.*.price']        = 'required|numeric|min:0';
        }
        if ($hasPhotos) {
            $rules['order_photos']   = 'array|max:10';
            $rules['order_photos.*'] = 'file|mimes:jpeg,jpg,png,webp,heic,pdf|max:20480'; // 20 MB
        }
        $request->validate($rules);

        DB::beginTransaction();
        try {
            // Determine status
            $initialStatus = ($hasPhotos && !$hasItems) ? 'photo_pending' : 'pending';
            $totalAmount = $hasItems
                ? collect($request->items)->sum(fn($item) => $item['quantity'] * $item['price'])
                : 0;

            $salesOrder = SalesOrder::create([
                'order_number' => 'SO-' . strtoupper(Str::random(8)),
                'user_id'      => $request->user_id,
                'staff_id'     => auth()->id(),
                'total_amount' => $totalAmount,
                'note'         => $request->note,
                'status'       => $initialStatus,
            ]);

            // ---- Save uploaded photos ----
            if ($hasPhotos) {
                $this->storePhotos($request->file('order_photos'), $salesOrder->id);
            }

            $allMergedOrderNumbers = [];
            if ($hasItems) {
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
            } // end if ($hasItems)

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

            // Push notification to all admins
            try {
                $customer    = User::find($request->user_id);
                $createdBy   = auth()->user();
                $adminUsers  = User::whereIn('role', ['admin'])->get();
                $details = [
                    'title'     => '🛒 New Sales Order by ' . $createdBy->name . ' for ' . ($customer ? $customer->name : 'Unknown') . ' — PKR ' . number_format($salesOrder->total_amount),
                    'actionURL' => route('sales-orders.show', $salesOrder->id),
                    'fas'       => 'fa-file-alt'
                ];
                Notification::send($adminUsers, new StatusNotification($details));
            } catch (\Exception $ne) {
                \Log::error('Sales Order notification failed: ' . $ne->getMessage());
            }

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

    public function updateItemPrice(Request $request, $itemId)
    {
        try {
            $item = SalesOrderItem::findOrFail($itemId);
            $salesOrder = SalesOrder::findOrFail($item->sales_order_id);
            
            $item->price = $request->price;
            $item->save();

            // Recalculate order total
            $salesOrder->total_amount = $salesOrder->items()->sum(DB::raw('quantity * price'));
            $salesOrder->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Price updated successfully',
                'total_amount' => $salesOrder->total_amount,
                'item_total' => $item->price * $item->quantity
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating price: ' . $e->getMessage()
            ], 500);
        }
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

    /* =========================================================
     *  PHOTO UPLOAD / SERVE METHODS
     * ========================================================= */

    /**
     * Upload photos to an existing sale order (AJAX / standard POST).
     */
    public function uploadPhotos(Request $request, $id)
    {
        $salesOrder = SalesOrder::findOrFail($id);

        $request->validate([
            'order_photos'   => 'required|array|min:1|max:10',
            'order_photos.*' => 'required|file|mimes:jpeg,jpg,png,webp,heic,pdf|max:20480',
        ]);

        // Enforce global limit of 10 per order
        $existing = $salesOrder->photos()->count();
        $incoming = count($request->file('order_photos'));
        if ($existing + $incoming > 10) {
            $msg = "Cannot upload {$incoming} photo(s). Order already has {$existing} and limit is 10.";
            if ($request->ajax()) return response()->json(['status' => 'error', 'message' => $msg], 422);
            return back()->with('error', $msg);
        }

        $this->storePhotos($request->file('order_photos'), $salesOrder->id);

        // If order was photo_pending and now has items, keep status — admin handles it
        // If it was photo_pending and still no items, stays photo_pending

        if ($request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => $incoming . ' photo(s) uploaded successfully.',
                'photos'  => $salesOrder->fresh()->photos()->with('uploader')->get()->map(fn($p) => [
                    'id'            => $p->id,
                    'url'           => route('sales-orders.photos.view', [$salesOrder->id, $p->id]),
                    'original_name' => $p->original_name,
                    'human_size'    => $p->human_file_size,
                    'uploaded_by'   => $p->uploader->name ?? 'Staff',
                    'uploaded_at'   => $p->created_at->format('d M Y, h:i A'),
                ]),
            ]);
        }

        return back()->with('success', $incoming . ' photo(s) uploaded successfully.');
    }

    /**
     * Upload photos to an existing sale order (Customer Portal).
     */
    public function userUploadPhotos(Request $request, $id)
    {
        $salesOrder = SalesOrder::findOrFail($id);

        if ($salesOrder->user_id !== auth()->id()) {
            if ($request->ajax()) return response()->json(['status' => 'error', 'message' => 'Access denied.'], 403);
            return back()->with('error', 'Access denied.');
        }

        $request->validate([
            'order_photos'   => 'required|array|min:1|max:10',
            'order_photos.*' => 'required|file|mimes:jpeg,jpg,png,webp,heic,pdf|max:20480',
        ]);

        $existing = $salesOrder->photos()->count();
        $incoming = count($request->file('order_photos'));
        if ($existing + $incoming > 10) {
            $msg = "Cannot upload {$incoming} photo(s). Order already has {$existing} and limit is 10.";
            if ($request->ajax()) return response()->json(['status' => 'error', 'message' => $msg], 422);
            return back()->with('error', $msg);
        }

        $this->storePhotos($request->file('order_photos'), $salesOrder->id);

        if ($request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Photos uploaded successfully.',
                'photos'  => $salesOrder->fresh()->photos()->get()->map(fn($p) => [
                    'id'            => $p->id,
                    'url'           => route('user.sales-orders.photos.view', [$salesOrder->id, $p->id]),
                    'original_name' => $p->original_name,
                    'human_size'    => $p->human_file_size,
                    'uploaded_at'   => $p->created_at->format('d M Y')
                ]),
            ]);
        }

        return back()->with('success', 'Photos uploaded successfully.');
    }

    /**
     * Delete a single photo from a sale order.
     */
    public function deletePhoto($id, $photoId)
    {
        $salesOrder = SalesOrder::findOrFail($id);
        $photo      = SalesOrderPhoto::where('sales_order_id', $id)->findOrFail($photoId);

        // Remove file from disk
        if (Storage::exists($photo->disk_path)) {
            Storage::delete($photo->disk_path);
        }
        $photo->delete();

        if (request()->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Photo deleted.']);
        }
        return back()->with('success', 'Photo deleted.');
    }

    /**
     * Serve a photo file (protected — not publicly accessible).
     * Accessible by: admin/staff (backend) and the customer who owns the order (frontend).
     */
    public function viewPhoto($id, $photoId)
    {
        $salesOrder = SalesOrder::findOrFail($id);

        // Customer auth check: if not admin/staff, ensure they own the order
        if (auth()->check()) {
            $role = auth()->user()->role ?? 'user';
            if (!in_array($role, ['admin', 'staff', 'manager'])) {
                if ($salesOrder->user_id !== auth()->id()) {
                    abort(403, 'Access denied.');
                }
            }
        } else {
            abort(401);
        }

        $photo = SalesOrderPhoto::where('sales_order_id', $id)->findOrFail($photoId);

        if (!Storage::exists($photo->disk_path)) {
            abort(404, 'Photo file not found.');
        }

        $file     = Storage::get($photo->disk_path);
        $mimeType = $photo->mime_type ?? 'image/jpeg';

        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $photo->original_name . '"')
            ->header('Cache-Control', 'private, max-age=3600');
    }

    /**
     * Internal helper: saves uploaded files to private storage.
     */
    private function storePhotos(array $files, $salesOrderId)
    {
        $dir = 'sale-order-photos/' . $salesOrderId;

        foreach ($files as $file) {
            if (!$file->isValid()) continue;

            $ext      = $file->getClientOriginalExtension();
            $filename = Str::uuid() . '.' . $ext;
            $path     = $dir . '/' . $filename;

            Storage::put($path, file_get_contents($file));

            SalesOrderPhoto::create([
                'sales_order_id' => $salesOrderId,
                'filename'       => $filename,
                'original_name'  => $file->getClientOriginalName(),
                'disk_path'      => $path,
                'uploaded_by'    => auth()->id(),
                'file_size'      => $file->getSize(),
                'mime_type'      => $file->getMimeType(),
            ]);
        }
    }
}
