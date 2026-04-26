<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnhanceUsersTableForCustomers extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Customer-specific fields
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'transport_company')) {
                $table->string('transport_company')->nullable()->after('city');
            }
            if (!Schema::hasColumn('users', 'credit_limit')) {
                $table->float('credit_limit')->default(0)->after('transport_company');
            }
            if (!Schema::hasColumn('users', 'current_balance')) {
                $table->float('current_balance')->default(0)->after('credit_limit');
            }
            if (!Schema::hasColumn('users', 'customer_rating')) {
                $table->decimal('customer_rating', 3, 2)->default(5.00)->after('current_balance')->comment('1-5 rating');
            }
            if (!Schema::hasColumn('users', 'payment_rating')) {
                $table->decimal('payment_rating', 3, 2)->default(5.00)->after('customer_rating')->comment('Payment history rating');
            }
            if (!Schema::hasColumn('users', 'behavioral_rating')) {
                $table->decimal('behavioral_rating', 3, 2)->default(5.00)->after('payment_rating')->comment('Behavioral rating');
            }
            if (!Schema::hasColumn('users', 'loyalty_points')) {
                $table->integer('loyalty_points')->default(0)->after('behavioral_rating');
            }
            if (!Schema::hasColumn('users', 'goodwill_points')) {
                $table->integer('goodwill_points')->default(0)->after('loyalty_points');
            }
            if (!Schema::hasColumn('users', 'customer_category')) {
                $table->string('customer_category')->nullable()->after('goodwill_points'); // VIP, Regular, Wholesale, etc.
            }
            if (!Schema::hasColumn('users', 'payment_terms')) {
                $table->enum('payment_terms', ['cash', 'credit_7', 'credit_15', 'credit_30', 'credit_custom'])->default('cash')->after('customer_category');
            }
            if (!Schema::hasColumn('users', 'custom_payment_days')) {
                $table->integer('custom_payment_days')->nullable()->after('payment_terms');
            }
            
            // Vendor/Supplier specific (if user acts as vendor)
            if (!Schema::hasColumn('users', 'vendor_category')) {
                $table->string('vendor_category')->nullable()->after('custom_payment_days');
            }
            if (!Schema::hasColumn('users', 'vendor_rating')) {
                $table->decimal('vendor_rating', 3, 2)->default(5.00)->after('vendor_category');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'city', 'transport_company', 'credit_limit', 'current_balance',
                'customer_rating', 'payment_rating', 'behavioral_rating',
                'loyalty_points', 'goodwill_points', 'customer_category',
                'payment_terms', 'custom_payment_days', 'vendor_category', 'vendor_rating'
            ]);
        });
    }
}
