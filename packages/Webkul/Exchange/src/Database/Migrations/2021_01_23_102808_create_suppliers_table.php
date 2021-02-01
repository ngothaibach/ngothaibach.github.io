<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 30);
            $table->string('name', 128);
            $table->string('phone', 20);
            $table->string('address', 256);
            $table->string('email', 128);
            $table->string('company', 256)->nullable();
            $table->string('tax_id_num', 256)->nullable();
            $table->integer('supplier_group_id')->unsigned();
            $table->string('note', 20)->nullable();
            $table->date('created_date')->nullable();
            $table->date('updated_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            Schema::dropIfExists('suppliers');
        });
    }
}
