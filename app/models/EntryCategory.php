<?php

/**
 * Entry
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $entry_id
 */
class EntryCategory extends Eloquent {

	protected $fillable = ['category_id','entry_id'];

	public function category() {
		return $this->belongsTo('Category');
	}

	public function entry() {
		return $this->belongsTo('Entry');
    }
}