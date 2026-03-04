<?php

use Symfony\Component\Uid\Ulid;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rencana_diklats', function (Blueprint $table) {
            $table->ulid('id')->unique()->primary()->default(Ulid::generate());
            $table->string('name');
            $table->string('id_pegawai');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('metode');
            $table->string('penyelenggara');
            $table->integer('biaya')->nullable();
            $table->integer('transport')->nullable();
            $table->integer('akomodasi')->nullable();
            $table->integer('uang_saku')->nullable();
            $table->string('pembebanan_perjadin')->nullable();
            $table->string('akun_anggaran')->nullable();
            $table->string('status');
            $table->string('keterangan')->nullable();
            $table->timestamps();
            
            $table->foreign('penyelenggara')->references('id')->on('master_penyelenggaras')->onDelete('restrict');
            $table->foreign('id_pegawai')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rencana_diklats');
    }
};
