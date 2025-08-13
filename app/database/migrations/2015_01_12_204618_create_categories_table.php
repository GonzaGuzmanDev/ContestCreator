<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('categories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 128);
			$table->integer('parent_id')->unsigned()->unique('parent_id_UNIQUE');
			$table->integer('contest_id')->unsigned()->index('Castegory_Contest_FK_idx');
			$table->boolean('order')->default(0);
			$table->text('image', 65535)->nullable();
			$table->boolean('final')->default(0);
			$table->text('description', 65535)->nullable();
			$table->text('billing', 65535)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('categories');
	}

}
