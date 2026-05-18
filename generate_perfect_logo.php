<?php
// PHP Script to mathematically render a premium "DR" logo using the authentic Revue font (Black Background & Dark Blue Text)

$fontFile = __DIR__ . DIRECTORY_SEPARATOR . 'revue' . DIRECTORY_SEPARATOR . 'reve.ttf';

if (!file_exists($fontFile)) {
    die("❌ Error: Font file not found at: {$fontFile}\n");
}

echo "📂 Loading Revue font from: {$fontFile}\n";

$width = 1024;
$height = 1024;

// Create target canvas
$img = imagecreatetruecolor($width, $height);

// Enable alpha blending and save alpha
imagealphablending($img, true);
imagesavealpha($img, true);

// Colors
$bgCol = imagecolorallocate($img, 0, 0, 0); // Solid Pure Black (#000000)
imagefill($img, 0, 0, $bgCol);

// Draw a beautiful premium glowing gradient circle
$centerX = $width / 2;
$centerY = $height / 2;
$outerRadius = 450;
$innerRadius = 442;

// Draw a glowing sapphire blue circular ring (#2563eb) with smooth antialiasing
for ($r = $outerRadius; $r >= $innerRadius; $r--) {
    $opacity = 1.0 - (($outerRadius - $r) / ($outerRadius - $innerRadius));
    $alpha = (int)((1 - $opacity) * 127);
    $glowCol = imagecolorallocatealpha($img, 37, 99, 235, $alpha); // Sapphire Blue (#2563eb)
    imagefilledellipse($img, $centerX, $centerY, $r * 2, $r * 2, $glowCol);
}

// Re-fill inner circle with solid black
$innerBgCol = imagecolorallocate($img, 0, 0, 0);
imagefilledellipse($img, $centerX, $centerY, $innerRadius * 2, $innerRadius * 2, $innerBgCol);

// Draw an inner subtle sapphire/blue trim (#3b82f6)
$trimCol = imagecolorallocatealpha($img, 59, 130, 246, 70);
imageellipse($img, $centerX, $centerY, ($innerRadius - 8) * 2, ($innerRadius - 8) * 2, $trimCol);

// Font settings for "DR"
$fontSize = 320;
$text = "DR";

// Calculate bounding box to center the text perfectly
$bbox = imagettfbbox($fontSize, 0, $fontFile, $text);
$textWidth = $bbox[2] - $bbox[0];
$textHeight = $bbox[1] - $bbox[7];

// Centering coordinates
$x = ($width - $textWidth) / 2 - 20; // Slight shift left to balance visual weight
$y = ($height + $textHeight) / 2 - 40; // Center baseline vertically

echo "📐 Calculated Font Box Width: {$textWidth}px, Height: {$textHeight}px\n";
echo "🎯 Coordinates: X={$x}, Y={$y}\n";

// 1. Draw a beautiful dark drop shadow
$shadowOffset = 18;
$shadowCol = imagecolorallocatealpha($img, 0, 0, 0, 110);
imagettftext($img, $fontSize, 0, $x + $shadowOffset, $y + $shadowOffset, $shadowCol, $fontFile, $text);

// 2. Draw a gorgeous soft light-blue ambient glow (#60a5fa) behind the dark blue text to make it pop!
$glowOffset = 8;
$glowCol = imagecolorallocatealpha($img, 96, 165, 250, 100); // Light blue glow
for ($dx = -$glowOffset; $dx <= $glowOffset; $dx += 2) {
    for ($dy = -$glowOffset; $dy <= $glowOffset; $dy += 2) {
        if (abs($dx) + abs($dy) > 0) {
            imagettftext($img, $fontSize, 0, $x + $dx, $y + $dy, $glowCol, $fontFile, $text);
        }
    }
}

// 3. Draw the main text in deep, premium Dark Cobalt Blue (#1e40af)
$fgCol = imagecolorallocate($img, 30, 64, 175); // Dark Cobalt Blue (#1e40af)
imagettftext($img, $fontSize, 0, $x, $y, $fgCol, $fontFile, $text);

// Save generated logo
$outputPath = __DIR__ . DIRECTORY_SEPARATOR . 'dr_logo.png';
if (imagepng($img, $outputPath)) {
    echo "🎉 Saved perfect logo in root to: {$outputPath}\n";
} else {
    echo "❌ Failed to save perfect logo.\n";
}

imagedestroy($img);
echo "✨ Generation complete!\n";
