<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToIncriptionMetadatasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('inscription_metadata_values', function(Blueprint $table)
		{
			$table->foreign('inscription_id', 'InscriptionMD_Inscription_FK')->references('id')->on('inscriptions')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('inscription_metadata_field_id', 'InscriptionMD_ContestInscriptionMD_FK')->references('id')->on('inscription_metadata_fields')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('inscription_metadata_values', function(Blueprint $table)
		{
			$table->dropForeign('InscriptionMD_Inscription_FK');
			$table->dropForeign('InscriptionMD_ContestInscriptionMD_FK');
		});
	}

}
