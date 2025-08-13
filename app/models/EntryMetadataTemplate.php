<?php

/**
 * EntryMetadataTemplate
 *
 * @property integer $id
 * @property integer $contest_id
 * @property string $name
 * @property string $trans
 */
class EntryMetadataTemplate extends Eloquent {

	protected $table = "entry_metadata_templates";
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['contest_id','created_at','updated_at'];

	protected $fillable = ['contest_id', 'name'];

	public function contest() {
		return $this->belongsTo('Contest');
	}

	public function categories()
	{
		return $this->hasMany('Category', 'template_id', 'id');
	}

	public function EntryMetadataConfigTemplates(){
		return $this->hasMany('EntryMetadataConfigTemplate', 'template_id', 'id');
	}

	public function toArray(){
		$arr = parent::toArray();
		$idsArr = [];
		if(isset($arr['categories'])) {
			foreach ($arr['categories'] as $k => $conf) {
				array_push($idsArr, $conf['id']);
			}
			$arr['categories_ids'] = $idsArr;
			unset($arr['categories']);
		}
		if(isset($arr['trans'])) $arr['trans'] = json_decode($arr['trans'], true);
		return $arr;
	}
}