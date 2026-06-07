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
            $cart = session()->get('cart', []);
            return collect($cart)->sum('quantity');
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
            $sessionCart = session()->get('cart', []);
            $carts = collect();
            foreach ($sessionCart as $key => $item) {
                $cart = new Cart();
                $cart->id = $key; // fake id matches product_id for session carts
                $cart->product_id = $item['product_id'];
                $cart->quantity = $item['quantity'];
                $cart->price = $item['price'];
                $cart->amount = $item['amount'];
                // Load product relationship manually
                $product = \App\Models\Product::find($item['product_id']);
                $cart->setRelation('product', $product);
                $carts->push($cart);
            }
            return $carts;
        }
    }
    // Total amount cart
    public static function totalCartPrice($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Cart::where('user_id', $user_id)->where('order_id', null)->sum('amount');
        } else {
            $cart = session()->get('cart', []);
            return collect($cart)->sum('amount');
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

        $footers = [
            "سیلزمین یا سپلائی میں کے ساتھ ذاتي لين دين کي کمپني ذمہ دار نا ہو گي بغير بل کے کسي بهي سیلزمین کو وصولي نا دیں اور مال لیتے وقت تسلی کر لیں" => "ﺳﯿﻠﺰﻣﯿﻦ ﯾﺎ ﺳﭙﻼﺋﯽ ﻣﯿﮟ ﮐﮯ ﺳﺎﺗھ ذاﺗﯽ ﻟﯿﻦ دﯾﻦ ﮐﯽ ﮐﻤﭙﻨﯽ ذﻣہ دار ﻧﺎ ﮨﻮ ﮔﯽ ﺑﻐﯿﺮ ﺑﻞ ﮐﮯ ﮐﺴﯽ ﺑھﯽ ﺳﯿﻠﺰﻣﯿﻦ ﮐﻮ وﺻﻮﻟﯽ ﻧﺎ دﯾﮟ اور ﻣﺎل ﻟﯿﺘﮯ وﻗﺖ ﺗﺴﻠﯽ ﮐﺮ ﻟﯿﮟ",
            "ہم نے حلال میں ہی برکت رکھی ہے - جو وعدہ پورا کرے، وہی کامیاب ہے" => "ﮨﻢ ﻧﮯ ﺣﻼل ﻣﯿﮟ ﮨﯽ ﺑﺮﮐﺖ رﮐﮭﯽ ﮨﮯ - ﺟﻮ وﻋﺪہ ﭘﻮرا ﮐﺮے، وﮨﯽ ﮐﺎﻣﯿﺎب ﮨﮯ"
        ];
        if (isset($footers[$text])) {
            return $footers[$text];
        }

        $chars = [
            // Base => [Isolated, End, Middle, Beginning]
            'ا' => ["\u{0627}", "\u{FE8E}", "\u{FE8E}", "\u{0627}"],
            'آ' => ["\u{0622}", "\u{FE82}", "\u{FE82}", "\u{0622}"],
            'ب' => ["\u{0628}", "\u{FE90}", "\u{FE92}", "\u{FE91}"],
            'پ' => ["\u{067E}", "\u{FB57}", "\u{FB59}", "\u{FB58}"],
            'ت' => ["\u{062A}", "\u{FE96}", "\u{FE98}", "\u{FE97}"],
            'ٹ' => ["\u{0679}", "\u{FB67}", "\u{FB69}", "\u{FB68}"],
            'ث' => ["\u{062B}", "\u{FE9A}", "\u{FE9C}", "\u{FE9B}"],
            'ج' => ["\u{062C}", "\u{FE9E}", "\u{FEA0}", "\u{FE9F}"],
            'چ' => ["\u{0686}", "\u{FB7B}", "\u{FB7D}", "\u{FB7C}"],
            'ح' => ["\u{062D}", "\u{FEA2}", "\u{FEA4}", "\u{FEA3}"],
            'خ' => ["\u{062E}", "\u{FEA6}", "\u{FEA8}", "\u{FEA7}"],
            'د' => ["\u{062F}", "\u{FEAA}", "\u{FEAA}", "\u{062F}"],
            'ڈ' => ["\u{0688}", "\u{FB89}", "\u{FB89}", "\u{0688}"],
            'ذ' => ["\u{0630}", "\u{FEAC}", "\u{FEAC}", "\u{0630}"],
            'ر' => ["\u{0631}", "\u{FEAE}", "\u{FEAE}", "\u{0631}"],
            'ڑ' => ["\u{0691}", "\u{FB8D}", "\u{FB8D}", "\u{0691}"],
            'ز' => ["\u{0632}", "\u{FEB0}", "\u{FEB0}", "\u{0632}"],
            'ژ' => ["\u{0698}", "\u{FB8B}", "\u{FB8B}", "\u{0698}"],
            'س' => ["\u{0633}", "\u{FEB6}", "\u{FEB8}", "\u{FEB7}"],
            'ش' => ["\u{0634}", "\u{FEBA}", "\u{FEBC}", "\u{FEBB}"],
            'ص' => ["\u{0635}", "\u{FEBE}", "\u{FEC0}", "\u{FEBF}"],
            'ض' => ["\u{0636}", "\u{FEC2}", "\u{FEC4}", "\u{FEC3}"],
            'ط' => ["\u{0637}", "\u{FEC6}", "\u{FEC8}", "\u{FEC7}"],
            'ظ' => ["\u{0638}", "\u{FECA}", "\u{FECC}", "\u{FECB}"],
            'ع' => ["\u{0639}", "\u{FECE}", "\u{FED0}", "\u{FECF}"],
            'غ' => ["\u{063A}", "\u{FED2}", "\u{FED4}", "\u{FED3}"],
            'ف' => ["\u{0641}", "\u{FED6}", "\u{FED8}", "\u{FED7}"],
            'ق' => ["\u{0642}", "\u{FEDA}", "\u{FEDC}", "\u{FEDB}"],
            'ک' => ["\u{06A9}", "\u{FB8F}", "\u{FB91}", "\u{FB90}"],
            'گ' => ["\u{06AF}", "\u{FB93}", "\u{FB95}", "\u{FB94}"],
            'ل' => ["\u{0644}", "\u{FEE0}", "\u{FEE2}", "\u{FEE1}"],
            'م' => ["\u{0645}", "\u{FEE4}", "\u{FEE6}", "\u{FEE5}"],
            'ن' => ["\u{0646}", "\u{FEE8}", "\u{FEEA}", "\u{FEE9}"],
            'ں' => ["\u{06BA}", "\u{FB9F}", "\u{FB9F}", "\u{06BA}"],
            'و' => ["\u{0648}", "\u{FEEE}", "\u{FEEE}", "\u{0648}"],
            'ہ' => ["\u{06C1}", "\u{FBA7}", "\u{FBA9}", "\u{FBA8}"],
            'ھ' => ["\u{06BE}", "\u{FBAB}", "\u{FBAD}", "\u{FBAC}"],
            'ء' => ["\u{0621}", "\u{FE80}", "\u{FE80}", "\u{0621}"],
            'ی' => ["\u{06CC}", "\u{FBFE}", "\u{FC00}", "\u{FBFF}"],
            'ے' => ["\u{06D2}", "\u{FBAF}", "\u{FBAF}", "\u{06D2}"],
            'ئ' => ["\u{0626}", "\u{FE8A}", "\u{FE8C}", "\u{FE8B}"],
            '0' => ["\u{06F0}", "\u{06F0}", "\u{06F0}", "\u{06F0}"],
            '1' => ["\u{06F1}", "\u{06F1}", "\u{06F1}", "\u{06F1}"],
            '2' => ["\u{06F2}", "\u{06F2}", "\u{06F2}", "\u{06F2}"],
            '3' => ["\u{06F3}", "\u{06F3}", "\u{06F3}", "\u{06F3}"],
            '4' => ["\u{06F4}", "\u{06F4}", "\u{06F4}", "\u{06F4}"],
            '5' => ["\u{06F5}", "\u{06F5}", "\u{06F5}", "\u{06F5}"],
            '6' => ["\u{06F6}", "\u{06F6}", "\u{06F6}", "\u{06F6}"],
            '7' => ["\u{06F7}", "\u{06F7}", "\u{06F7}", "\u{06F7}"],
            '8' => ["\u{06F8}", "\u{06F8}", "\u{06F8}", "\u{06F8}"],
            '9' => ["\u{06F9}", "\u{06F9}", "\u{06F9}", "\u{06F9}"],
        ];

        $nonConnecting = ['ا', 'آ', 'د', 'ڈ', 'ذ', 'ر', 'ڑ', 'ز', 'ژ', 'و', 'ے', 'ء', ' '];
        
        // Split by non-Arabic blocks so English/Numbers remain LTR
        $blocks = preg_split('/([a-zA-Z0-9\.\-\/\:]+)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        
        // Reverse array of blocks to make overall string RTL
        $blocks = array_reverse($blocks);
        
        $out = '';
        foreach ($blocks as $block) {
            if (preg_match('/[a-zA-Z0-9\.\-\/\:]/', $block)) {
                $out .= $block;
            } else {
                // Shape and reverse the Arabic block
                $len = mb_strlen($block, 'UTF-8');
                $shapedBlock = '';
                for ($i = 0; $i < $len; $i++) {
                    $char = mb_substr($block, $i, 1, 'UTF-8');
                    if (!isset($chars[$char])) {
                        $shapedBlock = $char . $shapedBlock;
                        continue;
                    }

                    $prevChar = $i > 0 ? mb_substr($block, $i - 1, 1, 'UTF-8') : null;
                    $nextChar = $i < $len - 1 ? mb_substr($block, $i + 1, 1, 'UTF-8') : null;

                    $connectsRight = $prevChar && isset($chars[$prevChar]) && !in_array($prevChar, $nonConnecting);
                    $connectsLeft = $nextChar && isset($chars[$nextChar]);

                    if (in_array($char, $nonConnecting)) {
                        $connectsLeft = false;
                    }

                    if ($connectsRight && $connectsLeft) {
                        $shapedBlock = $chars[$char][2] . $shapedBlock; // Middle
                    } elseif ($connectsRight) {
                        $shapedBlock = $chars[$char][1] . $shapedBlock; // End
                    } elseif ($connectsLeft) {
                        $shapedBlock = $chars[$char][3] . $shapedBlock; // Beginning
                    } else {
                        $shapedBlock = $chars[$char][0] . $shapedBlock; // Isolated
                    }
                }
                $out .= $shapedBlock;
            }
        }
        
        return $out;
    }

    /**
     * Translate an English part name to Urdu dynamically using the local dictionary config.
     * Optionally reshapes the Urdu text for correct DomPDF rendering.
     *
     * @param string $title
     * @param bool $reshapedForPdf
     * @return string
     */
    public static function translatePartTitle($title, $reshapedForPdf = false)
    {
        // Only translate if explicitly requested via ?lang=ur query parameter
        if (request()->get('lang') !== 'ur') {
            return $title;
        }

        $config = config('parts_translations');
        if (!$config || !$config['enabled']) {
            return $title;
        }

        $dictionary = $config['dictionary'] ?? [];
        if (empty($dictionary)) {
            return $title;
        }

        // Split the title into words, keeping numbers and delimiters intact while separating letters from digits (e.g. 2.5in -> 2.5 and in)
        $words = preg_split('/([0-9]+\.?[0-9]*|[a-zA-Z]+|[\s,\-\/\(\)]+)/', $title, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        
        $translatedWords = [];
        foreach ($words as $word) {
            $trimmedWord = trim($word);
            if (empty($trimmedWord)) {
                $translatedWords[] = $word;
                continue;
            }

            // Find case-insensitive match in dictionary
            $matched = false;
            foreach ($dictionary as $en => $ur) {
                if (strcasecmp($trimmedWord, $en) === 0) {
                    $translatedWords[] = $ur;
                    $matched = true;
                    break;
                }
            }

            if (!$matched) {
                $translatedWords[] = $word; // Keep numbers, codes, or unmapped words as-is
            }
        }

        $translatedTitle = implode('', $translatedWords);

        if ($reshapedForPdf) {
            return self::reshapeUrdu($translatedTitle);
        }

        return $translatedTitle;
    }

    /**
     * Translate dynamic label keys to Urdu for standard table headers and meta labels on the PDF.
     * Always reshapes the Urdu output so Arabic/Urdu ligatures render perfectly in DomPDF.
     *
     * @param string $label
     * @param bool $reshaped
     * @return string
     */
    public static function translateLabel($label, $reshaped = true)
    {
        if (request()->get('lang') !== 'ur') {
            return $label;
        }

        $labels = [
            'Danyal Autos' => 'دانیال آٹوز',
            '12-BUTT MARKET BADAMI BAGH LAHORE' => '12-بٹ مارکیٹ، بدامی باغ، لاہور',
            'INVOICE' => 'انوائس',
            'Order #:' => 'آرڈر نمبر:',
            'Date:' => 'تاریخ:',
            'Due Date:' => 'واجب الادا تاریخ:',
            'Billed To' => 'بل بنام',
            'Phone:' => 'فون:',
            'Email:' => 'ای میل:',
            'Shipping Information' => 'شپنگ کی معلومات',
            'Type:' => 'شپنگ کی قسم:',
            'Courier:' => 'کورئیر کمپنی:',
            'Tracking:' => 'ٹریکنگ نمبر:',
            'Ship To:' => 'شپنگ ملک:',
            'Account Status' => 'اکاؤنٹ کی صورتحال',
            'Current Balance:' => 'موجودہ بقایا لیجر:',
            'Payment Status:' => 'ادائیگی کی صورتحال:',
            'DESCRIPTION' => 'تفصیل',
            'QTY' => 'تعداد',
            'PRICE' => 'قیمت',
            'DISCOUNT' => 'ڈسکاؤنٹ',
            'DISC. PRICE' => 'رعایتی قیمت',
            'TOTAL' => 'کل قیمت',
            'SKU:' => 'کوڈ:',
            'Brand:' => 'برانڈ:',
            'Model:' => 'ماڈل:',
            'Payment Instructions' => 'ادائیگی کی ہدایات',
            'Bank Name:' => 'بینک کا نام:',
            'Beneficiary:' => 'بینک اکاؤنٹ کا نام:',
            'Account No:' => 'بینک اکاؤنٹ نمبر:',
            'Please include the Order # with your payment.' => 'برائے مہربانی ادائیگی کے ساتھ آرڈر نمبر ضرور لکھیں۔',
            'Sub Total' => 'سب ٹوٹل',
            'Item Discounts' => 'آئٹم ڈسکاؤنٹ',
            'Coupon Discount' => 'کوپن ڈسکاؤنٹ',
            'Shipping' => 'شپنگ چارجز',
            'Grand Total' => 'میزانِ کل',
            'Current Bill Total' => 'موجودہ بل کل',
            'Amount Paid' => 'ادا شدہ رقم',
            'Previous Balance' => 'سابقہ واجب الادا بقایا',
            'Balance Due' => 'کل واجب الادا رقم',
            'THANK YOU FOR YOUR BUSINESS!' => 'آپ کی شراکت داری کا شکریہ!',
            'This is a computer generated document. | Danyal Autos' => 'یہ کمپیوٹر سے تیار کردہ بل ہے۔ | دانیال آٹوز',
            'COURIER' => 'کورئیر',
            'LOCAL' => 'لوکل',
            'UNPAID' => 'غیر ادا شدہ',
            'PAID' => 'ادا شدہ',
            'PARTIAL' => 'جزوی ادا شدہ',
        ];

        $translated = $labels[$label] ?? $label;

        if ($reshaped) {
            return self::reshapeUrdu($translated);
        }

        return $translated;
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