<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\Cart;
use Illuminate\Support\Str;
use Helper;
class CartController extends Controller
{
    protected $product=null;
    public function __construct(Product $product){
        $this->product=$product;
    }

    public function addToCart(Request $request){
        if (empty($request->slug)) {
            request()->session()->flash('error','Invalid Products');
            return back();
        }        
        $product = Product::where('slug', $request->slug)->first();
        if (empty($product)) {
            request()->session()->flash('error','Invalid Products');
            return back();
        }

        $price = ($product->price-($product->price*$product->discount)/100);

        if(Auth::check()) {
            $already_cart = Cart::where('user_id', auth()->user()->id)->where('order_id',null)->where('product_id', $product->id)->first();
            if($already_cart) {
                $already_cart->quantity = $already_cart->quantity + 1;
                $already_cart->amount = $price + $already_cart->amount;
                if ($already_cart->product->stock < $already_cart->quantity || $already_cart->product->stock <= 0) return back()->with('error','Stock not sufficient!.');
                $already_cart->save();
            } else {
                $cart = new Cart;
                $cart->user_id = auth()->user()->id;
                $cart->product_id = $product->id;
                $cart->price = $price;
                $cart->quantity = 1;
                $cart->amount = $price;
                if ($cart->product->stock < $cart->quantity || $cart->product->stock <= 0) return back()->with('error','Stock not sufficient!.');
                $cart->save();
                Wishlist::where('user_id',auth()->user()->id)->where('cart_id',null)->update(['cart_id'=>$cart->id]);
            }
        } else {
            $cart = session()->get('cart', []);
            if(isset($cart[$product->id])) {
                $cart[$product->id]['quantity']++;
                $cart[$product->id]['amount'] += $price;
                if ($product->stock < $cart[$product->id]['quantity'] || $product->stock <= 0) return back()->with('error','Stock not sufficient!.');
            } else {
                if ($product->stock < 1 || $product->stock <= 0) return back()->with('error','Stock not sufficient!.');
                $cart[$product->id] = [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => $price,
                    'amount' => $price,
                ];
            }
            session()->put('cart', $cart);
        }

        request()->session()->flash('success','Product successfully added to cart');
        return back();       
    }  

    public function singleAddToCart(Request $request){
        $request->validate([
            'slug'      =>  'required',
            'quant'     =>  'required',
        ]);

        $product = Product::where('slug', $request->slug)->first();
        if($product->stock < $request->quant[1]){
            return back()->with('error','Out of stock, You can add other products.');
        }
        if (($request->quant[1] < 1) || empty($product)) {
            request()->session()->flash('error','Invalid Products');
            return back();
        }    

        $price = ($product->price-($product->price*$product->discount)/100);
        $qty = $request->quant[1];

        if(Auth::check()) {
            $already_cart = Cart::where('user_id', auth()->user()->id)->where('order_id',null)->where('product_id', $product->id)->first();
            if($already_cart) {
                $already_cart->quantity = $already_cart->quantity + $qty;
                $already_cart->amount = ($price * $qty) + $already_cart->amount;
                if ($already_cart->product->stock < $already_cart->quantity || $already_cart->product->stock <= 0) return back()->with('error','Stock not sufficient!.');
                $already_cart->save();
            } else {
                $cart = new Cart;
                $cart->user_id = auth()->user()->id;
                $cart->product_id = $product->id;
                $cart->price = $price;
                $cart->quantity = $qty;
                $cart->amount = ($price * $qty);
                if ($cart->product->stock < $cart->quantity || $cart->product->stock <= 0) return back()->with('error','Stock not sufficient!.');
                $cart->save();
            }
        } else {
            $cart = session()->get('cart', []);
            if(isset($cart[$product->id])) {
                $cart[$product->id]['quantity'] += $qty;
                $cart[$product->id]['amount'] += ($price * $qty);
                if ($product->stock < $cart[$product->id]['quantity'] || $product->stock <= 0) return back()->with('error','Stock not sufficient!.');
            } else {
                if ($product->stock < $qty || $product->stock <= 0) return back()->with('error','Stock not sufficient!.');
                $cart[$product->id] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $price,
                    'amount' => ($price * $qty),
                ];
            }
            session()->put('cart', $cart);
        }

        request()->session()->flash('success','Product successfully added to cart.');
        return back();       
    } 
    
    public function cartDelete(Request $request){
        if(Auth::check()) {
            $cart = Cart::find($request->id);
            if ($cart && $cart->user_id == auth()->user()->id) {
                $cart->delete();
                request()->session()->flash('success','Cart successfully removed');
                return back();  
            }
        } else {
            $cart = session()->get('cart', []);
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
                request()->session()->flash('success','Cart successfully removed');
                return back();
            }
        }
        request()->session()->flash('error','Error please try again');
        return back();       
    }     

    public function cartClear(){
        if(Auth::check()) {
            Cart::where('user_id', auth()->user()->id)->where('order_id',null)->delete();
        } else {
            session()->forget('cart');
        }
        request()->session()->flash('success','Cart successfully cleared');
        return back(); 
    }

    public function cartUpdate(Request $request){
        if($request->quant){
            $error = array();
            $success = '';
            
            if(Auth::check()) {
                foreach ($request->quant as $k=>$quant) {
                    $id = $request->qty_id[$k];
                    $cart = Cart::find($id);
                    if($quant > 0 && $cart && $cart->user_id == auth()->user()->id) {
                        if($cart->product->stock < $quant){
                            request()->session()->flash('error','Out of stock');
                            return back();
                        }
                        $cart->quantity = ($cart->product->stock > $quant) ? $quant  : $cart->product->stock;
                        if ($cart->product->stock <=0) continue;
                        $cart->amount = $cart->price * $cart->quantity;
                        $cart->save();
                        $success = 'Cart successfully updated!';
                    } else {
                        $error[] = 'Cart Invalid!';
                    }
                }
            } else {
                $cart = session()->get('cart', []);
                foreach ($request->quant as $k=>$quant) {
                    $id = $request->qty_id[$k]; // for session carts, qty_id is the product_id
                    if(isset($cart[$id]) && $quant > 0) {
                        $product = Product::find($id);
                        if($product && $product->stock < $quant){
                            request()->session()->flash('error','Out of stock');
                            return back();
                        }
                        $cart[$id]['quantity'] = ($product->stock > $quant) ? $quant : $product->stock;
                        $cart[$id]['amount'] = $cart[$id]['price'] * $cart[$id]['quantity'];
                        $success = 'Cart successfully updated!';
                    } else {
                        $error[] = 'Cart Invalid!';
                    }
                }
                session()->put('cart', $cart);
            }
            return back()->with($error)->with('success', $success);
        } else {
            return back()->with('error', 'Cart Invalid!');
        }    
    }

    public function checkout(Request $request){
        return view('frontend.pages.checkout');
    }

    public function ajaxAddToCart(Request $request) {
        if (empty($request->slug)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid Products']);
        }        
        $product = Product::where('slug', $request->slug)->first();
        if (empty($product)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid Products']);
        }

        $price = ($product->price-($product->price*$product->discount)/100);

        if(Auth::check()) {
            $already_cart = Cart::where('user_id', auth()->user()->id)->where('order_id',null)->where('product_id', $product->id)->first();
            if($already_cart) {
                $already_cart->quantity = $already_cart->quantity + 1;
                $already_cart->amount = $price + $already_cart->amount;
                if ($already_cart->product->stock < $already_cart->quantity || $already_cart->product->stock <= 0) {
                    return response()->json(['status' => 'error', 'message' => 'Stock not sufficient!']);
                }
                $already_cart->save();
            } else {
                $cart = new Cart;
                $cart->user_id = auth()->user()->id;
                $cart->product_id = $product->id;
                $cart->price = $price;
                $cart->quantity = 1;
                $cart->amount = $price;
                if ($cart->product->stock < $cart->quantity || $cart->product->stock <= 0) {
                    return response()->json(['status' => 'error', 'message' => 'Stock not sufficient!']);
                }
                $cart->save();
                Wishlist::where('user_id',auth()->user()->id)->where('cart_id',null)->update(['cart_id'=>$cart->id]);
            }
        } else {
            $cart = session()->get('cart', []);
            if(isset($cart[$product->id])) {
                $cart[$product->id]['quantity']++;
                $cart[$product->id]['amount'] += $price;
                if ($product->stock < $cart[$product->id]['quantity'] || $product->stock <= 0) {
                    return response()->json(['status' => 'error', 'message' => 'Stock not sufficient!']);
                }
            } else {
                if ($product->stock < 1 || $product->stock <= 0) {
                    return response()->json(['status' => 'error', 'message' => 'Stock not sufficient!']);
                }
                $cart[$product->id] = [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => $price,
                    'amount' => $price,
                ];
            }
            session()->put('cart', $cart);
        }
        
        return response()->json(['status' => 'success', 'message' => 'Product added to cart', 'cart_count' => Helper::cartCount()]);
    }

    public function ajaxGetCart() {
        $carts = Helper::getAllProductFromCart();
        $total_amount = Helper::totalCartPrice();
        
        $html = '';
        foreach($carts as $cart) {
            $photo = explode(',', $cart->product->photo)[0];
            
            if(Auth::check()) {
                $priceText = '$'.number_format($cart->price, 2);
            } else {
                $priceText = '<span style="color:#f59e0b;font-size:11px;">Price hidden (Login to view)</span>';
            }

            $html .= '
            <div class="d-flex align-items-center mb-3 p-2 border rounded" style="background:#fff;">
                <img src="'.$photo.'" alt="'.$cart->product->title.'" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                <div class="ml-3 flex-grow-1">
                    <h6 class="mb-0" style="font-size:13px; font-weight:700;">'.$cart->product->title.'</h6>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <span class="text-muted" style="font-size:12px;">'.$cart->quantity.' x '.$priceText.'</span>
                        <a href="'.route('cart-delete', $cart->id).'" class="text-danger" style="font-size:12px;"><i class="fa fa-trash"></i></a>
                    </div>
                </div>
            </div>';
        }
        
        if(count($carts) == 0) {
            $html = '<div class="text-center py-4 text-muted"><i class="fa fa-shopping-cart fa-3x mb-3"></i><p>Your cart is empty.</p></div>';
        }

        return response()->json([
            'status' => 'success',
            'html' => $html,
            'total' => Auth::check() ? number_format($total_amount, 2) : '---',
            'count' => Helper::cartCount()
        ]);
    }
}
