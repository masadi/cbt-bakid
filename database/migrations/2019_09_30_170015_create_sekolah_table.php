<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSekolahTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sekolah', function (Blueprint $table) {
            $table->uuid('sekolah_id');
			$table->string('npsn');
			$table->string('nama');
			$table->string('nss')->nullable();
			$table->string('alamat')->nullable();
			$table->string('kode_wilayah', 8);
			$table->string('desa_kelurahan')->nullable();
			$table->string('kecamatan')->nullable();
			$table->string('kabupaten')->nullable();
			$table->string('provinsi')->nullable();
			$table->string('desa_id', 8)->nullable();
			$table->string('kecamatan_id', 8)->nullable();
			$table->string('kabupaten_id', 8)->nullable();
			$table->string('provinsi_id', 8)->nullable();
			$table->string('kode_pos')->nullable();
			$table->string('lintang')->nullable();
			$table->string('bujur')->nullable();
			$table->string('no_telp')->nullable();
			$table->string('no_fax')->nullable();
			$table->string('email')->nullable();
			$table->string('website')->nullable();
			$table->integer('status_sekolah');
			$table->timestamps();
			$table->softDeletes();
			$table->primary('sekolah_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::dropIfExists('sekolah');
    }
}
