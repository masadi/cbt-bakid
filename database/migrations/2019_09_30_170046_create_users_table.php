<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->uuid('sekolah_id')->nullable();
			$table->string('name');
            $table->string('email')->unique();
            $table->string('password');
			$table->rememberToken();
			$table->timestamp('last_login_at')->nullable();
			$table->string('last_login_ip')->nullable();
			$table->timestamps();
			$table->softDeletes();
			$table->timestamp('last_sync');
			$table->primary('user_id');
			$table->foreign('sekolah_id')->references('sekolah_id')->on('sekolah')
				->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::dropIfExists('users');
    }
}
