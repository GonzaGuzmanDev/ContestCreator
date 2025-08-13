<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBillingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('billing', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('entry_id')->unsigned()->index('Billing_Entry_FK_idx');
			$table->integer('transaction_id')->unsigned();
			$table->boolean('payment_gateway');
			$table->decimal('price', 9);
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
		Schema::drop('billing');
	}

}
