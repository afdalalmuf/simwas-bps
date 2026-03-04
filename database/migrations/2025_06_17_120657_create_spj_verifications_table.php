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
        Schema::create('spj_verifications', function (Blueprint $table) {
            $table->id();
            $table->ulid('spj_diklat_id'); // Foreign key to SPJ Diklat, if applicable
            $table->foreign('spj_diklat_id')->references('id_spjDiklat')->on('spj_diklat')->onDelete('cascade');
            $table->enum('status', ['valid', 'invalid'])->nullable(); // Status of the SPJ verification
            $table->text('comments')->nullable(); // Comments from the verifier
            $table->ulid('verifier_id')->nullable(); // ID of the user who verified
            $table->foreign('verifier_id')->references('id')->on('users')->onDelete('restrict');
            $table->timestamp('verified_at')->nullable(); // Timestamp when the SPJ was verified
            $table->enum('document_type', [
                'surat-tugas',
                'spd',
                'form-permintaan',
                'laporan',
                'kak',
                'surat-pemanggilan',
                'hotel',
                'translok',
                'transport-berangkat',
                'transport-pulang'
            ]); // Type of SPJ, e.g., 'diklat', 'non-diklat'
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
        Schema::dropIfExists('spj_verifications');
    }
};
