<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVotingSessionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('voting_sessions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('contest_id')->unsigned()->index('VotingSession_Contest_FK_idx');
			$table->string('name', 45);
			$table->text('config', 65535)->nullable();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('voting_sessions');
	}

}
