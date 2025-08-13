<?php

/**
 * ExportResult
 *
 * @property integer $id
 * @property integer $contest_id
 * @property string $voting_session_code
 * @property integer $type
 * @property string $config
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ExportResult extends Eloquent {

	const TYPE_EXCEL = 0;
	const TYPE_JSON = 1;
	const TYPE_DOC = 2;

	protected $fillable = ['contest_id','voting_session_code','type','config'];
}