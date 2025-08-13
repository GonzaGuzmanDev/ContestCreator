<?php

/**
 * EntryMetadataField
 * @property-read \Illuminate\Database\Eloquent\Collection|\EntryMetadataConfigTemplate[] $EntryMetadataConfigTemplate
 */
class EntryMetadataField extends MetadataField {

	protected $table = "entry_metadata_fields";

	protected $hidden = ['entry_id','created_at','updated_at'];

	public function EntryMetadataConfigTemplate() {
		return $this->hasMany('EntryMetadataConfigTemplate', 'entry_metadata_field_id', 'id');
	}

	public function EntryMetadataValues() {
		return $this->hasMany('EntryMetadataValue');
	}

	/*public function toArray(){
		$arr = parent::toArray();
		$idsArr = [];
		if(isset($arr['entry_metadata_config_template'])) {
			foreach ($arr['entry_metadata_config_template'] as $k => $conf) {
				$idsArr[$conf['template_id']] = ['required' => $conf['required'], 'visible' => $conf['visible'], 'order' => $conf['order'], 'config' => $conf['config']];
			}
			$arr['entry_metadata_config_template'] = $idsArr;
		}
		return $arr;
	}*/
}