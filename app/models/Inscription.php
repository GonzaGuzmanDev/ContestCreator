<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * Inscription
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $contest_id
 * @property integer $inscription_type_id
 * @property string $email
 * @property string $invitename
 * @property boolean $role
 * @property string $permits
 * @property integer $active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $start_at
 * @property \Carbon\Carbon $deadline1_at
 * @property \Carbon\Carbon $deadline2_at
 * @method static \Illuminate\Database\Query\Builder|\Inscription whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Inscription whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Inscription whereContestId($value)
 * @method static \Illuminate\Database\Query\Builder|\Inscription whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\Inscription whereRole($value)
 * @method static \Illuminate\Database\Query\Builder|\Inscription wherePermits($value)
 * @method static \Illuminate\Database\Query\Builder|\Inscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Inscription whereUpdatedAt($value)
 */
class Inscription extends Eloquent {

	const INSCRIPTOR = 1;
	const COLABORATOR = 2;
	const JUDGE = 3;
	const OWNER = 4;

	use SoftDeletingTrait;

	protected $fillable = ['contest_id','user_id','inscription_type_id','email','invitename','role','permits','start_at','deadline1_at','deadline2_at'];

	/**
	 * @var array
	 */
	protected $hidden = ['created_at','updated_at']; //'id','inscription_type_id','user_id','contest_id',

	public function contest() {
		return $this->belongsTo('Contest');
	}

	public function user() {
		return $this->belongsTo('User');
	}

	public function scopeVotingSession($query){
		//$query->select('id', 'code', 'name', 'start_at', 'finish_at');
	}

    public function userLimited() {
        return $this->belongsTo('User')->select('inscription_type_id','role');
    }

	public function inscriptionType() {
		return $this->belongsTo('InscriptionType');
	}

	public function votingUsers() {
		return $this->hasMany('VotingUser');
	}

	public function inscriptionMetadatas() {
		return $this->hasMany('InscriptionMetadataValue');
	}

	public function insMetaAndFields() {
		return $this->hasMany('InscriptionMetadataValue')->with('InscriptionMetadataField');
	}

	public function toArray()
	{
		$array = parent::toArray();
		if(isset($array['permits'])) $array['permits'] = json_decode($array['permits'], true);
		return $array;
	}

    static public function getAllRoles(){
        return array(
            self::OWNER => Lang::get('user.owner'),
			self::COLABORATOR => Lang::get('user.colaborator'),
            self::INSCRIPTOR => Lang::get('user.inscriptor'),
            self::JUDGE => Lang::get('user.judge')
        );
    }
}