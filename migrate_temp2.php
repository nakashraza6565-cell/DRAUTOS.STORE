<?php
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "<h1>Database Inspector</h1>";
    
    // 1. Check Columns with Data Type
    $columns = \Illuminate\Support\Facades\DB::select("
        SELECT COLUMN_NAME, DATA_TYPE, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'manufacturing_bills'
    ");
    echo "<h3>manufacturing_bills columns detailed:</h3><pre>";
    foreach ($columns as $c) {
        echo "{$c->COLUMN_NAME} - Type: {$c->DATA_TYPE} - Full: {$c->COLUMN_TYPE} - Null: {$c->IS_NULLABLE} - Default: {$c->COLUMN_DEFAULT}\n";
    }
    echo "</pre><hr>";

    // 2. Check Triggers
    $triggers = \Illuminate\Support\Facades\DB::select("SHOW TRIGGERS");
    echo "<h3>All Database Triggers:</h3><pre>";
    print_r($triggers);
    echo "</pre><hr>";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
