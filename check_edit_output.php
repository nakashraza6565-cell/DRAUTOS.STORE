<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Boot Laravel
require __DIR__ . '/drautos/vendor/autoload.php';
$app = require_once __DIR__ . '/drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;
use App\Models\PaymentReminder;

// Get the latest order with items
$order = Order::has('cart_info')->orderBy('id', 'desc')->first();

if (!$order) {
    echo "No order found with items!\n";
    exit;
}

$reminder = PaymentReminder::where('reference_number', $order->order_number)->first();
$paid_at_pos = $order->total_amount;
if ($reminder) {
    $paid_at_pos = $order->total_amount - $reminder->amount;
}

echo "Order Number: " . $order->order_number . " (ID: " . $order->id . ")\n";
echo "Cart Info Items Count: " . $order->cart_info->count() . "\n\n";

// Render the view backend.order.edit
try {
    $html = view('backend.order.edit', compact('order', 'reminder', 'paid_at_pos'))->render();
    
    // Find the script section containing cart initialization
    if (preg_match('/let rawCart\s*=\s*(.*?);/s', $html, $matches)) {
        echo "Found rawCart in HTML:\n";
        echo $matches[0] . "\n\n";
    } else {
        echo "Could not find rawCart in rendered HTML!\n";
    }
    
    if (preg_match('/<script>(.*?)<\/script>/s', $html, $matches)) {
        // Just print a segment of the script
        echo "Found script tags in HTML.\n";
    }
} catch (\Exception $e) {
    echo "Error rendering view: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
