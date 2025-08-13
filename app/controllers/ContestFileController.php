<?php

use Illuminate\Filesystem\Filesystem;

class ContestFileController extends \BaseController {

    /**
     * Display a listing of the resource.
     * @return string
     * @internal param int $page
     * @internal param string $query
     */
    public function index()
	{
        $pageItems = 20;
        $page = (int) Input::get('page');
        $page = max($page, 1);
        $query = Input::get('query');
        if ($page > 0)
        {
            Paginator::setCurrentPage($page);
        }
        $orderBy = Input::get('orderBy');
        $orderDir = Input::get('orderDir');
//        protected $fillable = ['name', 'label', 'type', 'extension'];
        switch($orderBy)
        {
            case "name":
            case "label":
            case "type":
            case "extension":
            case "command":
                break;
            default:
                $orderBy = "name";
                $orderDir = 'asc';
        }
        if($orderDir == 'false')
        {
            $orderDir = 'desc';
        }
        else
        {
            $orderDir = 'asc';
        }
        $data = ContestFile::where('name', 'LIKE', '%'.$query.'%')->orderBy($orderBy, $orderDir)->paginate($pageItems, ['id', 'name', 'contest_id', 'user_id', 'status']);
        $pagination = [
            'last' => $data->getLastPage(),
            'page' => $data->getCurrentPage(),
            'perPage' => $data->getPerPage(),
            'total' => $data->getTotal(),
            'orderBy' => $orderBy,
            'orderDir' => $orderDir == 'asc',
            'query' => $query,
        ];
        return Response::json(['status' => 200, 'data' => $data->getItems(), 'pagination' => $pagination]);
	}


	/**
	 * Store a newly created resource in storage.
	 * @return Response
	 */
	public function store()
	{
        $input = Input::only('name','contest_id','user_id','status');
        $rules = array(
            'name' => 'required|min:2|max:255',
            'contest_id' => 'required|integer',
            'user_id' => 'required|integer',
            'status' => 'required|integer|between:0,9',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails())
        {
            $messages = $validator->messages();
            return Response::json(array('errors'=>$messages));
        }
        else
        {
            // transformar el comando en json
            $user = ContestFile::create($input);
            return Response::json($user);
        }
	}


	/**
	 * Display the specified resource.
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        $contest = ContestFile::find($id);
        return Response::json($contest);
	}


	/**
	 * Update the specified resource in storage.
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        $input = Input::only('name','contest_id','user_id','status');
        $rules = array(
            'name' => 'required|min:2|max:255',
            'contest_id' => 'required|integer',
            'user_id' => 'required|integer',
            'status' => 'required|integer|between:0,9',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails())
        {
            $messages = $validator->messages();
            return Response::json(array('errors'=>$messages));
        }
        else
        {
            $contest = ContestFile::find($id)->update($input);
            return Response::json($contest);
        }
	}


	/**
	 * Remove the specified resource from storage.
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        $contestFile = ContestFile::destroy($id);
        return Response::json($contestFile);
	}


    /**
     * Devuelve el thumbnail
     * @param $contest string
     * @param $code string
     */
    public function getFileThumb($contest, $code) {
        /**
         * @var $con Contest
         * @var $contestFile ContestFile
         */
        $query = Contest::where('code', '=', $contest);
        $con = $query->firstOrFail();
        if (!$con) App::abort(404, Lang::get('contest.notfound'));
        $query = ContestFile::where('code', '=', $code);
        $contestFile = $query->firstOrFail();
        if (!$contestFile) App::abort(404, Lang::get('contest.notfound'));
        $path = $contestFile->getThumbsPath();
        switch ($contestFile->type) {
            case Format::VIDEO:
                $thumbPath = $path."th-0002.jpg";
                break;
            case Format::AUDIO:
            case Format::IMAGE:
                $thumbPath = $path."th.jpg";
                break;
            default:
                $thumbPath = "";
        }
        $fs = new Filesystem();
        if (!$fs->exists($thumbPath)) {
            $thumbPath = storage_path('default/media-'.$contestFile->type.'.png');
        }
        if (strpos($_SERVER["SERVER_SOFTWARE"], 'nginx') !== false) {
            $path = '/storage/'.$thumbPath;
            $header = 'X-Accel-Redirect: '.$path;
        } else {
            $header = 'X-Sendfile: '.$thumbPath;
        }
        header('Content-type: image/jpeg');
        header($header);
        exit();
    }


    /**
     * Devuelve el thumbnail
     * @param $contest string
     * @param $code string
     */
    public function getFileVersion($contest, $code, $versionId, $extension) {
        /**
         * @var $con Contest
         * @var $contestFile ContestFile
         */
        $query = Contest::where('code', '=', $contest);
        $con = $query->firstOrFail();
        if (!$con) App::abort(404, Lang::get('contest.notfound'));
        $query = ContestFile::where('code', '=', $code);
        $contestFile = $query->firstOrFail();
        if (!$contestFile) App::abort(404, Lang::get('contest.notfound'));

        /** @var $contestFileVersion ContestFileVersion*/
        $contestFileVersion = ContestFileVersion::where('id', $versionId)
            ->where('contest_file_id', $contestFile->id)
            //->where('source', 0)->firstOrFail();
            ->firstOrFail();
        if (!$contestFileVersion) App::abort(404, Lang::get('contest.notfound'));

        $path = $contestFileVersion->getPath();
        $mime = Format::getMimeType($contestFileVersion->extension);
        $fs = new Filesystem();
        if (!$fs->exists($path)) {
            if (!$contestFile) App::abort(404, Lang::get('contest.notfound'));
        }
        header('Content-type: ' . $mime);
        if (isset($_SERVER['HTTP_RANGE']) && preg_match('/\Abytes=[0-9]+-\z/', $_SERVER['HTTP_RANGE'])) {

            // Set Content-Range header which should be like "bytes 2375680-12103815/12103816"
            // That is start byte | dash | last byte | slash | total byte size
            // See RFC2616 section 14.16 http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.16
            $length = $contestFileVersion->size;
            $start = substr($_SERVER['HTTP_RANGE'], 6, -1);
            $end = $length - 1;
            header("Content-Range: bytes $start-$end/$length");

            // X-Sendfile2 does not set the 206 status code, we have to set it manually
            // See RFC2616 section 10.2.7 http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.2.7
            header("HTTP/1.1 206 Partial content");

            // The X-Sendfile2 with resume support should be like "/path/to/file 2375680-"
            header("X-Sendfile2: $path $start-");
        }else {
            if (strpos($_SERVER["SERVER_SOFTWARE"], 'nginx') !== false) {
                $header = 'X-Accel-Redirect: ' . $path;
            } else {
                $header = 'X-Sendfile: ' . $path;
            }
            header($header);
        }
        exit();
    }
}