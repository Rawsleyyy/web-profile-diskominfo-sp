<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('skms', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('jenis_layanan_id');
        $table->string('nama');
        $table->string('no_whatsapp');
        $table->integer('usia');
        $table->string('jenis_kelamin');
        $table->string('pendidikan');
        $table->string('pekerjaan');
        $table->string('kecamatan');
        $table->string('kelurahan');
        $table->string('jawaban_1');
        $table->string('jawaban_2');
        $table->string('jawaban_3');
        $table->string('jawaban_4');
        $table->string('jawaban_5');
        $table->string('jawaban_6');
        $table->string('jawaban_7');
        $table->string('jawaban_8');
        $table->string('jawaban_9');
        $table->text('saran')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skms');
    }
};
