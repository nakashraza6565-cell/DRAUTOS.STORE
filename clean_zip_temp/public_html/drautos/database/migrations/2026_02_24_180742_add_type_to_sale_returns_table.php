<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('sale_returns', function (Blueprint $table) {
            $table->enum('type', ['return', 'claim'])->default('return')->after('return_number');
            $table->unsignedBigInteger('processed_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_returns', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->unsignedBigInteger('processed_by')->nullable(false)->change();
        });
    }
};
