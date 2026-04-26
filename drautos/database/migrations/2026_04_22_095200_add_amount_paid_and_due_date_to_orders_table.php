<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'amount_paid')) {
                $table->float('amount_paid')->nullable()->after('total_amount');
            }
            if (!Schema::hasColumn('orders', 'due_date')) {
                $table->string('due_date')->nullable()->after('amount_paid');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['amount_paid', 'due_date']);
        });
    }
};
