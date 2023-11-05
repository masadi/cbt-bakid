<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePesertaDidikTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peserta_didik', function (Blueprint $table) {
            $table->uuid('peserta_didik_id');
			$table->string('nama');
			$table->string('nisn')->nullable();
			$table->string('jenis_kelamin')->nullable();
			$table->string('tempat_lahir');
			$table->date('tanggal_lahir');
            $table->string('photo')->nullable();
			$table->timestamps();
			$table->primary('peserta_didik_id');
        });
		Schema::table('users', function (Blueprint $table) {
            $table->uuid('peserta_didik_id')->nullable();
            $table->foreign('peserta_didik_id')->references('peserta_didik_id')->on('peserta_didik')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['peserta_didik_id']);
            $table->dropColumn('peserta_didik_id');
        });
        Schema::dropIfExists('peserta_didik');
    }
}
