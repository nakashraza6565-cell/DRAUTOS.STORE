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
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE supplier_ledgers MODIFY amount DECIMAL(15, 2) DEFAULT 0');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE supplier_ledgers MODIFY balance DECIMAL(15, 2) DEFAULT 0');

        if (Schema::hasColumn('suppliers', 'current_balance')) {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE suppliers MODIFY current_balance DECIMAL(15, 2) DEFAULT 0');
        }

        \Illuminate\Support\Facades\DB::statement('ALTER TABLE customer_ledgers MODIFY amount DECIMAL(15, 2) DEFAULT 0');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE customer_ledgers MODIFY balance DECIMAL(15, 2) DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE supplier_ledgers MODIFY amount FLOAT DEFAULT 0');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE supplier_ledgers MODIFY balance FLOAT DEFAULT 0');

        if (Schema::hasColumn('suppliers', 'current_balance')) {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE suppliers MODIFY current_balance FLOAT DEFAULT 0');
        }

        \Illuminate\Support\Facades\DB::statement('ALTER TABLE customer_ledgers MODIFY amount FLOAT DEFAULT 0');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE customer_ledgers MODIFY balance FLOAT DEFAULT 0');
    }
};
