<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== SERVER GIT REPAIR & DEPLOYMENT RUNNER ===\n\n";

$descriptorspec = array(
   0 => array("pipe", "r"),
   1 => array("pipe", "w"),
   2 => array("pipe", "w")
);

// 1. Configure git to disable multi-threading and preloading (fixes Hostinger thread limit error)
$configs = [
    'git config core.preloadIndex false',
    'git config index.threads 1',
    'git config pack.threads 1',
    'git config gc.auto 0'
];

foreach ($configs as $cmd) {
    echo "Running: $cmd\n";
    $process = proc_open($cmd . ' 2>&1', $descriptorspec, $pipes);
    if (is_resource($process)) {
        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        proc_close($process);
        echo "Output: " . trim($output) . "\n\n";
    } else {
        echo "FAILED to run config command.\n\n";
    }
}

// 2. Perform the git pull using single-threaded mode
echo "Running: git pull origin main\n";
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
    if ($error) echo "Stderr:\n$error\n";
} else {
    echo "proc_open failed to run git pull!\n";
}

// 3. Reset view cache
echo "\nClearing view cache...\n";
$viewDir = __DIR__ . '/drautos/storage/framework/views';
if (is_dir($viewDir)) {
    $files = glob($viewDir . '/*.php');
    $count = 0;
    foreach ($files as $f) {
        if (unlink($f)) $count++;
    }
    echo "Deleted $count compiled view file(s).\n";
}

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache cleared.\n";
}

// 4. Self-destruct for security
echo "\nSelf-deleting script...\n";
unlink(__FILE__);
echo "Done!\n";
