<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVotingUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('voting_users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('inscription_id')->unsigned()->index('VotingUser_Inscription_FK_idx');
			$table->integer('voting_session_id')->unsigned()->index('VotingUser_Voting_FK_idx');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('voting_users');
	}

}
