<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEntryMetadataFilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('entry_metadata_files', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('contest_file_id')->unsigned()->index('EntryMetadataFile_ContestFile_idx');
			$table->integer('entry_metadata_value_id')->unsigned()->index('EntryMetadataFile_Metadata_FK_idx');
			$table->boolean('order')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('entry_metadata_files');
	}

}
