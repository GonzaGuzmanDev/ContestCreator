<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToVotingSessionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('voting_sessions', function(Blueprint $table)
		{
			$table->foreign('contest_id', 'VotingSession_Contest_FK')->references('id')->on('contests')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('voting_sessions', function(Blueprint $table)
		{
			$table->dropForeign('VotingSession_Contest_FK');
		});
	}

}
