<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_ledgers', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->default(0)->change();
            $table->decimal('balance', 15, 2)->default(0)->change();
        });

        if (Schema::hasColumn('suppliers', 'current_balance')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->decimal('current_balance', 15, 2)->default(0)->change();
            });
        }

        Schema::table('customer_ledgers', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->default(0)->change();
            $table->decimal('balance', 15, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_ledgers', function (Blueprint $table) {
            $table->float('amount')->default(0)->change();
            $table->float('balance')->default(0)->change();
        });

        if (Schema::hasColumn('suppliers', 'current_balance')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->float('current_balance')->default(0)->change();
            });
        }

        Schema::table('customer_ledgers', function (Blueprint $table) {
            $table->float('amount')->default(0)->change();
            $table->float('balance')->default(0)->change();
        });
    }
};
