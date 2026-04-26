<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('packaging_purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('packaging_item_id');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->decimal('quantity', 10, 2);
            $table->decimal('price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->string('invoice_no')->unique();
            $table->date('purchase_date');
            $table->timestamps();

            $table->foreign('packaging_item_id')->references('id')->on('packaging_items')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packaging_purchases');
    }
};
