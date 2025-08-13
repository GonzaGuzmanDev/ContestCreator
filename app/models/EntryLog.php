<?php

/**
 * Entry_log
 *
 * @property integer $id
 * @property string $error
 * @property integer $entry_id
 * @property integer $user_id
 * @property string $read_by
 */
class EntryLog extends Eloquent {

    const LOG_STATUS_MSG = 5;

	protected $fillable = ['entry_id', 'msg', 'user_id', 'read_by'];
	protected $hidden = ['id','entry_id', 'updated_at', 'user_id'];

	protected $casts = [
		'read_by' => 'array',
	];

	protected $table = 'entry_log';

	public function entry() {
		return $this->belongsTo('Entry');
	}

	public function user() {
		return $this->belongsTo('User');
	}
}