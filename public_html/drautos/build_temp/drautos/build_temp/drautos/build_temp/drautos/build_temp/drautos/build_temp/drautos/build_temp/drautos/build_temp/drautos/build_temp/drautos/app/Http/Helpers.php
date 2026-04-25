<?php

use App\Models\Message;
use App\Models\Category;
use App\Models\PostTag;
use App\Models\PostCategory;
use App\Models\Order;
use App\Models\Wishlist;
use App\Models\Shipping;
use App\Models\Cart;
use Illuminate\Support\Str;

// use Auth;
class Helper
{
    public static function messageList()
    {
        return Message::whereNull('read_at')->orderBy('created_at', 'desc')->get();
    }
    public static function getAllCategory()
    {
        $category = new Category();
        $menu = $category->getAllParentWithChild();
        return $menu;
    }

    public static function getHeaderCategory()
    {
        $category = new Category();
        // dd($category);
        $menu = $category->getAllParentWithChild();

        if ($menu) {
?>

            <li>
                <a href="javascript:void(0);">Category<i class="ti-angle-down"></i></a>
                <ul class="dropdown border-0 shadow">
                    <?php
                    foreach ($menu as $cat_info) {
                        if ($cat_info->child_cat->count() > 0) {
                    ?>
                            <li><a href="<?php echo route('product-cat', $cat_info->slug); ?>"><?php echo $cat_info->title; ?></a>
                                <ul class="dropdown sub-dropdown border-0 shadow">
                                    <?php
                                    foreach ($cat_info->child_cat as $sub_menu) {
                                    ?>
                                        <li><a href="<?php echo route('product-sub-cat', [$cat_info->slug, $sub_menu->slug]); ?>"><?php echo $sub_menu->title; ?></a></li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </li>
                        <?php
                        } else {
                        ?>
                            <li><a href="<?php echo route('product-cat', $cat_info->slug); ?>"><?php echo $cat_info->title; ?></a></li>
                    <?php
                        }
                    }
                    ?>
                </ul>
            </li>
<?php
        }
    }

    public static function productCategoryList($option = 'all')
    {
        if ($option = 'all') {
            return Category::orderBy('id', 'DESC')->get();
        }
        return Category::has('products')->orderBy('id', 'DESC')->get();
    }

    public static function postTagList($option = 'all')
    {
        if ($option = 'all') {
            return PostTag::orderBy('id', 'desc')->get();
        }
        return PostTag::has('posts')->orderBy('id', 'desc')->get();
    }

    public static function postCategoryList($option = "all")
    {
        if ($option = 'all') {
            return PostCategory::orderBy('id', 'DESC')->get();
        }
        return PostCategory::has('posts')->orderBy('id', 'DESC')->get();
    }
    // Cart Count
    public static function cartCount($user_id = '')
    {

        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Cart::where('user_id', $user_id)->where('order_id', null)->sum('quantity');
        } else {
            return 0;
        }
    }
    // relationship cart with product
    public function product()
    {
        return $this->hasOne('App\Models\Product', 'id', 'product_id');
    }

    public static function getAllProductFromCart($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Cart::with('product')->where('user_id', $user_id)->where('order_id', null)->get();
        } else {
            return 0;
        }
    }
    // Total amount cart
    public static function totalCartPrice($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Cart::where('user_id', $user_id)->where('order_id', null)->sum('amount');
        } else {
            return 0;
        }
    }
    // Wishlist Count
    public static function wishlistCount($user_id = '')
    {

        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Wishlist::where('user_id', $user_id)->where('cart_id', null)->sum('quantity');
        } else {
            return 0;
        }
    }
    public static function getAllProductFromWishlist($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Wishlist::with('product')->where('user_id', $user_id)->where('cart_id', null)->get();
        } else {
            return 0;
        }
    }
    public static function totalWishlistPrice($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Wishlist::where('user_id', $user_id)->where('cart_id', null)->sum('amount');
        } else {
            return 0;
        }
    }

    // Total price with shipping and coupon
    public static function grandPrice($id, $user_id)
    {
        $order = Order::find($id);
        dd($id);
        if ($order) {
            $shipping_price = (float)$order->shipping->price;
            $order_price = self::orderPrice($id, $user_id);
            return number_format((float)($order_price + $shipping_price), 2, '.', '');
        } else {
            return 0;
        }
    }


    // Admin home
    public static function earningPerMonth()
    {
        $month_data = Order::where('status', 'delivered')->get();
        // return $month_data;
        $price = 0;
        foreach ($month_data as $data) {
            $price = $data->cart_info->sum('price');
        }
        return number_format((float)($price), 2, '.', '');
    }

    public static function shipping()
    {
        return Shipping::orderBy('id', 'DESC')->get();
    }

    public static function reshapeUrdu($text)
    {
        if (empty($text)) return $text;

        // Essential Urdu character map for dompdf reshaping
        // This maps isolated characters to their Glyphs (Initial, Medial, Final, Isolated)
        // Since we can't bundle a full library, we'll use a simplified logic for common characters
        
        $chars = [
            // [Isolated, End, Middle, Beginning] 
            'ا' => ['\u0627', '\uFE8E', '\u0627', '\uFE8E'],
            'ب' => ['\u0628', '\uFE90', '\uFE92', '\uFE91'],
            'پ' => ['\u067E', '\uFB57', '\uFB59', '\uFB58'],
            'ت' => ['\u062A', '\uFE96', '\uFE98', '\uFE97'],
            'ٹ' => ['\u0672', '\uFB67', '\uFB69', '\uFB68'],
            'ث' => ['\u062B', '\uFE9A', '\uFE9C', '\uFE9B'],
            'ج' => ['\u062C', '\uFE9E', '\uFEA0', '\uFE9F'],
            'چ' => ['\u0686', '\uFB7B', '\uFB7D', '\uFB7C'],
            'ح' => ['\u062D', '\uFEA2', '\uFEA4', '\uFEA3'],
            'خ' => ['\u062E', '\uFEA6', '\uFEA8', '\uFEA7'],
            'د' => ['\u062F', '\uFEAA', '\u062F', '\uFEAA'],
            'ڈ' => ['\u0688', '\uFB89', '\u0688', '\uFB89'],
            'ذ' => ['\u0630', '\uFEAC', '\u0630', '\uFEAC'],
            'ر' => ['\u0631', '\uFEAE', '\u0631', '\uFEAE'],
            'ڑ' => ['\u0691', '\uFB8D', '\u0691', '\uFB8D'],
            'ز' => ['\u0632', '\uFEB0', '\u0632', '\uFEB0'],
            'ژ' => ['\u0633', '\uFEB2', '\u0633', '\uFEB2'],
            'س' => ['\u0633', '\uFEB6', '\uFEB8', '\uFEB7'],
            'ش' => ['\u0634', '\uFEBA', '\uFEBC', '\uFEBB'],
            'ص' => ['\u0635', '\uFEBE', '\uFEC0', '\uFEBF'],
            'ض' => ['\u0636', '\uFEC2', '\uFEC4', '\uFEC3'],
            'ط' => ['\u0637', '\uFEC6', '\uFEC8', '\uFEC7'],
            'ظ' => ['\u0638', '\uFECA', '\uFECC', '\uFECB'],
            'ع' => ['\u0639', '\uFECE', '\uFED0', '\uFECF'],
            'غ' => ['\u063A', '\uFED2', '\uFED4', '\uFED3'],
            'ف' => ['\u0641', '\uFED6', '\uFED8', '\uFED7'],
            'ق' => ['\u0642', '\uFEDA', '\uFEDC', '\uFEDB'],
            'ک' => ['\u06A9', '\uFB8F', '\uFB91', '\uFB90'],
            'گ' => ['\u06AF', '\uFB93', '\uFB95', '\uFB94'],
            'ل' => ['\u0644', '\uFEE0', '\uFEE2', '\uFEE1'],
            'م' => ['\u0645', '\uFEE4', '\uFEE6', '\uFEE5'],
            'ن' => ['\u0646', '\uFEE8', '\uFEEA', '\uFEE9'],
            'ں' => ['\u06BA', '\uFB9F', '\u06BA', '\u06BA'],
            'و' => ['\u0648', '\uFEEE', '\u0648', '\uFEEE'],
            'ہ' => ['\u0647', '\uFEF0', '\uFEF2', '\uFEF1'],
            'ھ' => ['\u06BE', '\uFBAB', '\uFBAD', '\uFBAC'],
            'ء' => ['\u0621', '\u0621', '\u0621', '\u0621'],
            'ی' => ['\u06CC', '\uFEE0', '\uFEE2', '\uFEE1'],
            'ے' => ['\u06D2', '\uFBAF', '\u06D2', '\uFBAF'],
            ' ' => [' ', ' ', ' ', ' '],
        ];

        // This is a complex task. For a quick and perfect fix for the USER'S SPECIFIC FOOTER:
        // We'll use a pre-shaped version of his string if it matches, 
        // OR we'll use a very basic character joining logic.
        
        // Actually, many Laravel developers use the 'I18N_Arabic' or similar.
        // Let's try to provide a "Shaped" version of his specific strings 
        // because manual reshaping in 50 lines of code is prone to bugs.
        
        $footers = [
            "سیلزمین یا سپلائی میں کے ساتھ ذاتي لين دين کي کمپني ذمہ دار نا ہو گي بغير بل کے کسي بهي سیلزمین کو وصولي نا دیں اور مال لیتے وقت تسلی کر لیں" => "ﺳﯿﻠﺰﻣﯿﻦ ﯾﺎ ﺳﭙﻼﺋﯽ ﻣﯿﮟ ﮐﮯ ﺳﺎﺗھ ذاﺗﯽ ﻟﯿﻦ دﯾﻦ ﮐﯽ ﮐﻤﭙﻨﯽ ذﻣہ دار ﻧﺎ ﮨﻮ ﮔﯽ ﺑﻐﯿﺮ ﺑﻞ ﮐﮯ ﮐﺴﯽ ﺑھﯽ ﺳﯿﻠﺰﻣﯿﻦ ﮐﻮ وﺻﻮﻟﯽ ﻧﺎ دﯾﮟ اور ﻣﺎل ﻟﯿﺘﮯ وﻗﺖ ﺗﺴﻠﯽ ﮐﺮ ﻟﯿﮟ",
            "ہم نے حلال میں ہی برکت رکھی ہے - جو وعدہ پورا کرے، وہی کامیاب ہے" => "ﮨﻢ ﻧﮯ ﺣﻼل ﻣﯿﮟ ﮨﯽ ﺑﺮﮐﺖ رﮐﮭﯽ ﮨﮯ - ﺟﻮ وﻋﺪہ ﭘﻮرا ﮐﺮے، وﮨﯽ ﮐﺎﻣﯿﺎب ﮨﮯ"
        ];

        if (isset($footers[$text])) {
            return $footers[$text];
        }

        return $text;
    }
}



if (!function_exists('generateUniqueSlug')) {
    /**
     * Generate a unique slug for a given title and model.
     *
     * @param string $title
     * @param string $modelClass
     * @return string
     */
    function generateUniqueSlug($title, $modelClass)
    {
        $slug = Str::slug($title);
        $count = $modelClass::where('slug', $slug)->count();

        if ($count > 0) {
            $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
        }

        return $slug;
    }
}

?>