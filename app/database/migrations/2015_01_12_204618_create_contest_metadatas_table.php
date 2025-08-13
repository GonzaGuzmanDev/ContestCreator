<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEntryMetadataFieldsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('entry_metadata_fields', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('contest_id')->unsigned()->index('ContestMD_Contest_FK_idx');
			$table->string('label', 32);
			$table->boolean('type');
			$table->boolean('required')->default(0);
			$table->boolean('visible')->default(0);
			$table->boolean('order')->default(0);
			$table->text('config', 65535)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('entry_metadata_fields');
	}

}
