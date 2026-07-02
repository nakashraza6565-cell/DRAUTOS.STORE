<?php
header('Content-Type: text/plain');
define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\InventoryIncoming;

$incomings = InventoryIncoming::whereIn('batch_number', [208, 209])->get();
foreach ($incomings as $inc) {
    echo "ID: " . $inc->id . "\n";
    echo "Batch Number: " . $inc->batch_number . "\n";
    echo "Supplier ID: " . $inc->supplier_id . "\n";
    echo "Total Cost: " . $inc->total_cost . "\n";
    echo "Created At: " . $inc->created_at . "\n";
    echo "Items Count: " . $inc->items()->count() . "\n";
    foreach ($inc->items as $item) {
        echo "  - Item Name: " . $item->item_name . ", Qty: " . $item->quantity . ", Cost: " . $item->cost . "\n";
    }
    echo "---------------------------\n";
}
