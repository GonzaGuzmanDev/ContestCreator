<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContestFileVersionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contest_file_versions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('format_id')->unsigned()->index('ContestFileVersion_Format_FK_idx');
			$table->integer('contest_file_id')->unsigned()->index('ContestFileVersion_File_FK_idx');
			$table->integer('size')->unsigned();
			$table->string('sizes', 10)->nullable();
			$table->decimal('duration', 12)->nullable();
			$table->string('extension', 4);
			$table->boolean('source')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('contest_file_versions');
	}

}
