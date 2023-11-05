<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJawabanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jawaban', function (Blueprint $table) {
            $table->uuid('jawaban_id');
            $table->uuid('soal_id');
            $table->smallInteger('urut');
            $table->string('opsi', 1);
            $table->longText('deskripsi');
            $table->decimal('benar', 1,0);
            $table->timestamps();
            $table->primary('jawaban_id');
            $table->foreign('soal_id')->references('soal_id')->on('soal')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jawaban');
    }
}
