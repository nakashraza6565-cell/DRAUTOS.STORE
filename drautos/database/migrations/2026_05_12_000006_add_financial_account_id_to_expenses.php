<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinancialAccountIdToExpenses extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('expenses', 'financial_account_id')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->unsignedBigInteger('financial_account_id')->nullable()->after('amount');
            });
        }
    }

    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('financial_account_id');
        });
    }
}
