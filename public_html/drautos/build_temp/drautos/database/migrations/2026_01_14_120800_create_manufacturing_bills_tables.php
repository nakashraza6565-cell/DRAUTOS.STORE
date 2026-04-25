<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManufacturingBillsTables extends Migration
{
    public function up()
    {
        // Bill of Manufacturing (BOM)
        Schema::create('manufacturing_bills', function (Blueprint $table) {
            $table->id();
            $table->string('bom_number')->unique();
            $table->unsignedBigInteger('product_id'); // finished product
            $table->integer('batch_quantity')->default(1); // how many units this BOM produces
            $table->float('material_cost')->default(0);
            $table->float('machining_cost')->default(0);
            $table->float('labour_cost')->default(0);
            $table->float('packaging_cost')->default(0);
            $table->float('overhead_cost')->default(0);
            $table->float('total_cost_per_unit')->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
        
        // BOM Components (materials used)
        Schema::create('manufacturing_bill_components', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manufacturing_bill_id');
            $table->unsignedBigInteger('component_product_id'); // material/component product
            $table->float('quantity_required'); // quantity needed for one batch
            $table->string('unit')->default('piece'); // piece, kg, gram, liter, etc.
            $table->float('cost_per_unit');
            $table->float('total_cost');
            $table->timestamps();
            
            $table->foreign('manufacturing_bill_id', 'mfg_bill_fk')->references('id')->on('manufacturing_bills')->onDelete('cascade');
            $table->foreign('component_product_id', 'component_fk')->references('id')->on('products')->onDelete('cascade');
        });
        
        // Manufacturing Production Log
        Schema::create('manufacturing_productions', function (Blueprint $table) {
            $table->id();
            $table->string('production_number')->unique();
            $table->unsignedBigInteger('manufacturing_bill_id');
            $table->integer('quantity_produced');
            $table->date('production_date');
            $table->float('actual_cost')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('produced_by'); // staff user_id
            $table->timestamps();
            
            $table->foreign('manufacturing_bill_id', 'prod_mfg_bill_fk')->references('id')->on('manufacturing_bills')->onDelete('cascade');
            $table->foreign('produced_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('manufacturing_productions');
        Schema::dropIfExists('manufacturing_bill_components');
        Schema::dropIfExists('manufacturing_bills');
    }
}
