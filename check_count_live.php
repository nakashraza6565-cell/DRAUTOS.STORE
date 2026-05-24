<?php
// Temporary script to retrieve live database counts securely
$env_path = __DIR__ . '/drautos/.env';
if (!file_exists($env_path)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'env file not found']);
    exit;
}

// Custom parser to avoid parse_ini_file issues with Laravel env keys containing special chars
$env = [];
$lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    $parts = explode('=', $line, 2);
    if (count($parts) === 2) {
        $key = trim($parts[0]);
        $val = trim($parts[1]);
        // strip quotes if any
        if (preg_match('/^"([^"]*)"$/', $val, $m) || preg_match('/^\'([^\']*)\'$/', $val, $m)) {
            $val = $m[1];
        }
        $env[$key] = $val;
    }
}

$host = isset($env['DB_HOST']) ? $env['DB_HOST'] : 'localhost';
$db   = isset($env['DB_DATABASE']) ? $env['DB_DATABASE'] : 'u909342762_dr';
$user = isset($env['DB_USERNAME']) ? $env['DB_USERNAME'] : 'u909342762_dr';
$pass = isset($env['DB_PASSWORD']) ? $env['DB_PASSWORD'] : '';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     
     // Get live counts
     $total = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
     $active = $pdo->query("SELECT COUNT(*) FROM products WHERE status = 'active'")->fetchColumn();
     $inactive = $pdo->query("SELECT COUNT(*) FROM products WHERE status = 'inactive'")->fetchColumn();
     
     header('Content-Type: application/json');
     echo json_encode([
         'success' => true,
         'total' => (int)$total,
         'active' => (int)$active,
         'inactive' => (int)$inactive
     ]);
} catch (\PDOException $e) {
     header('Content-Type: application/json');
     echo json_encode([
         'success' => false,
         'error' => $e->getMessage()
     ]);
}
