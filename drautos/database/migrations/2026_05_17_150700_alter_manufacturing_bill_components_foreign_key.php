<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterManufacturingBillComponentsForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manufacturing_bill_components', function (Blueprint $table) {
            // Drop old foreign key constraint
            // We use try catch because on some databases the foreign key name might be slightly different or already dropped.
            try {
                $table->dropForeign('component_fk');
            } catch (\Exception $e) {
                // Ignore if constraint doesn't exist
            }
            
            // Re-link component_product_id to production_factors table
            try {
                $table->foreign('component_product_id')->references('id')->on('production_factors')->onDelete('cascade');
            } catch (\Exception $e) {
                // Fallback
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manufacturing_bill_components', function (Blueprint $table) {
            try {
                $table->dropForeign(['component_product_id']);
            } catch (\Exception $e) {}
            
            try {
                $table->foreign('component_product_id')->references('id')->on('products')->onDelete('cascade');
            } catch (\Exception $e) {}
        });
    }
}
