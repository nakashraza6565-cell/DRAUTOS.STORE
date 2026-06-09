<?php
$logFile = __DIR__ . '/../storage/logs/laravel.log';
if (!file_exists($logFile)) {
    die("No log file found at: " . $logFile);
}
$lines = file($logFile);
$lastLines = array_slice($lines, -300);
echo "<pre>";
foreach ($lastLines as $line) {
    echo htmlspecialchars($line);
}
echo "</pre>";
