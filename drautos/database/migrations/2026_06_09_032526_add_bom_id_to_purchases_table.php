<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('raw_material_purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('manufacturing_bill_id')->nullable()->after('notes');
            $table->foreign('manufacturing_bill_id')->references('id')->on('manufacturing_bills')->onDelete('set null');
        });

        Schema::table('raw_material_purchase_items', function (Blueprint $table) {
            $table->unsignedBigInteger('factor_id')->nullable()->change();
            $table->string('item_name')->nullable()->after('factor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raw_material_purchases', function (Blueprint $table) {
            $table->dropForeign(['manufacturing_bill_id']);
            $table->dropColumn('manufacturing_bill_id');
        });

        Schema::table('raw_material_purchase_items', function (Blueprint $table) {
            $table->dropColumn('item_name');
            $table->unsignedBigInteger('factor_id')->nullable(false)->change();
        });
    }
};
