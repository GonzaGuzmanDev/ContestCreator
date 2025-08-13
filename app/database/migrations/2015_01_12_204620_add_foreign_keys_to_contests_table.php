<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToContestsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('contests', function(Blueprint $table)
		{
			$table->foreign('user_id', 'Contest_User_FK')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('contests', function(Blueprint $table)
		{
			$table->dropForeign('Contest_User_FK');
		});
	}

}
