<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== DATABASE REPAIR & ORDER SHIFT SYSTEM ===\n\n";

// Bootstrap Laravel
define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // ----------------------------------------------------
    // PART 1: Revert Order ID 1297 back to User ID 540
    // ----------------------------------------------------
    echo "--- Reverting accidental shift of Order 1297 ---\n";
    $order1297 = \App\Models\Order::find(1297);
    if ($order1297) {
        $oldUser = \App\User::find(540);
        if ($oldUser) {
            $order1297->user_id = 540;
            $order1297->first_name = $oldUser->name;
            $order1297->last_name = '';
            $order1297->save();
            \App\Models\Cart::where('order_id', 1297)->update(['user_id' => 540]);
            echo "Successfully reverted Order ID 1297 (#{$order1297->order_number}) back to User ID 540 ({$oldUser->name}).\n";
        } else {
            echo "Warning: Original User ID 540 not found. Order 1297 not reverted.\n";
        }
    } else {
        echo "Order ID 1297 not found.\n";
    }

    // ----------------------------------------------------
    // PART 2: Shift correct Order (number 2806213043)
    // ----------------------------------------------------
    echo "\n--- Shifting correct Order 2806213043 ---\n";
    $orderNumber = '2806213043';
    $toUserId = 80; // Makkah Autos (M)
    
    $order = \App\Models\Order::where('order_number', $orderNumber)->first();
    $toUser = \App\User::find($toUserId);
    
    if (!$order) {
        throw new \Exception("Order number $orderNumber not found in database.");
    }
    if (!$toUser) {
        throw new \Exception("Target User ID $toUserId not found in database.");
    }
    
    $fromUserId = $order->user_id;
    $fromUser = \App\User::find($fromUserId);
    $fromUserName = $fromUser ? $fromUser->name : "Unknown (ID $fromUserId)";
    
    // Update order owner
    $order->user_id = $toUserId;
    $order->first_name = $toUser->name;
    $order->last_name = '';
    $order->save();
    echo "Shifted Order #{$orderNumber} (ID: {$order->id}) from $fromUserName (ID: $fromUserId) to {$toUser->name} (ID: $toUserId).\n";
    
    // Update cart items
    $cartCount = \App\Models\Cart::where('order_id', $order->id)->update(['user_id' => $toUserId]);
    echo "Updated $cartCount cart items to User ID $toUserId.\n";
    
    // Update payment reminders (if column exists)
    if (\Illuminate\Support\Facades\Schema::hasColumn('payment_reminders', 'user_id')) {
        $reminderCount = \App\Models\PaymentReminder::where('reference_number', $orderNumber)->update(['user_id' => $toUserId]);
        echo "Updated $reminderCount payment reminders to User ID $toUserId.\n";
    } else {
        echo "Note: 'user_id' column does not exist in 'payment_reminders'. Checked and skipped.\n";
    }
    
    // Update customer ledger entries
    $ledgerEntries = \App\Models\CustomerLedger::where('reference_id', $order->id)
        ->orWhere('description', 'like', "%{$orderNumber}%")
        ->get();
        
    echo "Found " . $ledgerEntries->count() . " customer ledger entries to update:\n";
    foreach ($ledgerEntries as $entry) {
        $entry->user_id = $toUserId;
        $entry->save();
        echo "  Updated Ledger ID {$entry->id} (Type: {$entry->type}, Amount: {$entry->amount}) to User ID $toUserId.\n";
    }
    
    // ----------------------------------------------------
    // PART 3: Recalculate balances for all affected users
    // ----------------------------------------------------
    echo "\n--- Settling and recalculating ledger balances ---\n";
    $affectedUsers = array_unique([$fromUserId, $toUserId, 540]);
    foreach ($affectedUsers as $uid) {
        $user = \App\User::find($uid);
        if ($user) {
            $newBal = \App\Models\CustomerLedger::updateBalance($uid);
            echo "  User ID $uid ({$user->name}) balance updated to: Rs. " . number_format($newBal, 2) . "\n";
        }
    }
    
    echo "\n=== Database Shift and Settlement Complete! ===\n";
    
} catch (\Exception $e) {
    echo "\n❌ Database Error: " . $e->getMessage() . "\n";
}

// Self-destruct
echo "\nSelf-deleting script...\n";
unlink(__FILE__);
echo "Done!\n";
