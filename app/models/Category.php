<?php

/**
 * Category
 *
 * @property integer $id
 * @property string $name
 * @property integer $parent_id
 * @property integer $contest_id
 * @property integer $template_id
 * @property boolean $order
 * @property string $image
 * @property boolean $final
 * @property string $description
 * @property string $trans
 * @property number $price
 * @property-read \Category $parentCategory
 * @property-read \Contest $contest
 * @method static \Illuminate\Database\Query\Builder|\Category whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereContestId($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereImage($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereFinal($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Category wherePrice($value)
 */
class Category extends Eloquent {

	protected $fillable = [];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['contest_id','created_at','updated_at'];


	public function contest() {
		return $this->belongsTo('Contest');
	}

	public function entries() {
		return $this->belongsToMany('Entry', 'entry_categories','category_id','entry_id');
	}

	public function categories()
	{
		return $this->hasMany('Category', 'parent_id', 'id')->orderBy('order');
	}

	public function template()
	{
		return $this->hasOne('EntryMetadataTemplate', 'template_id', 'id');
	}

	public function childrenCategories()
	{
		return $this->categories()->with('childrenCategories')->orderBy('order')->with('CategoryConfigType');
	}

	public function parentCategory()
	{
		return $this->belongsTo('Category', 'parent_id', 'id');
	}

	public function CategoryConfigType()
	{
		return $this->hasMany('CategoryConfigType', 'category_id');
	}

	public function votingCategories() {
		return $this->hasMany('VotingCategory');
	}

	public function getPrice(){
		if($this->price != null) return $this->price;
		if($this->parent_id != null) return $this->parentCategory->getPrice();
		return $this->contest->getBillingData()['mainPrice'];
	}

	public function toArray(){
		$arr = parent::toArray();
		$idsArr = [];
		if(isset($arr['category_config_type'])) {
			foreach ($arr['category_config_type'] as $k => $conf) {
				array_push($idsArr, $conf['inscription_type_id']);
			}
			$arr['category_config_type'] = $idsArr;
		}
		if(isset($arr['trans'])) $arr['trans'] = json_decode($arr['trans'], true);
		return $arr;
	}
}