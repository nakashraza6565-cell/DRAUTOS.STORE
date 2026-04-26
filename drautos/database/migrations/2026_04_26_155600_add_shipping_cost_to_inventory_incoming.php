<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippingCostToInventoryIncoming extends Migration
{
    public function up()
    {
        if (Schema::hasTable('inventory_incoming')) {
            Schema::table('inventory_incoming', function (Blueprint $table) {
                if (!Schema::hasColumn('inventory_incoming', 'shipping_cost')) {
                    $table->decimal('shipping_cost', 10, 2)->default(0)->after('status');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('inventory_incoming')) {
            Schema::table('inventory_incoming', function (Blueprint $table) {
                if (Schema::hasColumn('inventory_incoming', 'shipping_cost')) {
                    $table->dropColumn('shipping_cost');
                }
            });
        }
    }
}
