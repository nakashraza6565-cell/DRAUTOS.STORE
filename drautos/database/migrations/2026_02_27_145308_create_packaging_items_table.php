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
        Schema::create('packaging_items', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['sticker', 'box']);
            $table->string('name');
            $table->string('size')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->decimal('stock', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packaging_items');
    }
};
