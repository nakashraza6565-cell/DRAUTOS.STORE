<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$product = App\Models\Product::where('title', 'like', '%Brake Pipe UD-R%')->first();
if ($product) {
    echo "Product ID: " . $product->id . "\n";
    $bom = App\Models\ManufacturingBill::where('product_id', $product->id)->latest()->first();
    if ($bom) {
        echo "BOM Found: " . $bom->bom_number . "\n";
    } else {
        echo "No BOM found for this product.\n";
    }
} else {
    echo "Product not found.\n";
}
