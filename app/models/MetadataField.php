<?php

/**
 * MetadataField
 *
 * @property integer $id
 * @property integer $contest_id
 * @property string $label
 * @property string $description
 * @property string $trans
 * @property boolean $type
 * @property boolean $required
 * @property boolean $visible
 * @property boolean $private
 * @property boolean $order
 * @property string $config
 * @method static \Illuminate\Database\Query\Builder|\EntryMetadataField whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\EntryMetadataField whereContestId($value)
 * @method static \Illuminate\Database\Query\Builder|\EntryMetadataField whereLabel($value)
 * @method static \Illuminate\Database\Query\Builder|\EntryMetadataField whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\EntryMetadataField whereRequired($value)
 * @method static \Illuminate\Database\Query\Builder|\EntryMetadataField whereVisible($value)
 * @method static \Illuminate\Database\Query\Builder|\EntryMetadataField whereOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\EntryMetadataField whereConfig($value)
 */
class MetadataField extends Eloquent {

	const TEXT = 1;
	const TEXTAREA = 2;
	const DATE = 3;
	const SELECT = 4;
	const MULTIPLE = 5;
	const NUMBER = 6;
	const TITLE = 7;
	const DESCRIPTION = 8;
	const EMAIL = 9;
	const FILE = 10;
	const MULTIPLEWITHCOLUMNS = 11;
	const TAB = 12;
	const LINK = 13;
	const RICHTEXT = 14;

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('contest_id');

	protected $fillable = [];

	public function contests() {
		return $this->hasMany('Contest');
	}

	public function scopePublic($query) {
		$query->where('private', 0);
	}

	public function toArray()
	{
		$array = parent::toArray();
		if(isset($array['config'])) $array['config'] = json_decode($array['config'], true);
		if(isset($array['trans'])) $array['trans'] = json_decode($array['trans'], true);
		return $array;
	}

	static public function getAllTypesData(){
		return array(
			self::TEXT => Lang::get('metadata.text'),
			self::TEXTAREA => Lang::get('metadata.textarea'),
			self::RICHTEXT => Lang::get('metadata.richtext'),
			self::FILE => Lang::get('metadata.file'),
			self::TITLE => Lang::get('metadata.title'),
			self::DESCRIPTION => Lang::get('metadata.description'),
			self::TAB => Lang::get('metadata.tab'),
			self::DATE => Lang::get('metadata.date'),
			self::NUMBER => Lang::get('metadata.number'),
			self::SELECT => Lang::get('metadata.select'),
			self::MULTIPLE => Lang::get('metadata.multiple'),
			self::MULTIPLEWITHCOLUMNS => Lang::get('metadata.multiplewithcolumns'),
			self::EMAIL => Lang::get('metadata.email'),
			self::LINK => Lang::get('metadata.link')
		);
	}

	static public function getJSTypes(){
		return array(
			"TEXT" => self::TEXT,
			"TEXTAREA" => self::TEXTAREA,
			"RICHTEXT" => self::RICHTEXT,
			"FILE" => self::FILE,
			"TITLE" => self::TITLE,
			"DESCRIPTION" => self::DESCRIPTION,
			"TAB" => self::TAB,
			"DATE" => self::DATE,
			"NUMBER" => self::NUMBER,
			"SELECT" => self::SELECT,
			"MULTIPLE" => self::MULTIPLE,
			"MULTIPLEWITHCOLUMNS" => self::MULTIPLEWITHCOLUMNS,
			"EMAIL" => self::EMAIL,
			"LINK" => self::LINK,
		);
	}

	static public function getEditablesTypes(){
		return array(
			self::TEXT,
			self::TEXTAREA,
			self::RICHTEXT,
			self::FILE,
			self::DATE,
			self::NUMBER,
			self::EMAIL,
			self::SELECT,
			self::MULTIPLE,
			self::MULTIPLEWITHCOLUMNS
		);
	}

	static public function getNotEditablesTypes(){
		return array(
			self::TITLE,
			self::DESCRIPTION,
			self::TAB,
			self::LINK
		);
	}

	static public function getDateTypes(){
		return array(
			self::DATE
		);
	}

}