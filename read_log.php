<?php
require 'drautos/vendor/autoload.php';
$app = require_once 'drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$latestOrder = \App\Models\SalesOrder::orderBy('id', 'desc')->first();
echo "Latest SO: " . json_encode($latestOrder) . "\n";
$latestPhoto = \App\Models\SalesOrderPhoto::orderBy('id', 'desc')->first();
echo "Latest Photo: " . json_encode($latestPhoto) . "\n";
