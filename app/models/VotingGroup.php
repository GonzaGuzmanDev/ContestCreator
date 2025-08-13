<?php

/**
 * VotingCategory
 *
 * @property integer $id
 * @property integer $voting_session_id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\VotingCategory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\VotingCategory whereVotingSessionId($value)
 * @method static \Illuminate\Database\Query\Builder|\VotingCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\VotingCategory whereVoteType($value)
 * @method static \Illuminate\Database\Query\Builder|\VotingCategory whereVoteConfig($value)
 * @property VotingSession $votingSession
 * @property \Illuminate\Database\Eloquent\Collection|\VotingGroupEntryCategory[] $votingGroupEntryCategory
 * @property \Illuminate\Database\Eloquent\Collection|\EntryCategory[] $entryCategories
 */
class VotingGroup extends Eloquent {

	protected $fillable = ['voting_session_id','name'];

	protected $hidden = ['voting_session_id','created_at','updated_at','deleted_at','pivot'];

	public function votingSession() {
		return $this->belongsTo('VotingSession');
	}

	public function votingGroupEntryCategory() {
		return $this->hasMany('VotingGroupEntryCategory', 'voting_group_id', 'id');
	}

	public function entryCategories() {
		return $this->belongsToMany('EntryCategory', 'voting_group_entry_categories', 'voting_group_id', 'entry_category_id');
	}
}