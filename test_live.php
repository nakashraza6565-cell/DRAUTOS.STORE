<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== LARAVEL DOMPDF PATHS DIAGNOSTIC ===\n\n";

echo "Current Directory: " . __DIR__ . "\n";
echo "Server Time: " . date("Y-m-d H:i:s") . "\n\n";

// Bootstrap Laravel
$appFile = __DIR__ . '/drautos/bootstrap/app.php';
if (file_exists($appFile)) {
    echo "Laravel bootstrap found. Booting application...\n";
    $app = require_once $appFile;
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    
    echo "Laravel booted successfully!\n\n";
    
    // Check paths
    $paths = [
        'base_path()' => base_path(),
        'public_path()' => public_path(),
        'storage_path()' => storage_path(),
        'app_path()' => app_path(),
    ];
    
    foreach ($paths as $name => $val) {
        echo "{$name} = {$val}\n";
    }
    echo "\n";
    
    // Check file existences
    $filesToCheck = [
        base_path('revue/tahoma.ttf'),
        public_path('revue/tahoma.ttf'),
        __DIR__ . '/revue/tahoma.ttf',
        __DIR__ . '/public_html/revue/tahoma.ttf',
        '/home/u745585093/domains/drautos.store/public_html/revue/tahoma.ttf', // Typical Hostinger structure
        '/home/u745585093/public_html/revue/tahoma.ttf',
    ];
    
    echo "--- File Existence Diagnostics ---\n";
    foreach ($filesToCheck as $file) {
        $exists = file_exists($file) ? "YES (Size: " . filesize($file) . " bytes)" : "NO";
        echo "File: {$file}\nExists: {$exists}\n\n";
    }
    
    // Check write permissions for DomPDF cache
    $dompdfFontDir = storage_path('fonts');
    echo "--- DomPDF Font Cache Directory ---\n";
    echo "Path: {$dompdfFontDir}\n";
    echo "Exists: " . (file_exists($dompdfFontDir) ? "YES" : "NO") . "\n";
    echo "Writable: " . (is_writable($dompdfFontDir) ? "YES" : "NO") . "\n";
    
} else {
    echo "Laravel bootstrap not found at {$appFile}!\n";
}
