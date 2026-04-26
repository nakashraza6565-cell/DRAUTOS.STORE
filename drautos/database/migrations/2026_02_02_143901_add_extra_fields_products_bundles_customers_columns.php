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
        Schema::table('products', function (Blueprint $table) {
            $table->float('wholesale_price')->nullable();
            $table->float('retail_price')->nullable();
            $table->float('walkin_price')->nullable();
            $table->float('salesman_price')->nullable();
            $table->string('rack_number')->nullable();
            // shelf_number might already exist from 2026_01_14_120100_add_shelf_location_to_products_table.php
            // I'll check if it exists or use columnExists check, but best practice is to assume clean state modification if I know what I'm doing.
            // The user asked to add "rack number and shelf number". If shelf exists, I should check.
            if (!Schema::hasColumn('products', 'shelf_number')) {
                 $table->string('shelf_number')->nullable();
            }
            $table->string('color')->nullable();
            $table->string('type')->nullable(); // Assuming 'type' is a string field
        });

        Schema::table('bundles', function (Blueprint $table) {
            $table->float('wholesale_price')->nullable();
            $table->float('retail_price')->nullable();
            $table->float('walkin_price')->nullable();
            $table->float('salesman_price')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('customer_type')->nullable(); // 'wholesale', 'retail', 'walkin', 'salesman'
        });

        Schema::create('product_supplier', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('supplier_id');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_supplier');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['customer_type']);
        });

        Schema::table('bundles', function (Blueprint $table) {
            $table->dropColumn(['wholesale_price', 'retail_price', 'walkin_price', 'salesman_price']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'wholesale_price', 
                'retail_price', 
                'walkin_price', 
                'salesman_price', 
                'rack_number', 
                'shelf_number', 
                'color', 
                'type'
            ]);
        });
    }
};
