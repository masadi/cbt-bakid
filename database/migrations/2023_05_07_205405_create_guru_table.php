<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuruTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guru', function (Blueprint $table) {
            $table->uuid('guru_id');
            $table->string('nama');
            $table->string('nuptk', 16)->nullable();
            $table->string('nip', 18)->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('email');
            $table->string('photo')->nullable();
            $table->timestamps();
            $table->primary('guru_id');
        });
        Schema::table('pembelajaran', function (Blueprint $table) {
            $table->foreign('guru_id')->references('guru_id')->on('guru')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('guru_id')->nullable();
            $table->foreign('guru_id')->references('guru_id')->on('guru')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
        Schema::table('rombongan_belajar', function (Blueprint $table) {
            $table->uuid('guru_id')->nullable();
            $table->foreign('guru_id')->references('guru_id')->on('guru')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pembelajaran', function (Blueprint $table) {
            $table->dropForeign(['guru_id']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['guru_id']);
            $table->dropColumn('guru_id');
        });
        Schema::table('rombongan_belajar', function (Blueprint $table) {
            $table->dropForeign(['guru_id']);
            $table->dropColumn('guru_id');
        });
        Schema::dropIfExists('guru');
    }
}
