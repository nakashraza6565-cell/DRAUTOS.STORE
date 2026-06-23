<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpgradeDiesSystemTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Alter dies table
        Schema::table('dies', function (Blueprint $table) {
            if (!Schema::hasColumn('dies', 'product_id')) {
                $table->unsignedBigInteger('product_id')->nullable()->after('id');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            }
            if (!Schema::hasColumn('dies', 'maker_id')) {
                $table->unsignedBigInteger('maker_id')->nullable()->after('product_id');
                $table->foreign('maker_id')->references('id')->on('suppliers')->onDelete('set null');
            }
            if (!Schema::hasColumn('dies', 'making_cost')) {
                $table->decimal('making_cost', 15, 2)->default(0.00)->after('maker');
            }
            if (!Schema::hasColumn('dies', 'photos')) {
                $table->text('photos')->nullable()->after('photo');
            }
        });

        // 2. Alter manufacturing_productions table
        if (Schema::hasTable('manufacturing_productions')) {
            Schema::table('manufacturing_productions', function (Blueprint $table) {
                if (!Schema::hasColumn('manufacturing_productions', 'die_id')) {
                    $table->unsignedBigInteger('die_id')->nullable()->after('manufacturing_bill_id');
                    $table->foreign('die_id')->references('id')->on('dies')->onDelete('set null');
                }
            });
        }

        // 3. Create die_custody_logs table
        if (!Schema::hasTable('die_custody_logs')) {
            Schema::create('die_custody_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('die_id');
                $table->string('custody_of');
                $table->string('custody_phone')->nullable();
                $table->dateTime('handover_date');
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('die_id')->references('id')->on('dies')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 4. Create die_quality_reports table
        if (!Schema::hasTable('die_quality_reports')) {
            Schema::create('die_quality_reports', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('die_id');
                $table->string('quality_status');
                $table->dateTime('report_date');
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('reported_by')->nullable();
                $table->timestamps();

                $table->foreign('die_id')->references('id')->on('dies')->onDelete('cascade');
                $table->foreign('reported_by')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 5. Create die_expenses table
        if (!Schema::hasTable('die_expenses')) {
            Schema::create('die_expenses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('die_id');
                $table->unsignedBigInteger('supplier_id')->nullable();
                $table->date('expense_date');
                $table->decimal('amount', 15, 2);
                $table->string('description');
                $table->string('payment_method')->nullable();
                $table->unsignedBigInteger('financial_account_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('die_id')->references('id')->on('dies')->onDelete('cascade');
                $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
                $table->foreign('financial_account_id')->references('id')->on('financial_accounts')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
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
        Schema::dropIfExists('die_expenses');
        Schema::dropIfExists('die_quality_reports');
        Schema::dropIfExists('die_custody_logs');

        if (Schema::hasTable('manufacturing_productions')) {
            Schema::table('manufacturing_productions', function (Blueprint $table) {
                if (Schema::hasColumn('manufacturing_productions', 'die_id')) {
                    $table->dropForeign(['die_id']);
                    $table->dropColumn('die_id');
                }
            });
        }

        Schema::table('dies', function (Blueprint $table) {
            if (Schema::hasColumn('dies', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }
            if (Schema::hasColumn('dies', 'maker_id')) {
                $table->dropForeign(['maker_id']);
                $table->dropColumn('maker_id');
            }
            if (Schema::hasColumn('dies', 'making_cost')) {
                $table->dropColumn('making_cost');
            }
            if (Schema::hasColumn('dies', 'photos')) {
                $table->dropColumn('photos');
            }
        });
    }
}
