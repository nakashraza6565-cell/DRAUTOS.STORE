<?php
require 'drautos/vendor/autoload.php';
$app = require_once 'drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $orders = \App\Models\Order::select('user_id', 'first_name', 'last_name', \DB::raw('SUM(total_amount) as total_revenue'))
        ->groupBy('user_id', 'first_name', 'last_name')
        ->orderBy('total_revenue', 'DESC')
        ->limit(5)
        ->get();
    echo "Query 1 OK! Count: " . $orders->count() . "\n";
} catch (\Exception $e) {
    echo "Query 1 Error: " . $e->getMessage() . "\n";
}
