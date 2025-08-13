<?php

/**
 * InscriptionMetadataConfigType
 *
 * @property integer $id
 * @property integer $entry_metadata_field_id
 * @property integer $template_id
 * @property boolean $required
 * @property boolean $visible
 * @property-read \Illuminate\Database\Eloquent\Collection|\EntryMetadataFile[] $InscriptionMetadataField
 */
class EntryMetadataConfigTemplate extends Eloquent {

	protected $table = "entry_metadata_config_template";

	protected $fillable = ['entry_metadata_field_id','template_id','required','visible'];

	protected $hidden = ['id','entry_metadata_field_id','created_at','updated_at'];

	public function template() {
		return $this->belongsTo('EntryMetadataTemplate', 'template_id');
	}

	public function EntryMetadataField() {
		return $this->belongsTo('EntryMetadataField', 'entry_metadata_field_id')->orderBy('id','asc');
	}
}