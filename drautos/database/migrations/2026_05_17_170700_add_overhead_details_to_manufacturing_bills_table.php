<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOverheadDetailsToManufacturingBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manufacturing_bills', function (Blueprint $table) {
            if (!Schema::hasColumn('manufacturing_bills', 'overhead_details')) {
                $table->text('overhead_details')->nullable();
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
        Schema::table('manufacturing_bills', function (Blueprint $table) {
            if (Schema::hasColumn('manufacturing_bills', 'overhead_details')) {
                $table->dropColumn('overhead_details');
            }
        });
    }
}
