<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionFactorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_factors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['material', 'labor', 'overhead', 'service'])->default('material');
            $table->string('unit')->nullable()->comment('e.g., kg, pcs, hours');
            $table->decimal('cost_price', 10, 2)->default(0)->comment('Default cost per unit');
            $table->decimal('stock_quantity', 10, 2)->default(0)->comment('For materials only');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_factors');
    }
}
