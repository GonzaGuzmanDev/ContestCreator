<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('first_name', 255);
			$table->string('last_name', 255);
			$table->string('email', 320);
			$table->string('password', 255);
			$table->string('remember_token', 255)->nullable();
			$table->string('verify_token', 255)->nullable();
			$table->boolean('status')->nullable()->default(0);
			$table->boolean('active')->nullable()->default(0);
			$table->boolean('verified')->nullable()->default(0);
			$table->boolean('super')->nullable()->default(0);
			$table->timestamp('last_seen_at')->nullable();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
