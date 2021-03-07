<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateExchangeNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exchange_notes', function (Blueprint $table) {
            Schema::table('exchange_notes', function (Blueprint $table) {
                $table->string('supplier_id')->nullable();
                $table->string('from_inventory_source_id')->nullable();
                $table->string('to_inventory_source_id')->nullable();
                $table->string('created_date')->nullable();
                $table->string('updated_date')->nullable();
                $table->string('transfer_date')->nullable();
                $table->string('receipt_date')->nullable();
                $table->string('note')->nullable();                
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exchange_notes', function (Blueprint $table) {
            //
        });
    }
}
