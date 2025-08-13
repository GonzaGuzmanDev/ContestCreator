<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToVotingCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('voting_categories', function(Blueprint $table)
		{
			$table->foreign('voting_session_id', 'VotingCategory_VotingSession_FK')->references('id')->on('voting_sessions')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('category_id', 'VotingCategory_Category_FK')->references('id')->on('categories')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('voting_categories', function(Blueprint $table)
		{
			$table->dropForeign('VotingCategory_VotingSession_FK');
			$table->dropForeign('VotingCategory_Category_FK');
		});
	}

}
