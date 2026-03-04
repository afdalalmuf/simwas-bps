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
        Schema::table('kategori_kompetensis', function (Blueprint $table) {
            $table->integer('kat_simpeg')->nullable()->after('bentuk');
        });
        Schema::table('jenis_kompetensis', function (Blueprint $table) {
            $table->integer('jenis_simpeg')->nullable()->after('nama');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kategori_kompetensis', function (Blueprint $table) {
            //
        });
        Schema::table('jenis_kompetensis', function (Blueprint $table) {
            //
        });
    }
};
