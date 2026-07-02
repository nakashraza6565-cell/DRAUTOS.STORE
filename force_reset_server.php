<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== SERVER GIT FORCE RESET ===\n\n";

$descriptorspec = [
   0 => ["pipe", "r"],
   1 => ["pipe", "w"],
   2 => ["pipe", "w"]
];

function runCmd($cmd, $descriptorspec) {
    $process = proc_open($cmd . ' 2>&1', $descriptorspec, $pipes);
    if (is_resource($process)) {
        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $returnValue = proc_close($process);
        return [$returnValue, $output];
    }
    return [-1, 'proc_open failed'];
}

// Step 1: Git status before
echo "--- Git Status BEFORE ---\n";
[$rc, $out] = runCmd('git status', $descriptorspec);
echo $out . "\n";

// Step 2: Hard reset to origin/main
echo "--- Running: git fetch origin ---\n";
[$rc, $out] = runCmd('git fetch origin', $descriptorspec);
echo "Return: $rc\n$out\n\n";

echo "--- Running: git reset --hard origin/main ---\n";
[$rc, $out] = runCmd('git reset --hard origin/main', $descriptorspec);
echo "Return: $rc\n$out\n\n";

// Step 3: Clean untracked files (but not vendor/storage)
echo "--- Running: git clean -fd (removes untracked files except ignored) ---\n";
[$rc, $out] = runCmd('git clean -fd --exclude=drautos/vendor --exclude=drautos/storage --exclude=drautos/.env', $descriptorspec);
echo "Return: $rc\n$out\n\n";

// Step 4: Git status after
echo "--- Git Status AFTER ---\n";
[$rc, $out] = runCmd('git status', $descriptorspec);
echo $out . "\n";

// Step 5: Verify the new file exists
echo "--- File Check ---\n";
if (file_exists(__DIR__ . '/check_incomings_live.php')) {
    echo "✅ check_incomings_live.php EXISTS on server!\n";
} else {
    echo "❌ check_incomings_live.php NOT FOUND on server.\n";
}

// Self-destruct
unlink(__FILE__);
echo "\nScript self-deleted. Done!\n";
