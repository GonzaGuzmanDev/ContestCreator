<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFormatsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('formats', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('label', 32);
			$table->boolean('type');
			$table->string('extension', 4);
			$table->text('command', 65535)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('formats');
	}

}
