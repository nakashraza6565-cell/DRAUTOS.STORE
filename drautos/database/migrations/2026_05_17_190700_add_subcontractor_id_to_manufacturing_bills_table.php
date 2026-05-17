<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubcontractorIdToManufacturingBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manufacturing_bills', function (Blueprint $table) {
            if (!Schema::hasColumn('manufacturing_bills', 'subcontractor_id')) {
                $table->unsignedBigInteger('subcontractor_id')->nullable();
                $table->foreign('subcontractor_id')->references('id')->on('suppliers')->onDelete('set null');
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
            if (Schema::hasColumn('manufacturing_bills', 'subcontractor_id')) {
                $table->dropForeign(['subcontractor_id']);
                $table->dropColumn('subcontractor_id');
            }
        });
    }
}
