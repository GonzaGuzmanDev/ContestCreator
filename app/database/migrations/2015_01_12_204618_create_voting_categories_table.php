<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVotingCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('voting_categories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('voting_session_id')->unsigned()->index('VotingCategory_VotingSession_FK_idx');
			$table->integer('category_id')->unsigned()->index('VotingCategory_Category_FK_idx');
			$table->boolean('vote_type');
			$table->text('vote_config', 65535)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('voting_categories');
	}

}
