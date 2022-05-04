<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Ejecuta las migraciones
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('nombre');
            $table->integer('user_id');
            $table->integer('saldo')->default(0);
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
        Schema::dropIfExists('accounts');
    }
}
