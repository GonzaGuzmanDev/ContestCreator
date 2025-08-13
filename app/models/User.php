<?php

use App\Services\OxoMailer;
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

/**
 * User
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property string $verify_token
 * @property string $notifications
 * @property boolean $status
 * @property boolean $active
 * @property boolean $verified
 * @property boolean $super
 * @property string $last_seen_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereActive($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereSuper($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereLastSeenAt($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereUpdatedAt($value)
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\Inscription[] $inscriptions
 */
class User extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    const NotificationNewUser = "newUser";
    const NotificationNewEntry = "newEntry";
    const NotificationEntryFinalized = "entryFinalized";
    const NotificationEntryPaid = "entryPaid";
    const NotificationNewMessage = "newMessage";
    const NotificationEntryApproved = "entryApproved";
    const NotificationEntryError = "entryError";
    const NotificationMediaError = "mediaError";
    const NotificationContestsNotifications = "contestsNotifications";
    const NotificationContestsErrors = "errorNotifications";

    const DefaultNotifications = [
        self::NotificationNewUser => true,
        self::NotificationNewEntry => true,
        self::NotificationEntryFinalized => true,
        self::NotificationEntryPaid => true,
        self::NotificationNewMessage => true,
        self::NotificationEntryApproved => true,
        self::NotificationEntryError => true,
        self::NotificationMediaError => true,
        self::NotificationContestsNotifications => true,
        self::NotificationContestsErrors => false,
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password', 'remember_token', 'verify_token');

    /**
     *
     * @var array
     */
    protected $fillable = ['email','first_name','last_name','password','super', 'active'];

    public function contests() {
        return $this->hasMany('Contest');
    }

    public function entries() {
        return $this->hasMany('Entry');
    }

    public function inscriptions() {
        return $this->hasMany('Inscription');
    }

    public function contestFiles() {
        return $this->hasMany('ContestFile');
    }

    public function fullName(){
        return $this->first_name." ".$this->last_name;
    }

    /**
     * Indica si el usuario es un superadmin
     * @return bool
     */
    public function isSuperAdmin(){
        if($this->super == 0) return 0;
        else return !!$this->super;
    }

    /**
     * Envía un mail al usuario para verificar su dirección de mail
     */
    public function verifyEmail()
    {
        // Once we have the reminder token, we are ready to send a message out to the
        // user with a link to reset their password. We will then redirect back to
        // the current URI having nothing set in the session to indicate errors.
        $token = str_random(60);
        $this->verify_token = $token;
        $this->save();

        $body = View::make('emails.auth.verify', ['token'=>$token])->render();
        $response = OxoMailer::sendMail([
            'email_to' => $this->getReminderEmail(),
            'subject' => Lang::get('account.verify.subject'),
            'body' => $body
        ]);

        return $response;
    }

    /**
     * Devuelve una URL con la imagen de perfil del usuario
     * @param string $version
     * @return string
     */
    public function getProfilePictureURL($version=''){
        return URL::to('profile/'.$this->email.'/picture/'.$version);
    }

    public function getArrayData(){
        $userData = $this->toArray();
        $userData['picThumbUrl'] = Auth::user()->getProfilePictureURL('thumb');
        return $userData;
    }

    public function getJsonData(){
        $userData = $this->getArrayData();
        return json_encode($userData);
    }

    public function toArray(){
        $data = parent::toArray();
        //$data['profileThumb'] = $this->getProfilePictureURL('thumb');
        $data['profileThumb'] = "";
        if(isset($data['notifications']) && $data['notifications'] != null){
            $data['notifications'] = array_merge(User::DefaultNotifications, json_decode($data['notifications'], true));
        }else{
            $data['notifications'] = User::DefaultNotifications;
        }
        return $data;
    }

    public function scopeBasic($query){
        $query->select(['id', 'first_name', 'last_name', 'email']);
    }

    public function getNotificationsConfig(){
        return array_merge(User::DefaultNotifications, $this->notifications != null ? json_decode($this->notifications, true) : []);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function canReceiveNotification($type){
        $notificationSettings = $this->getNotificationsConfig();
        if(array_key_exists($type, $notificationSettings)){
            return $notificationSettings[$type] == true;
        }
        return false;
    }

    public static function getRandomCode(){
        $seed = str_split('abcdefghijklmnopqrstuvwxyz'
            .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'); // and any other characters
        shuffle($seed); // probably optional since array_is randomized; this may be redundant
        $code = '';
        foreach (array_rand($seed, 2) as $k) $code .= $seed[$k];
        $seed = array_merge($seed, str_split('0123456789!'));
        shuffle($seed);
        foreach (array_rand($seed, 6) as $k) $code .= $seed[$k];
        return $code;
    }

    public static function getSimpleRandomCode(){
        $seed = str_split('abcdefghijklmnopqrstuvwxyz'); // and any other characters
        //shuffle($seed); // probably optional since array_is randomized; this may be redundant
        $code = '';
        foreach (array_rand($seed, 3) as $k) $code .= $seed[$k];
        $seed = str_split('0123456789');
        //shuffle($seed);
        foreach (array_rand($seed, 3) as $k) $code .= $seed[$k];
        return $code;
    }

    public function UpdateLastSeen(){
        $this->last_seen_at = date("Y-m-d H:i:s");
        $this->save();
    }
}