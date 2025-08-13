<?php

/**
 * Vote
 *
 * @property integer $id
 * @property integer $voting_user_id
 * @property integer $voting_session_id
 * @property integer $entry_category_id
 * @property integer $entry_id
 * @property integer $type
 * @property string $vote
 * @property float $vote_float
 * @property bool $abstain
 * @property int $criteria
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Vote whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Vote whereVotingUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Vote whereEntryId($value)
 * @method static \Illuminate\Database\Query\Builder|\Vote whereVote($value)
 * @method static \Illuminate\Database\Query\Builder|\Vote whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Vote whereUpdatedAt($value)
 */
class Vote extends Eloquent {

	const TYPE_SCORE = 0;
	const TYPE_EXTRA = 1;
	const TYPE_METAL = 2;
	const TYPE_YESNO = 3;

	const EXTRA_CHECKBOX = 0;
	const EXTRA_TEXTAREA = 1;

	const MIN_VOTES_PERC = 0;
	const MIN_VOTES_JUDGES = 1;

	protected $fillable = ['voting_session_id','voting_user_id','entry_category_id','type','vote','vote_float','abstain','criteria'];

	public function entry() {
		return $this->belongsTo('Entry');
	}

	public function votingUser() {
		return $this->belongsTo('VotingUser');
	}
}