<?php

/**
 * InscriptionType
 *
 * @property integer $id
 * @property integer $contest_id
 * @property integer $role
 * @property string $name
 * @property string $trans
 * @property bool $public
 * @property \Carbon\Carbon $start_at
 * @property \Carbon\Carbon $deadline1_at
 * @property \Carbon\Carbon $deadline2_at
 */
class InscriptionType extends Eloquent {


	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['contest_id','created_at','updated_at'];

	protected $fillable = ['contest_id', 'name', 'role','price', 'discount','start_at','deadline1_at','deadlin2_at'];

	public function contest() {
		return $this->belongsTo('Contest');
	}

	public function inscriptions() {
		return $this->hasMany('Inscription');
	}

	public function InscriptionMetadataConfigType(){
		return $this->hasMany('InscriptionMetadataConfigType', 'inscription_type_id');
	}

	public function CategoryConfigType()
	{
		return $this->hasMany('CategoryConfigType', 'inscription_type_id');
	}

	public function toArray(){
		$arr = parent::toArray();
		$idsArr = [];
		if(isset($arr['category_config_type'])) {
			foreach ($arr['category_config_type'] as $k => $conf) {
				array_push($idsArr, $conf['category_id']);
			}
			$arr['category_config_type'] = $idsArr;
		}
		if(isset($arr['trans'])) $arr['trans'] = json_decode($arr['trans'], true);
		return $arr;
	}
}