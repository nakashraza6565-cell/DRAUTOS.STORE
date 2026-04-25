<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShelfLocationToProductsTable extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('shelf_location')->nullable()->after('warehouse_id');
            $table->enum('unit_type', ['piece', 'box', 'bundle', 'kg', 'gram', 'liter'])->default('piece')->after('stock');
            $table->integer('box_quantity')->nullable()->after('unit_type')->comment('Quantity per box/bundle');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['shelf_location', 'unit_type', 'box_quantity']);
        });
    }
}
