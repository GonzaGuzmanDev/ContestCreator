<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEntryMetadataFieldsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('entry_metadata_fields', function(Blueprint $table)
		{
			$table->foreign('contest_id', 'ContestMD_Contest_FK')->references('id')->on('contests')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('entry_metadata_fields', function(Blueprint $table)
		{
			$table->dropForeign('ContestMD_Contest_FK');
		});
	}

}
