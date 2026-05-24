<?php
header('Content-Type: text/plain');
echo "=== GIT DEPLOYMENT RUNNER ===\n\n";

$methods = ['shell_exec', 'exec', 'system', 'passthru', 'proc_open'];
foreach ($methods as $m) {
    if (function_exists($m)) {
        echo "✅ Function '$m' is ENABLED!\n";
    } else {
        echo "❌ Function '$m' is DISABLED!\n";
    }
}

echo "\n--- Attempting Git Pull ---\n";
if (function_exists('exec')) {
    $output = [];
    $return_var = 0;
    exec('git pull origin main 2>&1', $output, $return_var);
    echo "Return Code: $return_var\n";
    echo "Output:\n" . implode("\n", $output) . "\n";
} else {
    echo "Cannot run exec() to perform git pull.\n";
}
