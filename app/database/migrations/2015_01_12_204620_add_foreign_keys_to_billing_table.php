<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToBillingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('billing', function(Blueprint $table)
		{
			$table->foreign('entry_id', 'Billing_Entry_FK')->references('id')->on('entries')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('billing', function(Blueprint $table)
		{
			$table->dropForeign('Billing_Entry_FK');
		});
	}

}
