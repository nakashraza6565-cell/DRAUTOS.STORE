<?php
header('Content-Type: text/plain; charset=utf-8');
set_time_limit(300);
ini_set('memory_limit', '512M');

echo "=== DATABASE SHIFT & SETTLEMENT SYSTEM ===\n\n";

// Bootstrap Laravel
define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $orderId = 1385;
    $fromUserId = 463; // Makkah Autos
    $toUserId = 156;   // Makkah Autos (M)
    
    $order = \App\Models\Order::find($orderId);
    $fromUser = \App\User::find($fromUserId);
    $toUser = \App\User::find($toUserId);
    
    if (!$order) {
        throw new \Exception("Order ID $orderId not found in database.");
    }
    if (!$toUser) {
        throw new \Exception("Target User ID $toUserId not found in database.");
    }
    
    echo "Before Shift Stats:\n";
    echo "  Order Number: #{$order->order_number}\n";
    echo "  Current Order User: {$order->first_name} {$order->last_name} (ID: {$order->user_id})\n";
    echo "  From User Balance (ID $fromUserId - {$fromUser->name}): Rs. " . number_format($fromUser->current_balance ?? 0, 2) . "\n";
    echo "  To User Balance (ID $toUserId - {$toUser->name}): Rs. " . number_format($toUser->current_balance ?? 0, 2) . "\n\n";
    
    // 1. Shift the Order to the new User ID
    $order->user_id = $toUserId;
    $order->first_name = $toUser->name;
    $order->last_name = '';
    $order->save();
    echo "1. Shifted Order record to User ID $toUserId ({$toUser->name}).\n";
    
    // 2. Shift all Cart items associated with this order
    $cartCount = \App\Models\Cart::where('order_id', $orderId)->update(['user_id' => $toUserId]);
    echo "2. Updated $cartCount cart items associated with this order to User ID $toUserId.\n";
    
    // 3. Shift associated Payment Reminders if user_id column exists
    if (\Illuminate\Support\Facades\Schema::hasColumn('payment_reminders', 'user_id')) {
        $reminderCount = \App\Models\PaymentReminder::where('reference_number', $order->order_number)->update(['user_id' => $toUserId]);
        echo "3. Updated $reminderCount payment reminders to User ID $toUserId.\n";
    } else {
        echo "3. Checked payment_reminders table: 'user_id' column does not exist, skipped.\n";
    }
    
    // 4. Shift and update Customer Ledger entries
    $ledgerEntries = \App\Models\CustomerLedger::where('reference_id', $orderId)
        ->orWhere('description', 'like', "%{$order->order_number}%")
        ->get();
        
    echo "4. Found " . $ledgerEntries->count() . " ledger entries to update:\n";
    foreach ($ledgerEntries as $entry) {
        $entry->user_id = $toUserId;
        $entry->save();
        echo "  - Updated Ledger ID {$entry->id} (Type: {$entry->type}, Amount: {$entry->amount}) to User ID $toUserId.\n";
    }
    
    // 5. Recalculate balances for both users to settle the accounts
    echo "\n5. Settle and recalculate balances (this may take a moment)...\n";
    $oldUserBalance = \App\Models\CustomerLedger::updateBalance($fromUserId);
    $newUserBalance = \App\Models\CustomerLedger::updateBalance($toUserId);
    
    // Also recalculate any other touched user IDs to keep the DB clean (e.g. 540 and 80 from the accidental test earlier)
    \App\Models\CustomerLedger::updateBalance(540);
    \App\Models\CustomerLedger::updateBalance(80);
    
    // Refresh user instances
    $fromUser = \App\User::find($fromUserId);
    $toUser = \App\User::find($toUserId);
    
    echo "\nAfter Shift Stats:\n";
    echo "  From User Balance (ID $fromUserId - {$fromUser->name}): Rs. " . number_format($fromUser->current_balance ?? 0, 2) . "\n";
    echo "  To User Balance (ID $toUserId - {$toUser->name}): Rs. " . number_format($toUser->current_balance ?? 0, 2) . "\n\n";
    
    echo "=== Database Shift and Settlement Complete! ===\n";
} catch (\Exception $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
}

// Self-destruct
echo "\nSelf-deleting script...\n";
unlink(__FILE__);
echo "Done!\n";
