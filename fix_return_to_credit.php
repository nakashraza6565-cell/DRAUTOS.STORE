<?php
/**
 * Fix Script: Change SR-20260629-0001 from Cash Refund to Credit to Account
 * 
 * Problem: Staff mistakenly saved return as "cash" refund.
 * - This created a debit ledger entry (cash refund payout) ID 2981 of 38,949
 * - The credit (return) entry ID 2980 is correct and stays
 * 
 * Fix:
 * 1. Update sale_returns refund_method from 'cash' to 'credit_note'
 * 2. Delete the erroneous debit ledger entry ID 2981 (Cash Refund Payout)
 * 3. Recalculate running balances from that point for customer 320
 */

// Read credentials from server .env
$envFile = '/home/u909342762/domains/drautos.store/public_html/drautos/.env';
if (!file_exists($envFile)) {
    $envFile = __DIR__ . '/drautos/.env';
}

$env = [];
foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
        list($key, $val) = explode('=', $line, 2);
        $env[trim($key)] = trim($val, '"\'');
    }
}

$host     = $env['DB_HOST']     ?? 'localhost';
$port     = $env['DB_PORT']     ?? '3306';
$dbname   = $env['DB_DATABASE'] ?? '';
$username = $env['DB_USERNAME'] ?? '';
$password = $env['DB_PASSWORD'] ?? '';

echo "Connecting to: $dbname @ $host as $username\n\n";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connected to database.\n\n";
    echo "=== RETURN STATUS FIX: Cash Refund → Credit to Account ===\n\n";

    // --- STEP 1: Verify the return record ---
    $stmt = $pdo->prepare("SELECT id, return_number, customer_id, total_return_amount, refund_method, status 
                           FROM sale_returns 
                           WHERE return_number = 'SR-20260629-0001'");
    $stmt->execute();
    $ret = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ret) {
        die("ERROR: Return SR-20260629-0001 not found!\n");
    }

    echo "Found Return:\n";
    echo "  ID: {$ret['id']}\n";
    echo "  Number: {$ret['return_number']}\n";
    echo "  Customer ID: {$ret['customer_id']}\n";
    echo "  Amount: " . number_format($ret['total_return_amount']) . "\n";
    echo "  Current Refund Method: {$ret['refund_method']}\n";
    echo "  Status: {$ret['status']}\n\n";

    $customerId = $ret['customer_id'];
    $returnId   = $ret['id'];

    // --- STEP 2: Check the erroneous ledger entry ---
    $stmt = $pdo->prepare("SELECT id, type, category, description, amount, balance, transaction_date 
                           FROM customer_ledgers 
                           WHERE id = 2981");
    $stmt->execute();
    $badEntry = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$badEntry) {
        echo "INFO: Ledger entry ID 2981 not found - may already be deleted.\n\n";
    } else {
        echo "Erroneous Ledger Entry (to be deleted):\n";
        echo "  ID: {$badEntry['id']}\n";
        echo "  Type: {$badEntry['type']}\n";
        echo "  Category: {$badEntry['category']}\n";
        echo "  Description: {$badEntry['description']}\n";
        echo "  Amount: " . number_format($badEntry['amount']) . "\n";
        echo "  Balance After: " . number_format($badEntry['balance']) . "\n";
        echo "  Date: {$badEntry['transaction_date']}\n\n";
    }

    // --- Show current ledger before fix ---
    echo "=== LEDGER BEFORE FIX (Last 8 entries for Customer {$customerId}) ===\n";
    $stmt = $pdo->prepare("SELECT id, transaction_date, type, category, amount, balance, description 
                           FROM customer_ledgers 
                           WHERE user_id = ? 
                           ORDER BY id DESC LIMIT 8");
    $stmt->execute([$customerId]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $sign = $r['type'] === 'credit' ? '+' : '-';
        echo "  [{$r['id']}] {$r['transaction_date']} | {$r['type']} | {$sign}" . number_format($r['amount']) . " | Bal: " . number_format($r['balance']) . " | {$r['description']}\n";
    }
    echo "\n";

    // --- BEGIN TRANSACTION ---
    $pdo->beginTransaction();

    // STEP 3: Update refund_method to credit_note
    $stmt = $pdo->prepare("UPDATE sale_returns SET refund_method = 'credit_note' WHERE id = ?");
    $stmt->execute([$returnId]);
    echo "Step 1 ✅ Updated sale_returns.refund_method → 'credit_note' (rows: {$stmt->rowCount()})\n";

    // STEP 4: Delete erroneous debit entry
    if ($badEntry) {
        $stmt = $pdo->prepare("DELETE FROM customer_ledgers WHERE id = 2981");
        $stmt->execute();
        echo "Step 2 ✅ Deleted erroneous cash refund debit entry ID 2981 (rows: {$stmt->rowCount()})\n";
    } else {
        echo "Step 2 ⏭  Skipped - entry ID 2981 already absent.\n";
    }

    // STEP 5: Recalculate running balances after entry 2980
    $stmt = $pdo->prepare("SELECT id, balance FROM customer_ledgers 
                           WHERE user_id = ? AND id <= 2980
                           ORDER BY id DESC LIMIT 1");
    $stmt->execute([$customerId]);
    $prevRow = $stmt->fetch(PDO::FETCH_ASSOC);
    $runningBalance = $prevRow ? (float)$prevRow['balance'] : 0;

    echo "Step 3 🔄 Recalculating balances from base: " . number_format($runningBalance) . " (entry ID {$prevRow['id']})\n";

    $stmt = $pdo->prepare("SELECT id, type, amount FROM customer_ledgers 
                           WHERE user_id = ? AND id > 2980
                           ORDER BY id ASC");
    $stmt->execute([$customerId]);
    $laterEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($laterEntries)) {
        echo "  No entries after 2980 to recalculate.\n";
    }

    $updateStmt = $pdo->prepare("UPDATE customer_ledgers SET balance = ? WHERE id = ?");
    foreach ($laterEntries as $entry) {
        $runningBalance += ($entry['type'] === 'credit') ? (float)$entry['amount'] : -(float)$entry['amount'];
        $updateStmt->execute([$runningBalance, $entry['id']]);
        $sign = $entry['type'] === 'credit' ? '+' : '-';
        echo "     Entry ID {$entry['id']}: {$entry['type']} {$sign}" . number_format($entry['amount']) . " → Balance: " . number_format($runningBalance) . "\n";
    }

    $pdo->commit();
    echo "\n✅✅ FIX COMMITTED SUCCESSFULLY!\n\n";

    // --- Show updated ledger after fix ---
    echo "=== LEDGER AFTER FIX (Last 8 entries for Customer {$customerId}) ===\n";
    $stmt = $pdo->prepare("SELECT id, transaction_date, type, category, amount, balance, description 
                           FROM customer_ledgers 
                           WHERE user_id = ? 
                           ORDER BY id DESC LIMIT 8");
    $stmt->execute([$customerId]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $sign = $r['type'] === 'credit' ? '+' : '-';
        echo "  [{$r['id']}] {$r['transaction_date']} | {$r['type']} | {$sign}" . number_format($r['amount']) . " | Bal: " . number_format($r['balance']) . " | {$r['description']}\n";
    }

    // Verify sale_returns update
    $stmt = $pdo->prepare("SELECT return_number, refund_method, status FROM sale_returns WHERE id = ?");
    $stmt->execute([$returnId]);
    $updated = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\n=== SALE RETURN FINAL STATE ===\n";
    echo "  Return Number : {$updated['return_number']}\n";
    echo "  Refund Method : {$updated['refund_method']}\n";
    echo "  Status        : {$updated['status']}\n";

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
        echo "❌ ROLLED BACK due to error.\n";
    }
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
