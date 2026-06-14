<?php
require 'drautos/vendor/autoload.php';
$app = require_once 'drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $incomings = \App\Models\InventoryIncoming::whereIn('reference_number', ['INC-20260522-0003', 'INC-20260522-0004'])
        ->with('items.product')
        ->get();

    foreach ($incomings as $inc) {
        echo "===========================================\n";
        echo "Incoming ID: {$inc->id} | Ref: {$inc->reference_number} | Cost: {$inc->total_cost} | Shipping: {$inc->shipping_cost}\n";
        echo "Items:\n";
        foreach ($inc->items as $item) {
            echo "  - Product ID: {$item->product_id} | Name: " . ($item->product->title ?? 'N/A') . " | Qty: {$item->quantity} | Unit Cost: {$item->unit_cost} | Total: {$item->total_cost}\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}


