<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExchangeNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchange_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', array('receipt', 'transfer'));
            $table->enum('status', array('temporary', 'received', 'cancel', 'transfering'));
            $table->integer('created_user_id')->unsigned();
            $table->integer('receipt_user_id')->unsigned();
            $table->integer('supplier_id')->unsigned();
            $table->decimal('total', $precision = 16, $scale = 4)->unsigned();
            $table->integer('from_inventory_source_id')->unsigned();
            $table->integer('to_inventory_source_id')->unsigned();
            $table->date('created_date')->nullable();
            $table->date('updated_date')->nullable();
            $table->date('transfer_date')->nullable();
            $table->date('receipt_date')->nullable();
            $table->string('note')->nullable();
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
            Schema::dropIfExists('exchange_notes');
        });
    }
}
