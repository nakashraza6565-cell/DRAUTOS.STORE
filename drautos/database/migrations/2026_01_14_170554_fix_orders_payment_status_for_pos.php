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
        // Change payment_status to string to support 'partial' and other statuses
        // Using DB::statement to avoid enum modification issues with doctrine/dbal
        DB::statement("ALTER TABLE orders MODIFY payment_status VARCHAR(191) NOT NULL DEFAULT 'unpaid'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Standard Laravel enum modification is tricky, but we can go back to enum if needed
        // DB::statement("ALTER TABLE orders MODIFY payment_status ENUM('paid','unpaid') NOT NULL DEFAULT 'unpaid'");
    }
};
