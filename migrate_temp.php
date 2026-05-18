<?php
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $triggers = \Illuminate\Support\Facades\DB::select("SHOW TRIGGERS LIKE 'manufacturing_bills'");
    echo "<h3>manufacturing_bills triggers:</h3><pre>";
    print_r($triggers);
    echo "</pre>";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
