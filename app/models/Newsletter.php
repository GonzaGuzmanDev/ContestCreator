<?php
/**
 * Newsletter
 * @property integer $id
 * @property integer $contest_id
 * @property string $reply_to
 * @property string $name
 * @property string $subject
 * @property string $email_body
 * @property string $send_when
 * @property integer $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */

class Newsletter extends Eloquent{

    const STATUS_WAITING = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_SEND = 2;

    protected $fillable = ['contest_id', 'name', 'subject', 'email_body'];

    public function contest() {
        return $this->belongsTo('Contest');
    }
}