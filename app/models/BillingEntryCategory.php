<?php

/**
 * Entry
 *
 * @property integer $id
 * @property integer $billing_id
 * @property integer $category_id
 * @property integer $entry_id
 * @property float $price
 * @property float $original_price
 * @property integer $discount_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Category $category
 * @property-read Entry $entry
 * @property-read Billing $billing
 */
class BillingEntryCategory extends Eloquent {

	use SoftDeletingTrait;

	protected $fillable = ['billing_id','category_id','entry_id'];
	protected $hidden = ['created_at','updated_at','id'];

	protected $table = "billing_entries_categories";

	public function billing() {
		return $this->belongsTo('Billing');
	}

	public function category() {
		return $this->belongsTo('Category');
	}

	public function entry() {
		return $this->belongsTo('Entry');
		//return $this->belongsTo('Entry')->withTrashed();
	}
}