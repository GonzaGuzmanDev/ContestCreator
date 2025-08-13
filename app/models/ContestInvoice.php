<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * Billing
 * @property integer $id
 * @property integer $contest_id
 * @property int $status
 * @property string $invoice_code
 * @property string $invoice_date
 * @property float $amount
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Contest whereUserId($value)
 * @property-read \User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\EntryCategory[] $entry_categories
 */
class ContestInvoice extends Eloquent {
	const STATUS_NOT_INVOICED = 0;
	const STATUS_INVOICED = 1;
	const STATUS_PAYED = 2;
	const STATUS_SWAP = 3;

	protected $fillable = ['contest_id'];
	protected $hidden = ['pivot'];

	public function contest() {
		return $this->belongsTo('Contest');
	}
}