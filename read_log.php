<?php
require 'drautos/vendor/autoload.php';
$app = require_once 'drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$photos = \App\Models\SalesOrderPhoto::orderBy('id', 'desc')->take(5)->get();
echo "Latest photos: " . json_encode($photos) . "\n";
$latestOrders = \App\Models\SalesOrder::orderBy('id', 'desc')->take(5)->get();
echo "Latest SOs: " . json_encode($latestOrders) . "\n";
