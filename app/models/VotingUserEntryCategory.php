<?php

/**
 * Entry
 *
 * @property integer $id
 * @property integer $entry_category_id
 * @property integer $voting_user_id
 */
class VotingUserEntryCategory extends Eloquent {
	protected $fillable = ['entry_category_id','voting_user_id'];

	public function entryCategory() {
		return $this->hasOne('EntryCategory');
	}

	public function votingUser(){
		return $this->belongsTo('VotingUser');
	}
}