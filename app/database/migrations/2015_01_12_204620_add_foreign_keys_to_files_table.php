<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToFilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('files', function(Blueprint $table)
		{
			$table->foreign('contest_id', 'File_Contest_FK')->references('id')->on('contests')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('user_id', 'File_User_FK')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('files', function(Blueprint $table)
		{
			$table->dropForeign('File_Contest_FK');
			$table->dropForeign('File_User_FK');
		});
	}

}
