<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_return_items', function (Blueprint $table) {
            // Track which source order each returned item came from
            $table->unsignedBigInteger('order_id')->nullable()->after('sale_return_id');
        });
    }

    public function down(): void
    {
        Schema::table('sale_return_items', function (Blueprint $table) {
            $table->dropColumn('order_id');
        });
    }
};
