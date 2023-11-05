<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJawabanSiswaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jawaban_siswa', function (Blueprint $table) {
            $table->uuid('jawaban_siswa_id');
            $table->uuid('jawaban_id');
            $table->string('opsi', 1);
            $table->uuid('soal_id');
            $table->uuid('user_id');
            $table->smallInteger('is_ragu')->default(0);
            $table->timestamps();
            $table->primary('jawaban_siswa_id');
            $table->foreign('jawaban_id')->references('jawaban_id')->on('jawaban')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('soal_id')->references('soal_id')->on('soal')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('user_id')->references('user_id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jawaban_siswa');
    }
}
