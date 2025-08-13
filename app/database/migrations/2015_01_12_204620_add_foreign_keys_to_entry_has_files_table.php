<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEntryMetadataFilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('entry_metadata_files', function(Blueprint $table)
		{
			$table->foreign('file_id', 'EntryMetadataFile_File_FK')->references('id')->on('contest_files')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('entry_metadata_value_id', 'EntryMetadataFile_EntryMetadataValue_FK')->references('id')->on('entry_metadata_values')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('entry_metadata_files', function(Blueprint $table)
		{
			$table->dropForeign('EntryHasFile_File_FK');
			$table->dropForeign('EntryHasFile_EntryMetadataValue_FK');
		});
	}

}
