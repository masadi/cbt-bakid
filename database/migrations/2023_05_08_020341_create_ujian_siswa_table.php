<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUjianSiswaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ujian_siswa', function (Blueprint $table) {
            $table->uuid('ujian_siswa_id');
            $table->uuid('ujian_id');
            $table->uuid('user_id');
            $table->unsignedBigInteger('waktu');
            $table->timestamps();
            $table->primary('ujian_siswa_id');
            $table->foreign('ujian_id')->references('ujian_id')->on('ujian')->onUpdate('CASCADE')->onDelete('CASCADE');
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
        Schema::dropIfExists('ujian_siswa');
    }
}
