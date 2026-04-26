<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Settings;
use App\User;
use App\Rules\MatchOldPassword;
use Hash;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Services\WhatsAppService;
use App\Models\CustomerLedger;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StatusNotification;

class AdminController extends Controller
{
    protected $whatsapp;

    public function __construct(WhatsAppService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    public function index()
    {
        $data = User::select(\DB::raw("COUNT(*) as count"), \DB::raw("DAYNAME(created_at) as day_name"), \DB::raw("DAY(created_at) as day"))
            ->where('created_at', '>', Carbon::today()->subDay(6))
            ->groupBy('day_name', 'day')
            ->orderBy('day')
            ->get();
        $array[] = ['Name', 'Number'];
        foreach ($data as $key => $value) {
            $array[++$key] = [$value->day_name, $value->count];
        }

        // System Analytics
        $category_count = \App\Models\Category::countActiveCategory();
        $product_count = \App\Models\Product::countActiveProduct();
        $order_count = \App\Models\Order::countActiveOrder();

        // Sales Analytics
        $today_sales = \App\Models\Order::whereDate('created_at', Carbon::today())->where('status', 'delivered')->sum('total_amount');
        $yesterday_sales = \App\Models\Order::whereDate('created_at', Carbon::yesterday())->where('status', 'delivered')->sum('total_amount');

        // Best Sellers
        $best_sellers = \App\Models\Cart::with('product')
            ->whereNotNull('order_id')
            ->select('product_id', \DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_id')
            ->orderBy('total_qty', 'DESC')
            ->limit(5)
            ->get();

        // Recent Customers
        $recent_customers = User::where('role', 'user')->orderBy('id', 'DESC')->limit(5)->get();

        // New Analytics for Dashboard
        $staff_count = User::whereIn('role', ['admin', 'manager', 'staff'])->count();
        $supplier_count = \App\Models\Supplier::where('status', 'active')->count();
        $total_stock_value = \App\Models\Product::sum(\DB::raw('price * stock')); // Retail value

        $active_register = \App\Models\CashRegister::where('status', 'open')->latest()->first();

        // Today's Tasks
        $today_tasks = \App\Models\Task::with('assignee')
            ->whereDate('start_date', Carbon::today())
            ->where('status', '!=', 'completed')
            ->orderBy('priority', 'DESC')
            ->get();

        // New Products
        $new_products = \App\Models\Product::orderBy('id', 'DESC')->limit(5)->get();

        // Order Stats (Last 7 Days)
        $order_stats = \App\Models\Order::select(
            \DB::raw('DATE(created_at) as date'),
            \DB::raw('COUNT(*) as count'),
            \DB::raw('SUM(total_amount) as amount')
        )
            ->where('created_at', '>=', Carbon::today()->subDays(6))
            ->groupBy('date')
            ->get();

        $order_labels = [];
        $order_counts = [];
        $order_amounts = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $label = Carbon::today()->subDays($i)->format('D');
            $order_labels[] = $label;

            $stat = $order_stats->firstWhere('date', $date);
            $order_counts[] = $stat ? $stat->count : 0;
            $order_amounts[] = $stat ? (float)$stat->amount : 0;
        }

        // Cash Register Current Balance
        $register_balance = 0;
        if ($active_register) {
            $sales = \App\Models\Order::where('created_at', '>=', $active_register->opened_at)
                ->whereIn('payment_method', ['cod', 'cash'])
                ->where('status', '!=', 'cancel')
                ->sum('total_amount');
            $register_balance = ($active_register->opening_amount ?? 0) + $sales;
        }

        // Today's Payment Reminders for Modal
        $today_reminders = \App\Models\PaymentReminder::with('party')
            ->dueToday()
            ->get();

        $low_stock_count = \App\Models\Product::whereRaw('stock <= COALESCE(low_stock_threshold, 0)')->count();

        // Packaging counts
        $sticker_count = \App\Models\PackagingItem::where('type', 'sticker')->sum('stock');
        $box_count = \App\Models\PackagingItem::where('type', 'box')->sum('stock');

        // New Dashboard Data Additions
        // Attendance Data
        $today_attendance = \App\Models\Attendance::with('user')
            ->whereDate('date', Carbon::today())
            ->get();
        $present_staff_count = $today_attendance->where('status', 'present')->count();
        
        $all_staff = User::whereIn('role', ['admin', 'manager', 'staff'])->orderBy('name', 'ASC')->get();

        // Financial Totals
        $total_payables = \App\Models\PaymentReminder::where('type', 'payable')->where('status', '!=', 'completed')->sum('amount');
        $total_receivables = \App\Models\PaymentReminder::where('type', 'receivable')->where('status', '!=', 'completed')->sum('amount');

        return view('backend.index')
            ->with('users', json_encode($array))
            ->with('category_count', $category_count)
            ->with('product_count', $product_count)
            ->with('low_stock_count', $low_stock_count)
            ->with('sticker_count', $sticker_count)
            ->with('box_count', $box_count)
            ->with('order_count', $order_count)
            ->with('today_sales', $today_sales)
            ->with('yesterday_sales', $yesterday_sales)
            ->with('best_sellers', $best_sellers)
            ->with('recent_customers', $recent_customers)
            ->with('staff_count', $staff_count)
            ->with('supplier_count', $supplier_count)
            ->with('total_stock_value', $total_stock_value)
            ->with('active_register', $active_register)
            ->with('register_balance', $register_balance)
            ->with('today_tasks', $today_tasks)
            ->with('new_products', $new_products)
            ->with('order_labels', json_encode($order_labels))
            ->with('order_counts', json_encode($order_counts))
            ->with('order_amounts', json_encode($order_amounts))
            ->with('today_reminders', $today_reminders)
            ->with('today_attendance', $today_attendance)
            ->with('present_staff_count', $present_staff_count)
            ->with('all_staff', $all_staff)
            ->with('total_payables', $total_payables)
            ->with('total_receivables', $total_receivables);
    }

    public function whatsappSettings()
    {
        return view('backend.whatsapp.settings');
    }

    public function whatsappSettingsUpdate(Request $request)
    {
        $request->validate([
            'instance_id' => 'required|string',
            'access_token' => 'required|string',
        ]);

        try {
            $this->updateEnv('WHATSAPP_INSTANCE_ID', $request->instance_id);
            $this->updateEnv('WHATSAPP_ACCESS_TOKEN', $request->access_token);

            // Clear config cache so changes take effect
            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            return redirect()->back()->with('success', 'WhatsApp settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update .env: ' . $e->getMessage());
        }
    }

    protected function updateEnv($key, $value)
    {
        $path = base_path('.env');
        if (File::exists($path)) {
            $content = File::get($path);

            // If the key exists, replace it
            if (strpos($content, $key . '=') !== false) {
                // Handle complex values by quoting if needed
                $newValue = $key . '=' . $value;
                $content = preg_replace("/^{$key}=.*/m", $newValue, $content);
            } else {
                // Otherwise append it
                $content .= "\n" . $key . '=' . $value;
            }

            File::put($path, $content);
        }
    }

    public function profile()
    {
        $profile = Auth()->user();
        // return $profile;
        return view('backend.users.profile')->with('profile', $profile);
    }

    public function profileUpdate(Request $request, $id)
    {
        // return $request->all();
        $user = User::findOrFail($id);
        $data = $request->all();
        $status = $user->fill($data)->save();
        if ($status) {
            request()->session()->flash('success', 'Successfully updated your profile');
        } else {
            request()->session()->flash('error', 'Please try again!');
        }
        return redirect()->back();
    }

    public function settings()
    {
        $data = Settings::first();
        return view('backend.setting')->with('data', $data);
    }

    public function settingsUpdate(Request $request)
    {
        // return $request->all();
        $this->validate($request, [
            'short_des' => 'required|string',
            'description' => 'required|string',
            'photo' => 'required',
            'logo' => 'required',
            'address' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
        ]);
        $data = $request->all();
        // return $data;
        $settings = Settings::first();
        // return $settings;
        $status = $settings->fill($data)->save();
        if ($status) {
            request()->session()->flash('success', 'Setting successfully updated');
        } else {
            request()->session()->flash('error', 'Please try again');
        }
        return redirect()->route('admin');
    }

    public function changePassword()
    {
        return view('backend.layouts.changePassword');
    }
    public function changPasswordStore(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);

        User::find(auth()->user()->id)->update(['password' => Hash::make($request->new_password)]);

        return redirect()->route('admin')->with('success', 'Password successfully changed');
    }

    // Pie chart
    public function userPieChart(Request $request)
    {
        // dd($request->all());
        $data = User::select(\DB::raw("COUNT(*) as count"), \DB::raw("DAYNAME(created_at) as day_name"), \DB::raw("DAY(created_at) as day"))
            ->where('created_at', '>', Carbon::today()->subDay(6))
            ->groupBy('day_name', 'day')
            ->orderBy('day')
            ->get();
        $array[] = ['Name', 'Number'];
        foreach ($data as $key => $value) {
            $array[++$key] = [$value->day_name, $value->count];
        }
        //  return $data;
        return view('backend.index')->with('course', json_encode($array));
    }

    // public function activity(){
    //     return Activity::all();
    //     $activity= Activity::all();
    //     return view('backend.layouts.activity')->with('activities',$activity);
    // }

    public function pos()
    {
        $customers = User::whereIn('role', ['user', 'customer'])->get();
        $categories = \App\Models\Category::where('status', 'active')->get();
        $brands = \App\Models\Brand::where('status', 'active')->get();
        $product_models = \App\Models\ProductModel::all();
        $suppliers = \App\Models\Supplier::where('status', 'active')->get();
        // Get unique cities for search dropdown
        $cities = User::whereNotNull('city')->where('city', '!=', '')->distinct()->pluck('city')->sort();
        $units = \App\Models\Unit::orderBy('name')->get();
        
        return view('backend.pos.index', compact('customers', 'categories', 'brands', 'product_models', 'cities', 'suppliers', 'units'));
    }

    public function storePosOrder(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required',
            'cart' => 'required|array',
            'total_amount' => 'required',
            'payment_method' => 'required',
            'amount_paid' => 'nullable|numeric',
            'due_date' => 'nullable|string'
        ]);

        // Create Order
        // Create Numeric Unique Order Number
        $order_number = date('ymd') . rand(1000, 9999);

        $order = new \App\Models\Order();
        $order->order_number = $order_number;
        $order->user_id = $data['customer_id'];
        $order->sub_total = $data['total_amount'];
        $order->total_amount = $data['total_amount'];
        $order->quantity = count($data['cart']);
        $order->payment_method = $data['payment_method'];

        // Fix: Default to 0 paid if not entered, instead of defaulting to whole amount
        $amount_paid = $request->amount_paid ?? 0;
        $order->amount_paid = $amount_paid;
        $order->due_date = $request->due_date;
        $pending_amount = $data['total_amount'] - $amount_paid;

        // Determine status
        if ($pending_amount <= 0) {
            $order->payment_status = 'paid';
        } elseif ($amount_paid > 0) {
            $order->payment_status = 'partial';
        } else {
            $order->payment_status = 'unpaid';
        }

        $order->status = 'new'; // POS orders start as new (matches DB enum: new,process,delivered,cancel)
        $order->order_type = 'local'; // Mark as POS order
        $order->staff_id = auth()->id(); // Track which staff/admin created the POS order
        $order->shipping_id = null; // No shipping for POS likely
        // Add name/email/phone from User
        $user = User::find($data['customer_id']);
        if ($user) {
            $names = explode(' ', $user->name, 2);
            $order->first_name = $names[0] ?: 'Customer';
            $order->last_name = $names[1] ?? 'Customer';
            $order->email = $user->email ?: ($user->phone ? $user->phone . '@local.com' : 'local@local.com');
            $order->phone = $user->phone ?: '0000000000';
            $order->address1 = $user->address ?: 'POS Counter';
            $order->country = 'Pakistan'; // Default for POS
            $order->courier_company = $user->courier_company;
            $order->courier_number = $user->courier_number;
        } else {
            $order->first_name = 'Walk-in';
            $order->last_name = 'Customer';
            $order->email = 'walkin@pos.local';
            $order->phone = '0000000000';
            $order->address1 = 'POS Counter';
        }
        $order->save();

        // Track Sales Order if provided
        $sales_order_id = $request->sales_order_id;
        if ($sales_order_id) {
            $order->sales_order_id = $sales_order_id;
            $order->save();
        }

        // Log the phone number found
        \Log::info("POS Order Created. Customer ID: {$data['customer_id']}, Phone: {$order->phone}");

        // Ledger Integration
        if ($user) {
            CustomerLedger::record(
                $user->id,
                now(),
                'debit',
                'order',
                'New Order #' . $order->order_number,
                $order->total_amount,
                $order->id
            );

            if ($amount_paid > 0) {
                CustomerLedger::record(
                    $user->id,
                    now(),
                    'credit',
                    'payment',
                    'Payment for Order #' . $order->order_number . ' via ' . $order->payment_method,
                    $amount_paid,
                    $order->id
                );
            }
        }

        // Customer Based Reminder Logic: Consolidate reminders for the same customer
        if ($pending_amount > 0 && $user) {
            $existingReminder = \App\Models\PaymentReminder::where('party_type', 'App\\User')
                ->where('party_id', $user->id)
                ->whereIn('status', ['pending', 'partially_paid'])
                ->where('type', 'receivable')
                ->first();

            if ($existingReminder) {
                // Update existing reminder
                $existingReminder->amount += $pending_amount;
                $existingReminder->notes = ($existingReminder->notes ? $existingReminder->notes . "\n" : "") . "Added POS Order " . $order_number;
                // Keep the original due date as it's the oldest debt, or update if user provided one
                if ($request->due_date) {
                    $existingReminder->due_date = \Carbon\Carbon::parse($request->due_date);
                }
                $existingReminder->save();
            } else {
                // Create new consolidated reminder
                \App\Models\PaymentReminder::create([
                    'type' => 'receivable',
                    'party_type' => 'App\\User',
                    'party_id' => $user->id,
                    'reference_number' => 'CUSTOMER-BAL',
                    'amount' => $pending_amount,
                    'due_date' => $request->due_date ? \Carbon\Carbon::parse($request->due_date) : now()->addDays(7),
                    'status' => 'pending',
                    'notes' => 'Generated from POS Order ' . $order_number
                ]);
            }
        }

        // Save Cart Items & Update Stock
        // Save Cart Items & Update Stock
        foreach ($data['cart'] as $item) {
            $type = $item['type'] ?? 'product';

            $cart = new \App\Models\Cart();
            $cart->order_id = $order->id;
            $cart->user_id = $order->user_id;
            $cart->price = $item['price'];
            $cart->status = 'progress';
            $cart->quantity = $item['qty'];
            $cart->amount = $item['price'] * $item['qty'];
            $cart->item_type = $type;

            if ($type == 'bundle') {
                $cart->bundle_id = $item['id'];
                // Deduct stock for bundle items
                $bundle = \App\Models\Bundle::find($item['id']);
                if ($bundle) {
                    foreach ($bundle->items as $bItem) {
                        $prod = \App\Models\Product::find($bItem->product_id);
                        if ($prod) {
                            $prod->decrement('stock', $bItem->quantity * $item['qty']);
                        }
                    }
                }
            } else {
                $cart->product_id = $item['id'];
                // Deduct stock for product
                $prod = \App\Models\Product::find($item['id']);
                if ($prod) {
                    $prod->decrement('stock', $item['qty']);
                }

                // Update Sales Order Item if applicable
                if (isset($item['so_item_id'])) {
                    $soItem = \App\Models\SalesOrderItem::find($item['so_item_id']);
                    if ($soItem) {
                        $soItem->increment('delivered_quantity', $item['qty']);
                        if ($soItem->delivered_quantity >= $soItem->quantity) {
                            $soItem->update(['status' => 'delivered']);
                        }
                    }
                }
            }
            $cart->save();
        }

        // Update Sales Order Status if applicable
        if ($sales_order_id) {
            $so = \App\Models\SalesOrder::with('items')->find($sales_order_id);
            if ($so) {
                $allDelivered = $so->items->every(function($item) {
                    return $item->delivered_quantity >= $item->quantity;
                });
                
                if ($allDelivered) {
                    $so->update(['status' => 'delivered']);
                } else {
                    $so->update(['status' => 'partially_delivered']);
                }
            }
        }

        // Send WhatsApp Notification with PDF Invoice
        $wa_status = false;
        try {
            if ($order->phone && $order->phone != '0000000000') {
                // Load cart_info relation needed for the PDF view
                $order->load('cart_info.product', 'cart_info.bundle', 'user', 'shipping');
                $wa_status = $this->whatsapp->sendOrderNotification($order);
            } else {
                \Log::warning('Skip WhatsApp: Invalid phone number for POS order');
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send POS WhatsApp: ' . $e->getMessage());
        }

        // Send notification to admins
        try {
            $admins = User::where('role', 'admin')->get();
            $details = [
                'title' => 'New POS order created by ' . auth()->user()->name,
                'actionURL' => route('order.show', $order->id),
                'fas' => 'fa-file-invoice-dollar'
            ];
            Notification::send($admins, new StatusNotification($details));
        } catch (\Exception $e) {
            \Log::error('Failed to send POS Admin Notification: ' . $e->getMessage());
        }

        return response()->json([
            'status'       => 'success',
            'invoice_url'  => route('order.pdf', $order->id),
            'thermal_url'  => route('order.thermal', $order->id),
            'wa_sent'      => $wa_status
        ]);
    }

    public function thermalPrint($id)
    {
        $order = \App\Models\Order::with('cart_info.product', 'cart_info.bundle')->findOrFail($id);
        return view('backend.order.thermal', compact('order'));
    }

    public function searchProducts(Request $request)
    {
        $query = $request->get('query');
        $cat_id = $request->get('cat_id');
        $brand_id = $request->get('brand_id');
        $model = $request->get('model');

        // Products
        $products = \App\Models\Product::with('brand')
            ->withSum(['carts as total_sold' => function($q) {
                $q->whereNotNull('order_id');
            }], 'quantity')
            ->where('status', 'active')
            ->when($request->exact_id, function($q) use ($query) {
                return $q->where('id', $query);
            })
            ->when($query && !$request->exact_id, function ($q) use ($query) {
                $q->where(function ($sq) use ($query) {
                    $sq->where('title', 'LIKE', "%{$query}%")
                        ->orWhere('barcode', 'LIKE', "%{$query}%")
                        ->orWhere('sku', 'LIKE', "%{$query}%");
                });
            })
            ->when($cat_id && $cat_id != 'all', function ($q) use ($cat_id) {
                $q->where('cat_id', $cat_id);
            })
            ->when($brand_id && $brand_id != 'all', function ($q) use ($brand_id) {
                $q->where('brand_id', $brand_id);
            })
            ->when($model && $model != 'all', function ($q) use ($model) {
                $q->where('model', $model);
            })
            ->orderByDesc('total_sold')
            ->limit(40)
            ->get();

        // Bundles
        $bundles = \App\Models\Bundle::where('status', 'active')
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sq) use ($query) {
                    $sq->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('sku', 'LIKE', "%{$query}%");
                });
            })
            ->limit(10)
            ->get();

        $results = [];

        foreach ($products as $p) {
            $p->item_type = 'product';
            $results[] = $p;
        }

        foreach ($bundles as $b) {
            $b->title = $b->name; // Map name to title for POS JS
            $b->item_type = 'bundle';
            // Bundles don't track stock directly, assume available
            $b->stock = 100; // Arbitrary number to show availability
            // Bundle photo? Maybe null or default
            $b->photo = null;
            $results[] = $b;
        }

        return response()->json($results);
    }

    public function getLastPurchase(Request $request)
    {
        $user_id = $request->get('customer_id');
        $item_type = $request->get('item_type') ?? 'product';
        $item_id = $request->get('item_id');

        // Skip for walk-in customer (ID 1)
        if (!$user_id || $user_id == 1) {
            return response()->json(['found' => false]);
        }

        $lastCart = \App\Models\Cart::whereHas('order', function($q) use ($user_id) {
                $q->where('user_id', $user_id);
            })
            ->where('item_type', $item_type)
            ->when($item_type == 'product', function($q) use ($item_id) {
                $q->where('product_id', $item_id);
            })
            ->when($item_type == 'bundle', function($q) use ($item_id) {
                $q->where('bundle_id', $item_id);
            })
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastCart && $lastCart->order) {
            return response()->json([
                'found' => true,
                'date' => $lastCart->order->created_at->format('M d, Y'),
                'price' => $lastCart->price,
                'quantity' => $lastCart->quantity
            ]);
        }

        return response()->json(['found' => false]);
    }

    public function payroll()
    {
        return view('backend.hr.payroll');
    }

    public function cashRegister()
    {
        return view('backend.pos.cash-register');
    }

    public function storageLink()
    {
        // check if the storage folder already linked;
        if (File::exists(public_path('storage'))) {
            // removed the existing symbolic link
            File::delete(public_path('storage'));

            //Regenerate the storage link folder
            try {
                Artisan::call('storage:link');
                request()->session()->flash('success', 'Successfully storage linked.');
                return redirect()->back();
            } catch (\Exception $exception) {
                request()->session()->flash('error', $exception->getMessage());
                return redirect()->back();
            }
        } else {
            try {
                Artisan::call('storage:link');
                request()->session()->flash('success', 'Successfully storage linked.');
                return redirect()->back();
            } catch (\Exception $exception) {
                request()->session()->flash('error', $exception->getMessage());
                return redirect()->back();
            }
        }
    }

    public function whatsappTest()
    {
        return view('backend.whatsapp.test');
    }

    public function whatsappTestSend(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'message' => 'required'
        ]);

        $result = $this->whatsapp->sendMessage($request->phone, $request->message);

        if ($result) {
            request()->session()->flash('success', 'WhatsApp test message queued successfully.');
        } else {
            request()->session()->flash('error', 'Failed to queue WhatsApp test message.');
        }

        return redirect()->back();
    }
}
