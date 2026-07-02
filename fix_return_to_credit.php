<?php
/**
 * Fix Script: Change SR-20260629-0001 from Cash Refund to Credit to Account
 * VERSION: 4 - Hardcoded correct live server DB credentials
 */

// Live server confirmed credentials (from check_env.php output)
$host     = 'localhost';
$port     = '3306';
$dbname   = 'u909342762_dr';
$username = 'u909342762_dr';
$password = '@C0oJ;G!u~a8';

echo "VERSION 4 - Correct Live DB Credentials\n";
echo "Connecting to: $dbname @ $host\n\n";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to database.\n\n";
    echo "=== RETURN STATUS FIX: Cash Refund to Credit to Account ===\n\n";

    // STEP 1: Verify the return record
    $stmt = $pdo->query("SELECT id, return_number, customer_id, total_return_amount, refund_method, status 
                          FROM sale_returns 
                          WHERE return_number = 'SR-20260629-0001'");
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

    $customerId = (int)$ret['customer_id'];
    $returnId   = (int)$ret['id'];

    // STEP 2: Check the erroneous ledger entry ID 2981
    $stmt = $pdo->query("SELECT id, type, category, description, amount, balance, transaction_date 
                          FROM customer_ledgers WHERE id = 2981");
    $badEntry = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$badEntry) {
        echo "INFO: Ledger entry ID 2981 not found - already deleted.\n\n";
    } else {
        echo "Erroneous Entry (debit) to delete:\n";
        echo "  ID: {$badEntry['id']} | Type: {$badEntry['type']} | Category: {$badEntry['category']}\n";
        echo "  Desc: {$badEntry['description']}\n";
        echo "  Amount: " . number_format($badEntry['amount']) . " | Balance: " . number_format($badEntry['balance']) . "\n\n";
    }

    // STEP 3: Show ledger before fix
    echo "=== LEDGER BEFORE FIX (Last 8 entries, Customer {$customerId}) ===\n";
    $stmt = $pdo->prepare("SELECT id, transaction_date, type, category, amount, balance, description 
                            FROM customer_ledgers WHERE user_id = ? ORDER BY id DESC LIMIT 8");
    $stmt->execute([$customerId]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $sign = $r['type'] === 'credit' ? '+' : '-';
        echo "  [{$r['id']}] {$r['transaction_date']} | {$r['type']} | {$sign}" . number_format($r['amount']) . " | Bal: " . number_format($r['balance']) . "\n       {$r['description']}\n";
    }
    echo "\n";

    // BEGIN TRANSACTION
    $pdo->beginTransaction();

    // STEP 4: Update refund_method to credit_note
    $stmt = $pdo->prepare("UPDATE sale_returns SET refund_method = 'credit_note' WHERE id = ?");
    $stmt->execute([$returnId]);
    echo "Step 1: Updated sale_returns refund_method to 'credit_note' (rows: {$stmt->rowCount()})\n";

    // STEP 5: Delete erroneous debit entry 2981
    if ($badEntry) {
        $stmt = $pdo->prepare("DELETE FROM customer_ledgers WHERE id = 2981");
        $stmt->execute();
        echo "Step 2: Deleted erroneous cash refund debit entry ID 2981 (rows: {$stmt->rowCount()})\n";
    } else {
        echo "Step 2: Skipped - entry ID 2981 already absent.\n";
    }

    // STEP 6: Recalculate balances from entry 2980 onwards
    $stmt = $pdo->prepare("SELECT id, balance FROM customer_ledgers WHERE user_id = ? AND id <= 2980 ORDER BY id DESC LIMIT 1");
    $stmt->execute([$customerId]);
    $prevRow = $stmt->fetch(PDO::FETCH_ASSOC);
    $runningBalance = $prevRow ? (float)$prevRow['balance'] : 0;

    echo "Step 3: Recalculating from base balance: " . number_format($runningBalance) . " (after entry ID {$prevRow['id']})\n";

    $stmt = $pdo->prepare("SELECT id, type, amount FROM customer_ledgers WHERE user_id = ? AND id > 2980 ORDER BY id ASC");
    $stmt->execute([$customerId]);
    $laterEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $updateStmt = $pdo->prepare("UPDATE customer_ledgers SET balance = ? WHERE id = ?");
    foreach ($laterEntries as $entry) {
        $runningBalance += ($entry['type'] === 'credit') ? (float)$entry['amount'] : -(float)$entry['amount'];
        $updateStmt->execute([$runningBalance, $entry['id']]);
        echo "     Entry {$entry['id']}: {$entry['type']} " . number_format($entry['amount']) . " => Balance: " . number_format($runningBalance) . "\n";
    }

    $pdo->commit();
    echo "\nFIX COMMITTED SUCCESSFULLY!\n\n";

    // STEP 7: Show updated ledger after fix
    echo "=== LEDGER AFTER FIX (Last 8 entries, Customer {$customerId}) ===\n";
    $stmt = $pdo->prepare("SELECT id, transaction_date, type, category, amount, balance, description 
                            FROM customer_ledgers WHERE user_id = ? ORDER BY id DESC LIMIT 8");
    $stmt->execute([$customerId]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $sign = $r['type'] === 'credit' ? '+' : '-';
        echo "  [{$r['id']}] {$r['transaction_date']} | {$r['type']} | {$sign}" . number_format($r['amount']) . " | Bal: " . number_format($r['balance']) . "\n       {$r['description']}\n";
    }

    // Final verification of sale_returns
    $stmt = $pdo->prepare("SELECT return_number, refund_method, status FROM sale_returns WHERE id = ?");
    $stmt->execute([$returnId]);
    $updated = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\n=== SALE RETURN FINAL STATE ===\n";
    echo "  Return  : {$updated['return_number']}\n";
    echo "  Method  : {$updated['refund_method']} (should be: credit_note)\n";
    echo "  Status  : {$updated['status']}\n";

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
        echo "ROLLED BACK due to error.\n";
    }
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
