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

Route::post('/direct-user-store', 'UsersController@store')->name('users.direct-store');

Route::get('/fix-db', function () {
    try {
        // Run Migrations
        Artisan::call('migrate', ['--force' => true]);

        // Clear all cache
        Artisan::call('optimize:clear');

        // Supplier Ledger Updates (Manual SQL)
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE supplier_ledgers ADD COLUMN IF NOT EXISTS payment_method VARCHAR(255) NULL AFTER category");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE supplier_ledgers ADD COLUMN IF NOT EXISTS payment_details JSON NULL AFTER payment_method");
        } catch (\Exception $e) {
        }

        // Customer Ledger Updates (Manual SQL)
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE customer_ledgers ADD COLUMN IF NOT EXISTS payment_method VARCHAR(255) NULL AFTER category");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE customer_ledgers ADD COLUMN IF NOT EXISTS payment_details JSON NULL AFTER payment_method");
        } catch (\Exception $e) {
        }

        return "Database Migrated and Cache Cleared! Please try opening the POS again.";
    } catch (\Exception $e) {
        return "Error during fix: " . $e->getMessage();
    }
});

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
Route::post('/product/search', [FrontendController::class, 'productSearch'])->name('product.search');
Route::get('/product-cat/{slug}', [FrontendController::class, 'productCat'])->name('product-cat');
Route::get('/product-sub-cat/{slug}/{sub_slug}', [FrontendController::class, 'productSubCat'])->name('product-sub-cat');
Route::get('/product-brand/{slug}', [FrontendController::class, 'productBrand'])->name('product-brand');
// Cart section
Route::get('/add-to-cart/{slug}', [CartController::class, 'addToCart'])->name('add-to-cart')->middleware('user');
Route::post('/add-to-cart', [CartController::class, 'singleAddToCart'])->name('single-add-to-cart')->middleware('user');
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

    // Debug & Fix Routes (Secure)
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

    // Notification
    Route::get('/notification/{id}', [NotificationController::class, 'show'])->name('admin.notification');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('all.notification');
    Route::delete('/notification/{id}', [NotificationController::class, 'delete'])->name('notification.delete');
    // ERP & POS Modules
    Route::get('/pos', [AdminController::class, 'pos'])->name('admin.pos');
    Route::post('/pos/order', [AdminController::class, 'storePosOrder'])->name('pos.store-order');
    Route::get('/pos/thermal/{id}', [AdminController::class, 'thermalPrint'])->name('order.thermal');
    Route::get('/order/pdf/{id}', [OrderController::class, 'pdf'])->name('order.pdf');
    Route::get('/pos/search-products', [AdminController::class, 'searchProducts'])->name('pos.search-products');
    Route::get('/pos/last-purchase', [AdminController::class, 'getLastPurchase'])->name('pos.last-purchase');

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
    Route::get('/customer-ledger/{user}/thermal', [App\Http\Controllers\CustomerLedgerController::class, 'thermalPrint'])->name('admin.customer-ledger.thermal');
    Route::get('/customer-ledger/transaction/{id}/voucher', [App\Http\Controllers\CustomerLedgerController::class, 'printTransactionVoucher'])->name('admin.customer-ledger.transaction-voucher');
    Route::post('/customer-ledger/{user}/whatsapp', [App\Http\Controllers\CustomerLedgerController::class, 'sendWhatsApp'])->name('admin.customer-ledger.whatsapp');

    Route::get('/supplier-ledger', [App\Http\Controllers\SupplierLedgerController::class, 'index'])->name('admin.supplier-ledger.index');
    Route::get('/supplier-ledger/{supplier}', [App\Http\Controllers\SupplierLedgerController::class, 'show'])->name('admin.supplier-ledger.show');
    Route::post('/supplier-ledger/store', [App\Http\Controllers\SupplierLedgerController::class, 'store'])->name('admin.supplier-ledger.store');
    Route::put('/supplier-ledger/{id}', [App\Http\Controllers\SupplierLedgerController::class, 'update'])->name('admin.supplier-ledger.update');
    Route::delete('/supplier-ledger/{id}', [App\Http\Controllers\SupplierLedgerController::class, 'destroy'])->name('admin.supplier-ledger.destroy');
    Route::get('/supplier-ledger/{supplier}/pdf', [App\Http\Controllers\SupplierLedgerController::class, 'generatePDF'])->name('admin.supplier-ledger.pdf');
    Route::get('/supplier-ledger/{supplier}/thermal', [App\Http\Controllers\SupplierLedgerController::class, 'thermalPrint'])->name('admin.supplier-ledger.thermal');
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
        Route::resource('purchase-orders', 'PurchaseOrderController');
    });

    Route::group(['middleware' => ['permission:view-die']], function () {
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
        Route::get('/dead-products', 'ReportController@deadProducts')->name('reports.dead-products');
        Route::get('/dead-products/pdf', 'ReportController@deadProductsPdf')->name('reports.dead-products.pdf');
        Route::get('/profit-loss', 'ReportController@profitLoss')->name('reports.profit-loss');
        Route::get('/profit-loss/pdf', 'ReportController@profitLossPdf')->name('reports.profit-loss.pdf');
        Route::get('/payables', 'ReportController@payables')->name('reports.payables');
        Route::get('/receivables', 'ReportController@receivables')->name('reports.receivables');
        Route::get('/product-analysis', 'ReportController@productAnalysis')->name('reports.product-analysis');
        Route::get('/product-analysis/pdf', 'ReportController@productAnalysisPdf')->name('reports.product-analysis.pdf');
        Route::get('/customer', 'ReportController@customer')->name('reports.customer');
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
    Route::post('/online-order/store', [HomeController::class, 'storeOnlineOrder'])->name('user.online-order.store');
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
