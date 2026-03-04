<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Uid\Ulid;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spj_diklat', function (Blueprint $table) {
            $table->ulid('id_spjDiklat')->unique()->primary()->default(Ulid::generate());
            $table->string('rencanaDiklat_id');
            $table->foreign('rencanaDiklat_id')->references('id')->on('rencana_diklats')->onDelete('restrict');
            $table->string('no_st');
            $table->date('tgl_mulai_st');
            $table->date('tgl_selesai_st');
            $table->string('st_path');
            $table->string('no_spd');
            $table->date('tgl_spd');
            $table->string('spd_path');
            $table->integer('nominal_hotel');
            $table->string('hotel_path')->nullable();
            $table->string('tipe_perjadin');
            $table->string('jarak')->nullable();
            $table->integer('nominal_transport_berangkat');
            $table->integer('nominal_transport_pulang');
            $table->date('tgl_transport_berangkat');
            $table->date('tgl_transport_pulang');
            $table->string('transport_berangkat_path');
            $table->string('transport_pulang_path');
            $table->integer('hari_diklat');
            $table->integer('uang_diklat');
            $table->integer('uang_harian_berangkat');
            $table->integer('uang_harian_pulang');
            $table->string('laporan_perjadin_path');
            $table->string('status');
            $table->string('catatan')->nullable();
            $table->string('verifikator_id')->nullable();
            $table->foreign('verifikator_id')->references('id')->on('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spj_diklat');
    }
};
