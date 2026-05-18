<?php
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // 1. Try to drop check constraint if exists
    try {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE manufacturing_bills DROP CONSTRAINT IF EXISTS manufacturing_bills_status_check");
    } catch (\Exception $ex) {}

    // 2. Reconstruct column to clear hidden constraints
    try {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE manufacturing_bills DROP COLUMN status");
    } catch (\Exception $ex) {}

    \Illuminate\Support\Facades\DB::statement("ALTER TABLE manufacturing_bills ADD COLUMN status VARCHAR(191) DEFAULT 'wip' AFTER notes");
    
    echo "✅ status column successfully reconstructed without constraints!";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
