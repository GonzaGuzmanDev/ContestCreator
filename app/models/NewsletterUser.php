<?php

/**
 * newsletterUser
 *
 * @property integer $id
 * @property integer $newsletter_id
 * @property string $email
 * @property int $status
 * @property string $sent
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class newsletterUser extends Eloquent {

    const PENDING_NOTIFICATION = 0;
    const NOTIFIED = 1;
    const VISITED_PAGE = 2;
    const REJECTED = 3;
    const ACCEPTED = 4;
    const RESEND = 5;

	protected $fillable = ['newsletter_id', 'email', 'status', 'sent'];

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