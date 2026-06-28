<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== SERVER GIT REPAIR & DEPLOYMENT RUNNER ===\n\n";

$descriptorspec = array(
   0 => array("pipe", "r"),
   1 => array("pipe", "w"),
   2 => array("pipe", "w")
);

// 1. Configure git to disable multi-threading and preloading (fixes Hostinger thread limit error)
$configs = [
    'git config core.preloadIndex false',
    'git config index.threads 1',
    'git config pack.threads 1',
    'git config gc.auto 0'
];

foreach ($configs as $cmd) {
    echo "Running: $cmd\n";
    $process = proc_open($cmd . ' 2>&1', $descriptorspec, $pipes);
    if (is_resource($process)) {
        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        proc_close($process);
        echo "Output: " . trim($output) . "\n\n";
    } else {
        echo "FAILED to run config command.\n\n";
    }
}

// 2. Perform the git pull using single-threaded mode
echo "Running: git pull origin main\n";
$process = proc_open('git pull origin main 2>&1', $descriptorspec, $pipes);
if (is_resource($process)) {
    fclose($pipes[0]);
    $output = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $error = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $returnValue = proc_close($process);
    
    echo "Return Value: $returnValue\n";
    echo "Output:\n$output\n";
    if ($error) echo "Stderr:\n$error\n";
} else {
    echo "proc_open failed to run git pull!\n";
}

// 3. Database Order Shift & Ledger Settlement Logic
try {
    echo "\n=== Starting Database Order Shift ===\n";
    
    // Bootstrap Laravel
    define('LARAVEL_START', microtime(true));
    require __DIR__.'/drautos/vendor/autoload.php';
    $app = require_once __DIR__.'/drautos/bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    $orderId = 1297;
    $fromUserId = 64;
    $toUserId = 80;
    
    $order = \App\Models\Order::find($orderId);
    $toUser = \App\User::find($toUserId);
    
    if (!$order) {
        throw new \Exception("Order ID $orderId not found in database.");
    }
    if (!$toUser) {
        throw new \Exception("Target User ID $toUserId not found in database.");
    }
    
    // 1. Shift the Order to the new User ID
    $oldUserId = $order->user_id;
    $order->user_id = $toUserId;
    $order->first_name = $toUser->name;
    $order->last_name = ''; // Makkah Autos (M) is a complete name
    $order->save();
    echo "Successfully shifted Order #{$order->order_number} from User ID $oldUserId to User ID $toUserId.\n";
    
    // 2. Shift all Cart items associated with this order
    $cartCount = \App\Models\Cart::where('order_id', $orderId)->update(['user_id' => $toUserId]);
    echo "Updated $cartCount cart items associated with this order to User ID $toUserId.\n";
    
    // 3. Shift associated Payment Reminders if any
    $reminderCount = \App\Models\PaymentReminder::where('reference_number', $order->order_number)->update(['user_id' => $toUserId]);
    echo "Updated $reminderCount payment reminders associated with this order to User ID $toUserId.\n";
    
    // 4. Shift and update Customer Ledger entries
    $ledgerEntries = \App\Models\CustomerLedger::where('reference_id', $orderId)
        ->orWhere('description', 'like', "%{$order->order_number}%")
        ->get();
        
    echo "Found " . $ledgerEntries->count() . " ledger entries to update.\n";
    foreach ($ledgerEntries as $entry) {
        $entry->user_id = $toUserId;
        $entry->save();
        echo "  Updated Ledger ID {$entry->id} (Type: {$entry->type}, Amount: {$entry->amount}) to User ID $toUserId.\n";
    }
    
    // 5. Recalculate balances for both users to settle the accounts
    echo "Recalculating ledger balances...\n";
    $oldUserBalance = \App\Models\CustomerLedger::updateBalance($fromUserId);
    $newUserBalance = \App\Models\CustomerLedger::updateBalance($toUserId);
    
    echo "Recalculation complete:\n";
    echo "  Makkah Autos (ID $fromUserId) New Balance: Rs. " . number_format($oldUserBalance, 2) . "\n";
    echo "  Makkah Autos (M) (ID $toUserId) New Balance: Rs. " . number_format($newUserBalance, 2) . "\n";
    
    echo "=== Database Shift Complete ===\n\n";
} catch (\Exception $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n\n";
}

// 4. Reset view cache
echo "\nClearing view cache...\n";
$viewDir = __DIR__ . '/drautos/storage/framework/views';
if (is_dir($viewDir)) {
    $files = glob($viewDir . '/*.php');
    $count = 0;
    foreach ($files as $f) {
        if (unlink($f)) $count++;
    }
    echo "Deleted $count compiled view file(s).\n";
}

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache cleared.\n";
}

// 5. Self-destruct for security
echo "\nSelf-deleting script...\n";
unlink(__FILE__);
echo "Done!\n";
