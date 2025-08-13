<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContestsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contests', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('code', 16);
			$table->string('name', 128);
			$table->integer('user_id')->unsigned()->index('User_FK_idx');
			$table->text('template', 65535)->nullable();
			$table->text('limits', 65535)->nullable();
			$table->text('sizes', 65535)->nullable();
			$table->text('billing', 65535)->nullable();
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
		Schema::drop('contests');
	}

}
