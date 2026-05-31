<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('delivery_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->date('date');
            $table->string('sender_name')->default('Danyal Autos (Lahore)');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('receiver_name');
            $table->string('courier_company')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->integer('no_of_cartons')->default(0);
            $table->integer('no_of_bags')->default(0);
            $table->integer('total_parcels')->default(0);
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_receipts');
    }
};
