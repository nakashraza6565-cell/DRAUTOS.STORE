<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== Live File Checker - A5 Page Sizing Status ===\n\n";

$purchasePdfPath = __DIR__ . '/drautos/resources/views/backend/purchase/pdf.blade.php';
if (file_exists($purchasePdfPath)) {
    $content = file_get_contents($purchasePdfPath);
    if (strpos($content, 'size: a5') !== false) {
        echo "✅ purchase/pdf.blade.php HAS size: a5!\n";
    } else if (strpos($content, 'size: a4') !== false) {
        echo "❌ purchase/pdf.blade.php STILL HAS size: a4!\n";
    } else {
        echo "❓ purchase/pdf.blade.php: neither size: a4 nor size: a5 found.\n";
    }
} else {
    echo "File not found: $purchasePdfPath\n";
}

$descriptorspec = array(
   0 => array("pipe", "r"),
   1 => array("pipe", "w"),
   2 => array("pipe", "w")
);

echo "\n--- Git Recent Log ---\n";
$process = proc_open('git log -n 1 --oneline 2>&1', $descriptorspec, $pipes);
if (is_resource($process)) {
    fclose($pipes[0]);
    $output = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    proc_close($process);
    echo "Latest Commit: " . trim($output) . "\n";
} else {
    echo "proc_open failed to run git log!\n";
}

echo "\n--- Attempting proc_open Git Pull with single-thread config ---\n";
// Configure git to be single-threaded to prevent Hostinger thread throttling
$configs = [
    'git config core.preloadIndex false',
    'git config index.threads 1',
    'git config pack.threads 1',
    'git config gc.auto 0'
];
foreach ($configs as $cmd) {
    $process = proc_open($cmd . ' 2>&1', $descriptorspec, $pipes);
    if (is_resource($process)) {
        fclose($pipes[0]);
        stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        proc_close($process);
    }
}

$process = proc_open('git pull origin main 2>&1', $descriptorspec, $pipes);
if (is_resource($process)) {
    fclose($pipes[0]);
    $output = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $returnValue = proc_close($process);
    echo "Git Pull Return Code: $returnValue\n";
    echo "Git Pull Output:\n$output\n";
} else {
    echo "proc_open failed to run git pull!\n";
}

// Check purchase view file again after pulling
if (file_exists($purchasePdfPath)) {
    $content = file_get_contents($purchasePdfPath);
    if (strpos($content, 'size: a5') !== false) {
        echo "\n✅ AFTER PULL: purchase/pdf.blade.php has size: a5!\n";
    } else {
        echo "\n❌ AFTER PULL: purchase/pdf.blade.php STILL has size: a4.\n";
    }
}
