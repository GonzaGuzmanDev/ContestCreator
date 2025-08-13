<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEntriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('entries', function(Blueprint $table)
		{
			$table->foreign('user_id', 'Entry_User_FK')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('NO ACTION');
			$table->foreign('contest_id', 'Entry_Contest_FK')->references('id')->on('contests')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('category_id', 'Entry_Category_FK')->references('id')->on('categories')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('entries', function(Blueprint $table)
		{
			$table->dropForeign('Entry_User_FK');
			$table->dropForeign('Entry_Contest_FK');
			$table->dropForeign('Entry_Category_FK');
		});
	}

}
