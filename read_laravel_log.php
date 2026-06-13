<?php
header('Content-Type: text/plain');
$logFile = __DIR__ . '/drautos/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $last_lines = array_slice($lines, -100);
    echo implode("", $last_lines);
} else {
    echo "Log file not found at: " . $logFile;
}
