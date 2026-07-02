<?php
/**
 * Fix Script: Change SR-20260629-0001 from Cash Refund to Credit to Account
 * Uses Laravel's own environment configuration from the server
 */

// Read DB credentials from the live .env
$envFile = __DIR__ . '/drautos/.env';
if (!file_exists($envFile)) {
    $envFile = __DIR__ . '/.env';
}

$env = [];
foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
        list($key, $val) = explode('=', $line, 2);
        $env[trim($key)] = trim($val, '"\'');
    }
}

$host = $env['DB_HOST'] ?? 'localhost';
$port = $env['DB_PORT'] ?? '3306';
$dbname = $env['DB_DATABASE'] ?? '';
$username = $env['DB_USERNAME'] ?? '';
$password = $env['DB_PASSWORD'] ?? '';

echo "Connecting to DB: $dbname @ $host as $username\n\n";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
    echo "  Amount: {$ret['total_return_amount']}\n";
    echo "  Current Refund Method: {$ret['refund_method']}\n";
    echo "  Status: {$ret['status']}\n\n";

    $customerId = $ret['customer_id'];
    $returnId = $ret['id'];

    // --- STEP 2: Verify the erroneous ledger entry ID 2981 ---
    $stmt = $pdo->prepare("SELECT id, type, category, description, amount, balance, transaction_date 
                           FROM customer_ledgers 
                           WHERE id = 2981");
    $stmt->execute();
    $badEntry = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$badEntry) {
        echo "WARNING: Ledger entry ID 2981 not found. May have already been deleted.\n\n";
    } else {
        echo "Erroneous Ledger Entry to be deleted:\n";
        echo "  ID: {$badEntry['id']}\n";
        echo "  Type: {$badEntry['type']}\n";
        echo "  Category: {$badEntry['category']}\n";
        echo "  Description: {$badEntry['description']}\n";
        echo "  Amount: {$badEntry['amount']}\n";
        echo "  Balance After: {$badEntry['balance']}\n";
        echo "  Date: {$badEntry['transaction_date']}\n\n";
    }

    // --- Show current ledger state before fix ---
    echo "=== CURRENT LEDGER (Last 8 entries for Customer $customerId) ===\n";
    $stmt = $pdo->prepare("SELECT id, transaction_date, type, category, description, amount, balance 
                           FROM customer_ledgers 
                           WHERE user_id = ? 
                           ORDER BY id DESC 
                           LIMIT 8");
    $stmt->execute([$customerId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo "  [{$r['id']}] {$r['transaction_date']} | {$r['type']} | {$r['category']} | {$r['amount']} | Bal: {$r['balance']} | {$r['description']}\n";
    }
    echo "\n";

    // --- BEGIN TRANSACTION ---
    $pdo->beginTransaction();

    // STEP 3: Update sale_returns refund_method to credit_note
    $stmt = $pdo->prepare("UPDATE sale_returns SET refund_method = 'credit_note' WHERE id = ?");
    $stmt->execute([$returnId]);
    $affected = $stmt->rowCount();
    echo "Step 1: Updated sale_returns refund_method to 'credit_note'. Rows affected: $affected\n";

    // STEP 4: Delete the erroneous debit ledger entry (cash refund payout)
    if ($badEntry) {
        $stmt = $pdo->prepare("DELETE FROM customer_ledgers WHERE id = 2981");
        $stmt->execute();
        $deleted = $stmt->rowCount();
        echo "Step 2: Deleted erroneous cash refund debit entry (ID 2981). Rows deleted: $deleted\n";
    } else {
        echo "Step 2: Skipped - entry ID 2981 not found (already deleted).\n";
    }

    // STEP 5: Recalculate running balances
    // Get balance from the entry just before 2981 (entry 2980)
    $stmt = $pdo->prepare("SELECT id, balance FROM customer_ledgers 
                           WHERE user_id = ? AND id <= 2980
                           ORDER BY id DESC LIMIT 1");
    $stmt->execute([$customerId]);
    $prevRow = $stmt->fetch(PDO::FETCH_ASSOC);
    $runningBalance = $prevRow ? (float)$prevRow['balance'] : 0;

    echo "Step 3: Recalculating running balances...\n";
    echo "  Balance base (from entry ID {$prevRow['id']}): $runningBalance\n";

    // Get all entries after 2980 in chronological order
    $stmt = $pdo->prepare("SELECT id, type, amount FROM customer_ledgers 
                           WHERE user_id = ? AND id > 2980
                           ORDER BY id ASC");
    $stmt->execute([$customerId]);
    $laterEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($laterEntries)) {
        echo "  No entries after 2980 to recalculate.\n";
    }

    foreach ($laterEntries as $entry) {
        if ($entry['type'] === 'credit') {
            $runningBalance += (float)$entry['amount'];
        } else {
            $runningBalance -= (float)$entry['amount'];
        }
        $stmt2 = $pdo->prepare("UPDATE customer_ledgers SET balance = ? WHERE id = ?");
        $stmt2->execute([$runningBalance, $entry['id']]);
        echo "  Updated entry ID {$entry['id']}: {$entry['type']} {$entry['amount']} → New balance: $runningBalance\n";
    }

    // COMMIT
    $pdo->commit();
    echo "\n✅ FIX APPLIED SUCCESSFULLY!\n\n";

    // --- Show updated ledger state after fix ---
    echo "=== UPDATED LEDGER (Last 8 entries for Customer $customerId) ===\n";
    $stmt = $pdo->prepare("SELECT id, transaction_date, type, category, description, amount, balance 
                           FROM customer_ledgers 
                           WHERE user_id = ? 
                           ORDER BY id DESC 
                           LIMIT 8");
    $stmt->execute([$customerId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo "  [{$r['id']}] {$r['transaction_date']} | {$r['type']} | {$r['category']} | {$r['amount']} | Bal: {$r['balance']} | {$r['description']}\n";
    }

    // Verify sale_returns update
    $stmt = $pdo->prepare("SELECT return_number, refund_method, status FROM sale_returns WHERE id = ?");
    $stmt->execute([$returnId]);
    $updated = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\n=== SALE RETURN VERIFIED ===\n";
    echo "  Return Number: {$updated['return_number']}\n";
    echo "  Refund Method: {$updated['refund_method']}\n";
    echo "  Status: {$updated['status']}\n";

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
        echo "ROLLED BACK due to error.\n";
    }
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
