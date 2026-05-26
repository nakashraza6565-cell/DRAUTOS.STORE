<?php
/**
 * purge_views.php — Minimal Blade View Cache Purger
 * Deletes compiled view files directly without booting Laravel.
 */
header('Content-Type: text/plain; charset=utf-8');

$viewCacheDir = __DIR__ . '/drautos/storage/framework/views';

if (!is_dir($viewCacheDir)) {
    echo "ERROR: View cache directory not found at: $viewCacheDir\n";
    exit(1);
}

$files = glob($viewCacheDir . '/*.php');
$deleted = 0;
$errors  = 0;

foreach ($files as $file) {
    if (unlink($file)) {
        $deleted++;
    } else {
        echo "FAILED to delete: $file\n";
        $errors++;
    }
}

echo "=== Blade View Cache Purge ===\n";
echo "Directory : $viewCacheDir\n";
echo "Deleted   : $deleted file(s)\n";
echo "Errors    : $errors\n";
echo "\nDone! Refresh your site now.\n";
