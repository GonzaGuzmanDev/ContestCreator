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
class CollectionKey extends Eloquent {

	protected $fillable = ['collection_id', 'key', 'email'];

	protected $hidden = ['collection_id', 'created_at','updated_at','deleted_at'];

	static public function createSimpleKey(){
		do{
			$key = User::getSimpleRandomCode();
			$ret = VotingSessionKey::where('key','=',$key)->get();
		}while(count($ret));
		return $key;
	}
}