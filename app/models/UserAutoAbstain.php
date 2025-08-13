<?php

/**
 * UserAutoAbstain
 *
 * @property integer $id
 * @property integer $voting_user_id
 * @property integer $voting_session_id
 * @property integer $entry_metadata_field_id
 * @property string $value
 */
class UserAutoAbstain extends Eloquent {
	protected $fillable = ['voting_user_id','voting_session_id','entry_metadata_field_id','value'];
}