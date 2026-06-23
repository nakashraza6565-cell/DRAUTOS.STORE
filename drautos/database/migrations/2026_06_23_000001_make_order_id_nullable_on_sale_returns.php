<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_returns', function (Blueprint $table) {
            // Drop existing FK before modifying
            $table->dropForeign(['order_id']);
            // Make nullable for multi-order consolidated returns
            $table->unsignedBigInteger('order_id')->nullable()->change();
            // Re-add FK allowing null
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('sale_returns', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->unsignedBigInteger('order_id')->nullable(false)->change();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }
};
