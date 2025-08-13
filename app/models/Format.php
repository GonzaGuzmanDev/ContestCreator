<?php

/**
 * Format
 *
 * @property integer $id
 * @property string $name
 * @property string $label
 * @property integer $position
 * @property boolean $type
 * @property boolean $active
 * @property string $extension
 * @property string $command
 * @method static \Illuminate\Database\Query\Builder|\Format whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Format whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Format whereLabel($value)
 * @method static \Illuminate\Database\Query\Builder|\Format whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\Format whereExtension($value)
 * @method static \Illuminate\Database\Query\Builder|\Format whereCommand($value)
 */
class Format extends Eloquent {

    /**
     * Constantes con los tipos de archivos
     */
    const VIDEO = 0;
    const IMAGE = 1;
    const AUDIO = 2;
    const DOCUMENT = 3;
    const OTHER = 4;

    const RESIZE_FIT = 0;
    const RESIZE_PERCENTAGE = 1;
    const RESIZE_FORCED = 2;

    /* Tamaños del thumb 400x400px */
    const THUMB_SIZE_MAX = 400;

	protected $fillable = ['name', 'label', 'type', 'extension', 'position', 'command', 'active'];

	/**
	 * Relación de formatos con todos los FilesVersion relacionados
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function ContestFileVersions() {
		return $this->hasMany('ContestFileVersion');
	}

    /**
     * Retorna un string del tipo de archivo
     * 0 - video
     * 1 - image
     * 2 - audio
     * 3 - document
     * @param $type int
     * @return string
     */
    static public function getFileTypeStr($type) {
        switch ($type) {
            case self::VIDEO:
                return Lang::get('general.video');
            case self::IMAGE:
                return Lang::get('general.image');
            case self::AUDIO:
                return Lang::get('general.audio');
            case self::DOCUMENT:
                return Lang::get('general.document');
            case self::OTHER:
                return Lang::get('general.other');
            default:
                return Lang::get('general.video');
        }
    }

    /**
     * Devuelve un array con los strings de cada tipo de archivo
     * @return array
    */
    static public function getAllTypesData(){
        return array(
            self::VIDEO => Lang::get('general.video'),
            self::IMAGE => Lang::get('general.image'),
            self::AUDIO => Lang::get('general.audio'),
            self::DOCUMENT => Lang::get('general.document'),
            self::OTHER => Lang::get('general.other')
        );
    }

    /**
     * @param string $extension
     * @return int
     */
    static public function getFileType($extension){
        //$extension = pathinfo($fileName, PATHINFO_EXTENSION);
        switch($extension){
            case "mp4":
            case "flv":
            case "avi":
            case "mov":
            case "wmv":
            case "m4v":
            case "mpg":
            case "mpeg":
            case "webm":
                return self::VIDEO;
            case "jpg":
            case "jpeg":
            case "png":
            case "tiff":
                return self::IMAGE;
            case "mp3":
            case "m4a":
            case "wav":
            case "aiff":
            case "wma":
                return self::AUDIO;
            case "doc":
            case "docx":
            case "pdf":
            case "xls":
            case "xlsx":
                return self::DOCUMENT;
        }
        return self::OTHER;
    }

    /**
     * @param string $extension
     * @return int
     */
    static public function getMimeType($extension){
        //$extension = pathinfo($fileName, PATHINFO_EXTENSION);
        switch(strtolower($extension)){
            case "mp4": return "video/mp4";
            case "webm": return "video/webm";
            case "flv": return "video/x-flv";
            case "avi": return "video/x-msvideo";
            case "mov": return "video/quicktime";
            case "wmv": return "video/x-ms-wmv";

            case "jpg":
            case "jpeg": return "image/jpeg";
            case "png": return "image/png";
            case "tiff": return "image/tiff";

            case "mp3": return "audio/mpeg3";
            case "wav": return "audio/wav";
            case "aiff": return "audio/aiff";
            case "wma": return "audio/x-ms-wma";

            case "pdf": return "application/pdf";
            case "doc": return "application/msword";
            case "docx": return "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
            case "ppt": return "application/vnd.ms-powerpointtd";
            case "pptx": return "application/vnd.openxmlformats-officedocument.presentationml.presentation";
            case "xls": return "application/vnd.ms-excel";
            case "xlsx": return "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";

            case "zip": return "application/zip";
        }
        return self::OTHER;
    }

    /**
     * @param string $mimetype
     * @return int
     */
    static public function getTypeFromMimeType($mimetype){
        switch($mimetype){
            case "video/mp4":
            case "video/webm":
            case "video/x-flv":
            case "video/x-msvideo":
            case "video/quicktime":
            case "video/x-ms-wmv":
                return self::VIDEO;
            case "image/jpeg":
            case "image/png":
            case "image/tiff":
                return self::IMAGE;
            case "audio/mpeg3":
            case "audio/wav":
            case "audio/aiff":
            case "audio/x-ms-wma":
                return self::AUDIO;
            case "application/pdf":
            case "application/msword":
            case "application/vnd.openxmlformats-officedocument.wordprocessingml.document":
            case "application/vnd.ms-powerpointtd":
            case "application/vnd.openxmlformats-officedocument.presentationml.presentation":
            case "application/vnd.ms-excel":
            case "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet":
                return self::DOCUMENT;
            //case "application/zip":
        }
        return self::OTHER;
    }

    /**
     * Parseamos el comando y devolvemos True si está todo OK.
     * VIDEO y AUDIO:
     *      comando derecho viejo (POR AHORA)
     * IMAGE:
     *      FIT         -> {"quality":100,"type":0,"width":1920,"height":1080,"rotate":0}
     *      PERCENTAGE  -> {"quality":100,"type":1,"percentage":50,"rotate":0}
     *      FORCED      -> {"quality":100,"type":2,"width":1920,"height":1080,"rotate":0}
     */
    public function parseCommand() {
        $command = null;
        if ($this->type == self::AUDIO || $this->type == self::VIDEO) {
            # '{"json":"comando de ffmpeg con los inserts necesarions"}'
            $command = $this->command;
        }
        if ($this->type == self::IMAGE) {
            $command = json_decode($this->command);
            if (!isset($command->quality)) $command->quality = 100;
            if (!isset($command->type)) $command->type = self::RESIZE_PERCENTAGE;
            if ($command->type == self::RESIZE_FIT || $command->type == self::RESIZE_FORCED){
                if(!isset($command->width)) $command->width = 1920;
                if(!isset($command->height)) $command->height = 1080;
            }
            if ($command->type == self::RESIZE_PERCENTAGE){
                if(!isset($command->percentage)) $command->percentage = 100;
            }
            if (!isset($command->rotate)) $command->rotate = 0;
        }
        return $command;
    }
}