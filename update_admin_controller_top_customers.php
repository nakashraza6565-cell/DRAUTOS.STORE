<?php
$file = __DIR__ . '/drautos/app/Http/Controllers/AdminController.php';
$content = file_get_contents($file);

// Replace $best_sellers with the new queries.
// Original:
$original_best_sellers = <<<'EOD'
        // Best Sellers
        $best_sellers = \App\Models\Cart::with('product')
            ->whereNotNull('order_id')
            ->select('product_id', \DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_id')
            ->orderBy('total_qty', 'DESC')
            ->limit(5)
            ->get();
EOD;

$new_queries = <<<'EOD'
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
EOD;

if (strpos($content, '// Best Sellers') !== false) {
    $content = str_replace($original_best_sellers, $new_queries, $content);
}

// We also need to add these variables to the compact() and remove best_sellers
$content = str_replace("'yesterday_sales', 'best_sellers', 'recent_customers', 'staff_count',", "'yesterday_sales', 'top_revenue_customers', 'top_order_customers', 'recent_customers', 'staff_count',", $content);

// And we can prepare JSON strings for the charts
$json_prep = <<<'EOD'
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
EOD;

$content = str_replace('$accounts = \App\Models\FinancialAccount::where(\'status\', \'active\')->get();', $json_prep, $content);

// Add these to compact
$content = str_replace("'total_incoming_amount', 'total_sales_amount', 'raw_dates'", "'total_incoming_amount', 'total_sales_amount', 'raw_dates', 'topRevNamesJson', 'topRevAmountsJson', 'topOrdNamesJson', 'topOrdCountsJson'", $content);

file_put_contents($file, $content);
echo "Successfully updated AdminController.\n";
