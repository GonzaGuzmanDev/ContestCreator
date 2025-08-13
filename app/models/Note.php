<?php

/**
 * Note
 *
 * @property integer $id
 * @property string $error
 * @property integer $inscription_id
 */
class Note extends Eloquent {

	protected $fillable = ['inscription_id', 'msg'];
	protected $hidden = ['updated_at', 'user_id'];

	protected $table = 'notes';
}