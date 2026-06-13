<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$app->make('Illuminate\Contracts\Http\Kernel')->handle($request);

$customers = [];
$categories = [];
$brands = [];
$product_models = [];
$suppliers = [];
$cities = [];
$units = [];
$accounts = collect([ (object)['id' => 1, 'type' => 'cash', 'name' => 'Cash', 'current_balance' => 0] ]);
$walkInId = 1;

try {
    $output = view('backend.pos.index', compact('customers', 'categories', 'brands', 'product_models', 'cities', 'suppliers', 'units', 'accounts', 'walkInId'))->render();
    echo "\n\nSUCCESS\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
}
