<?php
$logFile = __DIR__ . '/drautos/storage/logs/laravel.log';
if (!file_exists($logFile)) {
    echo "Log file not found.";
    exit;
}
$lines = file($logFile);
$lastLines = array_slice($lines, -150);
echo "<pre>";
echo htmlspecialchars(implode("", $lastLines));
echo "</pre>";
