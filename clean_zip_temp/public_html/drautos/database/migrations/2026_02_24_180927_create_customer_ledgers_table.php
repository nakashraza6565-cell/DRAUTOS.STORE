<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('customer_ledgers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('transaction_date');
            $table->enum('type', ['credit', 'debit']);
            $table->enum('category', ['order', 'payment', 'return', 'manual'])->default('manual');
            $table->string('description')->nullable();
            $table->float('amount');
            $table->float('balance')->default(0);
            $table->unsignedBigInteger('reference_id')->nullable(); // Can be order_id, return_id, etc.
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_ledgers');
    }
};
