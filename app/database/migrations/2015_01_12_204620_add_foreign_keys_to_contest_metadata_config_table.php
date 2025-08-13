<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEntryMetadataConfigCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('entry_metadata_field_config', function(Blueprint $table)
		{
			$table->foreign('entry_metadata_field_id', 'ContestMDConfig_ContestMD_FK')->references('id')->on('entry_metadata_fields')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('category_id', 'ContestMDConfig_Category_FK')->references('id')->on('categories')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('entry_metadata_field_config', function(Blueprint $table)
		{
			$table->dropForeign('ContestMDConfig_ContestMD_FK');
			$table->dropForeign('ContestMDConfig_Category_FK');
		});
	}

}
