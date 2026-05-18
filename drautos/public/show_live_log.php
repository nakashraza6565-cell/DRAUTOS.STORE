<?php
header('Content-Type: text/plain');
echo "=== SERVER PUBLIC DIRECTORY SCAN ===\n\n";
echo "Current Dir: " . __DIR__ . "\n";
echo "Parent Dir: " . realpath(__DIR__ . '/..') . "\n\n";

echo "--- Files in public/ ---\n";
print_r(scandir(__DIR__));

echo "\n--- Files in parent/ ---\n";
print_r(scandir(__DIR__ . '/..'));
