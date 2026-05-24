<?php
$env = parse_ini_file(__DIR__ . '/.env');
$host = isset($env['DB_HOST']) ? $env['DB_HOST'] : 'localhost';
$db   = isset($env['DB_DATABASE']) ? $env['DB_DATABASE'] : 'u909342762_dr';
$user = isset($env['DB_USERNAME']) ? $env['DB_USERNAME'] : 'u909342762_dr';
$pass = isset($env['DB_PASSWORD']) ? $env['DB_PASSWORD'] : '@C0oJ;G!u~a8';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     
     // Count products
     $total = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
     $active = $pdo->query("SELECT COUNT(*) FROM products WHERE status = 'active'")->fetchColumn();
     $inactive = $pdo->query("SELECT COUNT(*) FROM products WHERE status = 'inactive'")->fetchColumn();
     
     echo "SUCCESS\n";
     echo "Total Products: " . $total . "\n";
     echo "Active Products: " . $active . "\n";
     echo "Inactive Products: " . $inactive . "\n";
} catch (\PDOException $e) {
     echo "ERROR: " . $e->getMessage() . "\n";
}
