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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'loyalty_rating')) {
                $table->tinyInteger('loyalty_rating')->nullable()->default(0);
            }
            if (!Schema::hasColumn('users', 'goodwill_rating')) {
                $table->tinyInteger('goodwill_rating')->nullable()->default(0);
            }
            if (!Schema::hasColumn('users', 'payment_rating')) {
                $table->tinyInteger('payment_rating')->nullable()->default(0);
            }
            if (!Schema::hasColumn('users', 'behaviour_rating')) {
                $table->tinyInteger('behaviour_rating')->nullable()->default(0);
            }
        });

        Schema::table('suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('suppliers', 'loyalty_rating')) {
                $table->tinyInteger('loyalty_rating')->nullable()->default(0);
            }
            if (!Schema::hasColumn('suppliers', 'goodwill_rating')) {
                $table->tinyInteger('goodwill_rating')->nullable()->default(0);
            }
            if (!Schema::hasColumn('suppliers', 'payment_rating')) {
                $table->tinyInteger('payment_rating')->nullable()->default(0);
            }
            if (!Schema::hasColumn('suppliers', 'behaviour_rating')) {
                $table->tinyInteger('behaviour_rating')->nullable()->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['loyalty_rating', 'goodwill_rating', 'payment_rating', 'behaviour_rating']);
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['loyalty_rating', 'goodwill_rating', 'payment_rating', 'behaviour_rating']);
        });
    }
};
