<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('soal', function (Blueprint $table) {
            $table->uuid('soal_id');
            $table->uuid('ujian_id');
            $table->unsignedInteger('nomor');
            $table->longText('deskripsi');
            $table->timestamps();
            $table->primary('soal_id');
            $table->foreign('ujian_id')->references('ujian_id')->on('ujian')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('soal');
    }
}
