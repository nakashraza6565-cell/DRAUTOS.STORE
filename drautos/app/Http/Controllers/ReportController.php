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
        // Fetch all suppliers who have a pending balance in their ledger
        $suppliers = Supplier::where('current_balance', '>', 0)
            ->orderBy('current_balance', 'DESC')
            ->get();

        $totalPayable = $suppliers->sum('current_balance');

        // Map to the format expected by the existing view to avoid breaking it
        $bySupplier = $suppliers->map(function($supplier) {
            // Check if there are any specific payment reminders for this supplier to get the earliest due date
            $earliestReminder = PaymentReminder::where('party_id', $supplier->id)
                ->where('party_type', 'App\Models\Supplier')
                ->where('status', '!=', 'completed')
                ->orderBy('due_date', 'asc')
                ->first();

            return (object) [
                'party' => $supplier,
                'party_id' => $supplier->id,
                'total' => $supplier->current_balance,
                'earliest_due_date' => $earliestReminder ? $earliestReminder->due_date : null
            ];
        });

        // Chart Logic: Split by Supplier
        $chartTitle = "Payable Ledger Balance by Supplier";
        $chartLabels = $suppliers->pluck('name')->values();
        $chartData = $suppliers->pluck('current_balance')->values();

        return view('backend.reports.payables', compact('totalPayable', 'bySupplier', 'chartLabels', 'chartData', 'chartTitle'));
    }

    public function receivables(Request $request)
    {
        $city = $request->input('city');
        $interval = $request->input('interval', 'monthly');
        
        // Date filters for the trendline
        if ($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
            $startDate = \Carbon\Carbon::parse($request->start_date)->startOfDay();
            $endDate = \Carbon\Carbon::parse($request->end_date)->endOfDay();
        } else {
            $endDate = \Carbon\Carbon::now()->endOfDay();
            if ($interval == 'daily') {
                $startDate = \Carbon\Carbon::now()->subDays(14)->startOfDay();
            } elseif ($interval == 'weekly') {
                $startDate = \Carbon\Carbon::now()->subWeeks(8)->startOfWeek();
            } else {
                $startDate = \Carbon\Carbon::now()->subMonths(5)->startOfMonth();
            }
        }

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

        // Trendline Logic
        $trendLabels = [];
        $trendData = [];
        
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            $periodEnd = $current->copy();
            
            if ($interval == 'daily') {
                $periodEnd = $periodEnd->endOfDay();
                $label = $current->format('M d');
                $next = $current->copy()->addDay();
            } elseif ($interval == 'weekly') {
                $periodEnd = $periodEnd->endOfWeek();
                if ($periodEnd > $endDate) $periodEnd = $endDate->copy();
                $label = $current->format('M d') . ' - ' . $periodEnd->format('M d');
                $next = $current->copy()->addWeek();
            } else {
                $periodEnd = $periodEnd->endOfMonth();
                if ($periodEnd > $endDate) $periodEnd = $endDate->copy();
                $label = $current->format('M Y');
                $next = $current->copy()->addMonth();
            }
            
            // Calculate total AR at the end of this period
            $netAR = \App\Models\CustomerLedger::whereHas('user', function($q) {
                $q->whereIn('role', ['user', 'customer']);
            })
            ->where('transaction_date', '<=', $periodEnd)
            ->selectRaw('SUM(CASE WHEN type = "debit" THEN amount ELSE -amount END) as net')
            ->value('net') ?? 0;
            
            $trendLabels[] = $label;
            $trendData[] = round((float)$netAR, 2);
            
            $current = $next;
        }

        return view('backend.reports.receivables', compact('totalReceivable', 'byCustomer', 'cities', 'city', 'cityChartLabels', 'cityChartData', 'customerChartLabels', 'customerChartData', 'trendLabels', 'trendData', 'interval', 'startDate', 'endDate'));
    }

    public function productAnalysis(Request $request)
    {
        $isAllTime = !$request->start_date && !$request->end_date;

        if ($isAllTime) {
            $earliestOrder = DB::table('orders')->where('status', '!=', 'cancel')->min('created_at');
            $startDate = $earliestOrder ? Carbon::parse($earliestOrder)->startOfDay() : Carbon::now()->subYears(5)->startOfDay();
            $endDate   = Carbon::now()->endOfDay();
        } else {
            $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
            $endDate   = $request->end_date   ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();
        }

        $productId = $request->product_id;


        $products = Product::where('status', 'active')->orderBy('title')->get();
        
        $selectedProduct = null;
        $stats = [
            'gross_sold' => 0,
            'total_revenue' => 0,
            'returned_qty' => 0,
            'refunded_revenue' => 0,
            'return_ratio' => 0,
            'net_sold' => 0,
            'net_revenue' => 0,
            'total_cost' => 0,
            'gross_profit' => 0,
            'margin_loss_returns' => 0,
            'purchased_qty' => 0,
            'total_purchased_cost' => 0
        ];
        $salesHistory = [];
        $topProducts = collect();
        
        $chartLabels = [];
        $chartSalesData = [];
        $chartPurchasesData = [];
        $chartReturnsData = [];

        if ($productId) {
            $selectedProduct = Product::find($productId);
            
            // 1. Sales
            $sales = DB::table('carts')
                ->join('orders', 'carts.order_id', '=', 'orders.id')
                ->leftJoin('users', 'orders.user_id', '=', 'users.id')
                ->where('carts.product_id', $productId)
                ->where('orders.status', '!=', 'cancel') 
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->select(
                    'carts.quantity', 
                    'carts.price as unit_price',
                    'carts.amount',
                    'orders.created_at',
                    'orders.order_number',
                    'orders.id as order_id',
                    'orders.first_name',
                    'orders.last_name',
                    'orders.user_id',
                    'users.name as user_name'
                )
                ->get();

            // 2. Returns
            $returns = DB::table('sale_return_items')
                ->join('sale_returns', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')
                ->leftJoin('users', 'sale_returns.customer_id', '=', 'users.id')
                ->where('sale_return_items.product_id', $productId)
                ->where('sale_returns.status', 'approved')
                ->whereBetween('sale_returns.created_at', [$startDate, $endDate])
                ->select(
                    'sale_return_items.quantity',
                    'sale_return_items.unit_price',
                    'sale_return_items.total_price',
                    'sale_returns.created_at',
                    'sale_returns.return_number',
                    'sale_returns.id as return_id',
                    'sale_returns.customer_id',
                    'users.name as user_name'
                )
                ->get();

            // 3. Purchases (Incoming Goods)
            $purchases = DB::table('inventory_incoming_items')
                ->join('inventory_incoming', 'inventory_incoming_items.inventory_incoming_id', '=', 'inventory_incoming.id')
                ->leftJoin('suppliers', 'inventory_incoming.supplier_id', '=', 'suppliers.id')
                ->where('inventory_incoming_items.product_id', $productId)
                ->whereBetween('inventory_incoming.received_date', [$startDate, $endDate])
                ->select(
                    'inventory_incoming_items.quantity',
                    'inventory_incoming_items.unit_cost',
                    'inventory_incoming_items.total_cost',
                    'inventory_incoming.received_date as created_at',
                    'inventory_incoming.reference_number',
                    'inventory_incoming.id as incoming_id',
                    'inventory_incoming.supplier_id',
                    'suppliers.name as supplier_name'
                )
                ->get();

            // Cost per unit calculation
            $costPerUnit = $selectedProduct->purchase_price ?: 0;
            if ($costPerUnit == 0) {
                $costPerUnit = DB::table('inventory_incoming_items')
                    ->where('product_id', $productId)
                    ->avg('unit_cost') ?: 0;
            }

            // Populate Stats
            foreach ($sales as $sale) {
                $stats['gross_sold'] += $sale->quantity;
                $stats['total_revenue'] += $sale->amount;
            }
            foreach ($returns as $ret) {
                $stats['returned_qty'] += $ret->quantity;
                $stats['refunded_revenue'] += $ret->total_price;
            }
            foreach ($purchases as $p) {
                $stats['purchased_qty'] += $p->quantity;
                $stats['total_purchased_cost'] += $p->total_cost;
            }

            $stats['net_sold'] = $stats['gross_sold'] - $stats['returned_qty'];
            $stats['net_revenue'] = $stats['total_revenue'] - $stats['refunded_revenue'];
            
            if ($stats['gross_sold'] > 0) {
                $stats['return_ratio'] = ($stats['returned_qty'] / $stats['gross_sold']) * 100;
            }

            $stats['total_cost'] = $stats['net_sold'] * $costPerUnit;
            $stats['gross_profit'] = $stats['net_revenue'] - $stats['total_cost'];
            $stats['margin_loss_returns'] = $stats['returned_qty'] * ($selectedProduct->price - $costPerUnit);

            // Combine flow events chronologically
            $flowEvents = collect();

            foreach ($sales as $sale) {
                $flowEvents->push((object)[
                    'date' => Carbon::parse($sale->created_at),
                    'type' => 'sale',
                    'ref' => $sale->order_number,
                    'ref_url' => route('order.show', $sale->order_id),
                    'party_name' => $sale->user_name ?: ($sale->first_name . ' ' . $sale->last_name),
                    'party_url' => $sale->user_id ? route('admin.customer-ledger.show', $sale->user_id) : null,
                    'qty' => -$sale->quantity,
                    'unit_price' => $sale->unit_price,
                    'total' => $sale->amount
                ]);
            }

            foreach ($returns as $ret) {
                $flowEvents->push((object)[
                    'date' => Carbon::parse($ret->created_at),
                    'type' => 'return',
                    'ref' => $ret->return_number,
                    'ref_url' => route('returns.sale.show', $ret->return_id),
                    'party_name' => $ret->user_name ?: 'Walk-in Customer',
                    'party_url' => $ret->customer_id ? route('admin.customer-ledger.show', $ret->customer_id) : null,
                    'qty' => $ret->quantity,
                    'unit_price' => $ret->unit_price,
                    'total' => $ret->total_price
                ]);
            }

            foreach ($purchases as $p) {
                $flowEvents->push((object)[
                    'date' => Carbon::parse($p->created_at),
                    'type' => 'purchase',
                    'ref' => $p->reference_number,
                    'ref_url' => route('inventory-incoming.show', $p->incoming_id),
                    'party_name' => $p->supplier_name ?: 'Unknown Supplier',
                    'party_url' => $p->supplier_id ? route('admin.supplier-ledger.show', $p->supplier_id) : null,
                    'qty' => $p->quantity,
                    'unit_price' => $p->unit_cost,
                    'total' => $p->total_cost
                ]);
            }

            $salesHistory = $flowEvents->sortByDesc('date')->values()->all();

            // Chart data preparation
            $currentDate = $startDate->copy()->startOfDay();
            $targetEndDate = $endDate->copy()->startOfDay();

            $salesByDay = DB::table('carts')
                ->join('orders', 'carts.order_id', '=', 'orders.id')
                ->where('carts.product_id', $productId)
                ->where('orders.status', '!=', 'cancel')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->select(DB::raw('DATE(orders.created_at) as date'), DB::raw('SUM(carts.quantity) as qty'))
                ->groupBy('date')
                ->pluck('qty', 'date')
                ->toArray();

            $purchasesByDay = DB::table('inventory_incoming_items')
                ->join('inventory_incoming', 'inventory_incoming_items.inventory_incoming_id', '=', 'inventory_incoming.id')
                ->where('inventory_incoming_items.product_id', $productId)
                ->whereBetween('inventory_incoming.received_date', [$startDate, $endDate])
                ->select(DB::raw('DATE(inventory_incoming.received_date) as date'), DB::raw('SUM(inventory_incoming_items.quantity) as qty'))
                ->groupBy('date')
                ->pluck('qty', 'date')
                ->toArray();

            $returnsByDay = DB::table('sale_return_items')
                ->join('sale_returns', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')
                ->where('sale_return_items.product_id', $productId)
                ->where('sale_returns.status', 'approved')
                ->whereBetween('sale_returns.created_at', [$startDate, $endDate])
                ->select(DB::raw('DATE(sale_returns.created_at) as date'), DB::raw('SUM(sale_return_items.quantity) as qty'))
                ->groupBy('date')
                ->pluck('qty', 'date')
                ->toArray();

            while ($currentDate <= $targetEndDate) {
                $dateStr = $currentDate->format('Y-m-d');
                $chartLabels[] = $currentDate->format('d M');
                $chartSalesData[] = (int)($salesByDay[$dateStr] ?? 0);
                $chartPurchasesData[] = (int)($purchasesByDay[$dateStr] ?? 0);
                $chartReturnsData[] = (int)($returnsByDay[$dateStr] ?? 0);
                $currentDate->addDay();
            }
        } else {
            // Aggregate totals across all active products
            $sales = DB::table('carts')
                ->join('orders', 'carts.order_id', '=', 'orders.id')
                ->where('orders.status', '!=', 'cancel') 
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->select('carts.quantity', 'carts.amount')
                ->get();

            $returns = DB::table('sale_return_items')
                ->join('sale_returns', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')
                ->where('sale_returns.status', 'approved')
                ->whereBetween('sale_returns.created_at', [$startDate, $endDate])
                ->select('sale_return_items.quantity', 'sale_return_items.total_price')
                ->get();

            $purchases = DB::table('inventory_incoming_items')
                ->join('inventory_incoming', 'inventory_incoming_items.inventory_incoming_id', '=', 'inventory_incoming.id')
                ->whereBetween('inventory_incoming.received_date', [$startDate, $endDate])
                ->select('inventory_incoming_items.quantity', 'inventory_incoming_items.total_cost')
                ->get();

            foreach ($sales as $sale) {
                $stats['gross_sold'] += $sale->quantity;
                $stats['total_revenue'] += $sale->amount;
            }
            foreach ($returns as $ret) {
                $stats['returned_qty'] += $ret->quantity;
                $stats['refunded_revenue'] += $ret->total_price;
            }
            foreach ($purchases as $p) {
                $stats['purchased_qty'] += $p->quantity;
                $stats['total_purchased_cost'] += $p->total_cost;
            }

            $stats['net_sold'] = $stats['gross_sold'] - $stats['returned_qty'];
            $stats['net_revenue'] = $stats['total_revenue'] - $stats['refunded_revenue'];
            
            if ($stats['gross_sold'] > 0) {
                $stats['return_ratio'] = ($stats['returned_qty'] / $stats['gross_sold']) * 100;
            }

            // Fallback for aggregate cost
            $stats['total_cost'] = DB::table('carts')
                ->join('products', 'carts.product_id', '=', 'products.id')
                ->join('orders', 'carts.order_id', '=', 'orders.id')
                ->where('orders.status', '!=', 'cancel')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->sum(DB::raw('carts.quantity * COALESCE(products.purchase_price, 0)'));

            $stats['gross_profit'] = $stats['net_revenue'] - $stats['total_cost'];

            // =========================================================
            // Product-ranked leaderboard: sorted by total amount sold DESC
            // =========================================================
            $salesByProduct = DB::table('carts')
                ->join('orders', 'carts.order_id', '=', 'orders.id')
                ->join('products', 'carts.product_id', '=', 'products.id')
                ->where('orders.status', '!=', 'cancel')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->select(
                    'carts.product_id',
                    'products.title as product_title',
                    'products.sku',
                    'products.unit',
                    DB::raw('SUM(carts.quantity) as total_qty'),
                    DB::raw('SUM(carts.amount) as total_revenue')
                )
                ->groupBy('carts.product_id', 'products.title', 'products.sku', 'products.unit')
                ->orderByDesc('total_revenue')
                ->get();

            // Fetch return totals per product
            $returnsByProduct = DB::table('sale_return_items')
                ->join('sale_returns', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')
                ->where('sale_returns.status', 'approved')
                ->whereBetween('sale_returns.created_at', [$startDate, $endDate])
                ->select(
                    'sale_return_items.product_id',
                    DB::raw('SUM(sale_return_items.quantity) as returned_qty'),
                    DB::raw('SUM(sale_return_items.total_price) as refunded_amount')
                )
                ->groupBy('sale_return_items.product_id')
                ->get()
                ->keyBy('product_id');

            $topProducts = $salesByProduct->map(function ($row) use ($returnsByProduct) {
                $retData = $returnsByProduct->get($row->product_id);
                $returnedQty    = $retData ? (int)$retData->returned_qty : 0;
                $refundedAmount = $retData ? (float)$retData->refunded_amount : 0;
                $grossQty       = (int)$row->total_qty;
                $netQty         = $grossQty - $returnedQty;
                $netRevenue     = (float)$row->total_revenue - $refundedAmount;
                $returnRate     = $grossQty > 0 ? round(($returnedQty / $grossQty) * 100, 1) : 0;
                return (object)[
                    'product_id'      => $row->product_id,
                    'product_title'   => $row->product_title,
                    'sku'             => $row->sku,
                    'unit'            => $row->unit,
                    'gross_qty'       => $grossQty,
                    'returned_qty'    => $returnedQty,
                    'net_qty'         => $netQty,
                    'total_revenue'   => (float)$row->total_revenue,
                    'refunded_amount' => $refundedAmount,
                    'net_revenue'     => $netRevenue,
                    'return_rate'     => $returnRate,
                ];
            });

            $salesHistory = [];
        }

        return view('backend.reports.product_analysis', compact(
            'products', 'selectedProduct', 'stats', 'salesHistory', 'startDate', 'endDate',
            'chartLabels', 'chartSalesData', 'chartPurchasesData', 'chartReturnsData', 'topProducts', 'isAllTime'
        ));
    }

    public function productAnalysisPdf(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();
        $productId = $request->product_id;

        $selectedProduct = null;
        $stats = [
            'gross_sold' => 0,
            'total_revenue' => 0,
            'returned_qty' => 0,
            'refunded_revenue' => 0,
            'return_ratio' => 0,
            'net_sold' => 0,
            'net_revenue' => 0,
            'total_cost' => 0,
            'gross_profit' => 0,
            'margin_loss_returns' => 0,
            'purchased_qty' => 0,
            'total_purchased_cost' => 0
        ];
        $salesHistory = [];

        if ($productId) {
            $selectedProduct = Product::find($productId);
            
            // 1. Sales
            $sales = DB::table('carts')
                ->join('orders', 'carts.order_id', '=', 'orders.id')
                ->leftJoin('users', 'orders.user_id', '=', 'users.id')
                ->where('carts.product_id', $productId)
                ->where('orders.status', '!=', 'cancel') 
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->select(
                    'carts.quantity', 
                    'carts.price as unit_price',
                    'carts.amount',
                    'orders.created_at',
                    'orders.order_number',
                    'orders.id as order_id',
                    'orders.first_name',
                    'orders.last_name',
                    'orders.user_id',
                    'users.name as user_name'
                )
                ->get();

            // 2. Returns
            $returns = DB::table('sale_return_items')
                ->join('sale_returns', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')
                ->leftJoin('users', 'sale_returns.customer_id', '=', 'users.id')
                ->where('sale_return_items.product_id', $productId)
                ->where('sale_returns.status', 'approved')
                ->whereBetween('sale_returns.created_at', [$startDate, $endDate])
                ->select(
                    'sale_return_items.quantity',
                    'sale_return_items.unit_price',
                    'sale_return_items.total_price',
                    'sale_returns.created_at',
                    'sale_returns.return_number',
                    'sale_returns.id as return_id',
                    'sale_returns.customer_id',
                    'users.name as user_name'
                )
                ->get();

            // 3. Purchases (Incoming Goods)
            $purchases = DB::table('inventory_incoming_items')
                ->join('inventory_incoming', 'inventory_incoming_items.inventory_incoming_id', '=', 'inventory_incoming.id')
                ->leftJoin('suppliers', 'inventory_incoming.supplier_id', '=', 'suppliers.id')
                ->where('inventory_incoming_items.product_id', $productId)
                ->whereBetween('inventory_incoming.received_date', [$startDate, $endDate])
                ->select(
                    'inventory_incoming_items.quantity',
                    'inventory_incoming_items.unit_cost',
                    'inventory_incoming_items.total_cost',
                    'inventory_incoming.received_date as created_at',
                    'inventory_incoming.reference_number',
                    'inventory_incoming.id as incoming_id',
                    'inventory_incoming.supplier_id',
                    'suppliers.name as supplier_name'
                )
                ->get();

            $costPerUnit = $selectedProduct->purchase_price ?: 0;
            if ($costPerUnit == 0) {
                $costPerUnit = DB::table('inventory_incoming_items')
                    ->where('product_id', $productId)
                    ->avg('unit_cost') ?: 0;
            }

            foreach ($sales as $sale) {
                $stats['gross_sold'] += $sale->quantity;
                $stats['total_revenue'] += $sale->amount;
            }
            foreach ($returns as $ret) {
                $stats['returned_qty'] += $ret->quantity;
                $stats['refunded_revenue'] += $ret->total_price;
            }
            foreach ($purchases as $p) {
                $stats['purchased_qty'] += $p->quantity;
                $stats['total_purchased_cost'] += $p->total_cost;
            }

            $stats['net_sold'] = $stats['gross_sold'] - $stats['returned_qty'];
            $stats['net_revenue'] = $stats['total_revenue'] - $stats['refunded_revenue'];
            
            if ($stats['gross_sold'] > 0) {
                $stats['return_ratio'] = ($stats['returned_qty'] / $stats['gross_sold']) * 100;
            }

            $stats['total_cost'] = $stats['net_sold'] * $costPerUnit;
            $stats['gross_profit'] = $stats['net_revenue'] - $stats['total_cost'];
            $stats['margin_loss_returns'] = $stats['returned_qty'] * ($selectedProduct->price - $costPerUnit);

            $flowEvents = collect();

            foreach ($sales as $sale) {
                $flowEvents->push((object)[
                    'date' => Carbon::parse($sale->created_at),
                    'type' => 'sale',
                    'ref' => $sale->order_number,
                    'ref_url' => route('order.show', $sale->order_id),
                    'party_name' => $sale->user_name ?: ($sale->first_name . ' ' . $sale->last_name),
                    'party_url' => $sale->user_id ? route('admin.customer-ledger.show', $sale->user_id) : null,
                    'qty' => -$sale->quantity,
                    'unit_price' => $sale->unit_price,
                    'total' => $sale->amount
                ]);
            }

            foreach ($returns as $ret) {
                $flowEvents->push((object)[
                    'date' => Carbon::parse($ret->created_at),
                    'type' => 'return',
                    'ref' => $ret->return_number,
                    'ref_url' => route('returns.sale.show', $ret->return_id),
                    'party_name' => $ret->user_name ?: 'Walk-in Customer',
                    'party_url' => $ret->customer_id ? route('admin.customer-ledger.show', $ret->customer_id) : null,
                    'qty' => $ret->quantity,
                    'unit_price' => $ret->unit_price,
                    'total' => $ret->total_price
                ]);
            }

            foreach ($purchases as $p) {
                $flowEvents->push((object)[
                    'date' => Carbon::parse($p->created_at),
                    'type' => 'purchase',
                    'ref' => $p->reference_number,
                    'ref_url' => route('inventory-incoming.show', $p->incoming_id),
                    'party_name' => $p->supplier_name ?: 'Unknown Supplier',
                    'party_url' => $p->supplier_id ? route('admin.supplier-ledger.show', $p->supplier_id) : null,
                    'qty' => $p->quantity,
                    'unit_price' => $p->unit_cost,
                    'total' => $p->total_cost
                ]);
            }

            $salesHistory = $flowEvents->sortByDesc('date')->values()->all();
        }

        $pdf = \PDF::loadView('backend.reports.product_analysis_pdf', compact('selectedProduct', 'stats', 'salesHistory', 'startDate', 'endDate'));
        $filename = 'product_analysis_' . ($selectedProduct ? str_replace(' ', '_', strtolower($selectedProduct->title)) : 'all') . '_' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    public function customer(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate   = $request->end_date   ? Carbon::parse($request->end_date)->endOfDay()     : Carbon::now()->endOfDay();
        $customerId = $request->customer_id;

        // All customers for dropdown
        $customers = \App\User::whereIn('role', ['user', 'customer'])->orderBy('name')->get();

        $selectedCustomer = null;
        $lifetimeStats = [
            'total_sales'   => 0,
            'total_paid'    => 0,
            'outstanding'   => 0,
            'orders_count'  => 0,
            'returns_total' => 0,
            'customer_since'=> null,
        ];
        $periodStats = [
            'total_sales'   => 0,
            'total_paid'    => 0,
            'orders_count'  => 0,
            'avg_order'     => 0,
        ];
        $orders      = collect();
        $ledger      = collect();
        $returns     = collect();
        $topProducts = collect();
        $healthScorecard = null;


        if ($customerId) {
            $selectedCustomer = \App\User::find($customerId);

            if ($selectedCustomer) {
                // ── LIFETIME stats ───────────────────────────────────────
                $allOrders = Order::where('user_id', $customerId)->get();
                $lifetimeStats['orders_count']   = $allOrders->count();
                $lifetimeStats['total_sales']     = $allOrders->whereNotIn('status', ['cancelled'])->sum('total_amount');
                $lifetimeStats['total_paid']      = $allOrders->where('payment_status', 'paid')->whereNotIn('status', ['cancelled'])->sum('total_amount');
                $lifetimeStats['outstanding']     = $selectedCustomer->current_balance ?? 0;
                $lifetimeStats['customer_since']  = $allOrders->min('created_at');

                // Returns lifetime total
                $lifetimeStats['returns_total'] = \App\Models\SaleReturn::where('customer_id', $customerId)->sum('total_return_amount');

                // ── PERIOD stats (date-filtered) ──────────────────────────
                $periodOrders = Order::where('user_id', $customerId)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->whereNotIn('status', ['cancelled'])
                    ->get();

                $periodStats['orders_count'] = $periodOrders->count();
                $periodStats['total_sales']  = $periodOrders->sum('total_amount');
                $periodStats['total_paid']   = $periodOrders->where('payment_status', 'paid')->sum('total_amount');
                $periodStats['avg_order']    = $periodStats['orders_count'] > 0
                    ? $periodStats['total_sales'] / $periodStats['orders_count']
                    : 0;

                // ── ORDER HISTORY (ALL statuses, date-filtered, with items) ──
                $orders = Order::with(['cart_info.product'])
                    ->where('user_id', $customerId)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'DESC')
                    ->get();

                // ── LEDGER (ALL TIME) ─────────────────────────────────────
                $ledger = \App\Models\CustomerLedger::where('user_id', $customerId)
                    ->orderBy('transaction_date', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->get();

                // ── RETURNS (ALL TIME) ────────────────────────────────────
                $returns = \App\Models\SaleReturn::with(['items.product'])
                    ->where('customer_id', $customerId)
                    ->orderBy('return_date', 'DESC')
                    ->get();

                // ── TOP PRODUCTS ──────────────────────────────────────────
                $topProducts = DB::table('carts')
                    ->join('orders',   'carts.order_id',   '=', 'orders.id')
                    ->join('products', 'carts.product_id', '=', 'products.id')
                    ->where('orders.user_id', $customerId)
                    ->whereNotIn('orders.status', ['cancelled'])
                    ->whereNotNull('carts.product_id')
                    ->select(
                        'products.id',
                        'products.title',
                        'products.unit',
                        DB::raw('COUNT(carts.id)       as times_ordered'),
                        DB::raw('SUM(carts.quantity)   as total_qty'),
                        DB::raw('SUM(carts.amount)     as total_value')
                    )
                    ->groupBy('products.id', 'products.title', 'products.unit')
                    ->orderBy('total_value', 'DESC')
                    ->limit(10)
                    ->get();
                // ── HEALTH SCORECARD ─────────────────────────────────────

                // 1) Average Payment Recovery Days
                //    Logic: for each 'order' debit entry in ledger, find the
                //    next 'payment' credit entry and measure the gap in days.
                $orderDebits   = $ledger->where('type', 'debit')->where('category', 'order')
                                        ->sortBy('transaction_date')->values();
                $payCredit     = $ledger->where('type', 'credit')->where('category', 'payment')
                                        ->sortBy('transaction_date')->values();
                $recoveryDaysArr = [];
                foreach ($orderDebits as $debit) {
                    $debitDate = Carbon::parse($debit->transaction_date);
                    // Find the first payment credit that came AFTER this order
                    $matchedPayment = $payCredit->first(function($credit) use ($debitDate) {
                        return Carbon::parse($credit->transaction_date)->gt($debitDate);
                    });
                    if ($matchedPayment) {
                        $days = $debitDate->diffInDays(Carbon::parse($matchedPayment->transaction_date));
                        if ($days >= 0 && $days <= 365) { // sanity filter
                            $recoveryDaysArr[] = $days;
                        }
                    }
                }
                $avgRecoveryDays = count($recoveryDaysArr) > 0
                    ? round(array_sum($recoveryDaysArr) / count($recoveryDaysArr))
                    : null;

                // 2) Sales Trend: compare current period vs same-length previous period
                $periodLength  = $startDate->diffInDays($endDate);
                $prevStart     = $startDate->copy()->subDays($periodLength + 1);
                $prevEnd       = $startDate->copy()->subDay();
                $prevSales     = Order::where('user_id', $customerId)
                    ->whereBetween('created_at', [$prevStart, $prevEnd])
                    ->whereNotIn('status', ['cancelled'])
                    ->sum('total_amount');
                $trendPct      = 0;
                $trendDir      = 'stable'; // up | down | stable | new
                if ($prevSales > 0) {
                    $trendPct  = round((($periodStats['total_sales'] - $prevSales) / $prevSales) * 100, 1);
                    $trendDir  = $trendPct >= 5 ? 'up' : ($trendPct <= -5 ? 'down' : 'stable');
                } elseif ($periodStats['total_sales'] > 0) {
                    $trendDir  = 'new';
                    $trendPct  = 100;
                }

                // 3) Last Order Date
                $lastOrder     = $allOrders->whereNotIn('status', ['cancelled'])->sortByDesc('created_at')->first();
                $daysSinceLast = $lastOrder ? Carbon::parse($lastOrder->created_at)->diffInDays(Carbon::now()) : null;

                // 4) Star Rating (1–5 stars) — weighted score
                $recoveryRate  = $lifetimeStats['total_sales'] > 0
                    ? ($lifetimeStats['total_paid'] / $lifetimeStats['total_sales']) * 100
                    : 0;

                // Points per metric (max 5 each)
                $pRecovery = $recoveryRate >= 90 ? 5 : ($recoveryRate >= 75 ? 4 : ($recoveryRate >= 60 ? 3 : ($recoveryRate >= 40 ? 2 : 1)));

                $pDays = 5; // default if no data
                if ($avgRecoveryDays !== null) {
                    $pDays = $avgRecoveryDays <= 15  ? 5
                           : ($avgRecoveryDays <= 30  ? 4
                           : ($avgRecoveryDays <= 60  ? 3
                           : ($avgRecoveryDays <= 90  ? 2 : 1)));
                }

                $pTrend = $trendDir === 'up'  ? 5
                        : ($trendDir === 'new'  ? 4
                        : ($trendDir === 'stable' ? 3 : 1));

                $pActivity = 5; // default if no orders
                if ($daysSinceLast !== null) {
                    $pActivity = $daysSinceLast <= 30  ? 5
                               : ($daysSinceLast <= 60  ? 4
                               : ($daysSinceLast <= 90  ? 3
                               : ($daysSinceLast <= 180 ? 2 : 1)));
                }

                // Weighted: Recovery 40%, Days 30%, Trend 15%, Activity 15%
                $weightedScore = ($pRecovery * 0.40) + ($pDays * 0.30) + ($pTrend * 0.15) + ($pActivity * 0.15);
                $starRating    = round($weightedScore); // 1–5

                $healthLabel   = match($starRating) {
                    5 => 'Excellent',
                    4 => 'Good',
                    3 => 'Average',
                    2 => 'Watch Out',
                    default => 'Risky',
                };
                $healthColor   = match($starRating) {
                    5 => '#1cc88a',
                    4 => '#36b9cc',
                    3 => '#f6c23e',
                    2 => '#fd7e14',
                    default => '#e74a3b',
                };

                $healthScorecard = [
                    'recovery_rate'    => round($recoveryRate, 1),
                    'avg_recovery_days'=> $avgRecoveryDays,
                    'trend_pct'        => $trendPct,
                    'trend_dir'        => $trendDir,
                    'prev_period_sales'=> $prevSales,
                    'days_since_last'  => $daysSinceLast,
                    'star_rating'      => $starRating,
                    'health_label'     => $healthLabel,
                    'health_color'     => $healthColor,
                    'score_breakdown'  => [
                        'recovery' => $pRecovery,
                        'speed'    => $pDays,
                        'trend'    => $pTrend,
                        'activity' => $pActivity,
                    ],
                ];
            }
        }

        return view('backend.reports.customer', compact(
            'customers', 'selectedCustomer',
            'lifetimeStats', 'periodStats',
            'orders', 'ledger', 'returns', 'topProducts',
            'startDate', 'endDate',
            'healthScorecard'
        ));
    }


    public function customerPdf(Request $request)
    {
        $customerId = $request->customer_id;
        $startDate  = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfYear();
        $endDate    = $request->end_date   ? Carbon::parse($request->end_date)->endOfDay()     : Carbon::now()->endOfDay();

        if (!$customerId) {
            return redirect()->back()->with('error', 'Please select a customer first.');
        }

        $selectedCustomer = \App\User::findOrFail($customerId);

        $allOrders = Order::where('user_id', $customerId)->get();
        $lifetimeStats = [
            'total_sales'    => $allOrders->whereNotIn('status', ['cancelled'])->sum('total_amount'),
            'total_paid'     => $allOrders->where('payment_status', 'paid')->whereNotIn('status', ['cancelled'])->sum('total_amount'),
            'outstanding'    => $selectedCustomer->current_balance ?? 0,
            'orders_count'   => $allOrders->count(),
            'returns_total'  => \App\Models\SaleReturn::where('customer_id', $customerId)->sum('total_return_amount'),
            'customer_since' => $allOrders->min('created_at'),
        ];

        $orders = Order::with(['cart_info.product'])
            ->where('user_id', $customerId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'DESC')
            ->get();

        $ledger = \App\Models\CustomerLedger::where('user_id', $customerId)
            ->orderBy('transaction_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->get();

        $pdf = \PDF::loadView('backend.reports.customer_report_pdf', compact(
            'selectedCustomer', 'lifetimeStats', 'orders', 'ledger', 'startDate', 'endDate'
        ))->setPaper('a4', 'portrait');

        $filename = 'customer_report_' . str_replace(' ', '_', strtolower($selectedCustomer->name)) . '_' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }
}
