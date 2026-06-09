<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpgradeHrModuleSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Add fields to users table for dynamic salaries
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'salary_type')) {
                $table->string('salary_type')->default('monthly')->after('base_salary')->comment('daily, weekly, monthly');
            }
            if (!Schema::hasColumn('users', 'daily_wage')) {
                $table->decimal('daily_wage', 15, 2)->default(0)->after('salary_type');
            }
        });

        // 2. Add approval fields to expenses table
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'approval_status')) {
                $table->string('approval_status')->default('approved')->after('status')->comment('pending, approved, rejected');
            }
            if (!Schema::hasColumn('expenses', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approval_status');
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['salary_type', 'daily_wage']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'approved_by']);
        });
    }
}
