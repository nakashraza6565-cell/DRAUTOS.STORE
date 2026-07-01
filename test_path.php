<?php
header('Content-Type: text/plain; charset=utf-8');
define('LARAVEL_START', microtime(true));
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== PATH RESOLUTIONS ===\n";
echo "public_path(): " . public_path() . "\n";
echo "base_path(): " . base_path() . "\n";
echo "tahoma.ttf in public_path(): " . (file_exists(public_path('revue/tahoma.ttf')) ? 'YES' : 'NO') . "\n";
echo "tahoma.ttf in base_path(): " . (file_exists(base_path('revue/tahoma.ttf')) ? 'YES' : 'NO') . "\n";
echo "tahoma.ttf in relative path: " . (file_exists(__DIR__ . '/public_html/revue/tahoma.ttf') ? 'YES' : 'NO') . "\n";
echo "reve.ttf in public_path(): " . (file_exists(public_path('revue/reve.ttf')) ? 'YES' : 'NO') . "\n";
