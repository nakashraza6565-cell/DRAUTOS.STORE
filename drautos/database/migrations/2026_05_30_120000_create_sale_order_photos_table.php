<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleOrderPhotosTable extends Migration
{
    public function up()
    {
        Schema::create('sale_order_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_order_id');
            $table->string('filename');          // stored filename (UUID-based)
            $table->string('original_name');     // original uploaded filename
            $table->string('disk_path');         // full path in private storage
            $table->unsignedBigInteger('uploaded_by')->nullable(); // user who uploaded
            $table->unsignedBigInteger('file_size')->default(0);   // bytes
            $table->string('mime_type')->nullable();
            $table->timestamps();

            $table->foreign('sales_order_id')
                  ->references('id')->on('sales_orders')
                  ->onDelete('cascade');

            $table->foreign('uploaded_by')
                  ->references('id')->on('users')
                  ->onDelete('set null');

            $table->index('sales_order_id');
        });

        // Add photo_pending to sales_orders status if needed (safe to run)
        DB::statement("ALTER TABLE sales_orders MODIFY COLUMN status ENUM(
            'pending',
            'photo_pending',
            'processing',
            'partially_delivered',
            'delivered',
            'merged',
            'cancelled'
        ) DEFAULT 'pending'");
    }

    public function down()
    {
        Schema::dropIfExists('sale_order_photos');
    }
}
