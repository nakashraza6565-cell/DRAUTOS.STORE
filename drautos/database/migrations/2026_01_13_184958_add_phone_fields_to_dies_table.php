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
        Schema::table('dies', function (Blueprint $table) {
            $table->string('maker_phone')->nullable()->after('maker');
            $table->string('custody_phone')->nullable()->after('custody_of');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dies', function (Blueprint $table) {
            $table->dropColumn(['maker_phone', 'custody_phone']);
        });
    }
};
