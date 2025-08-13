<?php

/**
 * Ticket
 *
 * @property integer $id
 * @property string $code
 * @property integer $entry_category_id
 * @property integer $billing_entry_category_id
 */
class Ticket extends Eloquent {

    const INVALID = 0;
    const VALID = 1;
    const ALREADY_CHECKED = 2;
    const NOT_PAYED = 3;

	protected $fillable = ['entry_category_id','billing_entry_category_id'];

	public function entryCategory() {
		return $this->belongsTo('EntryCategory');
	}

	public function billingEntryCategory() {
		return $this->belongsTo('BillingEntryCategory');
    }
}