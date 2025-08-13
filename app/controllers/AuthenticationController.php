<?php

use \OAuth\OAuth2\Service\Facebook;

class AuthenticationController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
        Auth::logout();
        Session::flush();
        return Response::json(['flash' => Lang::get('login.finishSession')], 200);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

    public function logout(){
	    Auth::logout();
        Session::flush();
        Cookie::queue(Cookie::forget('laravel_session'));
        Cookie::forget('laravel_session');
        //return Redirect::guest('/#/login');
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $credentials = array(
            'email' =>  Input::get('email'),
            'password' =>  Input::get('password'));
        $remember = (Input::has('remember')) ? true : false;
        if(Auth::attempt($credentials, $remember))
        {
            if(!Config::get('registration.allowUnverified')){
                $credentials['verified'] = 1;
                if(Auth::attempt($credentials, $remember))
                {
                    return Response::json(['user' => Auth::user()->getArrayData()],202);
                }else{
                    Auth::logout();
                    return Response::json(['flash' => Lang::get('login.emailNotVerified')], 401);
                }
            }else{
                return Response::json(['user' => Auth::user()->getArrayData()],202);
            }
        }else{
            return Response::json(['flash' => Lang::get('login.authFailed')], 401);
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
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function registerUser()
    {
        $input = Input::only('email', 'first_name', 'last_name', 'new_password', 'repeat_password', 'accept', 'captcha', 'active');
        $rules = array(
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required|min:2',
            'last_name' => 'required|min:2',
            'new_password' => 'required|same:repeat_password|alpha_num|min:6',
            'accept' => 'required',
            'captcha' => 'required|captcha'
        );
        $messages = array(
            'accept.required' => Lang::get('login.acceptReq')
        );
        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails())
        {
            $messages = $validator->messages();
            return Response::json(array('errors'=>$messages, 'captchaUrl'=>Captcha::img()));
        }
        else
        {
            $input['password'] = Hash::make($input['new_password']);
            $input['active'] = 1;
            $user = User::create($input);
            //if(!Config::get('registration.allowUnverified')){
                $user->verifyEmail();
            //}
            $response = array('flash' => Lang::get('login.registerOk', array('email' => $user->email)));
            if(Config::get('registration.autologin') && Config::get('registration.allowUnverified')) {
                Auth::login($user);
                $response['user'] = Auth::user()->getArrayData();
            }
            return Response::json($response);
        }
    }

    /**
     * Verifica el estado de autenticación del usuario
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAuth($superCheck = false){
        if(Auth::check()){
            if(!$superCheck || Auth::user()->isSuperAdmin()) {
            //if($superCheck == 'super' || Auth::user()->isSuperAdmin()) {
                return Response::json(['auth' => true, 'user' => Auth::user()->getArrayData()], 202);
            }else{
                return Response::json(['flash' => Lang::get('login.authFailedSuperAdmin'), 'supercheck' => $superCheck], 401);
            }
        }
        return Response::json(['flash' => Lang::get('login.authFailed')], 401);
    }



    public function loginWithFacebook()
    {
        // get data from input
        $code = Input::get( 'code' );
        // get fb service
        $fb = OAuth::consumer( 'Facebook' ,'');
        // check if code is valid
        // if code is provided get user data and sign in
        if ( !empty( $code ) ) {
            // This was a callback request from facebook, get the token
            $token = $fb->requestAccessToken( $code );
            // Send a request with it
            $result = json_decode( $fb->request( '/me' ), true );

            $userService = UserService::firstOrNew(array('service'=>UserService::FACEBOOK, 'service_id'=>$result['id']));

            $hash = '';
            if($userService->exists){
                //El servicio para facebook ya se encuentra registrado, buscamos el user
                $user = User::findOrFail($userService->user_id);
                //Logueamos al usuario
                Auth::login($user);
            }else{
                if(isset($result['email'])){
                    //Chequeamos si el e-mail ya está registrado. De ser así, no creamos el usuario
                    $user = User::firstOrNew(array('email' => $result['email']));
                    if($user->exists){
                        //El e-mail ya está registrado, no podemos asignar ese e-mail a la cuenta nueva
                        /*
                         * Dos opciones, u obviamos el e-mail y lo guardamos como si no hubiesen mandado el permiso desde facebook
                         * o informamos que ya existe y que ingrese sus datos
                         */
                        unset($result['email']);
                        /*
                        echo "<pre>";
                        echo "La dirección de e-mail ".$result['email']." ya está registrada. Ingrese con sus datos o reestablezca la contraseña para poder ingresar";
                        //print_r($user);
                        exit();*/
                    }
                }
                //No hay un usuario vinculado a este servicio
                if(Auth::check()){
                    //Relacionamos al usuario logueado con el este servicio
                    $user = Auth::user();
                    $hash = '#/account/config';
                }else {
                    //No hay un usuario logueado, creamos un usuario nuevo
                    $user = new User();
                    if (isset($result['email'])) {
                        $user->email = $result['email'];
                    }
                    $user->first_name = $result['first_name'];
                    $user->last_name = $result['last_name'];
                    $user->active = 1;
                    $user->save();
                }

                //lo asignamos al servicio por el id
                $userService->user_id = $user->id;
                $userService->service_id = $result['id'];
                $userService->service = UserService::FACEBOOK;
                $userService->save();
                //Logueamos al usuario nuevo
                Auth::login($user);
            }
            return Redirect::to(URL::previous() . $hash);
        }
        // if not ask for permission first
        else {
            // get fb authorization
            $url = $fb->getAuthorizationUri();
            // return to facebook login url
            return Redirect::to( (string)$url );
        }
    }

    public function loginWithTwitter()
    {
        // get data from input
        $token = Input::get( 'oauth_token' );
        $verify = Input::get( 'oauth_verifier' );
        // get twitter service
        $tw = OAuth::consumer( 'Twitter' );
        // check if code is valid
        // if code is provided get user data and sign in
        if ( !empty( $token ) && !empty( $verify ) ) {
            // This was a callback request from twitter, get the token
            $token = $tw->requestAccessToken( $token, $verify );
            // Send a request with it
            $result = json_decode( $tw->request( 'account/verify_credentials.json' ), true );

            $userService = UserService::firstOrNew(array('service'=>UserService::TWITTER, 'service_id'=>$result['id']));

            $hash = '';
            if($userService->exists){
                //El servicio para facebook ya se encuentra registrado, buscamos el user
                $user = User::findOrFail($userService->user_id);
                //Logueamos al usuario
                Auth::login($user);
            }else{
                //Twitter no devuelve nunca e-mail así que ni chequeamos si existe
                //No hay un usuario vinculado a este servicio
                if(Auth::check()){
                    //Relacionamos al usuario logueado con el este servicio
                    $user = Auth::user();
                    $hash = '#/account/config';
                }else{
                    //No hay un usuario logueado, creamos un usuario nuevo
                    $user = new User();
                    $name_parts = explode(" ", $result['name']);
                    $last_name = array_pop($name_parts);
                    $first_name = implode(" ", $name_parts);
                    $user->first_name = $first_name;
                    $user->last_name = $last_name;
                    $user->active = 1;
                    $user->save();
                }

                //lo asignamos al servicio por el id
                $userService->user_id = $user->id;
                $userService->service_id = $result['id'];
                $userService->service = UserService::TWITTER;
                $userService->save();
                //Logueamos al usuario nuevo
                Auth::login($user);
            }
            return Redirect::to(URL::previous() . $hash);
        }
        // if not ask for permission first
        else {
            // get request token
            $reqToken = $tw->requestRequestToken();
            // get Authorization Uri sending the request token
            $url = $tw->getAuthorizationUri(array('oauth_token' => $reqToken->getRequestToken()));
            // return to twitter login url
            return Redirect::to( (string)$url );
        }
    }

    public function loginWithGoogle()
    {
        // get data from input
        $code = Input::get( 'code' );
        // get google service
        $googleService = OAuth::consumer( 'Google' );
        // check if code is valid
        // if code is provided get user data and sign in
        if ( !empty( $code ) ) {
            // This was a callback request from google, get the token
            $token = $googleService->requestAccessToken( $code );
            // Send a request with it
            $result = json_decode( $googleService->request( 'https://www.googleapis.com/oauth2/v1/userinfo' ), true );

            $userService = UserService::firstOrNew(array('service'=>UserService::GOOGLE, 'service_id'=>$result['id']));

            $hash = '';
            if($userService->exists){
                //El servicio para facebook ya se encuentra registrado, buscamos el user
                $user = User::findOrFail($userService->user_id);
                //Logueamos al usuario
                Auth::login($user);
            }else{
                if(isset($result['email'])){
                    //Chequeamos si el e-mail ya está registrado. De ser así, no creamos el usuario
                    $user = User::firstOrNew(array('email' => $result['email']));
                    if($user->exists){
                        //El e-mail ya está registrado, no podemos asignar ese e-mail a la cuenta nueva
                        /*
                         * Dos opciones, u obviamos el e-mail y lo guardamos como si no hubiesen mandado el permiso desde facebook
                         * o informamos que ya existe y que ingrese sus datos
                         */
                        unset($result['email']);
                        /*
                        echo "<pre>";
                        echo "La dirección de e-mail ".$result['email']." ya está registrada. Ingrese con sus datos o reestablezca la contraseña para poder ingresar";
                        //print_r($user);
                        exit();*/
                    }
                }
                //No hay un usuario vinculado a este servicio
                if(Auth::check()){
                    //Relacionamos al usuario logueado con el este servicio
                    $user = Auth::user();
                    $hash = '#/account/config';
                }else {
                    //No hay un usuario logueado, creamos un usuario nuevo
                    $user = new User();
                    if (isset($result['email'])) {
                        $user->email = $result['email'];
                    }
                    $user->first_name = $result['given_name'];
                    $user->last_name = $result['family_name'];
                    $user->active = 1;
                    $user->save();
                }
                //lo asignamos al servicio por el id
                $userService->user_id = $user->id;
                $userService->service_id = $result['id'];
                $userService->service = UserService::GOOGLE;
                $userService->save();
                //Logueamos al usuario nuevo
                Auth::login($user);
            }
            //return Redirect::to( "/" );
            return Redirect::to(URL::previous() . $hash);
        }
        // if not ask for permission first
        else {
            // get googleService authorization
            $url = $googleService->getAuthorizationUri();
            // return to google login url
            return Redirect::to((string)$url);
        }
    }

    public function disconnectWithFacebook(){
        $user = Auth::user();
        $userService = UserService::firstOrNew(array('service'=>UserService::FACEBOOK, 'user_id'=>$user->id));
        if($userService->exists){
            $userService->delete();
            Session::set('message', Lang::get('account.disconnected-facebook'));
            Session::set('messageType', 'info');
        }
        return Redirect::to(URL::previous() . '#/account/config');
    }
    public function disconnectWithTwitter(){
        $user = Auth::user();
        $userService = UserService::firstOrNew(array('service'=>UserService::TWITTER, 'user_id'=>$user->id));
        if($userService->exists){
            $userService->delete();
            Session::set('message', Lang::get('account.disconnected-twitter'));
            Session::set('messageType', 'info');
        }
        return Redirect::to(URL::previous() . '#/account/config');
    }
    public function disconnectWithGoogle(){
        $user = Auth::user();
        $userService = UserService::firstOrNew(array('service'=>UserService::GOOGLE, 'user_id'=>$user->id));
        if($userService->exists){
            $userService->delete();
            Session::set('message', Lang::get('account.disconnected-google+'));
            Session::set('messageType', 'info');
        }
        return Redirect::to(URL::previous() . '#/account/config');
    }

    /*public function userActiveInContest(){
        $user = Input::get('user');
        $contest = Input::get('contest');
        $conId = Contest::where('code', '=', $contest)->first(['id']);
        $validator = Inscription::where('user_id', '=', $user)->where('contest_id', '=', $conId['id'])->first(['active']);
        if($validator == null || $validator['active'] == 1) return Response::json(array('value' => true));
        if($validator['active'] == 0) return Response::json(array('value' => false,'flash' => Lang::get('login.blockUser')));
        return Response::json(array('value' => false,'flash' => Lang::get('login.TryLater')));
    }*/
}