<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== RAW PDO MAKKAH AUTOS ORDERS LIST ===\n\n";

// Bootstrap Laravel
define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $pdo = \DB::connection()->getPdo();
    $sql = "SELECT id, order_number, user_id, first_name, last_name, phone, total_amount, created_at 
            FROM orders 
            WHERE first_name LIKE '%Makkah%' 
               OR last_name LIKE '%Makkah%' 
               OR phone = '03118834066' 
               OR phone = '03009581335'
            ORDER BY id DESC";
            
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    echo "Found " . count($rows) . " orders:\n";
    foreach ($rows as $row) {
        echo "  ID: {$row['id']} | Num: {$row['order_number']} | User ID: {$row['user_id']} | Name: {$row['first_name']} {$row['last_name']} | Phone: {$row['phone']} | Total: {$row['total_amount']} | Date: {$row['created_at']}\n";
    }

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
