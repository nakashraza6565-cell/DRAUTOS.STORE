<?php
/**
 * Fix Script: Change SR-20260629-0001 from Cash Refund to Credit to Account
 * 
 * Problem: Staff mistakenly saved return as "cash" refund.
 * - This created a debit ledger entry (cash refund payout) ID 2981 of 38,949
 * - But the credit (return) entry ID 2980 is correct
 * 
 * Fix:
 * 1. Update sale_returns refund_method from 'cash' to 'credit_note'
 * 2. Delete the erroneous debit ledger entry ID 2981
 * 3. Recalculate running balances from that point forward for customer 320
 */

$host = 'localhost';
$dbname = 'u704900370_drautos';
$username = 'u704900370_drautos';
$password = 'NAkash@1995';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
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
                           WHERE id = 2981 AND user_id = ?");
    $stmt->execute([$customerId]);
    $badEntry = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$badEntry) {
        echo "WARNING: Ledger entry ID 2981 not found for customer $customerId. May have already been deleted.\n";
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
        $stmt = $pdo->prepare("DELETE FROM customer_ledgers WHERE id = 2981 AND user_id = ?");
        $stmt->execute([$customerId]);
        $deleted = $stmt->rowCount();
        echo "Step 2: Deleted erroneous cash refund debit entry (ID 2981). Rows deleted: $deleted\n";
    }

    // STEP 5: Recalculate running balances from entry after ID 2981 onwards
    // Get balance from the entry just before 2981 (which would be entry 2980)
    $stmt = $pdo->prepare("SELECT balance FROM customer_ledgers 
                           WHERE user_id = ? AND id < 2981
                           ORDER BY id DESC LIMIT 1");
    $stmt->execute([$customerId]);
    $prevRow = $stmt->fetch(PDO::FETCH_ASSOC);
    $runningBalance = $prevRow ? (float)$prevRow['balance'] : 0;

    echo "Step 3: Recalculating running balances from entry after ID 2980...\n";
    echo "  Starting balance (from ID 2980): $runningBalance\n";

    // Get all entries after 2980 (i.e., ID > 2980, since 2981 is now deleted) in order
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

    // Also verify sale_returns update
    $stmt = $pdo->prepare("SELECT return_number, refund_method, status FROM sale_returns WHERE id = ?");
    $stmt->execute([$returnId]);
    $updated = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\n=== SALE RETURN UPDATED ===\n";
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
