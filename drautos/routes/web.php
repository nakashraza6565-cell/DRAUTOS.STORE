<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\PostCommentController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\HomeController;
use \UniSharp\LaravelFilemanager\Lfm;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\ChequeController;

Route::post('/direct-user-store', 'UsersController@store')->name('users.direct-store');
Route::post('/direct-user-update/{id}', 'UsersController@posUpdate')->name('users.pos-update');

Route::get('.well-known/assetlinks.json', function () {
    return response()->json([
        [
            "relation" => ["delegate_permission/common.handle_all_urls"],
            "target" => [
                "namespace" => "android_app",
                "package_name" => "store.drautos.twa",
                "sha256_cert_fingerprints" => [
                    "C1:F7:69:B0:8A:42:CD:FA:6F:78:28:EA:E2:BA:CC:67:1E:EE:68:6F:D8:F7:2D:1F:CD:35:B6:7A:12:01:14:E1"
                ]
            ]
        ]
    ], 200, ['Content-Type' => 'application/json'], JSON_UNESCAPED_SLASHES);
});

// (Removed old fix-db route - moved to admin section)
Route::get('/debug-logs', function() {
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $lines = file($logFile);
        $last_lines = array_slice($lines, -250);
        return response()->json($last_lines);
    }
    return 'Log file not found.';
});

Route::get('/test-render-edit/{id}', function($id) {
    $user = \App\User::where('role', 'admin')->first();
    if ($user) {
        auth()->login($user);
    }
    $order = \App\Models\Order::with(['cart_info.product', 'cart_info.bundle'])->find($id);
    if (!$order) {
        return "Order not found";
    }
    $reminder = \App\Models\PaymentReminder::where('reference_number', $order->order_number)->first();
    $paid_at_pos = $order->total_amount;
    if($reminder) {
        $paid_at_pos = $order->total_amount - $reminder->amount;
    }
    return view('backend.order.edit', compact('order', 'reminder', 'paid_at_pos'));
});

Route::get('/fix-db', function () {
    try {
        $migrations = [
            '2026_06_23_000001_make_order_id_nullable_on_sale_returns.php',
            '2026_06_23_000002_add_order_id_to_sale_return_items.php',
            '2026_06_23_120000_upgrade_dies_system_tables.php',
        ];

        $output = [];
        foreach ($migrations as $file) {
            \Illuminate\Support\Facades\Artisan::call('migrate', [
                '--path' => 'database/migrations/' . $file,
                '--force' => true
            ]);
            $output[] = $file . ': ' . \Illuminate\Support\Facades\Artisan::output();
        }

        \Illuminate\Support\Facades\Artisan::call('optimize:clear');

        return "<h1>✅ Sale Return Migrations Done!</h1><pre>" . implode("\n", $output) . "</pre><br><a href='/admin/returns/sale' style='padding:10px 20px;background:#007bff;color:white;text-decoration:none;border-radius:5px;'>Go to Sale Returns</a>";
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        return "<h1>❌ Error</h1><p>" . $e->getMessage() . "</p><p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
    }
});


Route::get('/fix-balances', function () {
    try {
        $transactions = \App\Models\AccountTransaction::where('reference_type', 'SupplierLedger')->get();
        $count = 0;
        foreach ($transactions as $t) {
            if ($t->type == 'in') {
                $t->type = 'out';
                $t->save();
                $count++;
            }
        }

        $accounts = \App\Models\FinancialAccount::all();
        foreach ($accounts as $acc) {
            \App\Models\FinancialAccount::updateBalance($acc->id);
        }

        return "<h1>Balances Fixed!</h1><p>Swapped $count supplier payments from 'In' to 'Out' and recalculated all balances.</p><a href='/admin/financial-accounts'>Back to Accounts</a>";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

// ===== OpenRouter AI Test Route =====
Route::get('/test-ai', function () {
    $apiKey = env('OPENROUTER_API_KEY');
    if (!$apiKey) return '❌ OPENROUTER_API_KEY is MISSING from .env';

    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiKey,
        'Content-Type'  => 'application/json',
    ])->post('https://openrouter.ai/api/v1/chat/completions', [
        'model'      => 'google/gemma-4-26b-a4b-it:free',
        'messages'   => [['role' => 'user', 'content' => 'Say hello in one sentence.']],
        'max_tokens' => 50,
    ]);

    if ($response->successful()) {
        $text = $response->json()['choices'][0]['message']['content'] ?? 'No response';
        return '✅ AI is working! Model: Llama 3.3 70B | Response: ' . $text;
    }
    return '❌ Error (' . $response->status() . '): ' . $response->body();
});

// ===== Gemini API Diagnostic Route =====
Route::get('/test-gemini-key', function () {
    $apiKey = env('GEMINI_API_KEY');
    if (!$apiKey) return '❌ GEMINI_API_KEY is MISSING from .env';

    $maskedKey = substr($apiKey, 0, 8) . '...' . substr($apiKey, -6);
    $output = "=== Danyal Autos Gemini API Diagnostic Route ===\n\n";
    $output .= "🔑 Loaded API Key (Masked): $maskedKey\n";
    $output .= "🔑 Key Length: " . strlen($apiKey) . " characters\n\n";

    // 1. Try model discovery
    $listUrl = "https://generativelanguage.googleapis.com/v1beta/models?key=" . $apiKey;
    $output .= "🌐 Testing Model Discovery Endpoint (v1beta):\nURL: $listUrl\n";
    
    try {
        $response = \Illuminate\Support\Facades\Http::timeout(15)->get($listUrl);
        $output .= "📥 HTTP Response Code: " . $response->status() . "\n";
        $output .= "📥 Raw Discovery Response:\n" . json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n";
    } catch (\Throwable $e) {
        $output .= "❌ Discovery Error: " . $e->getMessage() . "\n\n";
    }

    // 2. Try generateContent on multiple models to see which one has quota
    $modelsToTest = [
        'gemini-2.5-flash',
        'gemini-3.5-flash',
        'gemini-flash-latest',
        'gemini-pro-latest',
        'gemini-2.0-flash'
    ];

    foreach ($modelsToTest as $model) {
        $generateUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $apiKey;
        $output .= "🌐 Testing Content Generation (v1beta / {$model}):\nURL: $generateUrl\n";

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => 'Hello, please say "Danyal Autos AI is ready" in one short sentence.']
                    ]
                ]
            ]
        ];

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($generateUrl, $payload);
            $output .= "📥 HTTP Response Code: " . $response->status() . "\n";
            $output .= "📥 Raw Generate Content Response:\n" . json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n";
        } catch (\Throwable $e) {
            $output .= "❌ Content Gen Error for {$model}: " . $e->getMessage() . "\n\n";
        }
    }

    return response($output)->header('Content-Type', 'text/plain');
});

Route::post('/admin/ai-chat', [\App\Http\Controllers\AIChatController::class, 'chat'])
    ->name('admin.ai-chat')
    ->middleware(['auth', 'admin']);

Route::get('/admin/chat/fetch', [\App\Http\Controllers\GroupChatController::class, 'fetchMessages'])->name('admin.chat.fetch')->middleware(['auth']);
Route::post('/admin/chat/send', [\App\Http\Controllers\GroupChatController::class, 'sendMessage'])->name('admin.chat.send')->middleware(['auth']);

use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\CustomerLedgerController;
/*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */

// CACHE CLEAR ROUTE
Route::get('cache-clear', function () {
    Artisan::call('optimize:clear');
    request()->session()->flash('success', 'Successfully cache cleared.');
    return redirect()->back();
})->name('cache.clear');

// HARD RESET ROUTE (clears OPcache + views from web process)
Route::get('hard-reset', function () {
    // Reset OPcache from within PHP web process
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    // Delete all compiled views
    $viewPath = storage_path('framework/views');
    $files = glob($viewPath . '/*.php');
    $deleted = 0;
    foreach ($files as $f) {
        if (unlink($f)) $deleted++;
    }
    return "<h2 style='font-family:monospace;padding:20px;'>✅ Hard Reset Done!<br><small>OPcache cleared. $deleted view file(s) deleted.</small><br><br><a href='/'>← Go to Homepage</a></h2>";
});


// STORAGE LINKED ROUTE
Route::get('storage-link', [AdminController::class, 'storageLink'])->name('storage.link');


// Premium Login & Register Routes (Defined early to avoid conflicts)
// Premium Login & Register Routes (Unique names for distinct URIs to avoid conflicts)
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::get('/auth/login', 'Auth\LoginController@showLoginForm')->name('login.form');
Route::get('/register', 'FrontendController@register')->name('register');
Route::get('/auth/register', 'FrontendController@register')->name('register.form');
Route::post('/register', 'FrontendController@registerSubmit')->name('register.submit');

Auth::routes(['register' => false]);

// Route::get('user/login', [FrontendController::class, 'login'])->name('login.form');
// Route::post('user/login', [FrontendController::class, 'loginSubmit'])->name('login.submit');
Route::get('user/logout', 'FrontendController@logout')->name('user.logout');

// Reset password
Route::get('password/reset', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
// Password Reset Routes
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Socialite
Route::get('login/{provider}/', [LoginController::class, 'redirect'])->name('login.redirect');
Route::get('login/{provider}/callback/', [LoginController::class, 'Callback'])->name('login.callback');

Route::get('/', [FrontendController::class, 'home'])->name('home');

// Frontend Routes
Route::get('/home', [FrontendController::class, 'index']);
Route::get('/about-us', [FrontendController::class, 'aboutUs'])->name('about-us');
Route::get('/contact', [FrontendController::class, 'contact'])->name('contact');
Route::post('/contact/message', [MessageController::class, 'store'])->name('contact.store');
Route::get('product-detail/{slug}', [FrontendController::class, 'productDetail'])->name('product-detail');
Route::match(['get', 'post'], '/product/search', [FrontendController::class, 'productSearch'])->name('product.search');
Route::get('/product-cat/{slug}', [FrontendController::class, 'productCat'])->name('product-cat');
Route::get('/product-sub-cat/{slug}/{sub_slug}', [FrontendController::class, 'productSubCat'])->name('product-sub-cat');
Route::get('/product-brand/{slug}', [FrontendController::class, 'productBrand'])->name('product-brand');
Route::get('/api/category/{id}/products', [FrontendController::class, 'getCategoryProductsAjax'])->name('api.category.products');
// Cart section
Route::get('/add-to-cart/{slug}', [CartController::class, 'addToCart'])->name('add-to-cart');
Route::post('/add-to-cart', [CartController::class, 'singleAddToCart'])->name('single-add-to-cart');

// AJAX Frontend Routes
Route::post('/ajax-add-to-cart', [CartController::class, 'ajaxAddToCart'])->name('ajax-add-to-cart');
Route::get('/ajax-get-cart', [CartController::class, 'ajaxGetCart'])->name('ajax-get-cart');
Route::post('/ajax-product-search', [FrontendController::class, 'ajaxSearch'])->name('ajax-product-search');
Route::get('cart-delete/{id}', [CartController::class, 'cartDelete'])->name('cart-delete');
Route::get('cart-clear', [CartController::class, 'cartClear'])->name('cart.clear');
Route::post('cart-update', [CartController::class, 'cartUpdate'])->name('cart.update');

Route::get('/cart', function () {
    return view('frontend.pages.cart');
})->name('cart');
Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout')->middleware('user');
// Wishlist
Route::get('/wishlist', function () {
    return view('frontend.pages.wishlist');
})->name('wishlist');
Route::get('/wishlist/{slug}', [WishlistController::class, 'wishlist'])->name('add-to-wishlist')->middleware('user');
Route::get('wishlist-delete/{id}', [WishlistController::class, 'wishlistDelete'])->name('wishlist-delete');
Route::post('cart/order', [OrderController::class, 'store'])->name('cart.order');
Route::get('order/pdf/{id}', [OrderController::class, 'pdf'])->name('order.pdf');
Route::get('order/whatsapp/{id}', [OrderController::class, 'sendWhatsApp'])->name('order.whatsapp');
Route::get('order/print/{id}', [OrderController::class, 'print'])->name('order.print');
Route::get('/income', [OrderController::class, 'incomeChart'])->name('product.order.income');
// Route::get('/user/chart',[AdminController::class, 'userPieChart'])->name('user.piechart');
Route::get('/product-grids', [FrontendController::class, 'productGrids'])->name('product-grids');
Route::get('/product-lists', [FrontendController::class, 'productLists'])->name('product-lists');
Route::match(['get', 'post'], '/filter', [FrontendController::class, 'productFilter'])->name('shop.filter');
// Order Track
Route::get('/product/track', [OrderController::class, 'orderTrack'])->name('order.track');
Route::post('product/track/order', [OrderController::class, 'productTrackOrder'])->name('product.track.order');
// Blog
Route::get('/blog', [FrontendController::class, 'blog'])->name('blog');
Route::get('/blog-detail/{slug}', [FrontendController::class, 'blogDetail'])->name('blog.detail');
Route::get('/blog/search', [FrontendController::class, 'blogSearch'])->name('blog.search');
Route::post('/blog/filter', [FrontendController::class, 'blogFilter'])->name('blog.filter');
Route::get('blog-cat/{slug}', [FrontendController::class, 'blogByCategory'])->name('blog.category');
Route::get('blog-tag/{slug}', [FrontendController::class, 'blogByTag'])->name('blog.tag');

// NewsLetter
Route::post('/subscribe', [FrontendController::class, 'subscribe'])->name('subscribe');

// Product Review
Route::resource('/review', 'ProductReviewController');
Route::post('product/{slug}/review', [ProductReviewController::class, 'store'])->name('review.store');

// Post Comment
Route::post('post/{slug}/comment', [PostCommentController::class, 'store'])->name('post-comment.store');
Route::resource('/comment', 'PostCommentController');
// Coupon
Route::post('/coupon-store', [CouponController::class, 'couponStore'])->name('coupon-store');
// Payment
Route::get('payment', [PayPalController::class, 'payment'])->name('payment');
Route::get('cancel', [PayPalController::class, 'cancel'])->name('payment.cancel');
Route::get('payment/success', [PayPalController::class, 'success'])->name('payment.success');


// Backend section start

Route::group(['prefix' => '/admin', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin');
    Route::get('/dashboard/chart-details', [AdminController::class, 'chartDetails'])->name('admin.chart.details');
    Route::get('/activity-logs', 'ActivityController@index')->name('admin.activity-logs');

    // Debug & Fix Routes (Secure)
    Route::get('/live-debug-log', function() {
        $logFile = storage_path('logs/laravel.log');
        if (!file_exists($logFile)) return 'No log file';
        $lines = file($logFile);
        $lastLines = array_slice($lines, -250);
        return response('<pre>' . htmlspecialchars(implode("", $lastLines)) . '</pre>')->header('Content-Type', 'text/html');
    });
    Route::get('/force-clear', function () {
        try {
            \Illuminate\Support\Facades\Artisan::call('optimize:clear');
            return "<h1>System Refreshed!</h1><p>The UI theme has been updated. Please refresh your browser (Ctrl+F5) or use Incognito mode to see the changes.</p><a href='/admin'>Back to Dashboard</a>";
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    });

    Route::get('/file-manager', function () {
        return view('backend.layouts.file-manager');
    })->name('file-manager');
    // user route - specific routes MUST come before resource() to avoid conflict
    Route::get('users/pending-requests', 'UsersController@pendingRequests')->name('users.pending');
    Route::resource('users', 'UsersController');
    Route::post('users/{id}/rating', 'UsersController@updateRating')->name('users.rating');
    Route::post('users/{id}/approve', 'UsersController@approve')->name('users.approve');
    // Banner
    Route::group(['middleware' => ['permission:view-banner']], function () {
        Route::resource('banner', 'BannerController');
    });
    // Brand
    Route::group(['middleware' => ['permission:view-brand']], function () {
        Route::resource('brand', 'BrandController');
        Route::post('/brand-quick', 'BrandController@quickStore')->name('brand.quick-store');
        Route::post('/brand/{id}/products', 'BrandController@updateProducts')->name('brand.products.update');
    });
    // Profile
    Route::get('/profile', [AdminController::class, 'profile'])->name('admin-profile');
    Route::post('/profile/{id}', [AdminController::class, 'profileUpdate'])->name('profile-update');
    // Category
    Route::group(['middleware' => ['permission:view-category']], function () {
        Route::resource('/category', 'CategoryController');
        Route::get('/category-print', 'CategoryController@printCatalog')->name('category.print');
        Route::get('/category/{id}/products', 'CategoryController@manageProducts')->name('category.products');
        Route::post('/category/{id}/products', 'CategoryController@updateProducts')->name('category.products.update');
        Route::post('/category-quick', 'CategoryController@quickStore')->name('category.quick-store');
    });
    // Product
    Route::group(['middleware' => ['permission:view-product']], function () {
        Route::post('/product/unit', 'ProductController@storeUnit')->name('product.store-unit');
        Route::post('/product/model', 'ProductController@storeModel')->name('product.store-model');
        Route::post('/product/{id}/update-photo', 'ProductController@updatePhoto')->name('product.update-photo');
        Route::post('/product/{id}/update-price', 'ProductController@updatePrice')->name('product.update-price');
        Route::post('/product/{id}/update-title', 'ProductController@updateTitle')->name('product.update-title');
        Route::get('/product/price-list', 'ProductController@priceList')->name('product.price-list');
        Route::get('/product/price-list/pdf', 'ProductController@priceListPDF')->name('product.price-list.pdf');
        Route::get('/product/search-simple', 'ProductController@searchSimple')->name('admin.product.search-simple');
        Route::resource('/product', 'ProductController');
        Route::post('/product-quick', 'ProductController@quickStore')->name('product.quick-store');
        Route::get('/low-stock', 'ProductController@lowStock')->name('product.low-stock');
    });
    // Ajax for sub category
    Route::post('/category/{id}/child', 'CategoryController@getChildByParent');
    // Blogs & Posts
    Route::group(['middleware' => ['permission:view-post']], function () {
        Route::resource('/post-category', 'PostCategoryController');
        Route::resource('/post-tag', 'PostTagController');
        Route::resource('/post', 'PostController');
    });
    // Message
    Route::resource('/message', 'MessageController');
    Route::get('/message/five', [MessageController::class, 'messageFive'])->name('messages.five');

    // Order
    Route::group(['middleware' => ['permission:view-order']], function () {
        Route::get('/order/search-by-number', 'OrderController@searchByNumber')->name('order.search-by-number');
        Route::post('/order/{id}/toggle-pin', 'OrderController@togglePin')->name('order.toggle-pin');
        Route::resource('/order', 'OrderController');
        Route::get('/local-orders', 'OrderController@localOrders')->name('order.local');
    });
    // Shipping
    Route::group(['middleware' => ['permission:view-shipping']], function () {
        Route::resource('/shipping', 'ShippingController');
    });
    // Coupon
    Route::group(['middleware' => ['permission:view-coupon']], function () {
        Route::resource('/coupon', 'CouponController');
    });
    // Settings
    Route::get('settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('setting/update', [AdminController::class, 'settingsUpdate'])->name('settings.update');
    Route::get('whatsapp-settings', [AdminController::class, 'whatsappSettings'])->name('admin.whatsapp-settings');
    Route::post('whatsapp-settings', [AdminController::class, 'whatsappSettingsUpdate'])->name('admin.whatsapp-settings.update');

    // Marketing Campaigns
    Route::get('/marketing/whatsapp-route-campaign', 'WhatsAppCampaignController@index')->name('whatsapp.campaign');

    // Notification
    Route::get('/notification/{id}', [NotificationController::class, 'show'])->name('admin.notification');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('all.notification');
    Route::delete('/notification/{id}', [NotificationController::class, 'delete'])->name('notification.delete');
    // ERP & POS Modules
    Route::get('pos', 'AdminController@pos')->name('admin.pos');
    Route::post('pos/order', 'AdminController@storePosOrder')->name('pos.store-order');
    Route::get('pos/thermal/{id}', 'AdminController@thermalPrint')->name('order.thermal');
    Route::get('clean-orphans', 'AdminController@cleanOrphans');
    // Route::get('/order/pdf/{id}', [OrderController::class, 'pdf'])->name('order.pdf');
    Route::get('/pos/search-products', [AdminController::class, 'searchProducts'])->name('pos.search-products');
    Route::get('/pos/last-purchase', [AdminController::class, 'getLastPurchase'])->name('pos.last-purchase');
    Route::get('/cheques/pending-customer', [ChequeController::class, 'getPendingCustomerCheques'])->name('cheques.pending-customer');

    // Sales Orders
    Route::get('sales-orders/{id}/thermal', 'SalesOrderController@thermalPrint')->name('sales-orders.thermal');
    Route::post('sales-orders/{id}/assign-staff', 'SalesOrderController@assignStaff')->name('sales-orders.assign-staff');
    Route::post('sales-orders/{id}/fulfill', 'SalesOrderController@fulfill')->name('sales-orders.fulfill');
    Route::post('sales-orders/{id}/toggle-priority', 'SalesOrderController@togglePriority')->name('sales-orders.toggle-priority');
    Route::post('sales-orders/{id}/add-item', 'SalesOrderController@addItem')->name('sales-orders.add-item');
    Route::post('sales-orders/item/{itemId}/update-price', 'SalesOrderController@updateItemPrice')->name('sales-orders.update-item-price');
    Route::delete('sales-orders/{id}/remove-item/{itemId}', 'SalesOrderController@removeItem')->name('sales-orders.remove-item');
    Route::get('sales-orders/pending-items/{userId}', 'SalesOrderController@getPendingItems')->name('sales-orders.pending-items');
    Route::get('sales-orders/get-price', 'SalesOrderController@getCustomerPrice')->name('sales-orders.get-price');
    // Sale Order Photos (evidence/reference)
    Route::post('sales-orders/{id}/upload-photos', 'SalesOrderController@uploadPhotos')->name('sales-orders.photos.upload');
    Route::delete('sales-orders/{id}/photos/{photoId}', 'SalesOrderController@deletePhoto')->name('sales-orders.photos.delete');
    Route::get('sales-orders/{id}/photos/{photoId}', 'SalesOrderController@viewPhoto')->name('sales-orders.photos.view');
    Route::resource('sales-orders', 'SalesOrderController');

    // Data Export Routes
    Route::get('/export/products', function () {
        $products = \App\Models\Product::all();
        $csv = "ID,Title,SKU,Brand,Model,Stock,Price,Wholesale_Price,Status\n";
        foreach ($products as $p) {
            $brand = $p->brand ? $p->brand->title : '';
            $csv .= "{$p->id},\"{$p->title}\",\"{$p->sku}\",\"{$brand}\",\"{$p->model}\",{$p->stock},{$p->price},{$p->wholesale_price},{$p->status}\n";
        }
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="products_export.csv"');
    })->name('export.products');

    Route::get('/export/sales', function () {
        $orders = \App\Models\Order::with('user')->get();
        $csv = "Order_Number,Date,Customer,Total_Amount,Payment_Status,Order_Status\n";
        foreach ($orders as $o) {
            $customer = $o->user ? $o->user->name : ($o->first_name . ' ' . $o->last_name);
            $csv .= "{$o->order_number},{$o->created_at},\"{$customer}\",{$o->total_amount},{$o->payment_status},{$o->status}\n";
        }
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="sales_export.csv"');
    })->name('export.sales');

    Route::get('/export/users', function () {
        $users = \App\User::all();
        $csv = "ID,Name,Email,Phone,Role,Customer_Type,Status\n";
        foreach ($users as $u) {
            $csv .= "{$u->id},\"{$u->name}\",\"{$u->email}\",\"{$u->phone}\",{$u->role},{$u->customer_type},{$u->status}\n";
        }
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="users_export.csv"');
    })->name('export.users');

    Route::get('/customer-ledger', [App\Http\Controllers\CustomerLedgerController::class, 'index'])->name('admin.customer-ledger.index');
    Route::get('/customer-ledger/{user}', [App\Http\Controllers\CustomerLedgerController::class, 'show'])->name('admin.customer-ledger.show');
    Route::post('/customer-ledger/store', [App\Http\Controllers\CustomerLedgerController::class, 'store'])->name('admin.customer-ledger.store');
    Route::put('/customer-ledger/{id}', [App\Http\Controllers\CustomerLedgerController::class, 'update'])->name('admin.customer-ledger.update');
    Route::delete('/customer-ledger/{id}', [App\Http\Controllers\CustomerLedgerController::class, 'destroy'])->name('admin.customer-ledger.destroy');
    Route::get('/customer-ledger/{user}/pdf', [App\Http\Controllers\CustomerLedgerController::class, 'generatePDF'])->name('admin.customer-ledger.pdf');
    Route::get('/customer-ledger/{user}/print', [App\Http\Controllers\CustomerLedgerController::class, 'print'])->name('admin.customer-ledger.print');
    Route::get('/customer-ledger/{user}/thermal', [App\Http\Controllers\CustomerLedgerController::class, 'thermalPrint'])->name('admin.customer-ledger.thermal');
    Route::get('/customer-ledger/transaction/{id}/voucher', [App\Http\Controllers\CustomerLedgerController::class, 'printTransactionVoucher'])->name('admin.customer-ledger.transaction-voucher');
    Route::get('/customer-ledger/transaction/{id}/voucher/pdf', [App\Http\Controllers\CustomerLedgerController::class, 'pdfTransactionVoucher'])->name('admin.customer-ledger.transaction-voucher.pdf');
    Route::post('/customer-ledger/{user}/whatsapp', [App\Http\Controllers\CustomerLedgerController::class, 'sendWhatsApp'])->name('admin.customer-ledger.whatsapp');

    // Financial Accounts (Bank/Wallets)
    Route::resource('financial-accounts', 'FinancialAccountController');

    Route::get('/product-selling-history', [AdminController::class, 'getProductSellingHistory'])->name('admin.product-selling-history');
    Route::get('/supplier-ledger', [App\Http\Controllers\SupplierLedgerController::class, 'index'])->name('admin.supplier-ledger.index');
    Route::get('/supplier-ledger/sync-incomings', [App\Http\Controllers\SupplierLedgerController::class, 'syncIncomings'])->name('admin.supplier-ledger.sync-incomings');
    Route::get('/supplier-ledger/{supplier}', [App\Http\Controllers\SupplierLedgerController::class, 'show'])->name('admin.supplier-ledger.show');
    Route::post('/supplier-ledger/store', [App\Http\Controllers\SupplierLedgerController::class, 'store'])->name('admin.supplier-ledger.store');
    Route::put('/supplier-ledger/{id}', [App\Http\Controllers\SupplierLedgerController::class, 'update'])->name('admin.supplier-ledger.update');
    Route::delete('/supplier-ledger/{id}', [App\Http\Controllers\SupplierLedgerController::class, 'destroy'])->name('admin.supplier-ledger.destroy');
    Route::get('/supplier-ledger/{supplier}/pdf', [App\Http\Controllers\SupplierLedgerController::class, 'generatePDF'])->name('admin.supplier-ledger.pdf');
    Route::get('/supplier-ledger/{supplier}/print', [App\Http\Controllers\SupplierLedgerController::class, 'print'])->name('admin.supplier-ledger.print');
    Route::get('/supplier-ledger/{supplier}/thermal', [App\Http\Controllers\SupplierLedgerController::class, 'thermalPrint'])->name('admin.supplier-ledger.thermal');
    Route::get('/supplier-ledger/transaction/{id}/voucher', [App\Http\Controllers\SupplierLedgerController::class, 'printTransactionVoucher'])->name('admin.supplier-ledger.transaction-voucher');
    Route::post('/supplier-ledger/{supplier}/whatsapp', [App\Http\Controllers\SupplierLedgerController::class, 'sendWhatsApp'])->name('admin.supplier-ledger.whatsapp');

    // WhatsApp Test
    Route::get('/whatsapp-test', [AdminController::class, 'whatsappTest'])->name('whatsapp.test');
    Route::post('/whatsapp-test', [AdminController::class, 'whatsappTestSend'])->name('whatsapp.test.send');
    Route::group(['middleware' => ['permission:view-purchase']], function () {
        Route::get('suppliers/export/{id}', 'SupplierController@exportCSV')->name('suppliers.export');
        Route::post('suppliers/whatsapp/send', 'SupplierController@sendWhatsApp')->name('suppliers.whatsapp.send');
        Route::post('suppliers/{id}/update-phone', 'SupplierController@updatePhone')->name('suppliers.update-phone');
        Route::resource('suppliers', 'SupplierController');
        Route::post('suppliers/{id}/rating', 'SupplierController@updateRating')->name('suppliers.rating');
        Route::post('/supplier-quick', 'SupplierController@quickStore')->name('supplier.quick-store');
        Route::resource('warehouses', 'WarehouseController');
        Route::get('purchase-orders/{id}/convert', 'PurchaseOrderController@convert')->name('purchase-orders.convert');
        Route::get('purchase-orders/{id}/thermal', 'PurchaseOrderController@thermalPrint')->name('purchase-orders.thermal');
        Route::get('purchase-orders/{id}/supplier-record', 'PurchaseOrderController@supplierRecord')->name('purchase-orders.supplier-record');
        Route::resource('purchase-orders', 'PurchaseOrderController');
    });

    Route::group(['middleware' => ['permission:view-die']], function () {
        Route::post('die-management/{id}/handover', 'DieController@recordHandover')->name('die-management.handover');
        Route::post('die-management/{id}/quality', 'DieController@recordQualityReport')->name('die-management.quality');
        Route::post('die-management/{id}/expense', 'DieController@recordExpense')->name('die-management.expense');
        Route::resource('die-management', 'DieController');
    });

    // Manufacturing (BOM)
    Route::group(['prefix' => 'manufacturing', 'as' => 'manufacturing.'], function () {
        Route::get('/', 'ManufacturingController@index')->name('index');
        Route::get('/create', 'ManufacturingController@create')->name('create');
        Route::post('/store', 'ManufacturingController@store')->name('store');
        Route::get('/{id}/show', 'ManufacturingController@show')->name('show');
        Route::get('/{id}/edit', 'ManufacturingController@edit')->name('edit');
        Route::put('/{id}', 'ManufacturingController@update')->name('update');
        Route::delete('/{id}', 'ManufacturingController@destroy')->name('destroy');
        Route::get('/{id}/clone', 'ManufacturingController@cloneRecipe')->name('clone');
        Route::get('/api/previous-bom/{product_id}', 'ManufacturingController@getPreviousBom')->name('api.previous-bom');

        // Factors of Production
        Route::post('production-factors/quick-store', 'ProductionFactorController@quickStore')->name('production-factors.quick-store');
        Route::get('production-factors/purchase', 'ProductionFactorController@purchaseForm')->name('production-factors.purchase.create');
        Route::post('production-factors/purchase', 'ProductionFactorController@purchaseStore')->name('production-factors.purchase.store');
        Route::get('production-factors/invoices', 'ProductionFactorController@invoices')->name('production-factors.invoices');
        Route::get('production-factors/invoices/{id}', 'ProductionFactorController@invoiceShow')->name('production-factors.invoice.show');
        Route::resource('production-factors', 'ProductionFactorController');

        // Production
        Route::get('/production', 'ManufacturingController@productionIndex')->name('production.index');
        Route::get('/production/create', 'ManufacturingController@productionCreate')->name('production.create');
        Route::post('/production/store', 'ManufacturingController@productionStore')->name('production.store');
    });

    // Packaging & Stock Handling
    Route::group(['prefix' => 'packaging', 'as' => 'packaging.'], function () {
        Route::get('/', 'PackagingController@index')->name('index');
        Route::get('/create', 'PackagingController@create')->name('create');
        Route::post('/store', 'PackagingController@store')->name('store');
        Route::get('/{id}/edit', 'PackagingController@edit')->name('edit');
        Route::put('/{id}', 'PackagingController@update')->name('update');
        Route::delete('/{id}', 'PackagingController@destroy')->name('destroy');

        // Purchases
        Route::get('/purchases', 'PackagingPurchaseController@index')->name('purchases.index');
        Route::get('/purchases/create', 'PackagingPurchaseController@create')->name('purchases.create');
        Route::post('/purchases/store', 'PackagingPurchaseController@store')->name('purchases.store');
        Route::get('/purchases/{id}/edit', 'PackagingPurchaseController@edit')->name('purchases.edit');
        Route::put('/purchases/{id}', 'PackagingPurchaseController@update')->name('purchases.update');
        Route::delete('/purchases/{id}', 'PackagingPurchaseController@destroy')->name('purchases.destroy');
        Route::get('/purchases/{id}/invoice', 'PackagingPurchaseController@invoice')->name('purchases.invoice');

        // Usage Records
        Route::get('/usage', 'PackagingController@usageIndex')->name('usage.index');
    });

    Route::group(['middleware' => ['role:admin']], function () {
        Route::resource('expenses', 'ExpenseController');
        
        // Delivery Receipts (Bilty)
        Route::get('delivery-receipts/get-customer/{id}', 'DeliveryReceiptController@getCustomer')->name('delivery-receipts.get-customer');
        Route::get('delivery-receipts/{id}/print', 'DeliveryReceiptController@print')->name('delivery-receipts.print');
        Route::resource('delivery-receipts', 'DeliveryReceiptController');

        Route::resource('attendance', 'AttendanceController');
        Route::get('attendance/show/{id}', 'AttendanceController@show')->name('attendance.show');
        Route::get('attendance/export/{id}', 'AttendanceController@exportCSV')->name('attendance.export');
        Route::post('attendance/check-in', 'AttendanceController@checkIn')->name('attendance.checkin');
        Route::post('attendance/check-out', 'AttendanceController@checkOut')->name('attendance.checkout');
        Route::get('/payroll', [AdminController::class, 'payroll'])->name('admin.payroll');
        Route::get('commissions', 'CommissionController@index')->name('commissions.index');
        Route::get('analytics', 'AnalyticsController@index')->name('global.analytics');
        Route::resource('staff', 'StaffController');
        Route::resource('roles', 'RoleController');
    });
    Route::group(['middleware' => ['permission:view-bundle']], function () {
        Route::get('/bundles/{id}/pdf', 'BundleController@generatePDF')->name('bundles.pdf');
        Route::resource('bundles', 'BundleController');
    });
    Route::get('commissions', 'CommissionController@index')->name('commissions.index');
    Route::get('analytics', 'AnalyticsController@index')->name('global.analytics');

    // Cash Register
    Route::group(['middleware' => ['permission:view-cash-register']], function () {
        Route::get('/cash-register', [CashRegisterController::class, 'index'])->name('admin.cash-register');
        Route::post('/cash-register/open', [CashRegisterController::class, 'store'])->name('cash-register.open');
        Route::post('/cash-register/close/{id}', [CashRegisterController::class, 'close'])->name('cash-register.close');
    });

    Route::resource('staff', 'StaffController');
    Route::get('/local-orders', 'OrderController@localOrders')->name('order.local');
    Route::get('/low-stock', 'ProductController@lowStock')->name('product.low-stock');

    // Payment Reminders & Notifications
    Route::prefix('payment-reminders')->group(function () {
        Route::get('/', 'PaymentReminderController@index')->name('payment-reminders.index');
        Route::post('/store', 'PaymentReminderController@store')->name('payment-reminders.store');
        Route::get('/{reminder}', 'PaymentReminderController@show')->name('payment-reminders.show');
        Route::put('/{reminder}', 'PaymentReminderController@update')->name('payment-reminders.update');
        Route::post('/{reminder}/payment', 'PaymentReminderController@recordPayment')->name('payment-reminders.record-payment');
        Route::post('/{reminder}/whatsapp', 'PaymentReminderController@sendWhatsAppReminder')->name('payment-reminders.send-whatsapp');
        Route::get('/today', 'PaymentReminderController@getTodayNotifications')->name('payment-reminders.today');
        Route::delete('/{reminder}', 'PaymentReminderController@destroy')->name('payment-reminders.destroy');
    });

    // Inventory Incoming Goods
    Route::prefix('inventory-incoming')->group(function () {
        Route::get('/', 'InventoryIncomingController@index')->name('inventory-incoming.index');
        Route::get('/create', 'InventoryIncomingController@create')->name('inventory-incoming.create');
        Route::post('/store', 'InventoryIncomingController@store')->name('inventory-incoming.store');
        Route::get('/{inventoryIncoming}', 'InventoryIncomingController@show')->name('inventory-incoming.show');
        Route::get('/{inventoryIncoming}/barcodes', 'InventoryIncomingController@printBarcodes')->name('inventory-incoming.print-barcodes');
        Route::post('/{inventoryIncoming}/verify', 'InventoryIncomingController@verify')->name('inventory-incoming.verify');
        Route::post('/{inventoryIncoming}/complete', 'InventoryIncomingController@complete')->name('inventory-incoming.complete');
        Route::get('/{inventoryIncoming}/thermal', 'InventoryIncomingController@thermalPrint')->name('inventory-incoming.thermal');
        Route::post('/item/{id}/update', 'InventoryIncomingController@updateItem')->name('inventory-incoming.item.update');
        Route::delete('/item/{id}/delete', 'InventoryIncomingController@deleteItem')->name('inventory-incoming.item.delete');
        Route::post('/{inventoryIncoming}/item/add', 'InventoryIncomingController@addItem')->name('inventory-incoming.item.add');
        Route::get('/search/products', 'InventoryIncomingController@searchProducts')->name('inventory-incoming.search-products');
    });

    // Cheque Management
    Route::prefix('cheques')->group(function () {
        Route::get('/', 'ChequeController@index')->name('cheques.index');
        Route::get('/create', 'ChequeController@create')->name('cheques.create');
        Route::post('/store', 'ChequeController@store')->name('cheques.store');
        Route::get('/{cheque}', 'ChequeController@show')->name('cheques.show');
        Route::get('/{cheque}/edit', 'ChequeController@edit')->name('cheques.edit');
        Route::put('/{cheque}', 'ChequeController@update')->name('cheques.update');
        Route::post('/{cheque}/clear', 'ChequeController@markCleared')->name('cheques.mark-cleared');
        Route::post('/{cheque}/bounce', 'ChequeController@markBounced')->name('cheques.mark-bounced');
        Route::post('/{cheque}/cancel', 'ChequeController@markCancelled')->name('cheques.mark-cancelled');
        Route::get('/calendar/events', 'ChequeController@getCalendarCheques')->name('cheques.calendar-events');
        Route::delete('/{cheque}', 'ChequeController@destroy')->name('cheques.destroy');
    });

    // Employee Payroll
    Route::prefix('payroll')->group(function () {
        Route::get('/', 'EmployeePayrollController@index')->name('payroll.index');
        Route::get('/{employee}', 'EmployeePayrollController@show')->name('payroll.show');
        Route::post('/payment', 'EmployeePayrollController@recordPayment')->name('payroll.record-payment');
        Route::post('/advance', 'EmployeePayrollController@recordAdvance')->name('payroll.record-advance');
        Route::post('/advance/{advance}/repay', 'EmployeePayrollController@recordRepayment')->name('payroll.record-repayment');
        Route::post('/commission/calculate', 'EmployeePayrollController@calculateCommission')->name('payroll.calculate-commission');
        Route::get('/{employee}/ledger', 'EmployeePayrollController@ledger')->name('payroll.ledger');
        Route::get('/payment/{payment}/voucher', 'EmployeePayrollController@printVoucher')->name('payroll.print-voucher');
        Route::get('/{employee}/commissions', 'EmployeePayrollController@getPendingCommissions')->name('payroll.pending-commissions');
    });

    // Returns Management
    Route::prefix('returns')->group(function () {
        // Sale Returns
        Route::prefix('sale')->group(function () {
            Route::get('/', 'ReturnsController@saleReturnsIndex')->name('returns.sale.index');
            // Smart multi-order return (customer-first) — must be before /{return} wildcard
            Route::get('/new', 'ReturnsController@createSmartSaleReturn')->name('returns.sale.create-smart');
            Route::get('/search-products', 'ReturnsController@searchCustomerProducts')->name('returns.sale.search-products');
            Route::get('/create/{order}', 'ReturnsController@createSaleReturn')->name('returns.sale.create');
            Route::post('/store', 'ReturnsController@storeSaleReturn')->name('returns.sale.store');
            Route::get('/{return}', 'ReturnsController@showSaleReturn')->name('returns.sale.show');
            Route::get('/{return}/print-thermal', 'ReturnsController@printThermalSale')->name('returns.sale.print-thermal');
            Route::post('/{return}/approve', 'ReturnsController@approveSaleReturn')->name('returns.sale.approve');
        });

        // Purchase Returns
        Route::prefix('purchase')->group(function () {
            Route::get('/', 'ReturnsController@purchaseReturnsIndex')->name('returns.purchase.index');
            Route::get('/create/{purchaseOrder}', 'ReturnsController@createPurchaseReturn')->name('returns.purchase.create');
            Route::post('/store', 'ReturnsController@storePurchaseReturn')->name('returns.purchase.store');
            Route::get('/{return}', 'ReturnsController@showPurchaseReturn')->name('returns.purchase.show');
            Route::get('/{return}/print-thermal', 'ReturnsController@printThermalPurchase')->name('returns.purchase.print-thermal');
            Route::post('/{return}/approve', 'ReturnsController@approvePurchaseReturn')->name('returns.purchase.approve');
        });
    });

    // Tasks & Calendar
    Route::prefix('tasks')->group(function () {
        Route::get('/', 'TaskController@index')->name('tasks.index');
        Route::get('/calendar', 'TaskController@calendar')->name('tasks.calendar');
        Route::post('/store', 'TaskController@store')->name('tasks.store');
        Route::match(['put', 'patch'], '/{task}', 'TaskController@update')->name('tasks.update');
        Route::post('/{task}/complete', 'TaskController@markCompleted')->name('tasks.mark-completed');
        Route::get('/calendar/events', 'TaskController@getCalendarEvents')->name('tasks.calendar-events');
        Route::get('/pending', 'TaskController@getPendingTasks')->name('tasks.pending');
        Route::get('/today', 'TaskController@getTodayTasks')->name('tasks.today');
        Route::delete('/{task}', 'TaskController@destroy')->name('tasks.destroy');
    });

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/sales', 'ReportController@sales')->name('reports.sales');
        Route::get('/sales/pdf', 'ReportController@salesPdf')->name('reports.sales.pdf');
        Route::get('/stock', 'ReportController@stock')->name('reports.stock');
        Route::get('/stock/pdf', 'ReportController@stockPdf')->name('reports.stock.pdf');
        Route::get('/cash-flow', 'ReportController@cashFlow')->name('reports.cash-flow');
        Route::get('/cash-flow/pdf', 'ReportController@cashFlowPdf')->name('reports.cash-flow.pdf');
        Route::get('/sales-purchases', 'ReportController@salesPurchases')->name('reports.sales-purchases');
        Route::get('/sales-purchases/pdf', 'ReportController@salesPurchasesPdf')->name('reports.sales-purchases.pdf');
        Route::get('/payables', 'ReportController@payables')->name('reports.payables');
        Route::get('/receivables', 'ReportController@receivables')->name('reports.receivables');
        Route::get('/product-analysis', 'ReportController@productAnalysis')->name('reports.product-analysis');
        Route::get('/product-analysis/pdf', 'ReportController@productAnalysisPdf')->name('reports.product-analysis.pdf');
        Route::get('/customer', 'ReportController@customer')->name('reports.customer');
        Route::get('/customer/pdf', 'ReportController@customerPdf')->name('reports.customer.pdf');
    });

    // Password Change
    Route::get('change-password', [AdminController::class, 'changePassword'])->name('change.password.form');
    Route::post('change-password', [AdminController::class, 'changPasswordStore'])->name('change.password');
});


// User section start
Route::group(['prefix' => '/user', 'middleware' => ['user']], function () {
    Route::get('/', [HomeController::class, 'index'])->name('user');
    // Profile
    Route::get('/profile', [HomeController::class, 'profile'])->name('user.setting');
    Route::post('/profile/{id}', [HomeController::class, 'profileUpdate'])->name('user-profile-update');
    //  Order
    Route::get('/order', [HomeController::class, 'orderIndex'])->name('user.order.index');
    Route::get('/sales-order/{id}', [HomeController::class, 'salesOrderShow'])->name('user.sales-order.show');
    Route::get('/order/show/{id}', [HomeController::class, 'orderShow'])->name('user.order.show');
    Route::delete('/order/delete/{id}', [HomeController::class, 'userOrderDelete'])->name('user.order.delete');
    
    // User Sale Order Photos
    Route::post('/sales-order/{id}/upload-photos', [\App\Http\Controllers\SalesOrderController::class, 'userUploadPhotos'])->name('user.sales-orders.photos.upload');
    Route::get('/sales-order/{id}/photos/{photoId}', [\App\Http\Controllers\SalesOrderController::class, 'viewPhoto'])->name('user.sales-orders.photos.view');

    // Product Review
    Route::get('/user-review', [HomeController::class, 'productReviewIndex'])->name('user.productreview.index');
    Route::delete('/user-review/delete/{id}', [HomeController::class, 'productReviewDelete'])->name('user.productreview.delete');
    Route::get('/user-review/edit/{id}', [HomeController::class, 'productReviewEdit'])->name('user.productreview.edit');
    Route::patch('/user-review/update/{id}', [HomeController::class, 'productReviewUpdate'])->name('user.productreview.update');

    // Post comment
    Route::get('user-post/comment', [HomeController::class, 'userComment'])->name('user.post-comment.index');
    Route::delete('user-post/comment/delete/{id}', [HomeController::class, 'userCommentDelete'])->name('user.post-comment.delete');
    Route::get('user-post/comment/edit/{id}', [HomeController::class, 'userCommentEdit'])->name('user.post-comment.edit');
    Route::patch('user-post/comment/udpate/{id}', [HomeController::class, 'userCommentUpdate'])->name('user.post-comment.update');

    // Online Order (POS)
    Route::get('/online-order', [HomeController::class, 'onlineOrder'])->name('user.online-order');
    Route::get('/online-order/{id}/edit', [HomeController::class, 'editOnlineOrder'])->name('user.online-order.edit');
    Route::post('/online-order/store', [HomeController::class, 'storeOnlineOrder'])->name('user.online-order.store');
    Route::post('/online-order/photo-store', [HomeController::class, 'storePhotoOrder'])->name('user.online-order.photo-store');
    Route::post('/save-basket/{id}', [HomeController::class, 'updateOnlineOrder'])->name('user.online-order.update');
    Route::get('/online-order/search', [HomeController::class, 'searchProducts'])->name('user.online-order.search');

    // Returns & Claims
    Route::get('/returns-claims', [HomeController::class, 'returnsIndex'])->name('user.returns.index');
    Route::get('/returns-claims/create/{order}', [HomeController::class, 'createReturn'])->name('user.returns.create');
    Route::post('/returns-claims/store', [HomeController::class, 'storeReturn'])->name('user.returns.store');

    // Ledger
    Route::get('/ledger', [HomeController::class, 'ledger'])->name('user.ledger');
    Route::get('/ledger/pdf', [HomeController::class, 'ledgerPDF'])->name('user.ledger.pdf');

    // Password Change
    Route::get('change-password', [HomeController::class, 'changePassword'])->name('user.change.password.form');
    Route::post('change-password', [HomeController::class, 'changPasswordStore'])->name('user.change.password');
});

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    Lfm::routes();
});


Route::get('/test-push', function() {
    $appId = env('ONESIGNAL_APP_ID');
    $restKey = env('ONESIGNAL_REST_API_KEY');

    if (!$appId || !$restKey) {
        return "Error: OneSignal keys are missing in .env!";
    }

    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'Authorization' => 'Basic ' . $restKey,
        'Content-Type' => 'application/json'
    ])->post('https://onesignal.com/api/v1/notifications', [
        'app_id' => $appId,
        'included_segments' => ['All'],
        'headings' => ['en' => 'Test Notification!'],
        'contents' => ['en' => 'If you see this on your phone, your PWA web push is working perfectly!'],
    ]);

    return "Push Sent! Response from OneSignal: " . $response->body();
});



Route::get('/test-onesignal-page', function() {
    $html = '<!DOCTYPE html>
<html>
<head>
<title>OneSignal Test</title>
</head>
<body>
<h1 style="font-family:sans-serif; text-align:center; margin-top:50px;">OneSignal Debug Test</h1>
<div id="status" style="text-align:center; font-family:sans-serif; font-size:16px; margin:20px; padding:20px; background:#f0f0f0;">Loading OneSignal...</div>
<div style="text-align:center; margin-top:20px;">
  <button id="subBtn" style="padding:15px 30px; font-size:18px; cursor:pointer; background:#e54b4b; color:white; border:none; border-radius:8px;">Subscribe to Notifications</button>
</div>
<div id="log" style="font-family:monospace; font-size:12px; margin:20px; padding:10px; background:#222; color:#0f0; max-height:300px; overflow:auto;"></div>
<script>
function log(msg) {
  document.getElementById("log").innerHTML += msg + "<br>";
  document.getElementById("status").innerText = msg;
}
log("Page loaded. Loading OneSignal SDK...");
window.OneSignalDeferred = window.OneSignalDeferred || [];
</script>
<script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js"></script>
<script>
OneSignalDeferred.push(async function(OneSignal) {
  try {
    log("OneSignal SDK loaded. Initializing...");
    await OneSignal.init({
      appId: "46461a8a-1e8f-4f50-9561-967e52304cba",
      notifyButton: { enable: true }
    });
    log("OneSignal initialized successfully!");
    log("Permission state: " + Notification.permission);
    log("Opted in: " + OneSignal.User.PushSubscription.optedIn);
    document.getElementById("subBtn").addEventListener("click", async function() {
      log("Subscribe button clicked...");
      try {
        await OneSignal.Notifications.requestPermission();
        log("After request - Permission: " + Notification.permission);
        log("After request - Opted in: " + OneSignal.User.PushSubscription.optedIn);
      } catch(e) {
        log("ERROR during requestPermission: " + e.message);
      }
    });
  } catch(e) {
    log("ERROR during init: " + e.message);
  }
});
</script>
</body>
</html>';
    return response($html)->header("Content-Type", "text/html");
});

Route::get('/ping-recent-activity', function() {
    $latest = \App\Models\ActivityLog::orderBy('created_at', 'desc')->first();
    if (!$latest) {
        return "No recent activity found!";
    }

    $appId = env('ONESIGNAL_APP_ID');
    $restKey = env('ONESIGNAL_REST_API_KEY');

    if (!$appId || !$restKey) {
        return "OneSignal keys are missing in .env!";
    }

    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'Authorization' => 'Basic ' . $restKey,
        'Content-Type' => 'application/json'
    ])->post('https://onesignal.com/api/v1/notifications', [
        'app_id' => $appId,
        'included_segments' => ['All'],
        'headings' => ['en' => 'DRAUTOS: ' . $latest->action],
        'contents' => ['en' => $latest->description],
        'url' => $latest->link ? (filter_var($latest->link, FILTER_VALIDATE_URL) ? $latest->link : url($latest->link)) : null,
    ]);

    return "Pinged! Activity: '{$latest->action}' -> '{$latest->description}'. Response: " . $response->body();
});
Route::get('/shop-by-brand', [App\Http\Controllers\FrontendController::class, 'shopByVehicleBrand'])->name('shop.vehicle.brand');

Route::get('/fix-missing-ledgers-8822', function() {
    $purchases = \App\Models\RawMaterialPurchase::with('items')->get();
    $restored = 0;
    foreach ($purchases as $purchase) {
        $exists = \App\Models\SupplierLedger::where('category', 'purchase')
            ->where('reference_id', $purchase->manufacturing_bill_id)
            ->where('supplier_id', $purchase->supplier_id)
            ->where('amount', $purchase->total_amount)
            ->exists();
        if (!$exists) {
            $descriptions = [];
            foreach ($purchase->items as $item) {
                $descriptions[] = $item->quantity . ' pcs of ' . $item->item_name;
            }
            $description = 'Purchased (Invoice: ' . $purchase->invoice_number . '): ' . implode(', ', $descriptions);
            \App\Models\SupplierLedger::record(
                $purchase->supplier_id, $purchase->purchase_date, 'debit', 'purchase',
                $description, $purchase->total_amount, $purchase->manufacturing_bill_id
            );
            $restored++;
        }
    }
    return 'Successfully restored ' . $restored . ' missing Raw Material Purchase ledger entries! You can now check the Supplier Ledger.';
});

Route::get('invoice-backpage/pdf', [App\Http\Controllers\AdminController::class, 'invoiceBackpagePDF'])->name('invoice.backpage.pdf');
