<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== SERVER GIT DIAGNOSTIC (PROC_OPEN) ===\n\n";

$descriptorspec = array(
   0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
   1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
   2 => array("pipe", "w")   // stderr is a pipe that the child will write to
);

$process = proc_open('git pull origin main 2>&1', $descriptorspec, $pipes);

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

echo "\n--- Git Status ---\n";
$process = proc_open('git status 2>&1', $descriptorspec, $pipes);
if (is_resource($process)) {
    fclose($pipes[0]);
    $output = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $returnValue = proc_close($process);
    echo "Git Status Output:\n$output\n";
}
