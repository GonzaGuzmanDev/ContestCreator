<?php

/**
 * CategoryConfigType
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $inscription_type_id
 */
class CategoryConfigType extends Eloquent {

	protected $table = "category_config_type";

	protected $fillable = ['category_id','inscription_type_id'];

	public function inscriptionType() {
		return $this->belongsTo('InscriptionType', 'inscription_type_id');
	}

	public function category() {
		return $this->belongsTo('Category', 'category_id')->orderBy('id','asc');
	}
}