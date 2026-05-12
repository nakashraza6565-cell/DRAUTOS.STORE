<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialAccountsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('financial_accounts')) {
            Schema::create('financial_accounts', function (Blueprint $row) {
                $row->id();
                $row->string('name');
                $row->string('type')->nullable(); // Bank, Wallet, Cash
                $row->string('account_number')->nullable();
                $row->decimal('current_balance', 15, 2)->default(0);
                $row->enum('status', ['active', 'inactive'])->default('active');
                $row->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('financial_accounts');
    }
}
