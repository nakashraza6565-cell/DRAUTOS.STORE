<?php
// PHP Script to mathematically crop the exact circular "DR" logo from the screenshot

$sourcePath = 'C:\\Users\\T L S\\.gemini\\antigravity\\brain\\06480665-7972-4842-b90a-b37ebbded63d\\.tempmediaStorage\\media_06480665-7972-4842-b90a-b37ebbded63d_1779114840618.png';

if (!file_exists($sourcePath)) {
    // Check if there are other files in case the name is slightly different
    $files = glob('C:\\Users\\T L S\\.gemini\\antigravity\\brain\\06480665-7972-4842-b90a-b37ebbded63d\\.tempmediaStorage\\media_*.png');
    if (count($files) > 0) {
        // Use the most recently modified file
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        $sourcePath = $files[0];
    } else {
        die("❌ Source screenshot file not found.");
    }
}

echo "📂 Loading image from: {$sourcePath}<br>\n";

$img = imagecreatefrompng($sourcePath);
if (!$img) {
    die("❌ Failed to load PNG image.");
}

$width = imagesx($img);
$height = imagesy($img);
echo "📐 Dimensions: {$width}x{$height}<br>\n";

$minX = $width;
$maxX = 0;
$minY = $height;
$maxY = 0;

// Scan the image to find the lavender/purple circle
// Bounding box of pixels matching the purple/blue color
for ($x = 0; $x < $width; $x++) {
    for ($y = 0; $y < $height; $y++) {
        $rgb = imagecolorat($img, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        
        // Match the light purple/blue circular ring or background color
        // RGB is around R:159, G:133, B:255 or R:60, G:55, B:125 for the dark indigo background inside the circle
        if (($r > 40 && $r < 170 && $g > 40 && $g < 170 && $b > 110) || ($r > 130 && $b > 200)) {
            // Make sure we are in the top half of the image (above the "Danyal Autos" text)
            if ($y < $height * 0.6) {
                if ($x < $minX) $minX = $x;
                if ($x > $maxX) $maxX = $x;
                if ($y < $minY) $minY = $y;
                if ($y > $maxY) $maxY = $y;
            }
        }
    }
}

echo "🎯 Bounding box detected: X({$minX} to {$maxX}), Y({$minY} to {$maxY})<br>\n";

$boxW = $maxX - $minX;
$boxH = $maxY - $minY;
echo "📦 Box size: {$boxW}x{$boxH}<br>\n";

if ($boxW <= 0 || $boxH <= 0) {
    // Fallback: manually crop the top square if detection failed
    $cropSize = min($width, (int)($height * 0.5));
    $cropX = (int)(($width - $cropSize) / 2);
    $cropY = 10;
} else {
    // The circle is a square bounding box. Make sure it's a perfect square
    $cropSize = max($boxW, $boxH) + 16; // Add a padding margin
    $centerX = $minX + ($boxW / 2);
    $centerY = $minY + ($boxH / 2);
    
    $cropX = (int)($centerX - ($cropSize / 2));
    $cropY = (int)($centerY - ($cropSize / 2));
    
    // Ensure bounds are clean
    if ($cropX < 0) $cropX = 0;
    if ($cropY < 0) $cropY = 0;
    if ($cropX + $cropSize > $width) $cropSize = $width - $cropX;
    if ($cropY + $cropSize > $height) $cropSize = $height - $cropY;
}

echo "✂️ Cropping: X: {$cropX}, Y: {$cropY}, Size: {$cropSize}x{$cropSize}<br>\n";

$cropped = imagecrop($img, [
    'x' => $cropX,
    'y' => $cropY,
    'width' => $cropSize,
    'height' => $cropSize
]);

if ($cropped !== false) {
    $targetDir = 'c:\\Users\\T L S\\proj\\drautos.store';
    $targetPath = $targetDir . '\\dr_logo.png';
    imagepng($cropped, $targetPath);
    echo "✅ Success! Saved cropped logo to: {$targetPath}<br>\n";
    
    // Also save in the brain directory for preview
    $brainPath = 'C:\\Users\\T L S\\.gemini\\antigravity\\brain\\06480665-7972-4842-b90a-b37ebbded63d\\dr_logo_cropped.png';
    imagepng($cropped, $brainPath);
    imagedestroy($cropped);
} else {
    echo "❌ Failed to crop image.<br>\n";
}

imagedestroy($img);
