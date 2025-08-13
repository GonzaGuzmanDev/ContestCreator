<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToContestFormatsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('contest_formats', function(Blueprint $table)
		{
			$table->foreign('contest_id', 'ContestFormat_Contest_FK')->references('id')->on('contests')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('format_id', 'ContestFormat_Format_FK')->references('id')->on('formats')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('contest_formats', function(Blueprint $table)
		{
			$table->dropForeign('ContestFormat_Contest_FK');
			$table->dropForeign('ContestFormat_Format_FK');
		});
	}

}
