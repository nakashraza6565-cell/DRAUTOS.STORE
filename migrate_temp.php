<?php
require __DIR__.'/drautos/vendor/autoload.php';
$app = require_once __DIR__.'/drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $columns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM manufacturing_bills");
    echo "<h3>manufacturing_bills columns:</h3><pre>";
    foreach ($columns as $c) {
        echo "{$c->Field} - {$c->Type} - Null: {$c->Null}\n";
    }
    echo "</pre>";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
