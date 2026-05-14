<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

use App\Models\Product;
$total = Product::count();
$active = Product::where('status', 'active')->count();
$inactive = Product::where('status', 'inactive')->count();

echo "Total Products: " . $total . "\n";
echo "Active Products: " . $active . "\n";
echo "Inactive Products: " . $inactive . "\n";
