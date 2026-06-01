<?php
$logPath = __DIR__ . '/../storage/logs/laravel.log';
if (file_exists($logPath)) {
    // Get last 50 lines
    $lines = file($logPath);
    $last_lines = array_slice($lines, -50);
    foreach ($last_lines as $line) {
        echo htmlspecialchars($line) . "<br>";
    }
} else {
    echo "Log file not found.";
}
