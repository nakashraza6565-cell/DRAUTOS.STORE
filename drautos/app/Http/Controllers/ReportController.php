<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\PaymentReminder;
use Carbon\Carbon;
use DB;

class ReportController extends Controller
{
    public function sales()
    {
        $salesByMonth = Order::select(
            DB::raw('sum(total_amount) as total'),
            DB::raw("DATE_FORMAT(created_at, '%M') as month")
        )
        ->where('status', 'delivered')
        ->groupBy('month')
        ->orderBy('created_at', 'ASC')
        ->get();

        $labels = $salesByMonth->pluck('month');
        $data = $salesByMonth->pluck('total');

        $recentSales = Order::with('user')->orderBy('id', 'DESC')->limit(10)->get();

        return view('backend.reports.sales', compact('labels', 'data', 'recentSales'));
    }

    public function salesPdf()
    {
        $salesByMonth = Order::select(
            DB::raw('sum(total_amount) as total'),
            DB::raw("DATE_FORMAT(created_at, '%M') as month")
        )
        ->where('status', 'delivered')
        ->groupBy('month')
        ->orderBy('created_at', 'ASC')
        ->get();

        $recentSales = Order::with('user')->orderBy('id', 'DESC')->limit(50)->get();
        $totalRevenue = $salesByMonth->sum('total');

        $pdf = \PDF::loadView('backend.reports.sales_pdf', compact('salesByMonth', 'recentSales', 'totalRevenue'));
        return $pdf->download('sales_report_'.date('Y-m-d').'.pdf');
    }

    public function stock()
    {
        $products = Product::where('status', 'active')->orderBy('stock', 'DESC')->get();
        $totalStockValue = $products->sum(function($product) {
            return $product->stock * ($product->purchase_price ?? 0);
        });

        // Get top products by stock quantity for the chart (limit to 10 for better visibility)
        $topProducts = Product::where('status', 'active')
            ->where('stock', '>', 0)
            ->orderBy('stock', 'DESC')
            ->limit(10)
            ->get();

        return view('backend.reports.stock', compact('products', 'totalStockValue', 'topProducts'));
    }

    public function stockPdf()
    {
        $products = Product::where('status', 'active')->orderBy('stock', 'DESC')->get();
        $totalStockValue = $products->sum(function($product) {
            return $product->stock * ($product->purchase_price ?? 0);
        });

        $pdf = \PDF::loadView('backend.reports.stock_pdf', compact('products', 'totalStockValue'));
        return $pdf->download('stock_report_'.date('Y-m-d').'.pdf');
    }

    public function deadProducts()
    {
        // Products not sold in the last 30 days
        $soldProductIds = DB::table('carts')
            ->where('created_at', '>', Carbon::now()->subMonth())
            ->pluck('product_id')
            ->unique();

        $deadProducts = Product::whereNotIn('id', $soldProductIds)
            ->where('status', 'active')
            ->get();

        return view('backend.reports.dead_products', compact('deadProducts'));
    }

    public function deadProductsPdf()
    {
        $soldProductIds = DB::table('carts')
            ->where('created_at', '>', Carbon::now()->subMonth())
            ->pluck('product_id')
            ->unique();

        $deadProducts = Product::whereNotIn('id', $soldProductIds)
            ->where('status', 'active')
            ->get();

        $pdf = \PDF::loadView('backend.reports.dead_products_pdf', compact('deadProducts'));
        return $pdf->download('dead_products_report_'.date('Y-m-d').'.pdf');
    }

    public function profitLoss()
    {
        $totalRevenue = Order::where('status', 'delivered')->sum('total_amount');
        
        // Approximation if purchase price isn't consistently tracked in orders
        // Ideal: sum of (selling_price - purchase_price) from order items
        $totalCostOfGoods = DB::table('carts')
            ->join('products', 'carts.product_id', '=', 'products.id')
            ->join('orders', 'carts.order_id', '=', 'orders.id')
            ->where('orders.status', 'delivered')
            ->sum(DB::raw('carts.quantity * products.purchase_price'));

        $totalExpenses = DB::table('expenses')->sum('amount');
        
        $netProfit = $totalRevenue - $totalCostOfGoods - $totalExpenses;

        return view('backend.reports.profit_loss', compact('totalRevenue', 'totalCostOfGoods', 'totalExpenses', 'netProfit'));
    }

    public function profitLossPdf()
    {
        $totalRevenue = Order::where('status', 'delivered')->sum('total_amount');
        $totalCostOfGoods = DB::table('carts')
            ->join('products', 'carts.product_id', '=', 'products.id')
            ->join('orders', 'carts.order_id', '=', 'orders.id')
            ->where('orders.status', 'delivered')
            ->sum(DB::raw('carts.quantity * products.purchase_price'));
        $totalExpenses = DB::table('expenses')->sum('amount');
        $netProfit = $totalRevenue - $totalCostOfGoods - $totalExpenses;

        $pdf = \PDF::loadView('backend.reports.profit_loss_pdf', compact('totalRevenue', 'totalCostOfGoods', 'totalExpenses', 'netProfit'));
        return $pdf->download('profit_loss_report_'.date('Y-m-d').'.pdf');
    }

    public function payables()
    {
        $payables = PaymentReminder::where('type', 'payable')->where('status', '!=', 'completed')->get();
        $totalPayable = $payables->sum('amount');
        
        $bySupplier = PaymentReminder::where('type', 'payable')
            ->where('status', '!=', 'completed')
            ->select('party_type', 'party_id', DB::raw('sum(amount) as total'), DB::raw('min(due_date) as earliest_due_date'))
            ->groupBy('party_type', 'party_id')
            ->with(['party' => function($query) {
                // withTrashed if supplier supports soft deletes
            }])
            ->get();

        // Chart Logic: Split by Supplier
        $chartTitle = "Payable Split by Supplier";
        $chartLabels = $bySupplier->map(function($s) { return $s->party->name ?? $s->party->company_name ?? 'Unknown'; })->values();
        $chartData = $bySupplier->pluck('total')->values();

        return view('backend.reports.payables', compact('payables', 'totalPayable', 'bySupplier', 'chartLabels', 'chartData', 'chartTitle'));
    }

    public function receivables(Request $request)
    {
        $city = $request->input('city');

        $query = \App\User::whereIn('role', ['user', 'customer'])
            ->where('current_balance', '>', 0);

        if ($city) {
            $query->where('city', $city);
        }

        $byCustomer = $query->orderBy('current_balance', 'desc')->get();
        $totalReceivable = $byCustomer->sum('current_balance');

        // Get unique cities for the dropdown filter (only for customers that have receivables)
        $cities = \App\User::whereIn('role', ['user', 'customer'])
            ->where('current_balance', '>', 0)
            ->whereNotNull('city')->where('city', '!=', '')
            ->distinct()->pluck('city');

        // City Chart Logic
        $cityChartLabels = [];
        $cityChartData = [];
        $cityGroups = $byCustomer->groupBy(function($item) {
            return $item->city ?? 'Unknown/No City';
        });
        foreach ($cityGroups as $cityName => $group) {
            $cityChartLabels[] = $cityName;
            $cityChartData[] = $group->sum('current_balance');
        }

        // Customer Chart Logic (Top 10 + Others)
        $topCustomers = $byCustomer->sortByDesc('current_balance')->take(10);
        $customerChartLabels = $topCustomers->map(function($c) { return $c->name ?? 'Unknown'; })->values()->toArray();
        $customerChartData = $topCustomers->pluck('current_balance')->values()->toArray();

        $othersBalance = $byCustomer->sortByDesc('current_balance')->skip(10)->sum('current_balance');
        if ($othersBalance > 0) {
            $customerChartLabels[] = 'Others';
            $customerChartData[] = $othersBalance;
        }

        return view('backend.reports.receivables', compact('totalReceivable', 'byCustomer', 'cities', 'city', 'cityChartLabels', 'cityChartData', 'customerChartLabels', 'customerChartData'));
    }
    public function productAnalysis(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();
        $productId = $request->product_id;

        $products = Product::where('status', 'active')->orderBy('title')->get();
        
        $selectedProduct = null;
        $stats = [
            'quantity_sold' => 0,
            'total_revenue' => 0,
            'total_cost' => 0,
            'gross_profit' => 0,
            'orders_count' => 0
        ];
        $salesHistory = [];

        if ($productId) {
            $selectedProduct = Product::find($productId);
            
            $query = DB::table('carts')
                ->join('orders', 'carts.order_id', '=', 'orders.id')
                ->where('carts.product_id', $productId)
                ->whereIn('orders.status', ['delivered', 'received', 'process', 'new'])
                ->where('orders.status', 'delivered') 
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->select(
                    'carts.quantity', 
                    'carts.price as unit_price',
                    'carts.amount',
                    'carts.created_at',
                    'orders.order_number',
                    'orders.id as order_id'
                );

            $salesHistory = $query->orderBy('created_at', 'DESC')->get();

            foreach ($salesHistory as $sale) {
                $stats['quantity_sold'] += $sale->quantity;
                $stats['total_revenue'] += $sale->amount;
                $stats['orders_count']++;
                
                $cost = 0;
                if ($selectedProduct) {
                    $purchasePrice = $selectedProduct->purchase_price ?? 0;
                    $cost = $purchasePrice * $sale->quantity;
                }
                $stats['total_cost'] += $cost;
            }
            $stats['gross_profit'] = $stats['total_revenue'] - $stats['total_cost'];
        } else {
            // Show Analysis for ALL products (Top Level Stats)
            // Or listing of all sales? Listing all sales might be heavy. 
            // Better to show aggregate stats for the period.
            
             $query = DB::table('carts')
                ->join('orders', 'carts.order_id', '=', 'orders.id')
                ->where('orders.status', 'delivered') 
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->select(
                    'carts.quantity', 
                    'carts.price as unit_price',
                    'carts.amount',
                    'carts.product_id',
                    'carts.created_at',
                    'orders.order_number',
                    'orders.id as order_id'
                );
            
            // Limit history to 100 for general view to avoid crash
            $salesHistory = $query->orderBy('created_at', 'DESC')->limit(100)->get();

            // Calculate aggregate stats
            foreach ($salesHistory as $sale) {
                 $stats['quantity_sold'] += $sale->quantity;
                 $stats['total_revenue'] += $sale->amount;
                 $stats['orders_count']++;
                 
                 // For all products, we need to fetch cost per product.
                 // This is expensive in a loop. Let's approximate or fetch eager.
                 // For now, let's skip Cost/Profit for "All Products" view or use simplified logic
                 // to avoid N+1.
            }
            // For general view, maybe 'selectedProduct' remaining null is enough signal to View
        }

        return view('backend.reports.product_analysis', compact('products', 'selectedProduct', 'stats', 'salesHistory', 'startDate', 'endDate'));
    }

    public function productAnalysisPdf(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();
        $productId = $request->product_id;

        $selectedProduct = null;
        $stats = [
            'quantity_sold' => 0,
            'total_revenue' => 0,
            'total_cost' => 0,
            'gross_profit' => 0,
            'orders_count' => 0
        ];
        $salesHistory = [];

        if ($productId) {
            $selectedProduct = Product::find($productId);
            
            $query = DB::table('carts')
                ->join('orders', 'carts.order_id', '=', 'orders.id')
                ->where('carts.product_id', $productId)
                ->where('orders.status', 'delivered') 
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->select(
                    'carts.quantity', 
                    'carts.price as unit_price',
                    'carts.amount',
                    'carts.created_at',
                    'orders.order_number',
                    'orders.id as order_id'
                );

            $salesHistory = $query->orderBy('created_at', 'DESC')->get();

            foreach ($salesHistory as $sale) {
                $stats['quantity_sold'] += $sale->quantity;
                $stats['total_revenue'] += $sale->amount;
                $stats['orders_count']++;
                
                $cost = 0;
                if ($selectedProduct) {
                    $purchasePrice = $selectedProduct->purchase_price ?? 0;
                    $cost = $purchasePrice * $sale->quantity;
                }
                $stats['total_cost'] += $cost;
            }
            $stats['gross_profit'] = $stats['total_revenue'] - $stats['total_cost'];
        }

        $pdf = \PDF::loadView('backend.reports.product_analysis_pdf', compact('selectedProduct', 'stats', 'salesHistory', 'startDate', 'endDate'));
        $filename = 'product_analysis_' . ($selectedProduct ? str_replace(' ', '_', strtolower($selectedProduct->title)) : 'all') . '_' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }
    public function customer(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();
        $customerId = $request->customer_id;

        // Fetch all customers for the dropdown
        $customers = \App\User::where('role', 'user')->orWhere('role', 'customer')->orderBy('name')->get();
        
        $selectedCustomer = null;
        $stats = [
            'total_sales' => 0,
            'total_paid' => 0,
            'total_pending' => 0, // Current outstanding balance
            'orders_count' => 0,
            'average_order_value' => 0
        ];
        $orders = [];

        if ($customerId) {
            $selectedCustomer = \App\User::find($customerId);
            
            // Get Orders within date range
            $ordersQuery = Order::where('user_id', $customerId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', '!=', 'cancelled');
                
            $orders = $ordersQuery->orderBy('created_at', 'DESC')->get();

            // Calculate Stats for the selected period
            $stats['total_sales'] = $orders->sum('total_amount');
            $stats['orders_count'] = $orders->count();
            
            // Calculate Paid amount (Available logic: status='delivered' implies paid OR if there is a specific payment status)
            // Assuming 'payment_status' == 'paid' implies fully paid. 
            // If checking 'status' == 'delivered', user might want that. 
            // I'll stick to 'payment_status' if available in Order model fillable.
            // Order.php shows 'payment_status'.
            $stats['total_paid'] = $orders->where('payment_status', 'paid')->sum('total_amount');
            
            if ($stats['orders_count'] > 0) {
                $stats['average_order_value'] = $stats['total_sales'] / $stats['orders_count'];
            }

            // Calculate Ledger/Pending Balance (All time)
            // Using PaymentReminder as source of truth for receivables
            $stats['total_pending'] = PaymentReminder::where('party_id', $customerId)
                ->where('type', 'receivable')
                ->where('status', '!=', 'completed')
                ->sum('amount');
            
            // If PaymentReminder is not used, maybe fallback to Unpaid Orders sum
            if ($stats['total_pending'] == 0) {
                 $stats['total_pending'] = Order::where('user_id', $customerId)
                    ->where('payment_status', 'unpaid')
                    ->where('status', '!=', 'cancelled')
                    ->sum('total_amount');
            }
        }

        // CSV Export
        if ($request->has('export') && $request->export == 'csv' && $selectedCustomer) {
            $filename = "customer_report_" . str_replace(' ', '_', strtolower($selectedCustomer->name)) . "_" . date('Y-m-d') . ".csv";
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $columns = ['Date', 'Order #', 'Status', 'Payment Status', 'Total Amount', 'Paid/Unpaid'];

            $callback = function() use ($orders, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($orders as $order) {
                    fputcsv($file, [
                        $order->created_at->format('Y-m-d'),
                        $order->order_number,
                        $order->status,
                        $order->payment_status,
                        $order->total_amount,
                        ($order->payment_status == 'paid') ? 'Paid' : 'Unpaid'
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return view('backend.reports.customer', compact('customers', 'selectedCustomer', 'stats', 'orders', 'startDate', 'endDate'));
    }
}
