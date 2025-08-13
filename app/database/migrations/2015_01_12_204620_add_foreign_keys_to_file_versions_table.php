<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToContestFileVersionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('contest_file_versions', function(Blueprint $table)
		{
			$table->foreign('contest_file_id', 'ContestFileVersion_File_FK')->references('id')->on('files')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('format_id', 'ContestFileVersion_Format_FK')->references('id')->on('formats')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('contest_file_versions', function(Blueprint $table)
		{
			$table->dropForeign('ContestFileVersion_File_FK');
			$table->dropForeign('ContestFileVersion_Format_FK');
		});
	}

}
