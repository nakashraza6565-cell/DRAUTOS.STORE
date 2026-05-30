<?php
// Force clear compiled views
$viewDir = __DIR__ . '/drautos/storage/framework/views';
$files = glob($viewDir . '/*.php');
$count = 0;
foreach ($files as $f) {
    if (unlink($f)) $count++;
}

// Also try to reset OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache reset: OK\n";
} else {
    echo "OPcache not available (CLI mode - OK)\n";
}

echo "Deleted $count compiled view file(s).\n";
echo "Done! Hard-refresh your browser now (Ctrl+Shift+R).\n";
