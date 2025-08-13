<?php

/**
 * VotingCategory
 *
 * @property integer $id
 * @property integer $voting_session_id
 * @property integer $voting_user_id
 * @property integer $voting_group_id
 * @property string $key
 * @property string $email
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\VotingCategory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\VotingCategory whereVotingSessionId($value)
 * @property VotingSession $votingSession
 * @property VotingUser $votingUser
 * @property VotingGroup $votingGroup
 */
class VotingSessionKey extends Eloquent {

	protected $fillable = ['voting_session_id','voting_user_id','voting_group_id','key', 'email'];

	protected $hidden = ['voting_session_id','voting_user_id','voting_group_id','created_at','updated_at','deleted_at'];

	public function votingSession() {
		return $this->belongsTo('VotingSession');
	}

	public function votingUser() {
		return $this->belongsTo('VotingUser');
	}

	public function votingGroup() {
		return $this->belongsTo('VotingGroup');
	}

	static public function createKey(){
		do{
			$key = User::getRandomCode();
			$ret = VotingSessionKey::where('key','=',$key)->get();
		}while(count($ret));
		return $key;
	}

	static public function createSimpleKey(){
		do{
			$key = User::getSimpleRandomCode();
			$ret = VotingSessionKey::where('key','=',$key)->get();
		}while(count($ret));
		return $key;
	}
}