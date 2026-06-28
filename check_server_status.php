<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== SERVER GIT STATUS DIAGNOSTIC ===\n\n";

$output = [];
$return_var = 0;
exec("git status", $output, $return_var);

echo "Return Value: $return_var\n";
echo "Output:\n";
echo implode("\n", $output) . "\n";
