<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('inventory_incoming', 'shipping_cost')) {
            Schema::table('inventory_incoming', function (Blueprint $table) {
                $table->decimal('shipping_cost', 15, 2)->default(0)->after('received_date');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('inventory_incoming', 'shipping_cost')) {
            Schema::table('inventory_incoming', function (Blueprint $table) {
                $table->dropColumn('shipping_cost');
            });
        }
    }
};
