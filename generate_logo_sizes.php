<?php
// PHP Script to process the generated logo and output standard icon sizes with high quality and transparency support

$sourcePath = 'C:\\Users\\T L S\\.gemini\\antigravity\\brain\\04bc3265-f91a-4be0-b599-99e16e644781\\drautos_logo_1779141363851.png';

if (!file_exists($sourcePath)) {
    die("❌ Error: Source logo file not found at: {$sourcePath}\n");
}

echo "📂 Loading source logo from: {$sourcePath}\n";
$info = getimagesize($sourcePath);
if (!$info) {
    die("❌ Error: Not a valid image file or unable to read file details.\n");
}
echo "ℹ️ Image MIME type: " . $info['mime'] . "\n";

switch ($info[2]) {
    case IMAGETYPE_GIF:
        $srcImg = imagecreatefromgif($sourcePath);
        break;
    case IMAGETYPE_JPEG:
        $srcImg = imagecreatefromjpeg($sourcePath);
        break;
    case IMAGETYPE_PNG:
        $srcImg = imagecreatefrompng($sourcePath);
        break;
    case 18: // IMAGETYPE_WEBP
        if (function_exists('imagecreatefromwebp')) {
            $srcImg = imagecreatefromwebp($sourcePath);
        } else {
            die("❌ Error: WebP support is not enabled in PHP GD.\n");
        }
        break;
    default:
        die("❌ Error: Unsupported image type: {$info[2]}\n");
}

if (!$srcImg) {
    die("❌ Error: Failed to load source image.\n");
}

$srcW = imagesx($srcImg);
$srcH = imagesy($srcImg);
echo "📐 Source dimensions: {$srcW}x{$srcH}\n";

$targetSizes = [128, 144, 152, 192, 256, 512];
$imagesDir = __DIR__ . DIRECTORY_SEPARATOR . 'images';

if (!is_dir($imagesDir)) {
    mkdir($imagesDir, 0755, true);
}

// Generate the resized icons
foreach ($targetSizes as $size) {
    echo "⚙️ Resizing to {$size}x{$size}...\n";
    
    // Create new blank image with transparency
    $dstImg = imagecreatetruecolor($size, $size);
    imagealphablending($dstImg, false);
    imagesavealpha($dstImg, true);
    
    // Smoothly resample the logo
    imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $size, $size, $srcW, $srcH);
    
    // Target file path
    $targetPath = $imagesDir . DIRECTORY_SEPARATOR . "hello-icon-{$size}.png";
    
    // Save image with maximum quality
    if (imagepng($dstImg, $targetPath, 0)) {
        echo "✅ Saved: {$targetPath}\n";
    } else {
        echo "❌ Failed to save: {$targetPath}\n";
    }
    
    imagedestroy($dstImg);
}

// Save a copy as images/drautos_logo.png for branding purposes
$brandLogoPath = $imagesDir . DIRECTORY_SEPARATOR . 'drautos_logo.png';
if (copy($sourcePath, $brandLogoPath)) {
    echo "🎉 Saved main branding logo to: {$brandLogoPath}\n";
} else {
    echo "❌ Failed to copy main branding logo.\n";
}

// Save a copy as images/favicon.png
$faviconPath = $imagesDir . DIRECTORY_SEPARATOR . 'favicon.png';
if (copy($imagesDir . DIRECTORY_SEPARATOR . 'hello-icon-128.png', $faviconPath)) {
    echo "🎉 Saved favicon logo to: {$faviconPath}\n";
} else {
    echo "❌ Failed to copy favicon logo.\n";
}

// Save a copy in the root as dr_logo.png to update any general assets
$rootLogoPath = __DIR__ . DIRECTORY_SEPARATOR . 'dr_logo.png';
if (copy($sourcePath, $rootLogoPath)) {
    echo "🎉 Saved root logo to: {$rootLogoPath}\n";
} else {
    echo "❌ Failed to copy root logo.\n";
}

imagedestroy($srcImg);
echo "\n✨ All logo assets successfully generated and deployed to standard directories!\n";
