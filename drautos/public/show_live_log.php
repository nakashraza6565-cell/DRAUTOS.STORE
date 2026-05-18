<?php
// Temporary script to inspect live server logs
$logPath = __DIR__ . '/../storage/logs/laravel.log';
if (file_exists($logPath)) {
    $lines = file($logPath);
    $lastLines = array_slice($lines, -150);
    echo "<pre>" . htmlspecialchars(implode("", $lastLines)) . "</pre>";
} else {
    echo "Log file not found at: " . $logPath;
}
