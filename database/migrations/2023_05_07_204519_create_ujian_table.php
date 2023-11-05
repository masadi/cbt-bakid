<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUjianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ujian', function (Blueprint $table) {
            $table->uuid('ujian_id');
            $table->uuid('pembelajaran_id');
            $table->string('nama', 100);
            $table->smallInteger('status')->default(0);
            $table->timestamps();
            $table->primary('ujian_id');
            $table->foreign('pembelajaran_id')->references('pembelajaran_id')->on('pembelajaran')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ujian');
    }
}
