<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorPaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('vendor_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique();
            $table->unsignedBigInteger('supplier_id');
            $table->float('amount');
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'cheque', 'bank_transfer', 'jazzcash', 'easypaisa'])->default('cash');
            $table->string('reference_number')->nullable(); // cheque no, transaction ID, etc.
            $table->unsignedBigInteger('purchase_order_id')->nullable(); // linked PO
            $table->unsignedBigInteger('cheque_id')->nullable(); // if paid via cheque
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('paid_by'); // admin user_id
            $table->timestamps();
            
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('set null');
            $table->foreign('cheque_id')->references('id')->on('cheques')->onDelete('set null');
            $table->foreign('paid_by')->references('id')->on('users')->onDelete('cascade');
        });
        
        // Supplier Categories
        Schema::create('supplier_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });
        
        // Link suppliers to categories
        Schema::table('suppliers', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('status');
            $table->enum('payment_terms', ['cash', 'credit_7', 'credit_15', 'credit_30', 'credit_custom'])->default('cash')->after('category_id');
            $table->integer('custom_payment_days')->nullable()->after('payment_terms');
            $table->float('current_balance')->default(0)->after('custom_payment_days');
            
            $table->foreign('category_id')->references('id')->on('supplier_categories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'payment_terms', 'custom_payment_days', 'current_balance']);
        });
        
        Schema::dropIfExists('supplier_categories');
        Schema::dropIfExists('vendor_payments');
    }
}
