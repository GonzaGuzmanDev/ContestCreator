<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContestFormatsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contest_formats', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('contest_id')->unsigned()->index('ContestFormat_Contest_FK_idx');
			$table->integer('format_id')->unsigned()->index('ContestFormat_Format_FK_idx');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('contest_formats');
	}

}
