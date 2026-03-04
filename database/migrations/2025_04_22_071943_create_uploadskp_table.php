<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Uid\Ulid;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upload_skp_bulanan', function(Blueprint $table){
            $table->ulid('id')->primary()->default(Ulid::generate());
            $table->string('tahun', 4);
            $table->string('jenis', 255);
            $table->string('bulan', 2)->nullable();
            $table->decimal('rating_hasil_kerja', total: 5, places:2)->nullable();
            $table->decimal('rating_perilaku_kerja', total: 5, places:2)->nullable();
            $table->string('predikat_kinerja', 255)->nullable();
            $table->string('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->string('status');
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
        Schema::dropIfExists('upload_skp_bulanan');
    }
};
