<?php
$logFile = __DIR__ . '/../storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $last_lines = array_slice($lines, -150);
    echo "<pre>";
    foreach ($last_lines as $line) {
        if (strpos($line, 'local.ERROR') !== false || strpos($line, 'Dashboard Error') !== false || strpos($line, 'Stack trace') !== false) {
            echo "<b>" . htmlspecialchars($line) . "</b>";
        } else {
            echo htmlspecialchars($line);
        }
    }
    echo "</pre>";
} else {
    echo "Log file not found.";
}
