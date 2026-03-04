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
        Schema::table('kompetensis', function (Blueprint $table) {
            $table->integer('skala')->after('pegawai_id');
            $table->integer('bentuk')->after('skala');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kompetensis', function (Blueprint $table) {
            //
        });
    }
};
