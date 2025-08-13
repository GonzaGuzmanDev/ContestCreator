<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToInscriptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('inscriptions', function(Blueprint $table)
		{
			$table->foreign('user_id', 'Inscription_User_FK')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('contest_id', 'Inscription_Contest_FK')->references('id')->on('contests')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('inscriptions', function(Blueprint $table)
		{
			$table->dropForeign('Inscription_User_FK');
			$table->dropForeign('Inscription_Contest_FK');
		});
	}

}
