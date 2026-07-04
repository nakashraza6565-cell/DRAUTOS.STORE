<?php
require __DIR__ . '/drautos/vendor/autoload.php';
$app = require_once __DIR__ . '/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $settings = null;
    $html = view('backend.order.backpage', compact('settings'))->render();
    
    // Embed the fonts as base64 in the rendered HTML
    $reveBase64 = base64_encode(file_get_contents(__DIR__ . '/drautos/public/revue/reve.ttf'));
    $tahomaBase64 = base64_encode(file_get_contents(__DIR__ . '/drautos/public/revue/tahoma.ttf'));
    
    // Replace URL paths with Base64 data URIs
    $reveTarget = 'url("' . str_replace('\\', '/', public_path('revue/reve.ttf')) . '")';
    $reveReplacement = 'url("data:font/truetype;charset=utf-8;base64,' . $reveBase64 . '")';
    $html = str_replace($reveTarget, $reveReplacement, $html);
    
    $tahomaTarget = 'url("' . str_replace('\\', '/', public_path('revue/tahoma.ttf')) . '")';
    $tahomaReplacement = 'url("data:font/truetype;charset=utf-8;base64,' . $tahomaBase64 . '")';
    $html = str_replace($tahomaTarget, $tahomaReplacement, $html);
    
    // Write out the compiled static self-contained HTML
    $outputPath = __DIR__ . '/backpage_compiled.html';
    file_put_contents($outputPath, $html);
    echo "Static HTML compiled successfully at: " . $outputPath . "\n";
} catch (\Exception $e) {
    echo "Error compiling HTML: " . $e->getMessage() . "\n";
}
