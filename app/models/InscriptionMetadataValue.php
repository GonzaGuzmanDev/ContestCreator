<?php

/**
 * InscriptionMetadataValue
 *
 * @property integer $id
 * @property integer $inscription_id
 * @property integer $inscription_metadata_field_id
 * @property string $value
 */
class InscriptionMetadataValue extends Eloquent {

	protected $fillable = ['inscription_id','inscription_metadata_field_id','value'];

	/**
	 * @var array
	 */
	protected $hidden = ['created_at','updated_at'];

	public function InscriptionMetadataField() {
		return $this->belongsTo('InscriptionMetadataField');
	}

	/*$$*/
	public function InscriptionMetadataValues(){
		return $this->hasMany('InscriptionMetadataValue', 'inscription_metadata_field_id');
	}

	public function inscription() {
		return $this->belongsTo('Inscription');
	}

	public function toArray()
    {
        $arr = parent::toArray();
        $metadataFieldType = $this->InscriptionMetadataField()->where('id', $arr['inscription_metadata_field_id'])->first();
        if($metadataFieldType->type == MetadataField::MULTIPLE) {
            $arr['value2'] = $arr['value'];
            unset($arr['value']);
            $arr['value'] = json_decode($arr['value2']);
            unset($arr['value2']);
        }
        return $arr;
    }
}