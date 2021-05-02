<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableOrdersTableColumnStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('grand_total', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_grand_total', 16, 4)->default(0)->nullable()->change();
            $table->decimal('grand_total_invoiced', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_grand_total_invoiced', 16, 4)->default(0)->nullable()->change();
            $table->decimal('grand_total_refunded', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_grand_total_refunded', 16, 4)->default(0)->nullable()->change();

            $table->decimal('sub_total', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_sub_total', 16, 4)->default(0)->nullable()->change();
            $table->decimal('sub_total_invoiced', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_sub_total_invoiced', 16, 4)->default(0)->nullable()->change();
            $table->decimal('sub_total_refunded', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_sub_total_refunded', 16, 4)->default(0)->nullable()->change();

            $table->decimal('discount_percent', 16, 4)->default(0)->nullable()->change();
            $table->decimal('discount_amount', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_discount_amount', 16, 4)->default(0)->nullable()->change();
            $table->decimal('discount_invoiced', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_discount_invoiced', 16, 4)->default(0)->nullable()->change();
            $table->decimal('discount_refunded', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_discount_refunded', 16, 4)->default(0)->nullable()->change();

            $table->decimal('tax_amount', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_tax_amount', 16, 4)->default(0)->nullable()->change();
            $table->decimal('tax_amount_invoiced', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_tax_amount_invoiced', 16, 4)->default(0)->nullable()->change();
            $table->decimal('tax_amount_refunded', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_tax_amount_refunded', 16, 4)->default(0)->nullable()->change();

            $table->decimal('shipping_amount', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_shipping_amount', 16, 4)->default(0)->nullable()->change();
            $table->decimal('shipping_invoiced', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_shipping_invoiced', 16, 4)->default(0)->nullable()->change();
            $table->decimal('shipping_refunded', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_shipping_refunded', 16, 4)->default(0)->nullable()->change();

            $table->decimal('shipping_discount_amount', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_shipping_discount_amount', 16, 4)->default(0)->nullable()->change();

            $table->decimal('collection_diff',16,4)->change();
            $table->decimal('customer_paid',16,4)->change();
            $table->decimal('customer_remain',16,4)->change();

            
            $table->integer('sales_id')->change();
            $table->integer('inventory_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('orders', function (Blueprint $table) {

        });
    }
}
