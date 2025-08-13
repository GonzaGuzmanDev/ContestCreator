<?php

/**
 * InscriptionMetadataField
 *
 * @property integer $role
 */
class InscriptionMetadataField extends MetadataField {

	protected $table = "inscription_metadata_fields";

	/**
	 * @var array
	 */
	protected $hidden = ['contest_id','created_at','updated_at'];

	public function inscriptionMetadatas() {
		return $this->hasMany('InscriptionMetadataValue');
	}

	public function InscriptionMetadataConfigTypes(){
		return $this->hasMany('InscriptionMetadataConfigType', 'inscription_metadata_field_id');
	}

	public function toArray(){
		$arr = parent::toArray();
		$idsArr = [];
		if(isset($arr['inscription_metadata_config_types'])) {
			foreach ($arr['inscription_metadata_config_types'] as $k => $conf) {
				$idsArr[$conf['inscription_type_id']] = ['required' => $conf['required'], 'visible' => $conf['visible'], 'order' => $conf['order'], 'config' => $conf['config']];
			}
			$arr['inscription_metadata_config_types'] = $idsArr;
		}
		return $arr;
	}
}