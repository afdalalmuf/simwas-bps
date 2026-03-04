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
        Schema::table('upload_skp_bulanan', function (Blueprint $table) {
            $table->string('kat_rating_hasil_kerja')->nullable()->after('rating_hasil_kerja');
            $table->string('kat_rating_perilaku_kerja')->nullable()->after('rating_perilaku_kerja');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
