<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeFinancialTables extends Migration
{
    public function up()
    {
        // Employee Salary Payments
        Schema::create('employee_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('payment_date');
            $table->enum('payment_type', ['salary', 'bonus', 'commission', 'overtime', 'other'])->default('salary');
            $table->float('amount');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'cheque', 'jazzcash', 'easypaisa'])->default('cash');
            $table->string('reference_number')->nullable();
            $table->string('month_year')->nullable(); // e.g., "2026-01" for January 2026 salary
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('paid_by'); // admin user_id
            $table->timestamps();
            
            $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('paid_by')->references('id')->on('users')->onDelete('cascade');
        });
        
        // Employee Advances/Loans
        Schema::create('employee_advances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->float('amount');
            $table->date('advance_date');
            $table->enum('status', ['active', 'partially_repaid', 'fully_repaid'])->default('active');
            $table->float('repaid_amount')->default(0);
            $table->float('balance')->default(0);
            $table->integer('installments')->default(1);
            $table->float('installment_amount')->default(0);
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('approved_by'); // admin user_id
            $table->timestamps();
            
            $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');
        });
        
        // Employee Advance Repayments
        Schema::create('employee_advance_repayments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advance_id');
            $table->float('amount');
            $table->date('repayment_date');
            $table->enum('repayment_method', ['salary_deduction', 'cash', 'other'])->default('salary_deduction');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('advance_id')->references('id')->on('employee_advances')->onDelete('cascade');
        });
        
        // Employee Commissions
        Schema::create('employee_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('order_id')->nullable(); // linked to sale order
            $table->float('sale_amount');
            $table->float('commission_rate'); // percentage
            $table->float('commission_amount');
            $table->date('commission_date');
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->unsignedBigInteger('payment_id')->nullable(); // linked to employee_payments
            $table->timestamps();
            
            $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('payment_id')->references('id')->on('employee_payments')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_commissions');
        Schema::dropIfExists('employee_advance_repayments');
        Schema::dropIfExists('employee_advances');
        Schema::dropIfExists('employee_payments');
    }
}
