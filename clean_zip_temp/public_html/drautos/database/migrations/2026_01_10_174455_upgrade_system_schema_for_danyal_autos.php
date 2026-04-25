<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpgradeSystemSchemaForDanyalAutos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Suppliers Table
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('company_name')->nullable();
            $table->string('rating')->default('5');
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // 2. Warehouses Table
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // 3. Die Management Table
        Schema::create('dies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rack_number')->nullable();
            $table->string('maker')->nullable();
            $table->string('die_type')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('custody_of')->nullable();
            $table->integer('goods_produced')->default(0);
            $table->string('quality_status')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // 4. Product Variants Table (for sizing/attributes)
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('variant_name'); // e.g., "Size", "Color"
            $table->string('variant_value'); // e.g., "Half Plate", "Full Plate", "Medium"
            $table->float('additional_price')->default(0);
            $table->integer('stock')->default(0);
            $table->string('sku')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
        });

        // 5. Update Products Table
        Schema::table('products', function (Blueprint $blueprint) {
            $blueprint->string('sku')->nullable()->unique()->after('slug');
            $blueprint->string('barcode')->nullable()->after('sku');
            $blueprint->float('purchase_price')->default(0)->after('price');
            $blueprint->integer('low_stock_threshold')->default(5)->after('stock');
            $blueprint->unsignedBigInteger('supplier_id')->nullable()->after('brand_id');
            $blueprint->unsignedBigInteger('warehouse_id')->nullable()->after('supplier_id');
            $blueprint->float('weight')->nullable()->after('warehouse_id');
            $blueprint->float('packaging_cost')->default(0)->after('purchase_price');
            $blueprint->string('batch_number')->nullable()->after('barcode');
            
            $blueprint->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $blueprint->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('set null');
        });

        // 6. Update Orders Table
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('order_type', ['store', 'local'])->default('store')->after('order_number');
            $table->string('consignment_number')->nullable()->after('status');
            $table->text('transport_details')->nullable()->after('consignment_number');
            $table->date('expected_delivery_date')->nullable()->after('transport_details');
            $table->text('pending_items_note')->nullable()->after('expected_delivery_date');
            $table->float('staff_commission')->default(0)->after('total_amount');
            $table->unsignedBigInteger('staff_id')->nullable()->after('user_id');
            $table->foreign('staff_id')->references('id')->on('users')->onDelete('set null');
        });

        // 7. Cash Register Sessions
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->float('opening_balance')->default(0);
            $table->float('closing_balance')->nullable();
            $table->float('expected_balance')->nullable();
            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();
            $table->string('status')->default('open');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        // 8. Employee Attendance
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->time('clock_in');
            $table->time('clock_out')->nullable();
            $table->string('lat_in')->nullable();
            $table->string('lng_in')->nullable();
            $table->string('status')->default('present'); // present, absent, leave
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
            $table->dropColumn(['order_type', 'consignment_number', 'transport_details', 'expected_delivery_date', 'pending_items_note', 'staff_commission', 'staff_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn(['sku', 'barcode', 'purchase_price', 'low_stock_threshold', 'supplier_id', 'warehouse_id', 'weight', 'packaging_cost', 'batch_number']);
        });

        Schema::dropIfExists('attendances');
        Schema::dropIfExists('cash_registers');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('dies');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('suppliers');
    }
}
