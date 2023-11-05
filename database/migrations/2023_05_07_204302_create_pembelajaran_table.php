<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembelajaranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembelajaran', function (Blueprint $table) {
            $table->uuid('pembelajaran_id');
            $table->uuid('rombongan_belajar_id');
            $table->uuid('guru_id');
            $table->unsignedBigInteger('mata_pelajaran_id');
            $table->string('nama_mata_pelajaran');
            $table->timestamps();
            $table->primary('pembelajaran_id');
            $table->foreign('mata_pelajaran_id')->references('mata_pelajaran_id')->on('mata_pelajaran')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pembelajaran');
    }
}
