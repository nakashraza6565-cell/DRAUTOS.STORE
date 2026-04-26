<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Use raw SQL to avoid doctrine/dbal dependency
        DB::statement("ALTER TABLE orders MODIFY payment_method VARCHAR(191) NOT NULL DEFAULT 'cod'");
        DB::statement("ALTER TABLE orders MODIFY country VARCHAR(191) NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('cod','paypal') NOT NULL DEFAULT 'cod'");
    }
};
