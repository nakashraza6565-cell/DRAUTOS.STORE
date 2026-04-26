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
        Schema::table('cash_registers', function (Blueprint $table) {
            if (!Schema::hasColumn('cash_registers', 'opening_amount')) {
                $table->decimal('opening_amount', 15, 2)->default(0)->after('user_id');
            }
            if (!Schema::hasColumn('cash_registers', 'closing_amount')) {
                $table->decimal('closing_amount', 15, 2)->nullable()->after('opening_amount');
            }
            if (!Schema::hasColumn('cash_registers', 'note')) {
                $table->text('note')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cash_registers', function (Blueprint $table) {
            // No easy rollback for conditional additions
        });
    }
};
