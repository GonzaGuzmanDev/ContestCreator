<?php

use App\Services\Image;
use Illuminate\Filesystem\Filesystem;

class UserController extends \BaseController {

    /**
     * Display a listing of the resource.
     * @return string
     * @internal param int $page
     * @internal param string $query
     */
    public function index()
	{
        //return User::all()->toJson();
        $pageItems = 20;
        $page = (int) Input::get('page');
        $page = max($page,1);
        $query = Input::get('query');
        $ppage = $page;
        if ($page > 0) {
            Paginator::setCurrentPage($page);
        }
        $orderBy = Input::get('orderBy');
        $orderDir = Input::get('orderDir');
        switch($orderBy){
            case "email":
            case "first_name":
            case "last_name":
            case "created_at":
                break;
            default:
                $orderBy = "email";
                $orderDir = 'asc';
        }
        if($orderDir == 'false'){
            $orderDir = 'desc';
        }else{
            $orderDir = 'asc';
        }
        $data = User::where('email', 'LIKE', '%'.$query.'%')->orWhere('first_name', 'LIKE', '%'.$query.'%')->orWhere('last_name', 'LIKE', '%'.$query.'%')->orderBy($orderBy, $orderDir)->paginate($pageItems, ['id', 'first_name', 'last_name', 'email', 'created_at', 'super']);
        $pagination = [
            'last' => $data->getLastPage(),
            'page' => $data->getCurrentPage(),
            'perPage' => $data->getPerPage(),
            'total' => $data->getTotal(),
            'orderBy'=>$orderBy,
            'orderDir'=>$orderDir == 'asc',
            'query'=>$query,
            //'items' => $data->getItems()
        ];
        /** @var User[] $list */
        $list = $data->getItems();
        foreach($list as $item){
            $item['picture'] = $item->getProfilePictureURL('thumb');
        }
        return Response::json(['status' => 200, 'data' => $list, 'pagination' => $pagination]);
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $input = Input::only('email', 'super', 'first_name', 'last_name', 'new_password', 'repeat_password');

        $rules = array(
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required|min:2',
            'last_name' => 'required|min:2',
            'new_password' => 'required|alpha_num|min:6|same:repeat_password',
        );
        $validator = Validator::make($input, $rules);

        if ($validator->fails())
        {
            $messages = $validator->messages();
            return Response::json(array('errors'=>$messages));
        }
        else
        {
            $input['password'] = Hash::make($input['new_password']);
            $user = User::create($input);
            return Response::json($user);
        }
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        $user = User::find($id);

        return Response::json($user);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        $input = Input::only('email', 'super', 'active', 'first_name', 'last_name', 'new_password', 'repeat_password');

        //return Response::json(array("input" => $input));

        $rules = array(
            'email' => 'required|email|unique:users,email,'.$id,
            'first_name' => 'required|min:2',
            'last_name' => 'required|min:2',
            'super' => '',
            'new_password' => 'same:repeat_password|alpha_num|min:6',
            'active' => ''
        );
        $validator = Validator::make($input, $rules);

        if ($validator->fails())
        {
            $messages = $validator->messages();
            return Response::json(array('errors'=>$messages));
        }
        else
        {
            if(isset($input['new_password']) && $input['new_password'] != '') $input['password'] = Hash::make($input['new_password']);
            $user = User::find($id)->update($input);
            return Response::json($user);
        }
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        $input = Input::only('captcha');
        $rules = array(
            'captcha' => 'required|captcha'
        );
        $validator = Validator::make($input, $rules);

        if ($validator->fails())
        {
            $messages = $validator->messages();
            return Response::json(array('errors'=>$messages, 'captchaUrl'=>Captcha::img()));
        }
        else {
            User::destroy($id);
            return Response::json(['status' => 200, 'flash' => Lang::get('user.userDeleted')]);
        }
	}

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function postAccountData()
    {
        $user = Auth::user();

        $input = Input::only('email', 'first_name', 'last_name');

        $rules = array(
            'email' => 'required|email|unique:users,email,'.$user->id,
            'first_name' => 'required|min:2',
            'last_name' => 'required|min:2'
        );
        $validator = Validator::make($input, $rules);

        if ($validator->fails())
        {
            $messages = $validator->messages();
            return Response::json(array('errors'=>$messages));
        }
        else
        {
            $user->update($input);
            return Response::json(array('flash'=>Lang::get('account.updated-data'), 'user'=>$user));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postAccountSecurity()
    {
        $user = Auth::user();
        Validator::extend('passcheck', function($attribute, $value, $parameters) {
            return Hash::check($value, Auth::user()->getAuthPassword());
        });

        $input = Input::only('current_password', 'new_password', 'repeat_password');

        $rules = array(
            'current_password' => 'passcheck',
            'new_password' => 'same:repeat_password|alpha_num|min:6',
        );
        $messages = array(
            'passcheck' => Lang::get('account.oldpasserror'),
        );
        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails())
        {
            $messages = $validator->messages();
            return Response::json(array('errors'=>$messages));
        }
        else
        {
            $input['password'] = Hash::make($input['new_password']);
            $user->update($input);
            return Response::json(array('flash'=>Lang::get('account.updated-pass')));
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function postDeleteAccount()
    {
        if(Input::get('sure')) {
            $user = Auth::user();
            $user->delete();
            return Response::json(array('flash' => Lang::get('account.deleteAccount.deleted')));
        }else{
            return Response::json(array('error' => Lang::get('general.error')));
        }
    }


    /**
     * Update the user language
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postLanguage()
    {
        $lang = Input::get('lang');
        if(in_array($lang, Config::get('app.languages'))) {
            Session::put('my.locale', $lang);
            App::setLocale($lang);
            return Response::json(array('flash' => Lang::get('account.changeLang.success')));
        }else{
            return Response::json(array('error' => Lang::get('account.changeLang.notfound')));
        }
    }


    /**
     * Update the user notifications setup
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postNotifications()
    {
        $notifications = [];
        $input = Input::all();
        foreach ($input as $inputCode => $inputValue){
            if(array_key_exists($inputCode, User::DefaultNotifications)){
                $notifications[$inputCode] = $inputValue == true;
            }
        }
        $user = Auth::user();
        $user->notifications = json_encode(array_merge(User::DefaultNotifications, $notifications));
        if($user->super == 1){
            $user->notifications = json_encode(['errorNotifications' => $notifications["errorNotifications"]]);
        };
        $user->save();
        return Response::json(array('flash' => Lang::get('account.notifications.saveSuccess')));
    }


    /**
     * Get available languages
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLanguage()
    {
        $data = [];
        foreach(Config::get('app.languages') as $langKey){
            $data[$langKey] = Lang::get('locale.'.$langKey);
        }
        return Response::json(['current'=>App::getLocale(), 'list'=>$data]);
    }


    /**
     * Get user social networks data
     *
     * @return Response
     */
    public function getSocial()
    {
        $user = Auth::user();
        $data = [];
        if ( Config::get('oauth-4-laravel.consumers') != null ) {
            $services = Config::get('oauth-4-laravel.consumers');
        } else {
            $services = Config::get('oauth-4-laravel::consumers');
        }
        $user_services = UserService::where('user_id','=', $user->id)->take(10)->get(array('service'));
        foreach($services as $k => $v){
            $exists = false;
            $user_services->each(function($v) use ($k, &$exists)
            {
                if(strtolower($v->service) == strtolower($k)) $exists = true;
            });
            $data[$k] = $exists;
        }
        return Response::json($data);
    }


    /**
     * Envía un e-mail de verificación al usuario
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVerifyEmail()
    {
        $user = Auth::user();
        $user->verifyEmail();
        return Response::json(array('flash' => Lang::get('account.verify.success')));
    }

    /**
     * Verifica el e-mail del usuario
     *
     * @param null $token
     * @return Response
     */
    public function getCompleteVerifyEmail($token=null) {
        /* @var $user User */
        if (is_null($token)) App::abort(404);
        $user = User::where('verify_token', '=', $token)->first();
        if (!!$user) {
            Auth::login($user);
            $user->verified = true;
            $user->verify_token = null;
            $user->save();
            return View::make('account.verify', array('flash'=>Lang::get('account.verify.complete')));
        } else {
            return View::make('account.verify', array('error'=>Lang::get('account.verify.tokenError')));
        }
    }

    public function getPicture($email, $version='') {
        if($version != '') $version = '.'.$version;
        $fs = new Filesystem();
        /* @var $user User */
        $validator = Validator::make(
            array(
                'email' => $email
            ),
            array(
                'email' => 'required|email'
            )
        );
        $path = 'profile_pictures/default.jpg';
        if (!$validator->fails())
        {
            $user = User::where('email','=',$email)->first();
            if($user){
                $path = 'profile_pictures/'.$user->id.$version.'.jpg';
                if(!$fs->exists(storage_path($path)) && $version != '') {
                    $path = 'profile_pictures/'.$user->id.'.jpg';
                }
            }
        }
        $localPath = storage_path($path);
        if(!$fs->exists($localPath)){
            $path = 'profile_pictures/default.jpg';
            $localPath = storage_path($path);
        }

        if (strpos($_SERVER["SERVER_SOFTWARE"], 'nginx')!==false){
            $path = '/storage/'.$path;
            $header = 'X-Accel-Redirect: '.$path;
        } else {
            $header = 'X-Sendfile: '.$localPath;
        }
        header('Content-type: image/jpeg');
        header($header);
        exit();
    }

    public function postPicture(){
        $fs = new Filesystem();
        $chunksDir = Config::get('upload.chunkspath');
        if(!$fs->exists($chunksDir)){
            $fs->makeDirectory($chunksDir);
        }

        if (1 == mt_rand(1, 100)) {
            \Flow\Uploader::pruneChunks($chunksDir);
        }

        $config = new \Flow\Config();

        $config->setTempDir($chunksDir);
        $file = new \Flow\File($config);
        $response = Response::make('', 200);

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!$file->checkChunk()) {
                return Response::make('', 404);
            }
        } else {
            if ($file->validateChunk()) {
                $file->saveChunk();
            } else {
                // error, invalid chunk upload request, retry
                return Response::make('', 400);
            }
        }
        $tmpfname = tempnam("/tmp", "FOO");
        if ($file->validateFile() && $file->save($tmpfname)) {
            //$fileName = Input::get('flowFilename');
            //$ret = JitImage::source($savePath)->toJpeg()->get();
            $savePath = storage_path('profile_pictures/'.Auth::id().'.jpg');
            Image::convert()->recipeAndSave($tmpfname, $savePath, 'profile');
            Image::convert()->recipeAndSave($tmpfname, $savePath, 'profile.thumb');
            Image::convert()->recipeAndSave($tmpfname, $savePath, 'profile.preview');
            $response = Response::make('Upload successful', 200);
        } else {
            //$response = Response::make('Give me more!', 200);
            // This is not a final chunk, continue to upload
        }
        return $response;
    }

    /**
     * Devuelve un listado de las inscriciones del usuario.
     * @param $userId
     * @return Response
     */
    public function getInscriptionsData($userId) {
        /** @var $user User */
        $user = User::find($userId);
        if (!$user) return Response::json(['error'=> 'User not found']);
        $inscriptionsData = array();
        $inscriptions = $user->inscriptions;
        foreach($inscriptions as $inscription) {
            $inscriptionData['id'] = $inscription->id;
            $inscriptionData['contest_id'] = $inscription->contest_id;
            $inscriptionData['contest_name'] = $inscription->contest->name;
            $inscriptionData['type'] = $inscription->type;
            $allRoles = $inscription->getAllRoles();
            $inscriptionData['role'] = $allRoles[$inscription->role];
            $inscriptionsData[] = $inscriptionData;
        }
        return Response::json(['inscriptions' => $inscriptionsData]);
    }
}