<?php
// Fetches the deploy script from GitHub raw and executes it
set_time_limit(120);

$rawUrl = 'https://raw.githubusercontent.com/nakashraza6565-cell/DRAUTOS.STORE/main/drautos/public/proc_deploy_files.php';

$ctx = stream_context_create(['http' => ['timeout' => 60]]);
$code = @file_get_contents($rawUrl, false, $ctx);

if (!$code) {
    echo 'FAIL: Could not fetch deploy script from GitHub. Error: ' . error_get_last()['message'];
    exit;
}

// Remove the <?php opening tag before eval
$code = preg_replace('/^<\?php\s*/i', '', $code);

echo '<pre>Fetched deploy script (' . strlen($code) . ' bytes). Running...<br>';
eval($code);
echo '</pre>';

// Self-delete
unlink(__FILE__);
