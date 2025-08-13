<?php

/**
 * InscriptionMetadataConfigType
 *
 * @property integer $id
 * @property integer $inscription_metadata_field_id
 * @property integer $inscription_type_id
 * @property boolean $required
 * @property boolean $visible
 * @property-read \Illuminate\Database\Eloquent\Collection|\EntryMetadataFile[] $InscriptionMetadataField
 */
class InscriptionMetadataConfigType extends Eloquent {

	protected $table = "inscription_metadata_config_type";

	protected $fillable = ['inscription_metadata_field_id','inscription_type_id','required','visible'];

	protected $hidden = ['id','inscription_metadata_field_id'];

	public function inscriptionType() {
		return $this->belongsTo('InscriptionType', 'inscription_type_id');
	}

	public function InscriptionMetadataField() {
		return $this->belongsTo('InscriptionMetadataField', 'inscription_metadata_field_id')->orderBy('id','asc');
	}
}