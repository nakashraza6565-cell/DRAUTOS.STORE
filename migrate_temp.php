<?php
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \Illuminate\Support\Facades\DB::statement("ALTER TABLE manufacturing_bills MODIFY COLUMN status VARCHAR(255) DEFAULT 'wip'");
    echo "✅ status column modified successfully!";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
