<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Order;
use App\Models\ProductReview;
use App\Models\PostComment;
use App\Rules\MatchOldPassword;
use Hash;
use App\Services\WhatsAppService;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Product;
use App\Models\Bundle;
use App\Models\Cart;
use App\Models\PaymentReminder;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\CustomerLedger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StatusNotification;
use App\Models\SalesOrder;

class HomeController extends Controller
{
    protected $whatsapp;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(WhatsAppService $whatsapp)
    {
        $this->middleware('auth');
        $this->whatsapp = $whatsapp;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


    public function index(Request $request){
        $user_id = auth()->user()->id;
        
        $query = Order::where('user_id', $user_id);

        if($request->date_from){
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if($request->date_to){
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if($request->status){
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('id','DESC')->limit(20)->get();
        
        // Calculate stats (stays same usually for overall progress, or filter them too?)
        // User asked to "check progress and results more deeply", 
        // usually that means stats should also be filtered.
        
        $stats = [
            'total_orders' => (clone $query)->count(),
            'pending_orders' => (clone $query)->whereIn('status', ['new', 'process'])->count(),
            'total_pending' => auth()->user()->current_balance, // Use ledger balance
            'total_paid' => (clone $query)->where('payment_status', 'paid')->sum('total_amount'),
            'total_amount' => (clone $query)->sum('total_amount')
        ];

        $recent_ledger = CustomerLedger::where('user_id', $user_id)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return view('user.index', compact('orders', 'stats', 'recent_ledger'));
    }

    public function profile(){
        $profile=Auth()->user();
        // return $profile;
        return view('user.users.profile')->with('profile',$profile);
    }

    public function profileUpdate(Request $request,$id){
        // return $request->all();
        $user=User::findOrFail($id);
        $data=$request->all();
        $status=$user->fill($data)->save();
        if($status){
            request()->session()->flash('success','Successfully updated your profile');
        }
        else{
            request()->session()->flash('error','Please try again!');
        }
        return redirect()->back();
    }

    // Order
    public function orderIndex(){
        $user_id = auth()->user()->id;
        
        // "Delivered" module: Finalized Orders
        $orders = Order::where('user_id', $user_id)
                      ->orderBy('id', 'DESC')
                      ->paginate(5000);

        // "Pending" module: Sales Orders (Quotes/Bookings)
        $sales_orders = SalesOrder::where('user_id', $user_id)
                                 ->orderBy('id', 'DESC')
                                 ->get();

        return view('user.order.index')
                ->with('orders', $orders)
                ->with('sales_orders', $sales_orders);
    }
    public function userOrderDelete($id)
    {
        $order=Order::find($id);
        if($order){
           if($order->status=="process" || $order->status=='delivered' || $order->status=='cancel'){
                return redirect()->back()->with('error','You can not delete this order now');
           }
           else{
                $status=$order->delete();
                if($status){
                    request()->session()->flash('success','Order Successfully deleted');
                }
                else{
                    request()->session()->flash('error','Order can not deleted');
                }
                return redirect()->route('user.order.index');
           }
        }
        else{
            request()->session()->flash('error','Order can not found');
            return redirect()->back();
        }
    }

    public function orderShow($id)
    {
        $order = Order::with(['cart_info.product', 'shipping'])
                      ->where('user_id', auth()->user()->id)
                      ->where('id', $id)
                      ->first();
        if (!$order) {
            return redirect()->route('user.order.index')->with('error', 'Order not found or access denied');
        }
        return view('user.order.show')->with('order', $order);
    }

    public function salesOrderShow($id)
    {
        $salesOrder = SalesOrder::with(['items.product.brand', 'user', 'staff'])
                               ->where('user_id', auth()->user()->id)
                               ->where('id', $id)
                               ->first();
        if (!$salesOrder) {
            return redirect()->route('user.order.index')->with('error', 'Pending order not found or access denied');
        }
        return view('user.order.sales_order_show')->with('salesOrder', $salesOrder);
    }
    // Product Review
    public function productReviewIndex(){
        $reviews=ProductReview::getAllUserReview();
        return view('user.review.index')->with('reviews',$reviews);
    }

    public function productReviewEdit($id)
    {
        $review=ProductReview::find($id);
        // return $review;
        return view('user.review.edit')->with('review',$review);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function productReviewUpdate(Request $request, $id)
    {
        $review=ProductReview::find($id);
        if($review){
            $data=$request->all();
            $status=$review->fill($data)->update();
            if($status){
                request()->session()->flash('success','Review Successfully updated');
            }
            else{
                request()->session()->flash('error','Something went wrong! Please try again!!');
            }
        }
        else{
            request()->session()->flash('error','Review not found!!');
        }

        return redirect()->route('user.productreview.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function productReviewDelete($id)
    {
        $review=ProductReview::find($id);
        $status=$review->delete();
        if($status){
            request()->session()->flash('success','Successfully deleted review');
        }
        else{
            request()->session()->flash('error','Something went wrong! Try again');
        }
        return redirect()->route('user.productreview.index');
    }

    public function userComment()
    {
        $comments=PostComment::getAllUserComments();
        return view('user.comment.index')->with('comments',$comments);
    }
    public function userCommentDelete($id){
        $comment=PostComment::find($id);
        if($comment){
            $status=$comment->delete();
            if($status){
                request()->session()->flash('success','Post Comment successfully deleted');
            }
            else{
                request()->session()->flash('error','Error occurred please try again');
            }
            return back();
        }
        else{
            request()->session()->flash('error','Post Comment not found');
            return redirect()->back();
        }
    }
    public function userCommentEdit($id)
    {
        $comments=PostComment::find($id);
        if($comments){
            return view('user.comment.edit')->with('comment',$comments);
        }
        else{
            request()->session()->flash('error','Comment not found');
            return redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userCommentUpdate(Request $request, $id)
    {
        $comment=PostComment::find($id);
        if($comment){
            $data=$request->all();
            // return $data;
            $status=$comment->fill($data)->update();
            if($status){
                request()->session()->flash('success','Comment successfully updated');
            }
            else{
                request()->session()->flash('error','Something went wrong! Please try again!!');
            }
            return redirect()->route('user.post-comment.index');
        }
        else{
            request()->session()->flash('error','Comment not found');
            return redirect()->back();
        }

    }

    public function changePassword(){
        return view('user.layouts.userPasswordChange');
    }
    public function changPasswordStore(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);
   
        User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
   
        return redirect()->route('user')->with('success','Password successfully changed');
    }

    public function returnsIndex()
    {
        $userId = auth()->user()->id;
        $returns = SaleReturn::where('customer_id', $userId)
            ->with(['order', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(5000);
            
        $deliveredOrders = Order::where('user_id', $userId)
            ->where('status', 'delivered')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.returns.index', compact('returns', 'deliveredOrders'));
    }

    public function createReturn(Order $order)
    {
        if ($order->user_id != auth()->user()->id) {
            return redirect()->back()->with('error', 'Unauthorized access');
        }
        $order->load('cart_info.product');
        return view('user.returns.create', compact('order'));
    }

    public function storeReturn(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'type' => 'required|in:return,claim',
            'reason' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        if ($order->user_id != auth()->user()->id) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        DB::beginTransaction();
        try {
            $returnNumber = ($validated['type'] == 'claim' ? 'CL-' : 'SR-') . date('Ymd') . '-' . str_pad(SaleReturn::count() + 1, 4, '0', STR_PAD_LEFT);

            $return = SaleReturn::create([
                'return_number' => $returnNumber,
                'order_id' => $order->id,
                'customer_id' => $order->user_id,
                'return_date' => now(),
                'total_return_amount' => 0, // Will update after approval or if price known
                'reason' => $validated['reason'],
                'type' => $validated['type'],
                'status' => 'pending',
                'processed_by' => null, // Not yet processed
            ]);

            $totalAmount = 0;
            foreach ($validated['items'] as $itemData) {
                // Find matching cart item to get price
                $cartItem = $order->cart_info->where('product_id', $itemData['product_id'])->first();
                $unitPrice = $cartItem ? $cartItem->price : 0;
                $totalPrice = $unitPrice * $itemData['quantity'];
                $totalAmount += $totalPrice;

                SaleReturnItem::create([
                    'sale_return_id' => $return->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'condition' => 'good', // default
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            $return->update(['total_return_amount' => $totalAmount]);

            // Notify Admins
            try {
                $admins = User::where('role', 'admin')->get();
                $details = [
                    'title' => 'New ' . ucfirst($validated['type']) . ' request from Customer',
                    'actionURL' => route('returns.sale.show', $return->id),
                    'fas' => ($validated['type'] == 'claim' ? 'fa-exclamation-triangle' : 'fa-undo-alt')
                ];
                Notification::send($admins, new StatusNotification($details));
            } catch (\Exception $e) {
                \Log::error('Failed to notify admins of user return/claim: ' . $e->getMessage());
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Your ' . $validated['type'] . ' request has been submitted and is pending approval.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function ledger(Request $request)
    {
        $user_id = auth()->user()->id;
        $query = CustomerLedger::where('user_id', $user_id);

        if ($request->date_from) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $allLedger = (clone $query)->orderBy('transaction_date', 'asc')->get();
        $graphLabels = [];
        $balanceHistory = [];
        $runningBalance = 0;
        
        foreach ($allLedger as $item) {
            $graphLabels[] = date('d M', strtotime($item->transaction_date));
            if ($item->type == 'debit') {
                $runningBalance += $item->amount;
            } else {
                $runningBalance -= $item->amount;
            }
            $balanceHistory[] = $runningBalance;
        }

        $ledger = $query->orderBy('transaction_date', 'desc')->orderBy('id', 'desc')->paginate(5000);
        $user = auth()->user();
        
        return view('user.ledger.index', compact('ledger', 'user', 'graphLabels', 'balanceHistory'));
    }

    public function onlineOrder() {
        $categories = Category::where('status', 'active')->get();
        $user = auth()->user();
        $balance = $user->current_balance ?? 0;
        $recent_ledger = CustomerLedger::where('user_id', $user->id)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();
            
        return view('user.pos.index', compact('categories', 'balance', 'recent_ledger'));
    }

    public function searchProducts(Request $request) {
        $query = $request->get('query');
        
        // Products
        $keywords = explode(' ', $query);
        $products = Product::where('status', 'active')
            ->where(function($q) use ($keywords) {
                foreach($keywords as $word) {
                    $q->orWhere('title', 'LIKE', "%{$word}%")
                      ->orWhere('barcode', 'LIKE', "%{$word}%")
                      ->orWhere('sku', 'LIKE', "%{$word}%")
                      ->orWhereHas('brand', function($b) use ($word) {
                          $b->where('title', 'LIKE', "%{$word}%");
                      });
                }
            })
            ->limit(20)
            ->get();
            
        // Bundles
        $bundles = Bundle::where('status', 'active')
             ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get();
            
        $results = [];
        
        foreach($products as $p) {
            $p->item_type = 'product';
            $results[] = $p;
        }
        
        foreach($bundles as $b) {
            $b->title = $b->name;
            $b->item_type = 'bundle';
            $b->stock = 100;
            $b->photo = null; 
            $results[] = $b;
        }
        
        return response()->json($results);
    }

    public function storeOnlineOrder(Request $request)
    {
        \Log::info('storeOnlineOrder called', ['request' => $request->all()]);
        $data = $request->validate([
            'cart' => 'required|array',
            'total_amount' => 'required',
            'payment_method' => 'required'
        ]);

        $user = auth()->user();

        return \DB::transaction(function () use ($data, $user) {
            // Create Order
            $order_number = 'ORD-' . strtoupper(Str::random(10));
            
            $order = new Order();
            $order->order_number = $order_number;
            $order->user_id = $user->id;
            $order->sub_total = $data['total_amount'];
            $order->total_amount = $data['total_amount'];
            $order->quantity = count($data['cart']);
            $order->payment_method = $data['payment_method'];
            $order->payment_status = 'unpaid';
            $order->status = 'new';
            $order->order_type = 'store';
            
            $names = explode(' ', $user->name, 2);
            $order->first_name = $names[0] ?: 'Customer';
            $order->last_name = $names[1] ?? 'Customer';
            $order->email = $user->email;
            $order->phone = $user->phone ?: '0000000000';
            $order->address1 = $user->address ?: 'Online';
            $order->country = 'Pakistan';
            $order->courier_company = $user->courier_company;
            $order->courier_number = $user->courier_number;
            
            $order->save();

            // Ledger Integration: Add to account debt immediately
            CustomerLedger::record(
                $user->id,
                now(),
                'debit',
                'order',
                'Online Order #' . $order->order_number . ' (COD)',
                $order->total_amount,
                $order->id
            );

            // Create Payment Reminder for the unpaid balance (Online orders are COD)
            \App\Models\PaymentReminder::create([
                'type' => 'receivable',
                'party_type' => 'App\\User',
                'party_id' => $user->id,
                'reference_number' => $order->order_number,
                'amount' => $order->total_amount,
                'due_date' => now()->addDays(7),
                'status' => 'pending',
                'notes' => 'Auto-generated from Online Order ' . $order->order_number
            ]);

            // Save Cart Items & Update Stock
            foreach($data['cart'] as $item) {
                $type = $item['type'] ?? 'product';
                
                $cart = new Cart();
                $cart->order_id = $order->id;
                $cart->user_id = $order->user_id;
                $cart->price = $item['price'];
                $cart->status = 'progress';
                $cart->quantity = $item['qty'];
                $cart->amount = $item['price'] * $item['qty'];
                $cart->item_type = $type;

                if ($type == 'bundle') {
                     $cart->bundle_id = $item['id'];
                     $bundle = Bundle::find($item['id']);
                     if($bundle) {
                         foreach($bundle->items as $bItem) {
                             $prod = Product::find($bItem->product_id);
                             if($prod) {
                                 $prod->decrement('stock', $bItem->quantity * $item['qty']);
                             }
                         }
                     }
                } else {
                    $cart->product_id = $item['id'];
                    $product = Product::find($item['id']);
                    if($product) {
                        $product->decrement('stock', $item['qty']);
                    }
                }
                $cart->save();
            }

            // WhatsApp Notification with PDF Invoice
            try {
                if ($order->phone && $order->phone != '0000000000') {
                    // Load relations required by the PDF view
                    $order->load('cart_info.product', 'cart_info.bundle', 'user', 'shipping');
                    $this->whatsapp->sendOrderNotification($order);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send Online WhatsApp: ' . $e->getMessage());
            }

            // Notify Admins
            try {
                $admins = User::where('role', 'admin')->get();
                $details = [
                    'title' => 'New Online order created by ' . $user->name,
                    'actionURL' => route('order.show', $order->id),
                    'fas' => 'fa-shopping-cart'
                ];
                Notification::send($admins, new StatusNotification($details));
            } catch (\Exception $e) {
                \Log::error('Failed to notify admins of online order: ' . $e->getMessage());
            }

            return response()->json([
                'status'   => 'success',
                'message'  => 'Order placed successfully',
                'order_id' => $order->id
            ]);
        });
    }
}
