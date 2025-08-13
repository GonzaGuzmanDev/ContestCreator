<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEntryMetadataConfigCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('entry_metadata_field_config', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('entry_metadata_field_id')->unsigned()->index('ContestMDConfig_ContestMD_FK_idx');
			$table->integer('category_id')->unsigned()->index('ContestMDConfig_Category_FK_idx');
			$table->boolean('required')->default(0);
			$table->boolean('visible')->default(0);
			$table->boolean('order')->default(0);
			$table->text('config', 65535)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('entry_metadata_field_config');
	}

}
