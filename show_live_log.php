<?php
// Temporary script to inspect live server logs from repository root
$logPath = __DIR__ . '/drautos/storage/logs/laravel.log';
if (file_exists($logPath)) {
    $lines = file($logPath);
    $lastLines = array_slice($lines, -150);
    echo "<h2>Live Server Error Log</h2>";
    echo "<pre>" . htmlspecialchars(implode("", $lastLines)) . "</pre>";
} else {
    echo "Log file not found at: " . $logPath;
}
