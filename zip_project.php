<?php
/**
 * 📦 Reliable ZIP Creator
 * This script will zip the project while skipping locked files.
 */

$rootPath = realpath('public_html');
$zipFile = 'DRAUTOS_PRODUCTION.zip';

if (file_exists($zipFile)) {
    unlink($zipFile);
}

$zip = new ZipArchive();
$zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);

$count = 0;
foreach ($files as $name => $file) {
    if (!$file->isDir()) {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);

        // Skip heavy junk
        if (strpos($relativePath, 'node_modules') !== false || 
            strpos($relativePath, '.git') !== false ||
            strpos($relativePath, '.zip') !== false) {
            continue;
        }

        try {
            if ($zip->addFile($filePath, $relativePath)) {
                $count++;
            }
        } catch (Exception $e) {
            echo "Skipping locked file: $relativePath\n";
        }
    }
}

$zip->close();
echo "✅ Done! Zipped $count files into $zipFile\n";
?>
