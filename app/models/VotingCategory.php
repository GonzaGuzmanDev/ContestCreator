<?php

/**
 * VotingCategory
 *
 * @property integer $id
 * @property integer $voting_session_id
 * @property integer $category_id
 * @property boolean $vote_type
 * @property string $vote_config
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\VotingCategory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\VotingCategory whereVotingSessionId($value)
 * @method static \Illuminate\Database\Query\Builder|\VotingCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\VotingCategory whereVoteType($value)
 * @method static \Illuminate\Database\Query\Builder|\VotingCategory whereVoteConfig($value)
 */
class VotingCategory extends Eloquent {

	protected $fillable = ['voting_session_id','category_id'];

	protected $hidden = ['id'];

	public function category() {
		return $this->belongsTo('Category');
	}

	public function votingSession() {
		return $this->belongsTo('VotingSession');
	}
    public function toArray()
    {
        $array = parent::toArray();
        if(isset($array['vote_config'])) $array['vote_config'] = json_decode($array['vote_config'], true);
        return $array;
    }
}