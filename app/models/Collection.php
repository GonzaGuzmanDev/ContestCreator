<?php
/**
 * Collection
 * @property integer $id
 * @property integer $contest_id
 * @property string code
 * @property string $name
 * @property integer $private
 * @property \Carbon\Carbon $start_at
 * @property \Carbon\Carbon $finish_at
 * @property integer $voting_session_id
 * @property integer $show_prize
 * @property string $config
 * @property string $metadata_config
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */

class Collection extends Eloquent{

    use SoftDeletingTrait;

    protected $fillable = ['contest_id', 'name', 'private', 'start_at', 'finish_at', 'voting_session_id', 'config', 'metadata_config', 'show_prize'];

    public function contest() {
        return $this->belongsTo('Contest');
    }

    static public function createCode(){
        do{
            $code = User::getRandomCode();
            $ret = VotingSession::where('code','=',$code)->get();
        }while(count($ret));
        return $code;
    }
}