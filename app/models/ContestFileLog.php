<?php

/**
 * Contest_file_id
 *
 * @property integer $id
 * @property string $error
 * @property integer $contest_file_id
 */
class ContestFileLog extends Eloquent {

	protected $fillable = ['contest_file_id', 'msg', 'user_id'];

	protected $table = 'contest_file_log';

	public function file() {
		return $this->hasOne('File');
	}
}