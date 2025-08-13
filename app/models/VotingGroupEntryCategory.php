<?php

/**
 * Entry
 *
 * @property integer $id
 * @property integer $entry_category_id
 * @property integer $voting_group_id
 */
class VotingGroupEntryCategory extends Eloquent {
	protected $fillable = ['entry_category_id','voting_group_id'];

	public function entryCategory() {
		return $this->hasOne('EntryCategory');
	}

	public function votingGroup(){
		return $this->belongsTo('VotingGroup');
	}
}