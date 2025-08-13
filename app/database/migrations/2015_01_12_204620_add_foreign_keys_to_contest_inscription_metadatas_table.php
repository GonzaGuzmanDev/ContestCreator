<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToInscriptionMetadataFieldsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('inscription_metadata_fields', function(Blueprint $table)
		{
			$table->foreign('contest_id', 'ContestIncriptionMd_Contest_FK')->references('id')->on('contests')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('inscription_metadata_fields', function(Blueprint $table)
		{
			$table->dropForeign('ContestIncriptionMd_Contest_FK');
		});
	}

}
