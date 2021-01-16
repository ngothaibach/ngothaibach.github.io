<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductExchangeNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_exchange_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('exchange_note_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('transfer_qty');
            $table->integer('receipt_qty')->default(0);;
            $table->decimal('price', 12, 4)->default(0);
            $table->decimal('discount', 12, 4)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_exchange_notes', function (Blueprint $table) {
            Schema::dropIfExists('product_exchange_notes');
        });
    }
}
