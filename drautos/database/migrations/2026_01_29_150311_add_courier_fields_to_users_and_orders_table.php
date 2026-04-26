<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCourierFieldsToUsersAndOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'courier_company')) {
                $table->string('courier_company')->nullable()->after('shipping_address');
            }
            if (!Schema::hasColumn('users', 'courier_number')) {
                $table->string('courier_number')->nullable()->after('courier_company');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'courier_company')) {
                $table->string('courier_company')->nullable()->after('address2');
            }
            if (!Schema::hasColumn('orders', 'courier_number')) {
                $table->string('courier_number')->nullable()->after('courier_company');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['courier_company', 'courier_number']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['courier_company', 'courier_number']);
        });
    }
}
