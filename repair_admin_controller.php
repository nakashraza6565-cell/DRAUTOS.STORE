<?php
$file = __DIR__ . '/drautos/app/Http/Controllers/AdminController.php';
$content = file_get_contents($file);

$search = <<<'EOD'
        $product_count = \App\Models\Product::countActiveProduct();
        // New Analytics for Dashboard
EOD;

$replace = <<<'EOD'
        $product_count = \App\Models\Product::countActiveProduct();
        $order_count = \App\Models\Order::countActiveOrder();

        // Sales Analytics
        $today_sales = \App\Models\Order::whereDate('created_at', \Carbon\Carbon::today())->where('status', 'delivered')->sum('total_amount');
        $yesterday_sales = \App\Models\Order::whereDate('created_at', \Carbon\Carbon::yesterday())->where('status', 'delivered')->sum('total_amount');

        // Top 5 Customers by Revenue
        $top_revenue_customers = \App\Models\Order::with('user')
            ->select('user_id', 'first_name', 'last_name', \DB::raw('SUM(total_amount) as total_revenue'))
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end->copy()->endOfDay())
            ->groupBy('user_id', 'first_name', 'last_name')
            ->orderBy('total_revenue', 'DESC')
            ->limit(5)
            ->get();

        // Top 5 Customers by Orders
        $top_order_customers = \App\Models\Order::with('user')
            ->select('user_id', 'first_name', 'last_name', \DB::raw('COUNT(*) as total_orders'))
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end->copy()->endOfDay())
            ->groupBy('user_id', 'first_name', 'last_name')
            ->orderBy('total_orders', 'DESC')
            ->limit(5)
            ->get();

        // Recent Customers
        $recent_customers = \App\User::where('role', 'user')->orderBy('id', 'DESC')->limit(5)->get();

        // New Analytics for Dashboard
EOD;

$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Repaired AdminController.\n";
