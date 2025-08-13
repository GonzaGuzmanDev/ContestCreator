<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToVotingUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('voting_users', function(Blueprint $table)
		{
			$table->foreign('inscription_id', 'VotingUser_Inscription_FK')->references('id')->on('inscriptions')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('voting_session_id', 'VotingUser_VotingSession_FK')->references('id')->on('voting_sessions')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('voting_users', function(Blueprint $table)
		{
			$table->dropForeign('VotingUser_Inscription_FK');
			$table->dropForeign('VotingUser_VotingSession_FK');
		});
	}

}
