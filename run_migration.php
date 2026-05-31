<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== SERVER DB MIGRATE ===\n\n";

$descriptorspec = array(
   0 => array("pipe", "r"),
   1 => array("pipe", "w"),
   2 => array("pipe", "w")
);

$process = proc_open('cd drautos && php artisan migrate --path=database/migrations/2026_05_31_230239_create_delivery_receipts_table.php --force 2>&1', $descriptorspec, $pipes);

if (is_resource($process)) {
    fclose($pipes[0]);
    $output = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $error = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $returnValue = proc_close($process);
    
    echo "Return Value: $returnValue\n";
    echo "Output:\n$output\n";
    echo "Stderr:\n$error\n";
} else {
    echo "proc_open failed!\n";
}
