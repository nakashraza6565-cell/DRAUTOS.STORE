<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChequesTable extends Migration
{
    public function up()
    {
        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['received', 'paid']); // received from customer or paid to vendor
            $table->string('cheque_number');
            $table->float('amount');
            $table->date('cheque_date');
            $table->date('clearing_date')->nullable();
            $table->morphs('party'); // customer or vendor (polymorphic)
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->enum('status', ['pending', 'cleared', 'bounced', 'cancelled'])->default('pending');
            $table->date('actual_clearing_date')->nullable();
            $table->integer('delay_days')->default(0);
            $table->text('notes')->nullable();
            $table->string('reference_number')->nullable(); // linked invoice/payment
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['cheque_date', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('cheques');
    }
}
