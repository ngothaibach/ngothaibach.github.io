<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableInvoicesStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
            $table->decimal('sub_total', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_sub_total', 16, 4)->default(0)->nullable()->change();

            $table->decimal('grand_total', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_grand_total', 16, 4)->default(0)->nullable()->change();

            $table->decimal('shipping_amount', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_shipping_amount', 16, 4)->default(0)->nullable()->change();

            $table->decimal('tax_amount', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_tax_amount', 16, 4)->default(0)->nullable()->change();

            $table->decimal('discount_amount', 16, 4)->default(0)->nullable()->change();
            $table->decimal('base_discount_amount', 16, 4)->default(0)->nullable()->change();

            $table->decimal('collection_diff',16,4)->change();
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
