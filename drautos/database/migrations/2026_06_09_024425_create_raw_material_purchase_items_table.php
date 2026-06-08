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
        Schema::create('raw_material_purchase_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('factor_id');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 12, 4)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->foreign('purchase_id')->references('id')->on('raw_material_purchases')->onDelete('cascade');
            $table->foreign('factor_id')->references('id')->on('production_factors')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_material_purchase_items');
    }
};
