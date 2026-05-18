<?php
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \Illuminate\Support\Facades\DB::statement("ALTER TABLE manufacturing_bills ADD COLUMN subcontractor_id BIGINT UNSIGNED NULL");
    echo "✅ subcontractor_id added successfully!";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
