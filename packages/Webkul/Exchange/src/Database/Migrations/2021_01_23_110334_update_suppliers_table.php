<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('company', 256)->nullable()->change();
            $table->string('tax_id_num', 256)->nullable()->change();
            $table->integer('supplier_group_id')->unsigned()->change();
            $table->string('note', 20)->nullable()->change();
            $table->date('created_date')->nullable()->change();
            $table->date('updated_date')->nullable()->change();
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
            //
        });
    }
}
