<?php
/**
 * 🚀 Auto-Extraction Script for Hostinger
 * This script will find any .zip file in the folder and extract it.
 */

$zipFiles = glob("*.zip");

if (empty($zipFiles)) {
    die("❌ No ZIP files found. Please make sure your .zip is uploaded to this folder.");
}

// Select the first zip file found
$targetZip = $zipFiles[0];
echo "📦 Found file: <b>$targetZip</b><br>";
echo "⏳ Extracting... please wait...<br>";

$zip = new ZipArchive;
if ($zip->open($targetZip) === TRUE) {
    $zip->extractTo('./');
    $zip->close();
    echo "✅ <b>Success!</b> Everything has been extracted.<br>";
    echo "⚠️ <i>Reminder: Delete this extract.php file now for security.</i>";
} else {
    echo "❌ <b>Failed!</b> Could not open the ZIP file. It might be corrupted or still uploading.";
}
?>
