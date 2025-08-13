<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIncriptionMetadatasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('inscription_metadata_values', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('inscription_id')->unsigned()->index('InscriptionMD_Inscription_idx');
			$table->integer('inscription_metadata_field_id')->unsigned()->index('InscriptionMD_ContestInscriptionMD_FK_idx');
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
		Schema::drop('inscription_metadata_values');
	}

}
