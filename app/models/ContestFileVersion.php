<?php
use Illuminate\Filesystem\Filesystem;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

/**
 * ContestFileVersion
 *
 * @property integer $id
 * @property integer $format_id
 * @property integer $contest_file_id
 * @property integer $size
 * @property string $sizes
 * @property float $duration
 * @property string $extension
 * @property boolean $source
 * @property integer $status
 * @property float $percentage
 * @method static \Illuminate\Database\Query\Builder|\ContestFileVersion whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\ContestFileVersion whereFormatId($value)
 * @method static \Illuminate\Database\Query\Builder|\ContestFileVersion whereContestFileId($value)
 * @method static \Illuminate\Database\Query\Builder|\ContestFileVersion whereSize($value)
 * @method static \Illuminate\Database\Query\Builder|\ContestFileVersion whereSizes($value)
 * @method static \Illuminate\Database\Query\Builder|\ContestFileVersion whereDuration($value)
 * @method static \Illuminate\Database\Query\Builder|\ContestFileVersion whereExtension($value)
 * @method static \Illuminate\Database\Query\Builder|\ContestFileVersion whereSource($value)
 * @method static \Illuminate\Database\Query\Builder|\ContestFileVersion whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\ContestFileVersion wherePercentage($value)
 * @property string $eta 
 * @property string $description 
 * @property boolean $cdn_status 
 * @property string $config
 * @property string $storage_bucket
 * @property string $cloud_encoder_id
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $uploaded_at
 * @property-read \ContestFile $contestFile
 * @property-read \Format $format 
 * @method static \Illuminate\Database\Query\Builder|\ContestFileVersion whereEta($value)
 * @method static \Illuminate\Database\Query\Builder|\ContestFileVersion whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\ContestFileVersion whereCdnStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\ContestFileVersion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\ContestFileVersion whereCreatedAt($value)
 */
class ContestFileVersion extends Eloquent {

	const QUEUED = 0;
	const ENCODING = 1;
	const AVAILABLE = 2;
	const ERROR = 3;
	const UPLOADING = 4;
	const CANCELED = 5;
	const UPLOAD_INTERRUPTED = 6;

    const DOWNLOADABLE = ["doc","docx","xls","xlsx","pptx","ai","zip"];
    const NOT_DOWNLOADABLE = ["jpg","webm","mp3","mp4"];

    const UPLOAD_INTERRUPTED_TIMEOUT = 60;

	protected $fillable = ['contest_file_id','format_id','extension','status','storage_bucket'];
	protected $hidden = ['contest_file_id','format_id'];
    protected $dates = [
        'created_at',
        'updated_at',
        'uploaded_at',
        //'deleted_at'
    ];

	public function contestFile() {
		return $this->belongsTo('ContestFile', 'contest_file_id', 'id');
	}

	public function format() {
		return $this->belongsTo('Format');
	}

	public function getRelativePath() {
		return "contests/".$this->contestFile->contest_id."/".$this->id.".".$this->extension;
	}

	public function getPath() {
		//return storage_path('/media/'.$this->file->contest_id."/".$this->contest_file_id."/".$this->id.".".$this->extension);
		return storage_path('media/'.$this->contestFile->contest_id."/".$this->id.".".$this->extension);
	}

	public function getThumbsPath() {
		return storage_path('media/'.$this->contestFile->contest_id."/thumbs/".$this->contestFile->id."/");
	}

	public function getThumbsRelativePath() {
		return 'contests/'.$this->contestFile->contest_id."/thumbs/".$this->contestFile->id."/";
	}

    public function createThumb() {
        if(Config::get('cloud.enabled')) return;
        $fs = new Filesystem();
        $thumbPath = $this->contestFile->getThumbsPath();
        if(!$fs->exists($thumbPath)){
            $fs->makeDirectory($thumbPath, 0755, true);
        }
        switch($this->contestFile->type) {
            case Format::VIDEO:
                FFM::thumbnify($this->getPath(), $thumbPath."th", 5, $this->duration);
                break;
            case Format::AUDIO:
                FFM::generateWaveform($this->getPath(), $thumbPath."th.jpg");
                break;
            case Format::IMAGE:
                $image = new \Imagine\Imagick\Imagine();
                $image->open($this->getPath())
                    ->thumbnail(new Box(Format::THUMB_SIZE_MAX, Format::THUMB_SIZE_MAX), ImageInterface::THUMBNAIL_INSET)
                    ->save($thumbPath."th.jpg", array('quality' => 100));
                break;
        }
    }

    public function getURL(){
        if(!Config::get('cloud.enabled')) return $this->contestFile->getVersionURL($this);
        if($this->status != self::AVAILABLE) return false;
        return Cloud::Instance()->GetGCSecureUrl($this->getRelativePath(), $this->storage_bucket != null ? $this->storage_bucket : Config::get('cloud.streaming_bucket'));
    }

    public function getSymbolicURL(){
        $contest = Contest::where('id', $this->contestFile->contest_id)->select('code')->first();
        return url('/'.$contest['code'].'/fileUrl/'.$this->id);
        //return "https://newawards.oxobox.tv/awards-eskel/";
        /*if(!Config::get('cloud.enabled')) return $this->contestFile->getVersionURL($this);
        if($this->status != self::AVAILABLE) return false;
        return Cloud::Instance()->GetGCSecureUrl($this->getRelativePath(), $this->storage_bucket != null ? $this->storage_bucket : Config::get('cloud.streaming_bucket'));*/
    }

    /**
     * Override de esta funcion para que devuelva la direcci�n del thumbnail.
     * @return array
     */
    public function toArray(){
        $arr = parent::toArray();
        //$arr['url'] = $this->getURL();
        $arr['url'] = $this->getSymbolicURL();
        $arr['label'] = $this->source ? Lang::get('metadata.file.source') : $this->format->label;
        return $arr;
    }

    public function deleteFiles(){
        if(!Config::get('cloud.enabled')) {
            $fs = new Filesystem();
            $fs->delete($this->getPath());
            $fs->deleteDirectory($this->getThumbsPath());
        }else{
            Cloud::Instance()->DeleteFileFromGCStorage($this->getRelativePath(), $this->storage_bucket);
            Cloud::Instance()->DeleteFolderFromGCStorage($this->getThumbsRelativePath(), $this->storage_bucket);
        }
    }
}