<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableInvoiceItemsStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('invoice_items', function (Blueprint $table) {

            $table->decimal('price', 16,4)->default(0)->change();
            $table->decimal('base_price', 16,4)->default(0)->change();

            $table->decimal('total', 16,4)->default(0)->change();
            $table->decimal('base_total', 16,4)->default(0)->change();

            $table->decimal('tax_amount', 16,4)->default(0)->nullable()->change();
            $table->decimal('base_tax_amount', 16,4)->default(0)->nullable()->change();

            $table->decimal('discount_percent', 16, 4)->default(0)->nullable()->change();
            $table->decimal('discount_amount', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_discount_amount', 16, 4)->default(0)->nullable()->change();
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
