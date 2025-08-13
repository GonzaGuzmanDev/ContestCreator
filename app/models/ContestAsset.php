<?php
/**
 * ContestAsset
 *
 * @property integer $id
 * @property integer $contest_id
 * @property boolean $type
 * @property string $name
 * @property string $code
 * @property string $content
 * @property string $content_type
 * @property string $extension
 * @property-read \Contest $contest
 * @method static \Illuminate\Database\Query\Builder|\ContestAsset whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\ContestAsset whereContestId($value)
 * @method static \Illuminate\Database\Query\Builder|\ContestAsset whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\ContestAsset whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\ContestAsset whereContent($value)
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @method static \Illuminate\Database\Query\Builder|\ContestAsset whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\ContestAsset whereUpdatedAt($value)
 * @method static \ContestAsset staticPage()
 * @method static \ContestAsset staticAsset()
 */
class ContestAsset extends \Eloquent {

    const BIG_BANNER = 0;
    const SMALL_BANNER = 1;
    const HOME_HTML = 2;
    const HOME_BOTTOM_HTML = 3;
    const STATIC_PAGE = 4;
    const TERMS = 5;

    /* EMAILS */
    const INSCRIPTION_OK_EMAIL = 7;
    const INSCRIPTOR_INVITATION_EMAIL = 8;
    const JUDGE_INVITATION_EMAIL = 9;
    const COLLABORATOR_INVITATION_EMAIL = 10;
    const ENTRY_ERROR_EMAIL = 11;
    const ENTRY_APPROVED_EMAIL = 12;
    const MEDIA_ERROR_EMAIL = 13;
    const OTHER_PURPOSES_MAIL = 14;

    const VOTING_BOTTOM_HTML = 15;

    const NEW_INSCRIPTION_MESSAGE = 16;
    const NEW_JUDGE_INSCRIPTION_MESSAGE = 17;
    const ENTRY_FINALIZED_EMAIL = 18;

    const BIG_BANNER_HTML = 19;
    const SMALL_BANNER_HTML = 20;

    const GENERAL_FILE = 100;

    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'contest_assets';

    /**
     * @var array
     */
    protected $fillable = ['id', 'contest_id', 'type', 'name', 'code', 'content', 'extension', 'content_type'];

    /**
     * @var array
     */
    protected $hidden = ['contest_id'];

    public function contest() {
        return $this->belongsTo('Contest');
    }

    /**
     * Scope que devuelve los ContestAsset que son páginas estáticas.
     * @param $query
     * @return
     */
    public function scopeStaticPage($query)
    {
        return $query->where('type', '=', ContestAsset::STATIC_PAGE);
    }

    /**
     * Scope que devuelve los ContestAsset que son páginas estáticas.
     * @param $query
     * @return
     */
    public function scopeStaticAsset($query)
    {
        return $query->where('type', '=', ContestAsset::GENERAL_FILE);
    }

    public function getRelativePath() {
        return "contests/".$this->contest_id."/assets/".$this->id.".".$this->extension;
    }

    /**
     * Devuelve una URL para ir a ver el archivo binario
     * @param $contestCode
     * @return string
     */
    public function getURL($contestCode) {
        return URL::to($contestCode.'/asset/'.$this->id);
    }

    /** Returns the Google Cloud signed URL
     * @return string
     */
    public function getCloudURL() {
        try {
            return Cloud::Instance()->GetGCSecureUrl($this->getRelativePath(), $this->getBucket());
        } catch (Google_Exception $e) {
        }
    }

    public function getBucket(){
        return Config::get('cloud.streaming_bucket');
    }

    /**
     * Devuelve una URL con la página de este static page
     * @param $contestCode
     * @return string
     * @throws Google_Exception
     */
    public function getPageURL($contestCode) {
        if($this->type != self::STATIC_PAGE) return $this->getURL($contestCode);
        return URL::to($contestCode.'/#page/'.$this->code);
    }

    public function toArray(){
        $data = parent::toArray();
        if($this->exists) {
            if ($this->type == self::STATIC_PAGE) {
                $data['url'] = $this->getPageURL($this->contest->code);
            } else {
                $data['url'] = $this->getURL($this->contest->code);
                if (Format::getTypeFromMimeType($this->content_type) == Format::IMAGE) {
                    $data['preview'] = "<img src='".$data['url']."' />";
                }else{
                    $data['html'] = $this->content;
                }
            }
        }else{
            $data['url'] = "";
            $data['html'] = "";
        }
        return $data;
    }

    /**
     * @param $name
     * @return bool|null|string|string[]
     */
    public static function getCode($name){
        $name = preg_replace('~[^\\pL0-9_]+~u', '-', $name);
        $name = trim($name, "-");
        $name = iconv("utf-8", "us-ascii//TRANSLIT", $name);
        $name = strtolower($name);
        $name = preg_replace('~[^-a-z0-9_]+~', '', $name);
        return $name;
    }

    static public function getAllTypes(){
        return array(
            self::HOME_HTML => Lang::get('contest.homeHTML'),
            self::HOME_BOTTOM_HTML => Lang::get('contest.homeBottomHTML'),
            self::STATIC_PAGE=> Lang::get('contest.staticPage'),
            self::TERMS => Lang::get('contest.termsHtml'),

            self::INSCRIPTION_OK_EMAIL => Lang::get('contest.inscriptionEmail'),
            self::INSCRIPTOR_INVITATION_EMAIL => Lang::get('contest.inscriptorInvitationEmail'),
            self::JUDGE_INVITATION_EMAIL => Lang::get('contest.judgeInvitationEmail'),
            self::COLLABORATOR_INVITATION_EMAIL => Lang::get('contest.collaboratorInvitationEmail'),
            self::ENTRY_ERROR_EMAIL => Lang::get('contest.entryErrorEmail'),
            self::ENTRY_APPROVED_EMAIL => Lang::get('contest.entryApprovedEmail'),
            self::MEDIA_ERROR_EMAIL => Lang::get('contest.mediaErrorEmail'),
            self::OTHER_PURPOSES_MAIL => Lang::get('contest.otherPurposesEmail'),
            self::ENTRY_FINALIZED_EMAIL=> Lang::get('contest.entryFinalizedEmail'),

            self::VOTING_BOTTOM_HTML => Lang::get('contest.votingBottomHtml'),

            self::NEW_INSCRIPTION_MESSAGE => Lang::get('contest.newInscriptionMessage'),
            self::NEW_JUDGE_INSCRIPTION_MESSAGE => Lang::get('contest.newJudgeInscriptionMessage'),

            self::BIG_BANNER_HTML => Lang::get('contest.bigBannerHTML'),
            self::SMALL_BANNER_HTML => Lang::get('contest.smallBannerHTML'),
        );
    }

    static public function getAllTypesIds(){
        return array(
            self::HOME_HTML,
            self::HOME_BOTTOM_HTML,
            self::TERMS,
            self::INSCRIPTION_OK_EMAIL,
            self::INSCRIPTOR_INVITATION_EMAIL,
            self::JUDGE_INVITATION_EMAIL,
            self::COLLABORATOR_INVITATION_EMAIL,
            self::ENTRY_ERROR_EMAIL,
            self::ENTRY_APPROVED_EMAIL,
            self::ENTRY_FINALIZED_EMAIL,
            self::MEDIA_ERROR_EMAIL,
            self::OTHER_PURPOSES_MAIL,
            self::VOTING_BOTTOM_HTML,
            self::BIG_BANNER_HTML,
            self::SMALL_BANNER_HTML,
        );
    }
}