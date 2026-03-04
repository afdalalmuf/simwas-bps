<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('spj_diklat', function (Blueprint $table) {                        
            $table->string('no_st')->nullable()->change();
            $table->date('tgl_mulai_st')->nullable()->change();
            $table->date('tgl_selesai_st')->nullable()->change();
            $table->string('st_path')->nullable()->change();
            $table->string('no_spd')->nullable()->change();
            $table->date('tgl_spd')->nullable()->change();
            $table->string('spd_path')->nullable()->change();
            $table->integer('nominal_hotel')->nullable()->change();
            $table->string('tipe_perjadin')->nullable()->change();            
            $table->integer('nominal_transport_berangkat')->nullable()->change();
            $table->integer('nominal_transport_pulang')->nullable()->change();
            $table->date('tgl_transport_berangkat')->nullable()->change();
            $table->date('tgl_transport_pulang')->nullable()->change();
            $table->string('transport_berangkat_path')->nullable()->change();
            $table->string('transport_pulang_path')->nullable()->change();
            $table->integer('hari_diklat')->nullable()->change();
            $table->integer('uang_diklat')->nullable()->change();
            $table->integer('uang_harian_berangkat')->nullable()->change();
            $table->integer('uang_harian_pulang')->nullable()->change();
            $table->string('laporan_perjadin_path')->nullable()->change();
            $table->string('fpp_path')->after('laporan_perjadin_path')->nullable();
            $table->string('kak_path')->after('laporan_perjadin_path')->nullable();
            $table->string('surat_pemanggilan_path')->after('laporan_perjadin_path')->nullable();
            $table->date('date_dikirim')->after('verifikator_id')->nullable();
            $table->date('date_diterima')->after('verifikator_id')->nullable();
            $table->date('date_ditolak')->after('verifikator_id')->nullable();
            $table->string('rekening_id')->after('fpp_path')->nullable();
            $table->foreign('rekening_id')->references('id_rekening')->on('master_rekenings')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('spj_diklat', function (Blueprint $table) {
            $table->string('no_st')->nullable(false)->change();
            $table->date('tgl_mulai_st')->nullable(false)->change();
            $table->date('tgl_selesai_st')->nullable(false)->change();
            $table->string('st_path')->nullable(false)->change();
            $table->string('no_spd')->nullable(false)->change();
            $table->date('tgl_spd')->nullable(false)->change();
            $table->string('spd_path')->nullable(false)->change();
            $table->integer('nominal_hotel')->nullable(false)->change();
            $table->string('tipe_perjadin')->nullable(false)->change();            
            $table->integer('nominal_transport_berangkat')->nullable(false)->change();
            $table->integer('nominal_transport_pulang')->nullable(false)->change();
            $table->date('tgl_transport_berangkat')->nullable(false)->change();
            $table->date('tgl_transport_pulang')->nullable(false)->change();
            $table->string('transport_berangkat_path')->nullable(false)->change();
            $table->string('transport_pulang_path')->nullable(false)->change();
            $table->integer('hari_diklat')->nullable(false)->change();
            $table->integer('uang_diklat')->nullable(false)->change();
            $table->integer('uang_harian_berangkat')->nullable(false)->change();
            $table->integer('uang_harian_pulang')->nullable(false)->change();
            $table->string('laporan_perjadin_path')->nullable(false)->change();
        });
    }
};
