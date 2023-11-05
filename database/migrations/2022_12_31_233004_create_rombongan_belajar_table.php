<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRombonganBelajarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rombongan_belajar', function (Blueprint $table) {
            $table->uuid('rombongan_belajar_id');
			$table->string('nama');
			$table->integer('tingkat');
			$table->timestamps();
			$table->primary('rombongan_belajar_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rombongan_belajar');
    }
}
