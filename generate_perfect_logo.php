<?php
// PHP Script to mathematically render a premium "DR" logo using the authentic Revue font

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
$bgCol = imagecolorallocate($img, 14, 15, 18); // Deep Premium Graphite Slate (#0e0f12)
imagefill($img, 0, 0, $bgCol);

// Draw a beautiful premium glowing gradient circle
$centerX = $width / 2;
$centerY = $height / 2;
$outerRadius = 450;
$innerRadius = 442;

// Draw a glowing electric cyan/blue circular ring (#38bdf8) with smooth antialiasing
for ($r = $outerRadius; $r >= $innerRadius; $r--) {
    $opacity = 1.0 - (($outerRadius - $r) / ($outerRadius - $innerRadius));
    $alpha = (int)((1 - $opacity) * 127);
    $glowCol = imagecolorallocatealpha($img, 56, 189, 248, $alpha); // Electric Cyan (#38bdf8)
    imagefilledellipse($img, $centerX, $centerY, $r * 2, $r * 2, $glowCol);
}

// Re-fill inner circle to make it a ring
$innerBgCol = imagecolorallocate($img, 14, 15, 18);
imagefilledellipse($img, $centerX, $centerY, $innerRadius * 2, $innerRadius * 2, $innerBgCol);

// Draw an inner subtle silver trim (#e2e8f0)
$silverCol = imagecolorallocatealpha($img, 226, 232, 240, 80);
imageellipse($img, $centerX, $centerY, ($innerRadius - 8) * 2, ($innerRadius - 8) * 2, $silverCol);

// Font settings for "DR"
$fontSize = 320;
$text = "DR";

// Calculate bounding box to center the text perfectly
$bbox = imagettfbbox($fontSize, 0, $fontFile, $text);
$textWidth = $bbox[2] - $bbox[0];
$textHeight = $bbox[1] - $bbox[7];

// Fine-tuned visual offsets for Revue font centering
$x = ($width - $textWidth) / 2 - 20; // Slight shift left to balance visual weight
$y = ($height + $textHeight) / 2 - 40; // Center baseline vertically

echo "📐 Calculated Font Box Width: {$textWidth}px, Height: {$textHeight}px\n";
echo "🎯 Coordinates: X={$x}, Y={$y}\n";

// 1. Draw a beautiful soft drop shadow for that premium "lifted" look
$shadowOffset = 18;
$shadowCol = imagecolorallocatealpha($img, 0, 0, 0, 95); // Heavy semi-transparent black
imagettftext($img, $fontSize, 0, $x + $shadowOffset, $y + $shadowOffset, $shadowCol, $fontFile, $text);

// 2. Draw a secondary blue glow behind the text
$glowOffset = 6;
$glowCol = imagecolorallocatealpha($img, 56, 189, 248, 110); // Glowing Electric Cyan
for ($dx = -$glowOffset; $dx <= $glowOffset; $dx += 3) {
    for ($dy = -$glowOffset; $dy <= $glowOffset; $dy += 3) {
        if (abs($dx) + abs($dy) > 0) {
            imagettftext($img, $fontSize, 0, $x + $dx, $y + $dy, $glowCol, $fontFile, $text);
        }
    }
}

// 3. Draw the main crisp white text in the foreground
$fgCol = imagecolorallocate($img, 255, 255, 255); // Pure White
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
