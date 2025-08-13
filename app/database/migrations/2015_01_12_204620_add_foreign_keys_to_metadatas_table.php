<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEntryMetadataValuesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('entry_metadata_values', function(Blueprint $table)
		{
			$table->foreign('entry_metadata_field_id', 'EntryMetadataValue_ContestMD_FK')->references('id')->on('entry_metadata_fields')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('entry_id', 'EntryMetadataValue_Entry_FK')->references('id')->on('entries')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('entry_metadata_values', function(Blueprint $table)
		{
			$table->dropForeign('EntryMetadataValue_ContestMD_FK');
			$table->dropForeign('EntryMetadataValue_Entry_FK');
		});
	}

}
