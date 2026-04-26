<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRatingToUsersAndSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'rating')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('rating')->default(0)->nullable()->after('status');
            });
        }

        if (!Schema::hasColumn('suppliers', 'rating')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->integer('rating')->default(0)->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('rating');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('rating');
        });
    }
}
