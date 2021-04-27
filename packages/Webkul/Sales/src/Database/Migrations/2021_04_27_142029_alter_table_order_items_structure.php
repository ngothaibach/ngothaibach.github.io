<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableOrderItemsStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('price', 16,4)->default(0)->change();
            $table->decimal('base_price', 16,4)->default(0)->change();

            $table->decimal('total', 16,4)->default(0)->change();
            $table->decimal('base_total', 16,4)->default(0)->change();
            $table->decimal('total_invoiced', 16,4)->default(0)->change();
            $table->decimal('base_total_invoiced', 16,4)->default(0)->change();
            $table->decimal('amount_refunded', 16,4)->default(0)->change();
            $table->decimal('base_amount_refunded', 16,4)->default(0)->change();

            $table->decimal('discount_percent', 16, 4)->default(0)->nullable()->change();
            $table->decimal('discount_amount', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_discount_amount', 16, 4)->default(0)->nullable()->change();
            $table->decimal('discount_invoiced', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_discount_invoiced', 16, 4)->default(0)->nullable()->change();
            $table->decimal('discount_refunded', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_discount_refunded', 16, 4)->default(0)->nullable()->change();

            $table->decimal('tax_percent', 16, 4)->default(0)->nullable()->change();
            $table->decimal('tax_amount', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_tax_amount', 16, 4)->default(0)->nullable()->change();
            $table->decimal('tax_amount_invoiced', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_tax_amount_invoiced', 16, 4)->default(0)->nullable()->change();
            $table->decimal('tax_amount_refunded', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_tax_amount_refunded', 16, 4)->default(0)->nullable()->change();
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
    }
}
