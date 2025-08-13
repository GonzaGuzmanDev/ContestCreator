<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInscriptionMetadataFieldsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('inscription_metadata_fields', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('contest_id')->unsigned()->index('ContestIncriptionMd_Contest_FK_idx');
			$table->string('label', 32);
			$table->boolean('type');
			$table->boolean('order');
			$table->boolean('required');
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
		Schema::drop('inscription_metadata_fields');
	}

}
