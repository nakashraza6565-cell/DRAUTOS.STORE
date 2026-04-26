<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentRemindersTable extends Migration
{
    public function up()
    {
        Schema::create('payment_reminders', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['receivable', 'payable']); // to receive or to pay
            $table->morphs('party'); // customer or vendor
            $table->string('reference_number')->nullable(); // invoice/PO number
            $table->float('amount');
            $table->date('due_date');
            $table->enum('status', ['pending', 'partially_paid', 'completed', 'overdue'])->default('pending');
            $table->float('paid_amount')->default(0);
            $table->text('notes')->nullable();
            $table->boolean('whatsapp_sent')->default(false);
            $table->timestamp('whatsapp_sent_at')->nullable();
            $table->timestamps();
            
            $table->index(['due_date', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_reminders');
    }
}
