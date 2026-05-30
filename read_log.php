<?php
require 'drautos/vendor/autoload.php';
$app = require_once 'drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $photo = \App\Models\SalesOrderPhoto::create([
        'sales_order_id' => 1,
        'filename' => 'test',
        'original_name' => 'test',
        'disk_path' => 'test'
    ]);
    echo "Inserted photo: " . json_encode($photo);
} catch (\Exception $e) {
    echo "Error inserting photo: " . $e->getMessage();
}
