<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== GLOBAL DATABASE SEARCH ===\n\n";

// Bootstrap Laravel
define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$searchStr = '2806213043';
echo "Searching for '$searchStr' across all tables...\n\n";

try {
    $pdo = \DB::connection()->getPdo();
    $tablesResult = $pdo->query("SHOW TABLES");
    $tables = $tablesResult->fetchAll(\PDO::FETCH_COLUMN);

    $found = false;
    foreach ($tables as $table) {
        // Get all columns of the table
        $columnsResult = $pdo->query("DESCRIBE `$table`");
        $columns = $columnsResult->fetchAll(\PDO::FETCH_COLUMN);
        
        // Build a search query for this table
        $conditions = [];
        foreach ($columns as $column) {
            $conditions[] = "`$column` LIKE " . $pdo->quote("%$searchStr%");
        }
        
        if (empty($conditions)) continue;
        
        $sql = "SELECT * FROM `$table` WHERE " . implode(" OR ", $conditions) . " LIMIT 10";
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        if (!empty($results)) {
            $found = true;
            echo "Table: $table\n";
            echo "  Found " . count($results) . " matching row(s):\n";
            foreach ($results as $row) {
                echo "    " . json_encode($row) . "\n";
            }
            echo "\n";
        }
    }
    
    if (!$found) {
        echo "No matches found for '$searchStr' in the entire database.\n";
    }

} catch (\Exception $e) {
    echo "❌ Search Error: " . $e->getMessage() . "\n";
}
