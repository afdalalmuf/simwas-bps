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
        Schema::create('kipapp_syncs', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('month');
            $table->year('year');
            $table->string('niplama', 9);
            $table->uuid('id_pelaksana');
            $table->string('koderk');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('kegiatan');
            $table->text('capaian');
            $table->string('link');
            $table->boolean('capaianSKP')->default(true);
            $table->boolean('synced')->default(false);
            $table->timestamps();

            $table->index(['id_pelaksana', 'year', 'month']);

            $table->foreign('id_pelaksana')
                ->references('id_pelaksana')
                ->on('pelaksana_tugas')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kipapp_syncs');
    }
};
