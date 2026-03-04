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
            $table->integer('nominal_translok')->nullable()->after('nominal_transport_pulang');
            $table->string('translok_path')->nullable()->after('transport_pulang_path');
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
            //
        });
    }
};
