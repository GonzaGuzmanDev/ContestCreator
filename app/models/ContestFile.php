<?php

use App\Services\Image;
use Illuminate\Filesystem\Filesystem;

/**
 * ContestFile
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property integer $contest_id
 * @property integer $user_id
 * @property integer $type
 * @property integer $file_size
 * @property integer $height
 * @property integer $width
 * @property integer $thumbs
 * @property float $duration
 * @property boolean $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\ContestFile whereId($value) 
 * @method static \Illuminate\Database\Query\Builder|\ContestFile whereName($value) 
 * @method static \Illuminate\Database\Query\Builder|\ContestFile whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\ContestFile whereContestId($value)
 * @method static \Illuminate\Database\Query\Builder|\ContestFile whereUserId($value) 
 * @method static \Illuminate\Database\Query\Builder|\ContestFile whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\ContestFile whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\ContestFile whereCreatedAt($value) 
 * @method static \Illuminate\Database\Query\Builder|\ContestFile whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\ContestFileVersion[] $contestFileVersions
 */
class ContestFile extends Eloquent {

    const QUEUED = 0;
    const ENCODING = 1;
    const ENCODED = 2;
    const ERROR = 3;
    const UPLOADING = 4;
    const CANCELED = 5;
    const UPLOAD_INTERRUPTED = 6;

    const TECH_NO_STATE = 0;
    const TECH_OK = 1;
    const TECH_ERROR = 2;

	protected $fillable = ['name', 'contest_id', 'user_id', 'type', 'status', 'tech_status','code'];
	protected $hidden = ['contest_id', 'user_id', 'created_at', 'updated_at', 'pivot'];

    use SoftDeletingTrait;

	public function contest() {
		return $this->belongsTo('Contest');
	}

    public function getCloudThumbsPath() {
        return "contests/".$this->contest_id."/thumbs/".$this->id."/";
    }
    public function getThumbsPath() {
        return storage_path('media/'.$this->contest_id."/thumbs/".$this->id."/");
    }

	public function user() {
		return $this->belongsTo('User');
	}

	public function ContestFileVersions() {
		return $this->hasMany('ContestFileVersion');
	}

	public function EntryMetadataFiles() {
		return $this->hasMany('EntryMetadataFile');
	}

	public function EntryMetadataValues() {
		return $this->belongsToMany('EntryMetadataValue', 'entry_metadata_files');
	}

    public function EntryMetadataValuesNoFiles() {
		return $this->belongsToMany('EntryMetadataValue');
	}

	static public function createCode(){
		do{
            $code = User::getRandomCode();
            $ret = ContestFile::where('code','=',$code)->get();
		}while(count($ret));
		return $code;
	}

    /**
     * @param Contest $contest
     * @param string $tmpPath
     * @param string $fileName
     * @param $extension
     * @param User $user
     * @param int $size
     * @return ContestFile
     */
	static public function make($contest, $tmpPath, $fileName, $extension, $user, $size = 0){
		//$fileName = Input::file('file')->getClientOriginalName();
		//$extension = pathinfo($fileName, PATHINFO_EXTENSION);
		$fileType = Format::getFileType($extension);
		$fs = new Filesystem();
		$contestFile = new ContestFile();
		$contestFile->contest_id = $contest->id;
		$contestFile->user_id = $user->id;
		$contestFile->type = $fileType;
		$contestFile->name = $fileName;
		$contestFile->code = ContestFile::createCode();
        $contestFile->status = ContestFile::UPLOADING;
		$contestFile->save();

		$ContestFileVersion = new ContestFileVersion();
		$ContestFileVersion->contest_file_id = $contestFile->id;
		$ContestFileVersion->source = true;
		$ContestFileVersion->extension = $extension;
		if($tmpPath != null) {
            $ContestFileVersion->size = $fs->size($tmpPath);
            if ($contestFile->type == Format::VIDEO || $contestFile->type == Format::AUDIO) {
                $mediaInfo = FFM::getMediaInfo($tmpPath, 'json');
                $ContestFileVersion->duration = $mediaInfo['format']['duration'];
                foreach ($mediaInfo['streams'] as $stream) {
                    if ($stream['codec_type'] == 'video') {
                        $ContestFileVersion->sizes = $stream['width'] . "x" . $stream['height'];
                    }
                }
            }
            if ($contestFile->type == Format::IMAGE) {
                $ContestFileVersion->sizes = Image::convert()->getSizes($tmpPath);
            }
        }else{
            $ContestFileVersion->size = $size;
        }
        if(Config::get('cloud.enabled')){
            $ContestFileVersion->storage_bucket = $contest->storage_sources_bucket;
        }else {
            $ContestFileVersion->status = ContestFileVersion::AVAILABLE;
        }
		$ContestFileVersion->save();

        if($tmpPath != null) {
            $newPath = $ContestFileVersion->getPath();
            $folder = pathinfo($newPath, PATHINFO_DIRNAME);
            if(!$fs->exists($folder)){
                $fs->makeDirectory($folder);
            }
            $fs->move($tmpPath, $newPath);
            $ContestFileVersion->createThumb();
        }
        /** @var Format[] $formats */
        $formats = Format::where('active','=','1')->where('type','=',$fileType)->get();
        $encodeableVersions = false;
		foreach($formats as $format) {
            $ContestFileVersion = new ContestFileVersion();
            $ContestFileVersion->contest_file_id = $contestFile->id;
            $ContestFileVersion->format_id = $format->id;
            $ContestFileVersion->source = false;
            $ContestFileVersion->extension = $format->extension;
            $ContestFileVersion->status = ContestFileVersion::QUEUED;
            if(Config::get('cloud.enabled')){
                $ContestFileVersion->storage_bucket = Config::get('cloud.streaming_bucket');
            }
            $ContestFileVersion->save();
            $encodeableVersions = true;
        }
        if(!$encodeableVersions) {
            $contestFile->status = ContestFile::ENCODED;
            $contestFile->save();
        }
        //ContestFile::executeEncoder($contestFile->contest_id, $contestFile->type);
		return $contestFile;
	}

    /**
     * Devuelve el ContestFileVersion del Source
     * @return ContestFileVersion
     */
    public function getSource() {
        return ContestFileVersion::where("contest_file_id", "=", $this->id)->where("source", "=", 1)->first();
    }

    /**
     * Devuelve los ContestFileVersion a encodear
     * @return ContestFileVersion[]
     */
    public function getVersions() {
        return ContestFileVersion::where("contest_file_id", "=", $this->id)->where("source", "=", 0)->get();
    }

    public function checkVersionsStatus(){
        $allErrors = true;
        $moreThanSource = false;
        foreach ($this->contestFileVersions as $contestFileVersion){
            if(!$contestFileVersion->source) {
                if ($contestFileVersion->status != ContestFileVersion::CANCELED) {
                    $moreThanSource = true;
                    if ($contestFileVersion->status != ContestFileVersion::ERROR) {
                        $allErrors = false;
                    }
                }
            }elseif($contestFileVersion->status == ContestFileVersion::UPLOAD_INTERRUPTED){
                $this->status = ContestFile::UPLOAD_INTERRUPTED;
                $this->save();
                return;
            }elseif($contestFileVersion->status == ContestFileVersion::UPLOADING && $contestFileVersion->uploaded_at != null && $contestFileVersion->uploaded_at->diffInSeconds(\Carbon\Carbon::now()) > ContestFileVersion::UPLOAD_INTERRUPTED_TIMEOUT){
                $contestFileVersion->status = ContestFileVersion::UPLOAD_INTERRUPTED;
                $contestFileVersion->save();
                $this->status = ContestFile::UPLOAD_INTERRUPTED;
                $this->save();
                return;
            }
        }
        if($moreThanSource && $allErrors && $this->status != ContestFile::ERROR){
            $this->status = ContestFile::ERROR;
            $this->save();
        }
    }

    /**
     * Ejecuta los encoders
     * @param $contest_id
     * @param $type
     */
    static public function executeEncoder($contest_id = null, $type = null){
        if(Config::get('cloud.enabled')){
            $command = Config::get('cloud.manager');
        }else {
            switch ($type) {
                case Format::VIDEO:
                    $command = Config::get('encoder.video');
                    break;
                case Format::AUDIO:
                    $command = Config::get('encoder.audio');
                    break;
                case Format::IMAGE:
                    $command = Config::get('encoder.image');
                    break;
                case Format::DOCUMENT:
                    $command = Config::get('encoder.document');
                    break;
                default:
                    return;
            }
            if($command != '') {
                $command .= ' ' . $contest_id;
            }
        }
        time_nanosleep(0,200000000);
        if($command != ''){
            passthru($command." >> /tmp/error-output.txt 2>&1 &");
        }
    }

    /**
     * Devuelve una URL con la imagen de perfil del usuario
     * @return string
     */
    public function getThumbURL(){
        if(!Config::get('cloud.enabled')) return URL::to('/'.$this->contest->code.'/file/'.$this->code.'/thumb');
        //if($this->thumbs == 0) return URL('/img/media/media-'.$this->type.'.png');
        if($this->status != self::ENCODED) return URL('/img/media/media-'.$this->type.'.png');
        if($this->thumbs != self::ENCODING) return URL('/img/media/media-'.$this->type.'.png');
        $thumbPath = $this->getCloudThumbsPath();
        switch ($this->type) {
            case Format::VIDEO:
                $thumbPath .= "th-0002.jpg";
                break;
            case Format::DOCUMENT:
                $thumbPath .= "th-0000.jpg";
                break;
            case Format::AUDIO:
            case Format::IMAGE:
                $thumbPath .= "th.jpg";
                break;
            default:
                return URL('/img/media/media-'.$this->type.'.png');
        }
        return Cloud::Instance()->GetGCSecureUrl($thumbPath, Config::get('cloud.streaming_bucket'));
    }

    /**
     * @param ContestFileVersion $version
     * @return string
     */
    public function getVersionURL($version){
        return URL::to('/'.$this->contest->code.'/file/'.$this->code.'/v/'.$version->id.".".$version->extension);
    }

    /**
     * Override de esta funcion para que devuelva la dirección del thumbnail.
     * @return array
     */
    public function toArray(){
        $arr = parent::toArray();
        $arr['progress'] = 0;

        if($this->type == Format::VIDEO || $this->type == Format::AUDIO || $this->type == Format::IMAGE) {
            if ($this->status == ContestFile::ENCODING || $this->status == ContestFile::UPLOADING) {
                $fileVersions = $this->ContestFileVersions();
                $total = 0;

                /** @var $fvs ContestFileVersion[] */
                $fvs = $fileVersions->getResults();
                $prog = 0;
                foreach ($fvs as $fv) {
                    if ($this->status == ContestFile::UPLOADING && $fv->source == 1){
                        if($fv->uploaded_at != null && $fv->uploaded_at->diffInSeconds(\Carbon\Carbon::now()) > ContestFileVersion::UPLOAD_INTERRUPTED_TIMEOUT){
                            $fv->status = ContestFileVersion::UPLOAD_INTERRUPTED;
                            $fv->save();
                            $this->status = ContestFile::UPLOAD_INTERRUPTED;
                            $this->save();
                            continue;
                            //return $this->toArray();
                        }
                    }
                    if ($fv->source && $this->status == ContestFile::UPLOADING) {
                        $total++;
                        $prog += $fv->percentage;
                    }elseif (!$fv->source && $this->status == ContestFile::ENCODING && $fv->status != ContestFileVersion::CANCELED) {
                        $total++;
                        $prog += $fv->percentage;
                    }
                }
                $arr['total'] = $total;
                $arr['prog'] = $prog;
                $arr['progress'] = $total > 0 ? round($prog / $total, 2) : 0;
            }
        }else{
            $arr['total'] = 100;
            $arr['prog'] = 100;
            $arr['progress'] = 100;
        }
        $arr['thumb'] = $this->getThumbURL();
        return $arr;
    }
}