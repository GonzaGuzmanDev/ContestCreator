<?php

/**
 * Entry
 *
 * @property integer $id
 * @property integer $entry_category_id
 * @property integer $voting_user_id
 */
class VotingUserVotingGroup extends Eloquent {
	protected $fillable = ['voting_group_id','voting_user_id'];

	public function votingGroup() {
		return $this->hasOne('VotingGroup');
	}

	public function votingUser(){
		return $this->belongsTo('VotingUser');
	}
}