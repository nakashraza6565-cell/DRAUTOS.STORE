<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('supplier_ledgers', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('category');
            $table->json('payment_details')->nullable()->after('payment_method');
        });

        // Also add to customer_ledger for consistency if it doesn't have it
        if (Schema::hasTable('customer_ledgers')) {
            Schema::table('customer_ledgers', function (Blueprint $table) {
                if (!Schema::hasColumn('customer_ledgers', 'payment_method')) {
                    $table->string('payment_method')->nullable()->after('category');
                    $table->json('payment_details')->nullable()->after('payment_method');
                }
            });
        }
    }

    public function down()
    {
        Schema::table('supplier_ledgers', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_details']);
        });
        
        if (Schema::hasTable('customer_ledgers')) {
            Schema::table('customer_ledgers', function (Blueprint $table) {
                $table->dropColumn(['payment_method', 'payment_details']);
            });
        }
    }
};
