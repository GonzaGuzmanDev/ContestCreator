<?php namespace App\Services;
use Config, File, Log;
use ContestFile;
use ContestFileVersion;
use Format;
use Imagine\Exception\Exception;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Imagick\Imagine;

class Image {

    /**
     * Instance of the Imagine package
     * @var Imagine
     */
    protected $imagine;

    /**
     * Type of library used by the service
     * @var string
     */
    protected $library;

    /**
     * Returns object instance for chainable methods
     * @return object
     */
    public static function convert() {
        $image = new Image();
        return $image;
    }

    /**
     * Initialize the image service
     */
    public function __construct() {
        if (!$this->imagine) {
            $this->library = Config::get('image.library', 'gd');
            // Now create the instance
            if ($this->library == 'imagick') $this->imagine = new Imagine();
            elseif ($this->library == 'gmagick') $this->imagine = new \Imagine\Gmagick\Imagine();
            elseif ($this->library == 'gd') $this->imagine = new \Imagine\Gd\Imagine();
            else $this->imagine = new \Imagine\Gd\Imagine();
        }
    }


    /**
     * Devuelve el tamaño de la Imagen abierta
     * WIDTHxHEIGHT
     * @param $path string Path del archivo de imagen
     * @return string
     */
    public function getSizes($path="") {
        if ($path == "") return "";
        try {
            $box = $this->imagine->open($path)->getSize();
            return $box->getWidth()."x".$box->getHeight();
        } catch (Exception $e) {
            return "";
        }
    }
    /**
     * Resize an image
     * @param  string  $source
     * @param  string  $destination
     * @param  integer $width
     * @param  integer $height
     * @param  boolean $crop
     * @param  int $quality
     * @return boolean
     */
    public function resizeAndSave($source, $destination, $width=100, $height=null, $crop=false, $quality=90) {
        if ($source) {
            // The size
            if (!$height) $height = $width;
            // Quality
            $quality = Config::get('image.quality', $quality);
            // Create directory if missing
            try {
                // Set the size
                $size = new Box($width, $height);
                // Now the mode
                $mode = $crop ? ImageInterface::THUMBNAIL_OUTBOUND : ImageInterface::THUMBNAIL_INSET;
                if ( ! File::exists($destination) or (File::lastModified($destination) < File::lastModified($source))) {
                    $this->imagine->open($source)
                        ->thumbnail($size, $mode)
                        ->save($destination, array('quality' => $quality));
                }
            }
            catch (Exception $e) {
                Log::error('[IMAGE SERVICE] Failed to resize image "' . $source . '" [' . $e->getMessage() . ']');
            }
            return $destination;
        }
        return false;
    }

    /**
     * Creates image dimensions based on a configuration
     * @param  string $source
     * @param  string $destination
     * @param  string $recipe
     * @return boolean
     */
    public function recipeAndSave($source, $destination, $recipe) {
        // Get default dimensions
        $recipes = Config::get('image.recipes');
        // Paths info
        // $sourceInfo = pathinfo($source);
        $destinationInfo = pathinfo($destination);
        foreach ($recipes as $recipeKey => $recipeConfig) {
            if($recipeKey != $recipe) continue;
            // Get dimmensions and quality
            $width = (int) $recipeConfig[0];
            $height = isset($recipeConfig[1]) ? (int) $recipeConfig[1] : $width;
            $crop = isset($recipeConfig[2]) ? (bool) $recipeConfig[2] : false;
            $quality = isset($recipeConfig[3]) ? (int) $recipeConfig[3] : Config::get('image.quality');
            $recipeKeyParts = explode('.', $recipeKey);
            array_shift($recipeKeyParts);
            $sufix = implode('.', $recipeKeyParts);
            if($sufix!='') $sufix = '.'.$sufix;
            $newDestination = $destinationInfo['dirname'] . '/' . $destinationInfo['filename'] . $sufix . '.' . $destinationInfo['extension'];
            // Run resizer
            return $this->resizeAndSave($source, $newDestination, $width, $height, $crop, $quality);
        }
        return false;
    }

    /**
     * Resize an image
     * @param  string $url
     * @param  integer $width
     * @param  integer $height
     * @param  boolean $crop
     * @param int $quality
     * @return string
     * ImageInterface::THUMBNAIL_INSET:
     *      The original image is scaled down so it is fully contained within the thumbnail dimensions.
     *      The specified $width and $height will be considered maximum limits.
     *      Unless the given dimensions are equal to the original image’s aspect ratio,
     *      one dimension in the resulting thumbnail will be smaller than the given limit.
     * ImageInterface::THUMBNAIL_OUTBOUND:
     *      The thumbnail is scaled so that its smallest side equals the length of the corresponding side in the original image.
     *      Any excess outside of the scaled thumbnail’s area will be cropped, and the returned thumbnail will have the exact $width and $height specified.
     */
    public function resize($url, $width = 100, $height = null, $crop = false, $quality=90) {
        if ($url) {
            // URL info
            $info = pathinfo($url);
            // The size
            if (!$height) $height = $width;
            // Quality
            $quality = Config::get('image.quality', $quality);
            // Directories and file names
            $fileName = $info['basename'];
            $sourceDirPath = public_path().'/'.$info['dirname'];
            $sourceFilePath = $sourceDirPath.'/'.$fileName;
            $targetDirName = $width.'x'.$height.($crop ? '_crop' : '');
            $targetDirPath = $sourceDirPath.'/'.$targetDirName.'/';
            $targetFilePath = $targetDirPath.$fileName;
            $targetUrl = asset($info['dirname'].'/'.$targetDirName.'/'.$fileName);
            // Create directory if missing
            try {
                // Create dir if missing
                if ( ! File::isDirectory($targetDirPath) and $targetDirPath) @File::makeDirectory($targetDirPath);
                // Set the size
                $size = new Box($width, $height);
                // Now the mode
                $mode = $crop ? ImageInterface::THUMBNAIL_OUTBOUND : ImageInterface::THUMBNAIL_INSET;
                if ( ! File::exists($targetFilePath) or (File::lastModified($targetFilePath) < File::lastModified($sourceFilePath))) {
                    $this->imagine->open($sourceFilePath)
                        ->thumbnail($size, $mode)
                        ->save($targetFilePath, array('quality' => $quality));
                }
            }
            catch (Exception $e) {
                Log::error('[IMAGE SERVICE] Failed to resize image "'.$url.'" ['.$e->getMessage().']');
            }
            return $targetUrl;
        }
        return '';
    }

    /**
     * Convierte según el formato
     * @param $file_id int
     * IMAGE:
     *      FIT         -> {"quality":100,"type":0,"width":1920,"height":1080,"rotate":0}
     *      PERCENTAGE  -> {"quality":100,"type":1,"percentage":50,"rotate":0}
     *      FORCED      -> {"quality":100,"type":2,"width":1920,"height":1080,"rotate":0}
     */
    public function formatConvertAndSave($file_id) {
        /* @var $file ContestFile */
        $file = ContestFile::find($file_id);
        $versions = $file->getVersions();
        $source = $file->getSource();
        if(!$source || !File::exists($source->getPath())) {
            Log::error('[IMAGE SERVICE] Source not found FILE ID: "'.$file_id);
            return;
        }
        foreach ($versions as $version) {
            /* @var $format Format */
            $format = $version->format;
            $command = $format->parseCommand();
            $image = $this->imagine->open($source->getPath());
            try {
                switch($command->type){
                    case Format::RESIZE_FORCED:
                        $image->resize(new Box($command->width, $command->height));
                        break;
                    case Format::RESIZE_FIT:
                        $image->thumbnail(new Box($command->width, $command->height), ImageInterface::THUMBNAIL_INSET);
                        break;
                    case Format::RESIZE_PERCENTAGE:
                    default:
                        $box = $image->getSize();
                        $width = round($box->getWidth() * $command->percentage / 100);
                        $height = round($box->getHeight() * $command->percentage / 100);
                        $image->thumbnail(new Box($width, $height), ImageInterface::THUMBNAIL_INSET);
                }
                $image->save($version->getPath(), array('quality' => $command->quality));
            } catch (Exception $e) {
                Log::error('[IMAGE SERVICE] Failed to resize image ['.$e->getMessage().']');
            }
        }
    }
}