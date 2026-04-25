<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_ledgers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->date('transaction_date');
            $table->enum('type', ['debit', 'credit']); // debit = we owe more (purchase), credit = we paid (payment)
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->float('amount')->default(0);
            $table->float('balance')->default(0);
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->timestamps();
        });

        if (!Schema::hasColumn('suppliers', 'current_balance')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->float('current_balance')->default(0)->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('current_balance');
        });
        Schema::dropIfExists('supplier_ledgers');
    }
}
