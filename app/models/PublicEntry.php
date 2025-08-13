<?php

/**
 * Entry_log
 *
 * @property integer $id
 * @property string $error
 * @property integer $entry_id
 * @property integer $user_id
 * @property string $read_by
 */
class PublicEntry extends Eloquent {

	protected $fillable = ['entry_id', 'page_id'];

	protected $table = 'public_entries';

	public function entry() {
		return $this->belongsTo('Entry');
	}

	public function page() {
		return $this->belongsTo('ContestAsset');
	}
}