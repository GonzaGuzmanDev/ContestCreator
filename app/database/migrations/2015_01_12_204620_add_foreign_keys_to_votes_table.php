<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToVotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('votes', function(Blueprint $table)
		{
			$table->foreign('voting_user_id', 'Vote_VotingUser_FK')->references('id')->on('voting_users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('entry_id', 'Vote_Entry_FK')->references('id')->on('entries')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('votes', function(Blueprint $table)
		{
			$table->dropForeign('Vote_VotingUser_FK');
			$table->dropForeign('Vote_Entry_FK');
		});
	}

}
