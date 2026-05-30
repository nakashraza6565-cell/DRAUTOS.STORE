<?php
require 'drautos/vendor/autoload.php';
$app = require_once 'drautos/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    if (!Schema::hasTable('sale_order_photos')) {
        Schema::create('sale_order_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_order_id');
            $table->string('filename');
            $table->string('original_name');
            $table->string('disk_path');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->timestamps();

            $table->foreign('sales_order_id')->references('id')->on('sales_orders')->onDelete('cascade');
        });
        echo "Table sale_order_photos created successfully!";
    } else {
        echo "Table sale_order_photos already exists.";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
