<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinancialAccountIdToCashRegisters extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('cash_registers', 'financial_account_id')) {
            Schema::table('cash_registers', function (Blueprint $row) {
                $row->unsignedBigInteger('financial_account_id')->nullable()->after('user_id');
            });
        }
    }

    public function down()
    {
        Schema::table('cash_registers', function (Blueprint $row) {
            $row->dropColumn('financial_account_id');
        });
    }
}
