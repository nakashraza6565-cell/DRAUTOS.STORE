<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOpeningBalanceToFinancialAccounts extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('financial_accounts', 'opening_balance')) {
            Schema::table('financial_accounts', function (Blueprint $row) {
                $row->decimal('opening_balance', 15, 2)->default(0)->after('account_number');
            });
        }
    }

    public function down()
    {
        Schema::table('financial_accounts', function (Blueprint $row) {
            $row->dropColumn('opening_balance');
        });
    }
}
