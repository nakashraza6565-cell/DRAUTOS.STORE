<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateCartsTableForBundles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('bundle_id')->nullable()->after('product_id');
            $table->string('item_type')->default('product')->after('bundle_id');
            // Foreign key for bundle_id
            $table->foreign('bundle_id')->references('id')->on('bundles')->onDelete('cascade');
        });

        // Make product_id nullable using raw statement to avoid doctrine/dbal dependency issues
        DB::statement('ALTER TABLE carts MODIFY product_id BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['bundle_id']);
            $table->dropColumn('bundle_id');
            $table->dropColumn('item_type');
        });
        
        // Revert product_id to not null (warning: will fail if nulls exist)
        // DB::statement('ALTER TABLE carts MODIFY product_id BIGINT UNSIGNED NOT NULL');
    }
}
