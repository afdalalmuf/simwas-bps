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
        Schema::create('arsips', function (Blueprint $table) {
            $table->id();
            $table->string('kode_klasifikasi');
            $table->string('judul_berkas');
            $table->text('uraian')->nullable();
            $table->enum('skkaa', ['BIASA', 'TERBATAS', 'RAHASIA']);
            $table->string('unit_cipta');
            $table->integer('masa_retensi'); //dalam tahun 
            $table->enum('status', [
                'DRAFT',
                'AKTIF',
                'RETENSI HABIS',
                'NONAKTIF'
            ])->default('DRAFT');
            $table->date('tanggal_dibuat')->nullable();
            $table->date('berakhir_pada')->nullable();
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
        Schema::dropIfExists('arsips');
    }
};
