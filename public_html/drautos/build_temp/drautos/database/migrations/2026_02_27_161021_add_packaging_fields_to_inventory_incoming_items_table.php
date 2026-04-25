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
        Schema::table('inventory_incoming_items', function (Blueprint $table) {
            $table->unsignedBigInteger('packaging_item_id')->nullable()->after('product_id');
            $table->decimal('packaging_quantity', 15, 2)->default(0)->after('packaging_item_id');
            $table->decimal('packaging_cost', 15, 2)->default(0)->after('packaging_quantity');
            
            $table->foreign('packaging_item_id')->references('id')->on('packaging_items')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_incoming_items', function (Blueprint $table) {
            $table->dropForeign(['packaging_item_id']);
            $table->dropColumn(['packaging_item_id', 'packaging_quantity', 'packaging_cost']);
        });
    }
};
