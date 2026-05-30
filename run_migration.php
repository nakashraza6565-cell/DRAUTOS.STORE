<?php
/**
 * Direct migration script for sale_order_photos table
 * Run: php run_migration.php
 */

// Load .env
$envFile = __DIR__ . '/drautos/.env';
if (!file_exists($envFile)) die("Cannot find .env\n");

$env = [];
foreach (file($envFile) as $line) {
    $line = trim($line);
    if (!$line || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
    [$key, $val] = explode('=', $line, 2);
    $env[trim($key)] = trim($val, '"\'');
}

$host = $env['DB_HOST']     ?? '127.0.0.1';
$port = $env['DB_PORT']     ?? '3306';
$db   = $env['DB_DATABASE'] ?? '';
$user = $env['DB_USERNAME'] ?? '';
$pass = $env['DB_PASSWORD'] ?? '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connected to database: $db\n";
} catch (Exception $e) {
    die("❌ Connection failed: " . $e->getMessage() . "\n");
}

// 1. Create sale_order_photos table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS `sale_order_photos` (
        `id`             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `sales_order_id` BIGINT UNSIGNED NOT NULL,
        `filename`       VARCHAR(255) NOT NULL,
        `original_name`  VARCHAR(255) NOT NULL,
        `disk_path`      VARCHAR(500) NOT NULL,
        `uploaded_by`    BIGINT UNSIGNED NULL,
        `file_size`      BIGINT UNSIGNED DEFAULT 0,
        `mime_type`      VARCHAR(100) NULL,
        `created_at`     TIMESTAMP NULL,
        `updated_at`     TIMESTAMP NULL,
        FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`uploaded_by`)    REFERENCES `users`(`id`)         ON DELETE SET NULL,
        INDEX `idx_sop_order` (`sales_order_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✅ Table 'sale_order_photos' created (or already exists)\n";

// 2. Add photo_pending to sales_orders status ENUM
try {
    // Get the current column definition
    $stmt = $pdo->query("SHOW COLUMNS FROM `sales_orders` LIKE 'status'");
    $col = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($col && strpos($col['Type'], 'photo_pending') === false) {
        $pdo->exec("ALTER TABLE `sales_orders` MODIFY COLUMN `status` ENUM(
            'pending',
            'photo_pending',
            'processing',
            'partially_delivered',
            'delivered',
            'merged',
            'cancelled'
        ) DEFAULT 'pending'");
        echo "✅ Added 'photo_pending' to sales_orders.status ENUM\n";
    } else {
        echo "ℹ️  'photo_pending' already in status ENUM (or column not found)\n";
    }
} catch (Exception $e) {
    echo "⚠️  Could not modify status ENUM: " . $e->getMessage() . "\n";
}

// 3. Record migration in migrations table so artisan doesn't try to run it again
try {
    $batch = $pdo->query("SELECT MAX(batch) FROM migrations")->fetchColumn();
    $batch = ($batch ?: 0) + 1;
    $stmt = $pdo->prepare("INSERT IGNORE INTO `migrations` (migration, batch) VALUES (?, ?)");
    $stmt->execute(['2026_05_30_120000_create_sale_order_photos_table', $batch]);
    echo "✅ Migration recorded in migrations table (batch $batch)\n";
} catch (Exception $e) {
    echo "⚠️  Could not record migration: " . $e->getMessage() . "\n";
}

echo "\n🎉 Done! Database is ready for sale order photos.\n";
