<?php

namespace App\Http\Controllers;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Category;
use App\Models\PostTag;
use App\Models\PostCategory;
use App\Models\Post;
use App\Models\Cart;
use App\Models\Brand;
use App\User;
use Auth;
use Session;
use Newsletter;
use DB;
use Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StatusNotification;
class FrontendController extends Controller
{
   
    public function index(Request $request){
        $role = $request->user()->role;
        if (in_array($role, ['admin', 'staff', 'manager'])) {
            return redirect()->route('admin');
        }
        return redirect()->route($role);
    }

    public function home(){
        $featured=Product::where('status','active')->where('is_featured',1)->orderBy('price','DESC')->limit(2)->get();
        $posts=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        $banners=Banner::where('status','active')->limit(3)->orderBy('id','DESC')->get();
        // return $banner;
        $products=Product::where('status','active')->orderBy('id','DESC')->limit(8)->get();
        $category=Category::where('status','active')->where('is_parent',1)->orderBy('title','ASC')->get();
        // return $category;
        return view('frontend.index')
                ->with('featured',$featured)
                ->with('posts',$posts)
                ->with('banners',$banners)
                ->with('product_lists',$products)
                ->with('category_lists',$category);
    }   

    public function getCategoryProductsAjax($id){
        $category = Category::findOrFail($id);
        
        $products = Product::where('status','active')
            ->where(function($q) use ($id) {
                $q->where('cat_id', $id)
                  ->orWhere('sub_cat_id', $id);
            })
            ->orderBy('id','DESC')
            ->limit(12)
            ->get();
            
        $formatted = $products->map(function($product) {
            $photos = explode(',', $product->photo);
            $photoUrl = (count($photos) > 0 && !empty($photos[0])) ? $photos[0] : 'https://via.placeholder.com/400x300';
            
            $priceHtml = '';
            $after_discount = ($product->price - ($product->price * $product->discount) / 100);
            
            if (Auth::check()) {
                if ($product->discount > 0) {
                    $priceHtml = '<span class="price-amount" style="color: #f59e0b; font-weight: 700; font-size: 1.1rem;">$' . number_format($after_discount, 2) . '</span>' .
                                 '<del class="price-del text-muted ml-2" style="font-size: 0.9rem;">$' . number_format($product->price, 2) . '</del>';
                } else {
                    $priceHtml = '<span class="price-amount text-white" style="font-weight: 700; font-size: 1.1rem;">$' . number_format($product->price, 2) . '</span>';
                }
            } else {
                $priceHtml = '<span class="price-login"><a href="' . route('login') . '" style="color: #f59e0b; font-weight: 600; text-decoration: underline;">Login to see price</a></span>';
            }
            
            return [
                'id' => $product->id,
                'title' => $product->title,
                'slug' => $product->slug,
                'photo' => $photoUrl,
                'price_html' => $priceHtml,
                'discount' => $product->discount,
                'stock' => $product->stock,
                'condition' => $product->condition,
                'detail_url' => route('product-detail', $product->slug),
                'add_to_cart_url' => route('add-to-cart', $product->slug)
            ];
        });
        
        return response()->json([
            'status' => 'success',
            'category' => $category->title,
            'products' => $formatted
        ]);
    }

    public function aboutUs(){
        return view('frontend.pages.about-us');
    }

    public function contact(){
        return view('frontend.pages.contact');
    }

    public function productDetail($slug){
        $product_detail= Product::getProductBySlug($slug);
        // dd($product_detail);
        return view('frontend.pages.product_detail')->with('product_detail',$product_detail);
    }

    public function productGrids(){
        $products=Product::query();
        
        if(!empty($_GET['category'])){
            $slug=explode(',',$_GET['category']);
            // dd($slug);
            $cat_ids=Category::select('id')->whereIn('slug',$slug)->pluck('id')->toArray();
            // dd($cat_ids);
            $products->whereIn('cat_id',$cat_ids);
            // return $products;
        }
        if(!empty($_GET['model'])){
            $models=explode(',',$_GET['model']);
            $products->whereIn('model',$models);
        }
        if(!empty($_GET['brand'])){
            $slugs=explode(',',$_GET['brand']);
            $brand_ids=Brand::select('id')->whereIn('slug',$slugs)->pluck('id')->toArray();
            return $brand_ids;
            $products->whereIn('brand_id',$brand_ids);
        }
        if(!empty($_GET['sortBy'])){
            if($_GET['sortBy']=='title'){
                $products=$products->where('status','active')->orderBy('title','ASC');
            }
            if($_GET['sortBy']=='price'){
                $products=$products->orderBy('price','ASC');
            }
        }

        if(!empty($_GET['price'])){
            $price=explode('-',$_GET['price']);
            // return $price;
            // if(isset($price[0]) && is_numeric($price[0])) $price[0]=floor(Helper::base_amount($price[0]));
            // if(isset($price[1]) && is_numeric($price[1])) $price[1]=ceil(Helper::base_amount($price[1]));
            
            $products->whereBetween('price',$price);
        }

        $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        // Sort by number
        if(!empty($_GET['show'])){
            $products=$products->where('status','active')->paginate($_GET['show']);
        }
        else{
            $products=$products->where('status','active')->paginate(5000);
        }
        // Sort by name , price, category

      
        return view('frontend.pages.product-grids')->with('products',$products)->with('recent_products',$recent_products);
    }
    public function productLists(){
        $products=Product::query();
        
        if(!empty($_GET['category'])){
            $slug=explode(',',$_GET['category']);
            // dd($slug);
            $cat_ids=Category::select('id')->whereIn('slug',$slug)->pluck('id')->toArray();
            // dd($cat_ids);
            $products->whereIn('cat_id',$cat_ids)->paginate;
            // return $products;
        }
        if(!empty($_GET['model'])){
            $models=explode(',',$_GET['model']);
            $products->whereIn('model',$models);
        }
        if(!empty($_GET['brand'])){
            $slugs=explode(',',$_GET['brand']);
            $brand_ids=Brand::select('id')->whereIn('slug',$slugs)->pluck('id')->toArray();
            return $brand_ids;
            $products->whereIn('brand_id',$brand_ids);
        }
        if(!empty($_GET['sortBy'])){
            if($_GET['sortBy']=='title'){
                $products=$products->where('status','active')->orderBy('title','ASC');
            }
            if($_GET['sortBy']=='price'){
                $products=$products->orderBy('price','ASC');
            }
        }

        if(!empty($_GET['price'])){
            $price=explode('-',$_GET['price']);
            // return $price;
            // if(isset($price[0]) && is_numeric($price[0])) $price[0]=floor(Helper::base_amount($price[0]));
            // if(isset($price[1]) && is_numeric($price[1])) $price[1]=ceil(Helper::base_amount($price[1]));
            
            $products->whereBetween('price',$price);
        }

        $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        // Sort by number
        if(!empty($_GET['show'])){
            $products=$products->where('status','active')->paginate($_GET['show']);
        }
        else{
            $products=$products->where('status','active')->paginate(5000);
        }
        // Sort by name , price, category

      
        return view('frontend.pages.product-lists')->with('products',$products)->with('recent_products',$recent_products);
    }
    public function productFilter(Request $request){
            $data= $request->all();
            // return $data;
            $showURL="";
            if(!empty($data['show'])){
                $showURL .='&show='.$data['show'];
            }

            $sortByURL='';
            if(!empty($data['sortBy'])){
                $sortByURL .='&sortBy='.$data['sortBy'];
            }

            $catURL="";
            if(!empty($data['category'])){
                foreach($data['category'] as $category){
                    if(empty($catURL)){
                        $catURL .='&category='.$category;
                    }
                    else{
                        $catURL .=','.$category;
                    }
                }
            }

            $brandURL="";
            if(!empty($data['brand'])){
                foreach($data['brand'] as $brand){
                    if(empty($brandURL)){
                        $brandURL .='&brand='.$brand;
                    }
                    else{
                        $brandURL .=','.$brand;
                    }
                }
            }

            $modelURL="";
            if(!empty($data['model'])){
                // Handle both array and string cases for model parameter
                $models = is_array($data['model']) ? $data['model'] : [$data['model']];
                foreach($models as $model){
                    if(empty($modelURL)){
                        $modelURL .='&model='.$model;
                    }
                    else{
                        $modelURL .=','.$model;
                    }
                }
            }
            // return $brandURL;

            $priceRangeURL="";
            if(!empty($data['price_range'])){
                $priceRangeURL .='&price='.$data['price_range'];
            }
            if(request()->is('e-shop.loc/product-grids')){
                return redirect()->route('product-grids',$catURL.$brandURL.$modelURL.$priceRangeURL.$showURL.$sortByURL);
            }
            else{
                return redirect()->route('product-lists',$catURL.$brandURL.$modelURL.$priceRangeURL.$showURL.$sortByURL);
            }
    }
    public function productSearch(Request $request){
        $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        $products=Product::orwhere('title','like','%'.$request->search.'%')
                    ->orwhere('slug','like','%'.$request->search.'%')
                    ->orwhere('description','like','%'.$request->search.'%')
                    ->orwhere('summary','like','%'.$request->search.'%')
                    ->orwhere('price','like','%'.$request->search.'%')
                    ->orderBy('id','DESC')
                    ->paginate('9');
        return view('frontend.pages.product-grids')->with('products',$products)->with('recent_products',$recent_products);
    }

    public function ajaxSearch(Request $request){
        $search = $request->input('search', '');
        
        if (strlen($search) < 2) {
            return response()->json(['status' => 'success', 'html' => '', 'count' => 0]);
        }

        $products = Product::where('status', 'active')
            ->where(function($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                  ->orWhere('slug', 'like', '%'.$search.'%')
                  ->orWhere('sku', 'like', '%'.$search.'%')
                  ->orWhere('summary', 'like', '%'.$search.'%');
            })
            ->orderBy('id', 'DESC')
            ->limit(24)
            ->get();

        $html = '';
        foreach ($products as $product) {
            $photos = explode(',', $product->photo);
            $photo = $photos[0] ?? '';
            $after_discount = ($product->price - ($product->price * $product->discount) / 100);
            
            if (Auth::check()) {
                $priceHtml = '<span style="color: var(--primary); font-weight: 800; font-size: 1.1rem;">Rs. ' . number_format($after_discount, 2) . '</span>';
                $addBtn = '<a href="' . route('add-to-cart', $product->slug) . '" class="ajax-add-to-cart-btn btn btn-sm" data-slug="' . $product->slug . '" style="background: var(--primary); color: #fff; border: none; padding: 6px 14px; border-radius: 4px; font-size: 11px; font-weight: 700; cursor: pointer; text-transform: uppercase;"><i class="fa fa-cart-plus mr-1"></i> ADD</a>';
            } else {
                $priceHtml = '<a href="' . route('login') . '" style="color: var(--primary); font-weight: 600; font-size: 12px; text-decoration: underline;">Login to view price</a>';
                $addBtn = '<a href="' . route('login') . '" class="btn btn-sm" style="background: var(--bg-soft); color: var(--text-muted); border: 1px dashed #ccc; padding: 6px 14px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase;"><i class="fa fa-lock mr-1"></i> Login</a>';
            }

            $html .= '
            <div class="col-lg-3 col-md-4 col-6 mb-4">
                <div class="ajax-product-card" style="background: #fff; border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; transition: all 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <a href="' . route('product-detail', $product->slug) . '" style="display:block; overflow:hidden; height: 180px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                        <img src="' . $photo . '" alt="' . htmlspecialchars($product->title) . '" style="max-width:100%; max-height:100%; object-fit: contain; transition: transform 0.3s;" onmouseover="this.style.transform=\'scale(1.05)\'" onmouseout="this.style.transform=\'scale(1)\'">
                    </a>
                    <div style="padding: 14px;">
                        <a href="' . route('product-detail', $product->slug) . '" style="text-decoration: none;">
                            <h6 style="color: var(--primary); font-weight: 700; font-size: 13px; margin-bottom: 8px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="' . htmlspecialchars($product->title) . '">' . htmlspecialchars($product->title) . '</h6>
                        </a>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px; padding-top: 10px; border-top: 1px solid var(--border-color);">
                            ' . $priceHtml . '
                            ' . $addBtn . '
                        </div>
                        <div style="margin-top: 8px;">
                            <a href="' . route('product-detail', $product->slug) . '" style="color: var(--text-muted); font-size: 11px; text-decoration: none; font-weight: 600;"><i class="fa fa-eye mr-1"></i> View Details</a>
                        </div>
                    </div>
                </div>
            </div>';
        }

        return response()->json([
            'status' => 'success',
            'html' => $html,
            'count' => count($products)
        ]);
    }

    public function shopByVehicleBrand(Request $request) {
        $brand = $request->input('vehicle_brand');
        if (empty($brand)) {
            return redirect()->route('home');
        }

        $query = Product::where('status', 'active')->where('model', 'LIKE', '%' . $brand . '%');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                  ->orWhere('slug', 'like', '%'.$search.'%')
                  ->orWhere('description', 'like', '%'.$search.'%')
                  ->orWhere('summary', 'like', '%'.$search.'%')
                  ->orWhere('sku', 'like', '%'.$search.'%');
            });
        }

        $products = $query->orderBy('id', 'DESC')->paginate(20);
        $recent_products = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

        return view('frontend.pages.product-grids')->with('products', $products)->with('recent_products', $recent_products)->with('vehicle_brand', $brand);
    }

    public function productBrand(Request $request){
        $products=Brand::getProductByBrand($request->slug);
        $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        if(request()->is('e-shop.loc/product-grids')){
            return view('frontend.pages.product-grids')->with('products',$products->products)->with('recent_products',$recent_products);
        }
        else{
            return view('frontend.pages.product-lists')->with('products',$products->products)->with('recent_products',$recent_products);
        }

    }
    public function productCat(Request $request){
        $products=Category::getProductByCat($request->slug);
        // return $request->slug;
        $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(3)->get();

        if(request()->is('e-shop.loc/product-grids')){
            return view('frontend.pages.product-grids')->with('products',$products->products)->with('recent_products',$recent_products);
        }
        else{
            return view('frontend.pages.product-lists')->with('products',$products->products)->with('recent_products',$recent_products);
        }

    }
    public function productSubCat(Request $request){
        $products=Category::getProductBySubCat($request->sub_slug);
        // return $products;
        $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(3)->get();

        if(request()->is('e-shop.loc/product-grids')){
            return view('frontend.pages.product-grids')->with('products',$products->sub_products)->with('recent_products',$recent_products);
        }
        else{
            return view('frontend.pages.product-lists')->with('products',$products->sub_products)->with('recent_products',$recent_products);
        }

    }

    public function blog(){
        $post=Post::query();
        
        if(!empty($_GET['category'])){
            $slug=explode(',',$_GET['category']);
            // dd($slug);
            $cat_ids=PostCategory::select('id')->whereIn('slug',$slug)->pluck('id')->toArray();
            return $cat_ids;
            $post->whereIn('post_cat_id',$cat_ids);
            // return $post;
        }
        if(!empty($_GET['tag'])){
            $slug=explode(',',$_GET['tag']);
            // dd($slug);
            $tag_ids=PostTag::select('id')->whereIn('slug',$slug)->pluck('id')->toArray();
            // return $tag_ids;
            $post->where('post_tag_id',$tag_ids);
            // return $post;
        }

        if(!empty($_GET['show'])){
            $post=$post->where('status','active')->orderBy('id','DESC')->paginate($_GET['show']);
        }
        else{
            $post=$post->where('status','active')->orderBy('id','DESC')->paginate(5000);
        }
        // $post=Post::where('status','active')->paginate(5000);
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        return view('frontend.pages.blog')->with('posts',$post)->with('recent_posts',$rcnt_post);
    }

    public function blogDetail($slug){
        $post=Post::getPostBySlug($slug);
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        // return $post;
        return view('frontend.pages.blog-detail')->with('post',$post)->with('recent_posts',$rcnt_post);
    }

    public function blogSearch(Request $request){
        // return $request->all();
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        $posts=Post::orwhere('title','like','%'.$request->search.'%')
            ->orwhere('quote','like','%'.$request->search.'%')
            ->orwhere('summary','like','%'.$request->search.'%')
            ->orwhere('description','like','%'.$request->search.'%')
            ->orwhere('slug','like','%'.$request->search.'%')
            ->orderBy('id','DESC')
            ->paginate(5000);
        return view('frontend.pages.blog')->with('posts',$posts)->with('recent_posts',$rcnt_post);
    }

    public function blogFilter(Request $request){
        $data=$request->all();
        // return $data;
        $catURL="";
        if(!empty($data['category'])){
            foreach($data['category'] as $category){
                if(empty($catURL)){
                    $catURL .='&category='.$category;
                }
                else{
                    $catURL .=','.$category;
                }
            }
        }

        $tagURL="";
        if(!empty($data['tag'])){
            foreach($data['tag'] as $tag){
                if(empty($tagURL)){
                    $tagURL .='&tag='.$tag;
                }
                else{
                    $tagURL .=','.$tag;
                }
            }
        }
        // return $tagURL;
            // return $catURL;
        return redirect()->route('blog',$catURL.$tagURL);
    }

    public function blogByCategory(Request $request){
        $post=PostCategory::getBlogByCategory($request->slug);
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        return view('frontend.pages.blog')->with('posts',$post->post)->with('recent_posts',$rcnt_post);
    }

    public function blogByTag(Request $request){
        // dd($request->slug);
        $post=Post::getBlogByTag($request->slug);
        // return $post;
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        return view('frontend.pages.blog')->with('posts',$post)->with('recent_posts',$rcnt_post);
    }

    // Login
    public function login(){
        return view('frontend.pages.login');
    }
    public function loginSubmit(Request $request){
        $data= $request->all();
        
        // Determine if input is email or phone
        $field = filter_var($data['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        // Check if user exists and is pending
        $pendingUser = User::where($field, $data['email'])->where('status', 'pending')->first();
        if ($pendingUser) {
            request()->session()->flash('error', 'Your registration request is still pending approval. You will be notified via WhatsApp once approved.');
            return redirect()->back();
        }

        if(Auth::attempt([$field => $data['email'], 'password' => $data['password'], 'status' => 'active'])){
            Session::put('user', $data['email']);

            // Merge guest session cart into DB cart
            $sessionCart = session()->get('cart', []);
            if (!empty($sessionCart)) {
                foreach ($sessionCart as $item) {
                    $already_cart = Cart::where('user_id', auth()->user()->id)->where('order_id', null)->where('product_id', $item['product_id'])->first();
                    if($already_cart) {
                        $already_cart->quantity += $item['quantity'];
                        $already_cart->amount += $item['amount'];
                        $already_cart->save();
                    } else {
                        $cart = new Cart;
                        $cart->user_id = auth()->user()->id;
                        $cart->product_id = $item['product_id'];
                        $cart->price = $item['price'];
                        $cart->quantity = $item['quantity'];
                        $cart->amount = $item['amount'];
                        $cart->save();
                    }
                }
                session()->forget('cart');
            }

            request()->session()->flash('success','Successfully login');
            return redirect()->route('home');
        }
        else{
            request()->session()->flash('error','Invalid credentials or inactive account, please try again!');
            return redirect()->back();
        }
    }

    public function logout(){
        Session::forget('user');
        Auth::logout();
        request()->session()->flash('success','Logout successfully');
        return back();
    }

    public function register(){
        return view('auth.register');
    }
    public function registerSubmit(Request $request){
        $this->validate($request,[
            'name'     => 'string|required|min:2',
            'phone'    => 'required|string|min:10|unique:users,phone',
            'email'    => 'nullable|string|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);
        $data = $request->all();

        // If no email provided, auto-generate one from phone
        $email = !empty($data['email']) ? $data['email'] : ($data['phone'] . '@pending.local');

        $user = User::create([
            'name'     => $data['name'],
            'phone'    => $data['phone'],
            'email'    => $email,
            'password' => Hash::make($data['password']),
            'status'   => 'pending',  // Awaiting admin approval
        ]);

        if($user){
            // Notify Admins
            try {
                $admins = User::where('role', 'admin')->get();
                $details = [
                    'title' => 'New user registered: ' . $user->name . ' - PENDING APPROVAL',
                    'actionURL' => route('users.pending'),
                    'fas' => 'fa-user-clock'
                ];
                Notification::send($admins, new StatusNotification($details));
            } catch (\Exception $e) {
                \Log::error('Failed to notify admins of registration: ' . $e->getMessage());
            }

            // Send WhatsApp notification to user's phone
            try {
                $whatsapp = new WhatsAppService();
                $msg = "Assalam-o-Alaikum " . strtoupper($data['name']) . ",\n\n" .
                       "Thank you for registering with Dr Auto Store.\n\n" .
                       "Your registration request has been received and is currently PENDING APPROVAL by our admin team.\n\n" .
                       "You will receive a WhatsApp message on this number once your account is approved and ready to use.\n\n" .
                       "Regards,\nDr Auto Store Team";
                $whatsapp->sendMessage($data['phone'], $msg);
            } catch (\Exception $e) {
                // Log error silently, don't block registration
                \Log::warning('WhatsApp registration notification failed: ' . $e->getMessage());
            }

            return redirect()->route('register.form')->with('pending_approval', true);
        }
        else{
            request()->session()->flash('error','Please try again!');
            return back();
        }
    }
    public function create(array $data){
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'status'   => 'active',
        ]);
    }
    // Reset password
    public function showResetForm(){
        return view('auth.passwords.old-reset');
    }

    public function subscribe(Request $request){
        if(! Newsletter::isSubscribed($request->email)){
                Newsletter::subscribePending($request->email);
                if(Newsletter::lastActionSucceeded()){
                    request()->session()->flash('success','Subscribed! Please check your email');
                    return redirect()->route('home');
                }
                else{
                    Newsletter::getLastError();
                    return back()->with('error','Something went wrong! please try again');
                }
            }
            else{
                request()->session()->flash('error','Already Subscribed');
                return back();
            }
    }
    
}
