<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * Billing
 * @property integer $id
 * @property integer $contest_id
 * @property string $name
 * @property \Carbon\Carbon $start_at
 * @property \Carbon\Carbon $end_at
 * @property int $value
 * @property int $change
 * @property int $min_entries
 * @property int $max_entries
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Discount extends Eloquent {

	use SoftDeletingTrait;

	const CHANGE_PERCENTAGE = 1;
	const CHANGE_PRICE = 2;

	protected $fillable = ['name','value','change','min_entries','max_entries','start_at','end_at'];

	public function contest() {
		return $this->belongsTo('Contest');
	}
}