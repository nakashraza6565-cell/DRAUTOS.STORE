<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountTransactionsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('account_transactions')) {
            Schema::create('account_transactions', function (Blueprint $row) {
                $row->id();
                $row->unsignedBigInteger('financial_account_id');
                $row->decimal('amount', 15, 2);
                $row->enum('type', ['in', 'out']); // in = credit (received), out = debit (paid)
                $row->string('reference_type')->nullable(); // CustomerLedger, SupplierLedger, Expense, etc.
                $row->unsignedBigInteger('reference_id')->nullable();
                $row->string('description')->nullable();
                $row->date('transaction_date');
                $row->timestamps();

                $row->foreign('financial_account_id')->references('id')->on('financial_accounts')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('account_transactions');
    }
}
