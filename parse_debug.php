<?php
$json = file_get_contents('https://drautos.store/debug-logs');
$lines = json_decode($json, true);
if (!$lines) die("Failed to parse json");
foreach ($lines as $line) {
    if (stripos($line, 'error') !== false || stripos($line, 'exception') !== false || stripos($line, 'stack trace') !== false) {
        echo substr($line, 0, 800) . "\n\n";
    }
}
