<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIngredientTypeToManufacturingBillComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manufacturing_bill_components', function (Blueprint $table) {
            // Drop foreign key to production_factors (since it can now refer to products as well)
            try {
                $table->dropForeign(['component_product_id']);
            } catch (\Exception $e) {
                // Ignore if it doesn't exist
            }

            // Add ingredient_type column
            if (!Schema::hasColumn('manufacturing_bill_components', 'ingredient_type')) {
                $table->string('ingredient_type')->default('App\\Models\\ProductionFactor');
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
            if (Schema::hasColumn('manufacturing_bill_components', 'ingredient_type')) {
                $table->dropColumn('ingredient_type');
            }
            
            try {
                $table->foreign('component_product_id')->references('id')->on('production_factors')->onDelete('cascade');
            } catch (\Exception $e) {}
        });
    }
}
