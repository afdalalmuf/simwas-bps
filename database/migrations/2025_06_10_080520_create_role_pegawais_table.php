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
        Schema::create('role_pegawais', function (Blueprint $table) {
            $table->ulid('id_role_pegawai')->unique()->primary()->default(Ulid::generate());
            $table->string('id_pegawai');
            $table->foreign('id_pegawai')->references('id')->on('users')->onDelete('restrict');
            $table->string('id_role');
            $table->foreign('id_role')->references('id')->on('master_roles')->onDelete('restrict');                                   
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
        Schema::dropIfExists('role_pegawais');
    }
};
