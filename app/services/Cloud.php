<?php

class Cloud
{

    const GC_REQUESTS_ROOT = "https://www.googleapis.com/";

    /** @var $_gcClient Google_Client */
    private $_gcClient;

    /** @var $_gcStorageService Google_Service_Compute */
    private $_gcComputeService;

    /** @var $_gcStorageService Google_Service_Storage */
    private $_gcStorageService;

    /** @var $_gcStorageClient \Google\Cloud\Storage\StorageClient */
    private $_gcStorageClient;

    /** @var $_gcStorageClient \Google\Cloud\Storage\StorageClient */
    private $_gcServiceBuilder;

    /**
     * Call this method to get singleton
     *
     * @return Cloud
     * @throws Google_Exception
     */
    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new Cloud();
        }
        return $inst;
    }

    /**
     * Private ctor so nobody else can instance it
     *
     * @throws Google_Exception
     */
    private function __construct()
    {
        if($this->_gcClient == null){
            $this->_gcClient = new Google_Client();
            $this->_gcClient->setAuthConfig(Config::get('cloud.key'));
            $this->_gcClient->addScope(Google_Service_Storage::CLOUD_PLATFORM);
            //$storage = new Google_Service_Storage($this->_gcClient);
        }
        return $this->_gcClient;
    }

    /**
     * @return Google_Service_Compute
     */
    public function GetGoogleComputeService(){
        if($this->_gcComputeService == null){
            $this->_gcComputeService = new Google_Service_Compute($this->_gcClient);
        }
        return $this->_gcComputeService;
    }

    /**
     * @return Google_Service_Storage
     */
    public function GetGoogleStorageService(){
        if($this->_gcStorageService == null){
            $this->_gcStorageService = new Google_Service_Storage($this->_gcClient);
        }
        return $this->_gcStorageService;
    }

    /**
     * @return \Google\Cloud\ServiceBuilder
     */
    public function GetGoogleServiceBuilder(){
        if($this->_gcServiceBuilder == null){
            try {
                $this->_gcServiceBuilder = new \Google\Cloud\ServiceBuilder();
            } catch (Exception $ex) {
                Log::info("Error loading Google Service Builder. ".$ex->getMessage());
            }
        }
        return $this->_gcServiceBuilder;
    }

    /**
     * @return \Google\Cloud\Storage\StorageClient
     */
    public function GetGoogleStorageClient(){
        $this->GetGoogleServiceBuilder();
        if($this->_gcStorageClient == null){
            try {
                $this->_gcStorageClient = new \Google\Cloud\Storage\StorageClient([
                    'projectId' => Config::get('cloud.project'),
                    'keyFile' => json_decode(file_get_contents(Config::get('cloud.key')), true),
                    'keyFilePath' => Config::get('cloud.key')
                ]);
            } catch (Exception $ex) {
                Log::info("Error loading Google StorageClient. ".$ex->getMessage());
            }
        }
        return $this->_gcStorageClient;
    }

    /**
     * @return mixed
     */
    public function GetBuckets(){
        return $this->GetGoogleStorageService()->buckets->listBuckets(Config::get('cloud.project'))->getItems();
    }

    /**
     * @return mixed[]
     */
    public function GetInstances() {
        $instancesByZone = [];
        $zones = Config::get('cloud.instances_zone');
        foreach ($zones as $zone) {
            $instancesByZone[$zone] = $this->GetInstancesByZone($zone);
        }
        return $instancesByZone;
    }

    /**
     * @param $zone
     * @return Google_Service_Compute_InstanceList
     */
    public function GetInstancesByZone($zone) {
        return $this->GetGoogleComputeService()->instances->listInstances(Config::get('cloud.project'), $zone)->getItems();
    }

    /**
     * @param $path
     * @param $bucket
     * @param bool|false $forDownload
     * @param string $downloadName
     * @return string
     */
    public function GetGCSecureUrl($path, $bucket, $forDownload = false, $downloadName = ""){
        $clientEmail = $this->_gcClient->getConfig("client_email");
        $privKey = $this->_gcClient->getConfig("signing_key");

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        if(in_array($extension, ContestFileVersion::DOWNLOADABLE)){
            $forDownload = true;
            if($downloadName == ""){
                $downloadName = pathinfo($path, PATHINFO_BASENAME);
            }
        }
        $method = 'GET';
        $content_type = ($method == 'PUT') ? 'application/x-www-form-urlencoded' : '';
        $expires = time() + Config::get('cloud.secure_url_duration');
        $encoded_id = join('/', array_map('rawurlencode', explode('/', $path)));
        $to_sign = ($method . "\n" .
            /* Content-MD5 */ "\n" .
            $content_type . "\n" .
            $expires . "\n" .
            '/' . $bucket . '/' . $encoded_id);
        //echo "<pre>".$to_sign;exit();
        $signature = '*Signature will go here*';
        if (!openssl_sign( $to_sign, $signature, $privKey, 'sha256' )) {
            error_log( 'openssl_sign failed!' );
            $signature = '<failed>';
        } else {
            $signature = urlencode( base64_encode( $signature ) );
        }
        return ('https://storage.googleapis.com/' .
            $bucket . "/" . $path .
            '?GoogleAccessId=' . $clientEmail .
            '&Expires=' . $expires . '&Signature=' . $signature .
            ($forDownload ? '&response-content-disposition=attachment;filename='. $downloadName : '')
        );
    }

    /**
     * Borra esta versión de google cloud
     */
    public function deleteThumbFromGoogleCloud(){
        /*$tpath = $this->getThumbnailsPath(true,true,true);
        if(!$tpath) return;
        MediaSystem::deleteFromGCS($this->getGroup(), $this->cloud_storage_bucket_name, $tpath);*/
    }

    /**
     * @param Group $group
     * @param bool|false $forDownload
     * @return string
     */
    public function getGCThumbSecureUrl($group, $size, $num, $forDownload = false){
        /*global $fs;
        if($this->thumbs_status != self::THUMBSDONE) return false;
        $ipath = $this->getImgPath($num, $size, true, true, true);
        return $group->getGCSecureUrl($ipath, $this->cloud_storage_bucket_name, $forDownload, $fs->getFilename($ipath));*/
    }

    /**
     * @param string $path
     * @param int $size
     * @param string $bucket
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getGCRequestUri($path, $size, $bucket){
        $mimeType = $this->getMimeType($path);

        $body = [
            'content-type' => $mimeType,
            'Origin' => URL::to('/'),//Config::get('cloud.secure_url_origin'),
            'name' => $path
        ];

        /** @var GuzzleHttp\ClientInterface $httpReq */
        $httpReq = $this->_gcClient->authorize();

        $requestUrl = self::GC_REQUESTS_ROOT . 'upload/storage/v1/b/' . $bucket . '/o?uploadType=resumable';
        $headers = [
            "origin" =>  URL::to('/'),//Config::get('cloud.secure_url_origin'),
            'Access-Control-Allow-Origin' => "*",
            "X-Upload-Content-Type" => $mimeType,
            "X-Upload-Content-Length" => $size,
            "Content-Type" => "application/json; charset=UTF-8"
        ];
        try {
            $request = $httpReq->request('POST', $requestUrl, ['json' => $body, 'headers' => $headers]);
            if ($request->getStatusCode() != 200) {
                Log::error("Error requesting GCS upload url: ".$request->getReasonPhrase());
                exit();
            }
        }catch (Exception $e){
            Log::error("Error requesting GCS upload url: ".$e->getMessage());
        }

        $requestUri = $request->getHeader('Location')[0];
        //$this->cloud_storage_resume_uri = $request->getHeader('Location')[0];
        //$this->cloud_storage_resume_uri_date = date("Y-m-d H:i:s");
        return $requestUri;
    }

    /**
     * Remove an object
     *
     * @param string $path
     * @param string $bucketName
     * @param string $contents
     * @return bool
     * @internal param string $objectPath
     */
    public function UploadFileToGCStorage($path, $bucketName, $contents)
    {
        // upload action
        try {
            $bucket = $this->GetGoogleStorageClient()->bucket($bucketName);
            $object = $bucket->upload($contents, [
                'name' => $path
            ]);
        } catch (Exception $ex) {
            //Log::info(print_r($ex, true));
            Log::info("Error uploading file to Google Cloud Storage. 2 ".$ex->getMessage());
            // log error
            return false;
        }
        // log success

        return true;
    }

    /**
     * Remove an object
     *
     * @param $path
     * @param $bucket
     * @return bool
     * @internal param string $objectPath
     */
    public function DeleteFileFromGCStorage($path, $bucket)
    {
        // validator
        try {
            if (!$this->GetGoogleStorageService()->objects->get($bucket, $path)) {
                // log error
                return false;
            }
        } catch (Exception $ex) {
            //Log::info(print_r($ex, true));
            Log::info("ERR 1");
            // leave this empty
        }

        // remove action
        try {
            $this->GetGoogleStorageService()->objects->delete($bucket, $path);
        } catch (Exception $ex) {
            //Log::info(print_r($ex, true));
            Log::info("ERR 2");
            // log error
            return false;
        }
        // log success

        return true;
    }

    /**
     * Remove the specified container
     *
     * @param string $path
     * @return boolean
     */
    public function DeleteFolderFromGCStorage($path, $bucket)
    {
        if(!$path) return false;
        $c = array();
        $c['delimiter'] = '/';
        if (!empty($path) && $path != '/') {
            $c['prefix'] = $path;
        }
        $objects = null;

        // validator
        try {
            $objects = $this->GetGoogleStorageService()->objects->listObjects($bucket, $c);
            if (empty($objects)) {
                if (!$this->GetGoogleStorageService()->objects->get($bucket, $path)) {
                    // log error
                    return false;
                }
            }
        } catch (Exception $ex) {
            // leave this empty
        }

        // remove action
        try {
            if (empty($objects)) {
                $this->GetGoogleStorageService()->objects->delete($bucket, $path);
            } else {
                /**
                 * Process files first
                 */
                /** @var Google_Service_Storage_StorageObject[] $files */
                $files = $objects->getItems();
                if (!empty($files)) {
                    foreach ($files as $file) {
                        $this->DeleteFileFromGCStorage($file->getName(), $bucket);
                    }
                }
                /**
                 * And folders later
                 */
                $folders = $objects->getPrefixes();
                if (!empty($folders)) {
                    foreach ($folders as $folder) {
                        $this->DeleteFolderFromGCStorage($folder, $bucket);
                    }
                }
            }
        } catch (Exception $ex) {
            // log error
            return false;
        }
        // log success
        return true;
    }

    /**
     * Devuelve el Mime-type del archivo enviado. No hace falta que el archivo exista, puede enviarse simplemente un nombre de archivo.
     *
     * @uses getExtension
     * @param mixed $path Path o nombre de archivo.
     * @param bool $fordownload true si queremos el Mime-type necesario para forzar una descarga.
     * @return string
     */
    public function getMimeType($path,$fordownload=false){
        global $fs;
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if(!$fordownload){
            switch($ext){
                case "htm":
                case "html":	$ctype="text/html"; break;
                case "pdf":	$ctype="application/pdf"; break;
                case "exe":	$ctype="application/octet-stream"; break;
                case "zip":	$ctype="application/zip"; break;
                case "doc":	$ctype="application/msword"; break;
                case "xls":	$ctype="application/vnd.ms-excel"; break;
                case "ppt":	$ctype="application/vnd.ms-powerpoint"; break;
                case "gif":	$ctype="image/gif"; break;
                case "png":	$ctype="image/png"; break;
                case "jpeg":
                case "jpg":	$ctype="image/jpg"; break;
                case "aac":	$ctype="audio/aac"; break;
                case "mp3":	$ctype="audio/mpeg"; break;
                case "wav":	$ctype="audio/x-wav"; break;
                case "mpeg":
                case "mpg":
                case "mpe":	$ctype="video/mpeg"; break;
                case "mp4":	$ctype="video/mp4"; break;
                case "flv":	$ctype="video/x-flv"; break;
                case "mov":	$ctype="video/quicktime"; break;
                case "avi":	$ctype="video/x-msvideo"; break;
                case "wmv":	$ctype="video/x-ms-wmv"; break;
                case "webm":$ctype="video/webm"; break;
                case "swf":	$ctype="application/x-shockwave-flash"; break;
                default:	$ctype="application/force-download"; break;
            }
        }else{
            $ctype="application/force-download";
        }
        return	$ctype;
    }
}