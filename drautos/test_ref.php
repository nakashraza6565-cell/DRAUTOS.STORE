<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$transactions = \App\Models\AccountTransaction::take(10)->get();
foreach ($transactions as $t) {
    echo $t->reference_type . " #" . $t->reference_id . "\n";
    if ($t->reference_type) {
        $model = $t->reference_type::find($t->reference_id);
        if ($model) {
            if (isset($model->user)) {
                echo "User: " . $model->user->name . "\n";
            } elseif (isset($model->supplier)) {
                echo "Supplier: " . $model->supplier->name . "\n";
            }
        }
    }
}
