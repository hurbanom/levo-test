<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsCountTable extends Migration
{
    /**
     * Ejecuta las migraciones
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions_count', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_uuid');
            $table->integer('count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
