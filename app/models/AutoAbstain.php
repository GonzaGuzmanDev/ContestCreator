<?php

/**
 * AutoAbstain
 *
 * @property integer $id
 * @property integer $voting_session_id
 * @property integer $metadata_field_id
 */
class AutoAbstain extends Eloquent {
	protected $fillable = ['voting_session_id','metadata_field_id'];
}