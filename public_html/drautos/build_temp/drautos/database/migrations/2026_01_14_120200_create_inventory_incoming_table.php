<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryIncomingTable extends Migration
{
    public function up()
    {
        Schema::create('inventory_incoming', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->date('received_date');
            $table->string('invoice_number')->nullable(); // handwritten invoice number
            $table->unsignedBigInteger('received_by'); // staff user_id
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'verified', 'completed'])->default('pending');
            $table->timestamps();
            
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('set null');
            $table->foreign('received_by')->references('id')->on('users')->onDelete('cascade');
        });
        
        Schema::create('inventory_incoming_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_incoming_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->float('unit_cost');
            $table->float('total_cost');
            $table->string('batch_number')->nullable();
            $table->boolean('barcode_printed')->default(false);
            $table->timestamps();
            
            $table->foreign('inventory_incoming_id')->references('id')->on('inventory_incoming')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_incoming_items');
        Schema::dropIfExists('inventory_incoming');
    }
}
