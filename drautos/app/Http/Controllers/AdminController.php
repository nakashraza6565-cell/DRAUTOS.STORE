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

    public function index(Request $request)
    {
        try {
            $startDate = $request->input('start_date', Carbon::today()->subDays(6)->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));
            
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            
            $diffDays = $end->diffInDays($start);
            if ($diffDays > 31) { // Cap at 31 days max for charts
                $start = $end->copy()->subDays(31);
                $diffDays = 31;
                $startDate = $start->format('Y-m-d');
            }

        $data = User::select(\DB::raw("COUNT(*) as count"), \DB::raw("DAYNAME(created_at) as day_name"), \DB::raw("DAY(created_at) as day"))
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end->copy()->endOfDay())
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

        // Order Stats (Last N Days)
        $order_stats = \App\Models\Order::select(
            \DB::raw('DATE(created_at) as date'),
            \DB::raw('COUNT(*) as count'),
            \DB::raw('SUM(total_amount) as amount')
        )
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end->copy()->endOfDay())
            ->groupBy('date')
            ->get();
        // 1. One-time Migration: Fix Walk-in Customer Conflict
        // If User ID 1 is "Naqash Raza", we need a separate "Walk-in" user
        $walkInUser = \App\User::where('email', 'walkin@pos.local')->first();
        if (!$walkInUser) {
            $walkInUser = \App\User::create([
                'name' => 'Walk-in Customer',
                'email' => 'walkin@pos.local',
                'password' => bcrypt(\Illuminate\Support\Str::random(16)),
                'role' => 'customer',
                'status' => 'active',
                'phone' => '0000000000'
            ]);
        }

        // Migrate existing "Walk-in" orders from User ID 1 to the new Walk-in user
        if ($walkInUser->id != 1) {
            $migrated = \App\Models\Order::where('user_id', 1)
                ->where('first_name', 'Walk-in')
                ->update(['user_id' => $walkInUser->id]);
            
            if ($migrated > 0) {
                // Also migrate ledger entries
                \App\Models\CustomerLedger::where('user_id', 1)
                    ->where('description', 'LIKE', '%Walk-in%')
                    ->update(['user_id' => $walkInUser->id]);
                
                \App\Models\CustomerLedger::updateBalance($walkInUser->id);
                \App\Models\CustomerLedger::updateBalance(1);
            }
        }

        // Dashboard stats continue...
        $active_register = \App\Models\CashRegister::where('status', 'open')->first();

        // Today's Tasks
        $today_tasks = \App\Models\Task::with('assignee')
            ->whereDate('start_date', Carbon::today())
            ->where('status', '!=', 'completed')
            ->orderBy('priority', 'DESC')
            ->get();

        // New Products
        $new_products = \App\Models\Product::orderBy('id', 'DESC')->limit(5)->get();

        $order_labels = [];
        $order_counts = [];
        $order_amounts = [];
        $raw_dates = [];
        for ($i = $diffDays; $i >= 0; $i--) {
            $date = $end->copy()->subDays($i)->format('Y-m-d');
            $raw_dates[] = $date;
            $label = $diffDays > 7 ? $end->copy()->subDays($i)->format('M d') : $end->copy()->subDays($i)->format('D');
            $order_labels[] = $label;

            $stat = $order_stats->firstWhere('date', $date);
            $order_counts[] = $stat ? $stat->count : 0;
            $order_amounts[] = $stat ? (float)$stat->amount : 0;
        }

        // Cash Register Current Balance (Expected)
        $register_balance = 0;
        if ($active_register) {
            $opened_at = $active_register->opened_at;
            $accountId = $active_register->financial_account_id;
            $now = Carbon::now();

            // Total In (Sales)
            $posSales = \App\Models\CustomerLedger::whereBetween('created_at', [$opened_at, $now])
                        ->where('financial_account_id', $accountId)
                        ->where('type', 'credit')
                        ->where('category', 'payment')
                        ->sum('amount');

            // Total Out (Expenses + Supplier Payments)
            $expenses = \App\Models\Expense::whereBetween('created_at', [$opened_at, $now])
                        ->where('financial_account_id', $accountId)
                        ->sum('amount');

            $supplierPayments = \App\Models\SupplierLedger::whereBetween('created_at', [$opened_at, $now])
                                ->where('financial_account_id', $accountId)
                                ->where('type', 'credit')
                                ->where('category', 'payment')
                                ->sum('amount');

            $register_balance = ($active_register->opening_amount ?? 0) + $posSales - ($expenses + $supplierPayments);
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

        // Activity Feed (System Newspaper)
        $activity_logs = \App\Models\ActivityLog::with('user')
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->orderBy('created_at', 'DESC')
            ->get();
            
        // Get AI Summary Headlines
        $ai_headlines = class_exists('\App\Services\AIService') ? \App\Services\AIService::summarizeActivities($activity_logs) : null;

        // Cash Flow Analytics (Last N Days)
        $money_in = [];
        $money_out = [];
        $incoming_amounts = [];

        $incoming_goods = \App\Models\InventoryIncoming::with('items')
            ->where('received_date', '>=', $start)
            ->where('received_date', '<=', $end->copy()->endOfDay())
            ->get();

        for ($i = $diffDays; $i >= 0; $i--) {
            $date = $end->copy()->subDays($i)->format('Y-m-d');
            $in = \App\Models\AccountTransaction::whereDate('transaction_date', $date)
                ->where('type', 'in')
                ->sum('amount');
            $out = \App\Models\AccountTransaction::whereDate('transaction_date', $date)
                ->where('type', 'out')
                ->sum('amount');
            
            $money_in[] = (float)$in;
            $money_out[] = (float)$out;

            // Incoming Goods
            $dayIncoming = $incoming_goods->filter(function($item) use ($date) {
                return $item->received_date->format('Y-m-d') === $date;
            });
            $incoming_amounts[] = (float)$dayIncoming->sum(function($incoming) {
                return $incoming->items->sum('total_cost') + ($incoming->shipping_cost ?? 0);
            });
        }

        $total_money_in_7d  = array_sum($money_in);
        $total_money_out_7d = array_sum($money_out);
        $total_incoming_amount = array_sum($incoming_amounts);
        $total_sales_amount = array_sum($order_amounts);
        $money_in  = json_encode($money_in);
        $money_out = json_encode($money_out);
        $incoming_amounts = json_encode($incoming_amounts);
        $order_labels = json_encode($order_labels);
        $order_amounts = json_encode($order_amounts);
        $order_counts = json_encode($order_counts);
        $raw_dates = json_encode($raw_dates);
        $users = json_encode($array);
                $topRevNames = [];
        $topRevAmounts = [];
        foreach ($top_revenue_customers as $c) {
            $topRevNames[] = $c->user ? $c->user->name : ($c->first_name . ' ' . $c->last_name);
            $topRevAmounts[] = (float)$c->total_revenue;
        }

        $topOrdNames = [];
        $topOrdCounts = [];
        foreach ($top_order_customers as $c) {
            $topOrdNames[] = $c->user ? $c->user->name : ($c->first_name . ' ' . $c->last_name);
            $topOrdCounts[] = (int)$c->total_orders;
        }

        $topRevNamesJson = json_encode($topRevNames);
        $topRevAmountsJson = json_encode($topRevAmounts);
        $topOrdNamesJson = json_encode($topOrdNames);
        $topOrdCountsJson = json_encode($topOrdCounts);

        $accounts = \App\Models\FinancialAccount::where('status', 'active')->get();
        $recent_expense_titles = \App\Models\Expense::select('title')
            ->groupBy('title')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(20)
            ->pluck('title');

            // Get Current User's Account for pre-selection
            $staffAccId = class_exists('\App\Models\FinancialAccount') ? \App\Models\FinancialAccount::getStaffAccount() : null;

            return view('backend.index', compact(
                'users', 'category_count', 'product_count', 'order_count', 'today_sales',
                'yesterday_sales', 'top_revenue_customers', 'top_order_customers', 'recent_customers', 'staff_count',
                'supplier_count', 'total_stock_value', 'active_register', 'today_tasks',
                'new_products', 'order_labels', 'order_counts', 'order_amounts',
                'register_balance', 'today_reminders', 'low_stock_count', 'sticker_count',
                'box_count', 'today_attendance', 'present_staff_count', 'all_staff',
                'total_payables', 'total_receivables', 'activity_logs', 'ai_headlines',
                'money_in', 'money_out', 'accounts', 'staffAccId', 'recent_expense_titles',
                'incoming_amounts', 'total_money_in_7d', 'total_money_out_7d',
                'total_incoming_amount', 'total_sales_amount', 'raw_dates', 'topRevNamesJson', 'topRevAmountsJson', 'topOrdNamesJson', 'topOrdCountsJson'
            ));
        } catch (\Throwable $e) {
            \Log::error("Dashboard Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            throw $e;
        }
    }

        public function chartDetails(\Illuminate\Http\Request $request)
    {
        $date = $request->date;
        $type = $request->type;
        
        if ($type == 'cash_flow') {
            $inTransactions = \App\Models\AccountTransaction::with('account')
                ->whereDate('transaction_date', $date)
                ->where('type', 'in')
                ->get();
                
            $outTransactions = \App\Models\AccountTransaction::with('account')
                ->whereDate('transaction_date', $date)
                ->where('type', 'out')
                ->get();
                
            return view('backend.partials.chart_details', compact('inTransactions', 'outTransactions', 'date', 'type'));
        } elseif ($type == 'incoming_sales') {
            $incoming = \App\Models\InventoryIncoming::with(['supplier', 'items'])
                ->whereDate('received_date', $date)
                ->get();
                
            $sales = \App\Models\Order::with('user')
                ->whereDate('created_at', $date)
                ->get();
                
            return view('backend.partials.chart_details', compact('incoming', 'sales', 'date', 'type'));
        }
        
        return "Invalid chart type.";
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
                $topRevNames = [];
        $topRevAmounts = [];
        foreach ($top_revenue_customers as $c) {
            $topRevNames[] = $c->user ? $c->user->name : ($c->first_name . ' ' . $c->last_name);
            $topRevAmounts[] = (float)$c->total_revenue;
        }

        $topOrdNames = [];
        $topOrdCounts = [];
        foreach ($top_order_customers as $c) {
            $topOrdNames[] = $c->user ? $c->user->name : ($c->first_name . ' ' . $c->last_name);
            $topOrdCounts[] = (int)$c->total_orders;
        }

        $topRevNamesJson = json_encode($topRevNames);
        $topRevAmountsJson = json_encode($topRevAmounts);
        $topOrdNamesJson = json_encode($topOrdNames);
        $topOrdCountsJson = json_encode($topOrdCounts);

        $accounts = \App\Models\FinancialAccount::where('status', 'active')->get();
        
        $walkInUser = User::where('email', 'walkin@pos.local')->first();
        $walkInId = $walkInUser ? $walkInUser->id : 1;

        return view('backend.pos.index', compact('customers', 'categories', 'brands', 'product_models', 'cities', 'suppliers', 'units', 'accounts', 'walkInId'));
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
        
        // Calculate totals correctly
        $discount = $request->discount ?? 0;
        $order->coupon = $discount;
        $order->sub_total = $data['total_amount'] + $discount; // Total before global discount
        $order->total_amount = $data['total_amount']; // Final amount after all discounts
        
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

        $walkInUser = User::where('email', 'walkin@pos.local')->first();
        $walkInId = $walkInUser ? $walkInUser->id : 1;

        $order->status = ($data['customer_id'] == $walkInId) ? 'delivered' : 'new'; // Walk-in is auto-delivered, others start as new
        $order->order_type = 'local'; // Mark as POS order
        $order->staff_id = auth()->id(); // Track which staff/admin created the POS order
        $order->shipping_id = null; // No shipping for POS likely
        // Add name/email/phone from User
        $user = User::find($data['customer_id']);
        if ($user && $data['customer_id'] != $walkInId) {
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
            // Determine Financial Account from payment method
            $financialAccountId = is_numeric($data['payment_method']) ? $data['payment_method'] : null;
            
            // Fallback for legacy "cash" method or if no register is explicitly opened
            if (!$financialAccountId && strtolower($data['payment_method']) == 'cash') {
                $financialAccountId = \App\Models\FinancialAccount::getStaffAccount();
            }

            // For the description string
            $methodName = $data['payment_method'];
            if ($financialAccountId) {
                $acc = \App\Models\FinancialAccount::find($financialAccountId);
                $methodName = $acc ? $acc->name : 'Account';
            }

            // record the debt (Always debit)
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
                // record the payment (Always credit)
                CustomerLedger::record(
                    $user->id,
                    now(),
                    'credit',
                    'payment',
                    'Payment for Order #' . $order->order_number . ' via ' . $methodName,
                    $amount_paid,
                    $order->id,
                    null, // paymentMethod
                    null, // paymentDetails
                    $financialAccountId // 10th argument
                );
            }
        }

        // Partial Payment Logic: Create Reminder & Update Balance
        // SKIP for Walk-in Customer (ID 1) - They should not have credit
        if ($pending_amount > 0 && $user && $user->id != 1) {
            \App\Models\PaymentReminder::create([
                'type' => 'receivable',
                'party_type' => 'App\\User',
                'party_id' => $user->id,
                'reference_number' => $order_number,
                'amount' => $pending_amount,
                'due_date' => $request->due_date ? \Carbon\Carbon::parse($request->due_date) : now()->addDays(7),
                'status' => 'pending',
                'notes' => 'Generated from POS Order ' . $order_number
            ]);
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
            $customerName = $user ? $user->name : 'Walk-in Customer';
            $details = [
                'title' => '🛒 New POS Order by ' . auth()->user()->name . ' for ' . $customerName . ' — PKR ' . number_format($order->total_amount),
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
        $customerId = $request->get('customer_id');

        $staffId = auth()->id();

        // Products
        $products = \App\Models\Product::with(['brand', 'suppliers'])
            ->when($customerId && $customerId != 1, function($query) use ($customerId) {
                return $query->withSum(['carts as customer_sold' => function($q) use ($customerId) {
                    $q->whereHas('order', function($oq) use ($customerId) {
                        $oq->where('user_id', $customerId);
                    });
                }], 'quantity');
            })
            ->withSum(['carts as staff_sold' => function($q) use ($staffId) {
                $q->whereHas('order', function($oq) use ($staffId) {
                    $oq->where('staff_id', $staffId);
                });
            }], 'quantity')
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
            ->when($customerId && $customerId != 1, function($query) {
                return $query->orderByDesc('customer_sold');
            })
            ->orderByDesc('staff_sold')
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
    public function getProductSellingHistory(Request $request)
    {
        $productId = $request->get('product_id');
        $itemType = $request->get('item_type') ?? 'product';

        if (!$productId) return response()->json(['success' => false]);

        // Fetch last 5 sales with customer info
        $sales = \App\Models\Cart::with(['order.user'])
            ->whereHas('order', function($q) {
                // Exclude cancelled orders
                $q->where('status', '!=', 'cancel');
            })
            ->where('item_type', $itemType)
            ->when($itemType == 'product', function($q) use ($productId) {
                $q->where('product_id', $productId);
            })
            ->when($itemType == 'bundle', function($q) use ($productId) {
                $q->where('bundle_id', $productId);
            })
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->get()
            ->map(function($cart) {
                return [
                    'customer' => $cart->order->user->name ?? 'Walk-in Customer',
                    'price' => (float)$cart->price,
                    'qty' => (float)$cart->quantity,
                    'date' => $cart->created_at->format('d M, Y')
                ];
            });

        // Min/Max Prices in history
        $prices = \App\Models\Cart::where('item_type', $itemType)
            ->when($itemType == 'product', function($q) use ($productId) {
                $q->where('product_id', $productId);
            })
            ->when($itemType == 'bundle', function($q) use ($productId) {
                $q->where('bundle_id', $productId);
            })
            ->whereHas('order', function($q) {
                $q->where('status', '!=', 'cancel');
            })
            ->select(\DB::raw('MIN(price) as min_price'), \DB::raw('MAX(price) as max_price'))
            ->first();

        return response()->json([
            'success' => true,
            'history' => $sales,
            'min_price' => (float)($prices->min_price ?? 0),
            'max_price' => (float)($prices->max_price ?? 0)
        ]);
    }
}
