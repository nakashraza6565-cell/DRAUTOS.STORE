<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Shipping;
use App\User;
use PDF;
use Notification;
use Helper;
use Illuminate\Support\Str;
use App\Notifications\StatusNotification;
use App\Models\CustomerLedger;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orders=Order::with(['user', 'staff'])
            ->when($request->status, function($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->when($request->city, function($q) use ($request) {
                return $q->whereHas('user', function($sq) use ($request) {
                    $sq->where('city', $request->city);
                })->orWhere('address1', 'LIKE', "%{$request->city}%");
            })
            ->when($request->type, function($q) use ($request) {
                if($request->type == 'website') {
                    return $q->where('order_type', '!=', 'local')->orWhereNull('order_type');
                } elseif($request->type == 'local') {
                    return $q->where('order_type', 'local');
                }
            })
            ->when($request->staff_id, function($q) use ($request) {
                return $q->where('staff_id', $request->staff_id);
            })
            ->orderBy('pinned','DESC')->orderBy('created_at','DESC')->paginate(5000);
            
        $cities = User::whereNotNull('city')->where('city', '!=', '')->distinct()->pluck('city');
        $staffs = User::whereIn('role', ['admin', 'staff'])->orderBy('name', 'ASC')->get();
        return view('backend.order.index')->with('orders',$orders)->with('cities', $cities)->with('staffs', $staffs);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'first_name'=>'string|required',
            'last_name'=>'string|required',
            'address1'=>'string|required',
            'address2'=>'string|nullable',
            'coupon'=>'nullable|numeric',
            'phone'=>'numeric|required',
            'post_code'=>'string|nullable',
            'email'=>'string|required',
            'courier_company'=>'string|nullable',
            'courier_number'=>'string|nullable',
        ]);
        // return $request->all();

        if(empty(Cart::where('user_id',auth()->user()->id)->where('order_id',null)->first())){
            request()->session()->flash('error','Cart is Empty !');
            return back();
        }
        // $cart=Cart::get();
        // // return $cart;
        // $cart_index='ORD-'.strtoupper(uniqid());
        // $sub_total=0;
        // foreach($cart as $cart_item){
        //     $sub_total+=$cart_item['amount'];
        //     $data=array(
        //         'cart_id'=>$cart_index,
        //         'user_id'=>$request->user()->id,
        //         'product_id'=>$cart_item['id'],
        //         'quantity'=>$cart_item['quantity'],
        //         'amount'=>$cart_item['amount'],
        //         'status'=>'new',
        //         'price'=>$cart_item['price'],
        //     );

        //     $cart=new Cart();
        //     $cart->fill($data);
        //     $cart->save();
        // }

        // $total_prod=0;
        // if(session('cart')){
        //         foreach(session('cart') as $cart_items){
        //             $total_prod+=$cart_items['quantity'];
        //         }
        // }

        $order=new Order();
        $order_data=$request->all();
        $order_data['order_number']='ORD-'.strtoupper(Str::random(10));
        $order_data['user_id']=$request->user()->id;
        $order_data['shipping_id']=$request->shipping;
        $shipping=Shipping::where('id',$order_data['shipping_id'])->pluck('price');
        // return session('coupon')['value'];
        $order_data['sub_total']=Helper::totalCartPrice();
        $order_data['quantity']=Helper::cartCount();
        if(session('coupon')){
            $order_data['coupon']=session('coupon')['value'];
        }
        if($request->shipping){
            if(session('coupon')){
                $order_data['total_amount']=Helper::totalCartPrice()+$shipping[0]-session('coupon')['value'];
            }
            else{
                $order_data['total_amount']=Helper::totalCartPrice()+$shipping[0];
            }
        }
        else{
            if(session('coupon')){
                $order_data['total_amount']=Helper::totalCartPrice()-session('coupon')['value'];
            }
            else{
                $order_data['total_amount']=Helper::totalCartPrice();
            }
        }
        // return $order_data['total_amount'];
        $order_data['status']="new";
        if(request('payment_method')=='paypal'){
            $order_data['payment_method']='paypal';
            $order_data['payment_status']='paid';
        }
        else{
            $order_data['payment_method']='cod';
            $order_data['payment_status']='Unpaid';
        }
        $order->fill($order_data);
        $status=$order->save();
    
    // Ledger Integration
    if ($order && auth()->user() && (auth()->user()->role == 'user' || auth()->user()->role == 'customer')) {
        CustomerLedger::record(
            auth()->user()->id,
            now(),
            'debit',
            'order',
            'Store Order #' . $order->order_number . ' via ' . $order->payment_method,
            $order->total_amount,
            $order->id
        );

        if ($order->payment_status == 'paid') {
            CustomerLedger::record(
                auth()->user()->id,
                now(),
                'credit',
                'payment',
                'Payment for Order #' . $order->order_number,
                $order->total_amount,
                $order->id
            );
        }
    }

    if($order)
        // dd($order->id);
        // Notify all admins
        try {
            $admins = User::where('role', 'admin')->get();
            $details = [
                'title' => 'New order created by ' . auth()->user()->name,
                'actionURL' => route('order.show', $order->id),
                'fas' => 'fa-file-alt'
            ];
            Notification::send($admins, new StatusNotification($details));
        } catch (\Exception $e) {
            \Log::error('Failed to notify admins of checkout order: ' . $e->getMessage());
        }
        if(request('payment_method')=='paypal'){
            return redirect()->route('payment')->with(['id'=>$order->id]);
        }
        else{
            session()->forget('cart');
            session()->forget('coupon');
        }
        Cart::where('user_id', auth()->user()->id)->where('order_id', null)->update(['order_id' => $order->id]);

        // Send WhatsApp Notification with PDF Invoice
        try {
            if ($order->phone) {
                // Load relations needed for PDF
                $order->load('cart_info.product', 'cart_info.bundle', 'user', 'shipping');
                $waService = new \App\Services\WhatsAppService();
                $waService->sendOrderNotification($order);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send Online Order WhatsApp: ' . $e->getMessage());
        }

        request()->session()->flash('success','Your product successfully placed in order');
        return redirect()->route('home');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order=Order::find($id);
        // return $order;
        return view('backend.order.show')->with('order',$order);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order = Order::with('cart.product')->find($id);
        
        if(!$order) {
            request()->session()->flash('error', 'Order not found');
            return redirect()->route('order.index');
        }
        
        $reminder = \App\Models\PaymentReminder::where('reference_number', $order->order_number)->first();
        
        // Calculate paid at POS
        $paid_at_pos = $order->total_amount;
        if($reminder) {
            $paid_at_pos = $order->total_amount - $reminder->amount;
        }

        return view('backend.order.edit', compact('order', 'reminder', 'paid_at_pos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $order=Order::find($id);
        $this->validate($request,[
            'status'=>'required|in:new,process,delivered,cancel',
            'staff_id'=>'nullable|exists:users,id',
            'staff_commission'=>'nullable|numeric'
        ]);
        
        // Logic for Item Editing (Rebuild Cart)
        if ($request->has('items')) {
            // 1. Revert Stock if previously delivered
            if ($order->status == 'delivered') {
                foreach($order->cart as $cart){
                    $product = \App\Models\Product::find($cart->product_id);
                    if($product) {
                        $product->stock += $cart->quantity;
                        $product->save();
                    }
                }
            }
            
            // 2. Clear Old Cart
            $order->cart()->delete();
            
            // 3. Build New Cart
            $total_amount = 0;
            $total_qty = 0;
            foreach($request->items as $item) {
                $cart = new Cart();
                $cart->order_id = $order->id;
                $cart->user_id = $order->user_id;
                $cart->product_id = $item['id'];
                $cart->price = $item['price'];
                $cart->quantity = $item['qty'];
                $cart->amount = $item['price'] * $item['qty'];
                $cart->status = $request->status == 'delivered' ? 'progress' : 'new';
                $cart->save();
                
                $total_amount += $cart->amount;
                $total_qty += $cart->quantity;
            }
            
            // 4. Update Order Totals
            $order->sub_total = $total_amount;
            $order->total_amount = $total_amount; // Assuming no shipping/coupon re-calc for now
            $order->quantity = $total_qty;
            
            // 5. Deduct Stock if new status is delivered
            if ($request->status == 'delivered') {
                foreach($request->items as $item) {
                     $product = \App\Models\Product::find($item['id']);
                     if($product) {
                         $product->stock -= $item['qty'];
                         $product->save();
                     }
                }
            }
        } 
        else {
            // Legacy Status Update Only
            if($order->status != 'delivered' && $request->status == 'delivered'){
                foreach($order->cart as $cart){
                    $product=$cart->product;
                    $product->stock -= $cart->quantity;
                    $product->save();
                }
            }
            elseif($order->status == 'delivered' && $request->status != 'delivered'){
                foreach($order->cart as $cart){
                    $product=$cart->product;
                    $product->stock += $cart->quantity;
                    $product->save();
                }
            }
        }

        $status = $order->fill($request->only([
            'status', 'staff_id', 'staff_commission', 'first_name', 'last_name', 
            'phone', 'email', 'address1', 'courier_company', 'courier_number'
        ]))->save();
        
        // Update Commission
        if ($request->staff_id && $request->staff_commission) {
            $comm = \App\Models\EmployeeCommission::where('order_id', $order->id)->first();
            if (!$comm) {
                $comm = new \App\Models\EmployeeCommission();
                $comm->order_id = $order->id;
                $comm->status = 'pending';
            }
            $comm->employee_id = $request->staff_id;
            $comm->sale_amount = $order->total_amount;
            $comm->commission_amount = $request->staff_commission;
            $comm->commission_rate = ($order->total_amount > 0) ? ($request->staff_commission / $order->total_amount * 100) : 0;
            $comm->commission_date = now();
            $comm->save();
        }

        // --- SENSITIVE PAYMENT & LEDGER SYNC LOGIC ---
        $new_total = $order->total_amount;
        $amount_paid_at_counter = $request->amount_paid ?? 0;
        
        if ($order->user_id) {
            // 1. Sync the DEBIT (The Debt/Order Total) in Ledger
            $debit = CustomerLedger::where('user_id', $order->user_id)
                ->where('reference_id', $order->id)
                ->where('type', 'debit')
                ->first();
            
            if ($debit) {
                $debit->update(['amount' => $new_total]);
            } else {
                CustomerLedger::record($order->user_id, now(), 'debit', 'order', 'Order #' . $order->order_number, $new_total, $order->id);
            }

            // 2. Sync the CREDIT (The Initial Counter Payment) in Ledger
            $credit = CustomerLedger::where('user_id', $order->user_id)
                ->where('reference_id', $order->id)
                ->where('type', 'credit')
                ->first();
            
            if ($amount_paid_at_counter > 0) {
                if ($credit) {
                    $credit->update(['amount' => $amount_paid_at_counter]);
                } else {
                    CustomerLedger::record($order->user_id, now(), 'credit', 'payment', 'Payment for Order #' . $order->order_number, $amount_paid_at_counter, $order->id);
                }
            } elseif ($credit) {
                $credit->delete();
            }

            // Recalculate balance for this user (Ensures User->current_balance is correct)
            CustomerLedger::updateBalance($order->user_id);
        }

        // 3. Sync PaymentReminder (for Tracking and Reminders)
        $initial_pending = $new_total - $amount_paid_at_counter;
        
        $reminder = \App\Models\PaymentReminder::where('reference_number', $order->order_number)->first();
        
        if ($initial_pending > 0) {
            if (!$reminder) {
                $reminder = new \App\Models\PaymentReminder();
                $reminder->reference_number = $order->order_number;
                $reminder->type = 'receivable';
                $reminder->party_type = 'App\\User';
                $reminder->party_id = $order->user_id;
                $reminder->paid_amount = 0;
            }
            
            $reminder->amount = $initial_pending;
            $reminder->due_date = $request->due_date ?: ($reminder->due_date ?: now()->addDays(7));
            
            // Sync status
            if ($reminder->paid_amount >= $reminder->amount) {
                $reminder->status = 'completed';
                $order->payment_status = 'paid';
            } elseif ($reminder->paid_amount > 0) {
                $reminder->status = 'partially_paid';
                $order->payment_status = 'partial';
            } else {
                $reminder->status = 'pending';
                $order->payment_status = ($amount_paid_at_counter > 0) ? 'partial' : 'unpaid';
            }
            $reminder->save();
        } else {
            if ($reminder) $reminder->delete();
            $order->payment_status = 'paid';
        }
        $order->save();
        // --- END PAYMENT LOGIC ---


        
        if($status){
            request()->session()->flash('success','Successfully updated order');
        }
        else{
            request()->session()->flash('error','Error while updating order');
        }
        return redirect()->route('order.index');
    }

    /**
     * Toggle pin status of order
     */
    public function togglePin($id)
    {
        $order = Order::findOrFail($id);
        $order->pinned = !$order->pinned;
        $order->save();
        
        $message = $order->pinned ? 'Order pinned to top' : 'Order unpinned';
        request()->session()->flash('success', $message);
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order=Order::find($id);
        if($order){
            $status=$order->delete();
            if($status){
                request()->session()->flash('success','Order Successfully deleted');
            }
            else{
                request()->session()->flash('error','Order can not deleted');
            }
            return redirect()->route('order.index');
        }
        else{
            request()->session()->flash('error','Order can not found');
            return redirect()->back();
        }
    }

    public function orderTrack(){
        return view('frontend.pages.order-track');
    }

    public function productTrackOrder(Request $request){
        // return $request->all();
        $order=Order::where('user_id',auth()->user()->id)->where('order_number',$request->order_number)->first();
        if($order){
            if($order->status=="new"){
            request()->session()->flash('success','Your order has been placed. please wait.');
            return redirect()->route('home');

            }
            elseif($order->status=="process"){
                request()->session()->flash('success','Your order is under processing please wait.');
                return redirect()->route('home');
    
            }
            elseif($order->status=="delivered"){
                request()->session()->flash('success','Your order is successfully delivered.');
                return redirect()->route('home');
    
            }
            else{
                request()->session()->flash('error','Your order canceled. please try again');
                return redirect()->route('home');
    
            }
        }
        else{
            request()->session()->flash('error','Invalid order numer please try again');
            return back();
        }
    }

    // PDF generate
    public function pdf(Request $request, $id){
        $order=Order::getAllOrder($id);
        // return $order;
        $file_name=$order->order_number.'-'.$order->first_name.'.pdf';
        // return $file_name;
        $pdf=PDF::loadview('backend.order.pdf',compact('order'));
        return $pdf->download($file_name);
    }

    public function sendWhatsApp($id)
    {
        $order = Order::find($id);
        if (!$order) {
            request()->session()->flash('error', 'Order not found');
            return back();
        }

        if (!$order->phone) {
            request()->session()->flash('error', 'Customer phone number not found');
            return back();
        }

        try {
            // Load relations needed for PDF
            $order->load('cart_info.product', 'cart_info.bundle', 'user', 'shipping');
            $waService = new \App\Services\WhatsAppService();
            $waService->sendOrderNotification($order);
            
            request()->session()->flash('success', 'WhatsApp message sent successfully');
        } catch (\Exception $e) {
            \Log::error('Manual WhatsApp Order Error: ' . $e->getMessage());
            request()->session()->flash('error', 'Failed to send WhatsApp message: ' . $e->getMessage());
        }

        return back();
    }

    public function print(Request $request, $id)
    {
        $order = Order::getAllOrder($id);
        $type = $request->get('type', 'standard');
        
        if($type == 'thermal') {
            return view('backend.order.thermal')->with('order', $order);
        }
        
        return view('backend.order.pdf')->with('order', $order);
    }
    // Income chart
    public function incomeChart(Request $request){
        $year=\Carbon\Carbon::now()->year;
        // dd($year);
        $items=Order::with(['cart_info'])->whereYear('created_at',$year)->where('status','delivered')->get()
            ->groupBy(function($d){
                return \Carbon\Carbon::parse($d->created_at)->format('m');
            });
            // dd($items);
        $result=[];
        foreach($items as $month=>$item_collections){
            foreach($item_collections as $item){
                $amount=$item->cart_info->sum('amount');
                // dd($amount);
                $m=intval($month);
                // return $m;
                isset($result[$m]) ? $result[$m] += $amount :$result[$m]=$amount;
            }
        }
        $data=[];
        for($i=1; $i <=12; $i++){
            $monthName=date('F', mktime(0,0,0,$i,1));
            $data[$monthName] = (!empty($result[$i]))? number_format((float)($result[$i]), 2, '.', '') : 0.0;
        }
        return $data;
    }

    public function localOrders()
    {
        $orders = Order::with(['user', 'staff'])->where('order_type', 'local')->orderBy('id', 'DESC')->paginate(5000);
        $cities = User::whereNotNull('city')->where('city', '!=', '')->distinct()->pluck('city');
        return view('backend.order.index')->with('orders', $orders)->with('cities', $cities);
    }
    public function searchByNumber(Request $request) {
        $number = $request->get('number');
        $order = Order::where('order_number', 'LIKE', "%{$number}%")->first();
        if($order) {
            return response()->json(['id' => $order->id]);
        }
        return response()->json(['id' => null]);
    }
}
