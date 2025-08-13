<?php

/**
 * Vote
 *
 * @property integer $id
 * @property integer $voting_session_id
 * @property integer $entry_category_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\Vote whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Vote whereEntryId($value)
 * @method static \Illuminate\Database\Query\Builder|\Vote whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Vote whereUpdatedAt($value)
 */
class VotingShortlist extends Eloquent {

    protected $fillable = ['voting_session_id','entry_category_id'];

    protected $table = 'voting_shortlists';

    /*public function votingUser() {
        return $this->belongsTo('VotingUser');
    }*/
}