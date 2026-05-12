<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinancialAccountIdToLedgers extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('customer_ledgers', 'financial_account_id')) {
            Schema::table('customer_ledgers', function (Blueprint $row) {
                $row->unsignedBigInteger('financial_account_id')->nullable()->after('user_id');
            });
        }
        
        if (!Schema::hasColumn('supplier_ledgers', 'financial_account_id')) {
            Schema::table('supplier_ledgers', function (Blueprint $row) {
                $row->unsignedBigInteger('financial_account_id')->nullable()->after('supplier_id');
            });
        }
    }

    public function down()
    {
        Schema::table('customer_ledgers', function (Blueprint $row) {
            $row->dropColumn('financial_account_id');
        });
        
        Schema::table('supplier_ledgers', function (Blueprint $row) {
            $row->dropColumn('financial_account_id');
        });
    }
}
