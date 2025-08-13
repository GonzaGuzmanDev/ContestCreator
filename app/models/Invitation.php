<?php

/**
 * InvitationId
 *
 * @property integer $id
 * @property integer $contest_id
 * @property string $name
 * @property string $subject
 * @property string $content
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\VotingSession whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\VotingSession whereContestId($value)
 * @method static \Illuminate\Database\Query\Builder|\VotingSession whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\VotingSession whereSubject($value)
 * @method static \Illuminate\Database\Query\Builder|\VotingSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\VotingSession whereUpdatedAt($value)
 */
class Invitation extends Eloquent {

	protected $fillable = ['contest_id', 'email', 'password', 'sent'];

	public function contest() {
		return $this->belongsTo('Contest');
	}

	/*static public function createCode(){
		do{
			$code = md5(microtime());
			$ret = VotingSession::where('code','=',$code)->get();
		}while(count($ret));
		return $code;
	}*/
}