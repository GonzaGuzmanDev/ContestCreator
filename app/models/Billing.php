<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * Billing
 * @property integer $id
 * @property string $code
 * @property integer $user_id
 * @property integer $contest_id
 * @property string $transaction_id
 * @property string $method
 * @property string $payment_data
 * @property string $description
 * @property string $comments
 * @property int $status
 * @property string $error
 * @property float $price
 * @property float $paid
 * @property string $currency
 * @property \Carbon\Carbon $paid_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Contest whereUserId($value)
 * @property-read \User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\EntryCategory[] $entry_categories
 */
class Billing extends Eloquent {

	use SoftDeletingTrait;

	const METHOD_TRANSFER = "transfer";
	const METHOD_CHECK = "check";
	const METHOD_TCO = "TCO";
	const METHOD_MP = "MercadoPago";
	const METHOD_CLICPAGO = "ClicPago";
    const METHOD_STRIPE = "stripe";
    const METHOD_CREDITCARD = "creditcard";
    const METHOD_OTHER = "other";
    const CUSTOM_API = "customApi";

	const STATUS_PENDING = 0;
	const STATUS_SUCCESS = 1;
	const STATUS_ERROR = 2;
	const STATUS_PARTIALLY_PAID = 3;
    const UNPAID = 4;
    const STATUS_PROCESSING = 5;

    const ClicPagoEncryptionService = 'https://plataforma.clicpago.com/clicpago/api/rest/v1/encryptionService/encrypt.json?value=';
    const ClicPagoDescryptKey = '654321';

	protected $fillable = [];
	protected $hidden = ['pivot'];

	public function contest() {
		return $this->belongsTo('Contest');
	}

	public function entry() {
		return $this->belongsToMany('Entry')->nameLoaded();
	}

	public function user() {
		return $this->belongsTo('User');
	}

	public function billingEntryCategories() {
		return $this->hasMany('BillingEntryCategory');
	}

	public function scopeShort($query){
		$t = $this->getTable().".";
		$query->select($t.'id',$t.'status',$t.'method',$t.'price',$t.'currency',$t.'paid_at',$t.'payment_data',$t.'transaction_id')->get();
			//->groupBy($t.'id'); //$$ revisar
	}

	public function toArray(){
		$arr = parent::toArray();
		if(isset($arr['payment_data'])) $arr['payment_data'] = json_decode($arr['payment_data'], true);
		return $arr;
	}

	public static function getStatusName($status){
		switch($status){
			case self::STATUS_PENDING: return Lang::get('billing.status.pending');break;
			case self::STATUS_SUCCESS: return Lang::get('billing.status.success');break;
			case self::STATUS_ERROR: return Lang::get('billing.status.error');break;
			case self::STATUS_PROCESSING: return Lang::get('billing.status.processing');break;
			//default: return Lang::get('billing.status.waiting');break;
		}
	}

    static public function createCode(){
        do{
            $code = User::getRandomCode();
            $ret = Billing::where('code','=',$code)->get();
        }while(count($ret));
        return $code;
    }
}