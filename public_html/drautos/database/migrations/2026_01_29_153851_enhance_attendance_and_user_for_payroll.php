<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnhanceAttendanceAndUserForPayroll extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'base_salary')) {
                $table->decimal('base_salary', 12, 2)->default(0)->after('current_balance');
            }
            if (!Schema::hasColumn('users', 'overtime_rate')) {
                $table->decimal('overtime_rate', 10, 2)->default(0)->after('base_salary');
            }
        });

        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'total_hours')) {
                $table->decimal('total_hours', 5, 2)->default(0)->after('clock_out');
            }
            if (!Schema::hasColumn('attendances', 'overtime_hours')) {
                $table->decimal('overtime_hours', 5, 2)->default(0)->after('total_hours');
            }
            if (!Schema::hasColumn('attendances', 'is_manual')) {
                $table->boolean('is_manual')->default(false)->after('status');
            }
            if (!Schema::hasColumn('attendances', 'notes')) {
                $table->text('notes')->nullable()->after('is_manual');
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
            $table->dropColumn(['base_salary', 'overtime_rate']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['total_hours', 'overtime_hours', 'is_manual', 'notes']);
        });
    }
}
