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
        Schema::create('peminjaman_arsips', function (Blueprint $table) {
            $table->id();

            // Relasi ke arsip yang dipinjam
            $table->foreignId('arsip_id')
                ->constrained('arsips')
                ->onDelete('cascade');

            // Peminjam (pegawai)
            $table->char('user_id', 26);
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Alasan pengajuan dari pegawai
            $table->text('alasan_peminjaman');

            // Status: MENUNGGU | DISETUJUI | DITOLAK
            $table->enum('status', ['MENUNGGU', 'DISETUJUI', 'DITOLAK'])
                ->default('MENUNGGU');

            // Diisi oleh arsiparis saat menolak
            $table->text('alasan_penolakan')->nullable();

            // Diisi saat disetujui
            $table->timestamp('disetujui_pada')->nullable();

            // Akses otomatis berakhir 7 hari setelah disetujui
            $table->timestamp('berakhir_pada')->nullable();

            // Arsiparis yang memproses
            $table->char('diproses_oleh', 26)->nullable();
            $table->foreign('diproses_oleh')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

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
        Schema::dropIfExists('peminjaman_arsips');
    }
};
