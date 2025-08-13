<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEntryMetadataValuesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('entry_metadata_values', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('entry_id')->unsigned()->index('EntryMetadataValue_Entry_FK_idx');
			$table->integer('entry_metadata_field_id')->unsigned()->index('EntryMetadataValue_Contest_FK_idx');
			$table->text('value', 65535)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('entry_metadata_values');
	}

}
