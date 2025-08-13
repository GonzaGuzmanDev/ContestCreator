<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
    if(Request::segment(1) !== null){
        if(Session::get('lang_selected')){
            Session::put('locale', Session::get('lang_selected'));
            if(Session::has('locale')){
                App::setLocale(Config::get('app.locale'));
            }
        }
        else{
            $contest = Contest::where('code', Request::segment(1))->first();
            if(isset($contest->default_lang) && $contest->default_lang != null){
                Session::put('locale', $contest['default_lang']);
                if(Session::has('locale')){
                    App::setLocale(Session::get('locale'));
                }
            }
        }
    }
});


/*App::after(function($request, $response)
{
    //if ($request->ajax()) {
        $encoded = gzencode($response->getContent(), 9);
        $response->setContent($encoded);
        $response->header('Content-Length', strlen($encoded));
        $response->header('Content-Encoding', 'gzip');
    //}
});*/

/*
|--------------------------------------------------------------------------
| Authentication Filters§
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/
Route::filter('serviceAuth', function(){
    if(!Auth::check()){
        return Redirect::guest('/#/login');
        /*if(Request::isJson()) {
            return Redirect::guest('/#/login');
        }else{
            return Redirect::guest('/#/login');
        }*/
    }
});
Route::filter('serviceCSRF',function(){
    if (Session::token() != Request::header('csrf_token')) {
        return Response::json([
            'message' => 'I’m a teapot !!!'
        ], 418);
    }
});
Route::filter('superAdminCheck', function(){
    if(!Auth::check()){
        if(Request::isJson()) {
            return Response::json([
                'flash' => Lang::get('login.loginRequired')
            ], 401);
        }else{
            App::abort(401, Lang::get('login.loginRequired'));
        }
    }elseif(!Auth::user()->isSuperAdmin()){
        if(Request::isJson()) {
            return Response::json([
                'flash' => Lang::get('login.adminRequired')
            ], 401);
        }else{
            App::abort(401, Lang::get('login.adminRequired'));
        }
    }
});
Route::filter('contestAdminCheck', function($route){
    if(!Auth::check()){
        if(Request::isJson()) {
            return Response::json([
                'flash' => Lang::get('login.loginRequired')
            ], 401);
        }else{
            App::abort(401, Lang::get('login.loginRequired'));
        }
    }else{
        $contestCode = $route->getParameter('contest');
        $query = Contest::where('code', '=', $contestCode);
        /** @var Contest $con */
        $con = $query->firstOrFail();
        if($con) {
            $user = Auth::user();
            if (!$user->isSuperAdmin()) {
                //$inscription = $con->getUserInscription($user);
                $owner = $con->getUserInscription($user, Inscription::OWNER);
                $colaborator = $con->getUserInscription($user, Inscription::COLABORATOR);
                //if (!$inscription || ($inscription->role != Inscription::OWNER && $inscription->role != Inscription::COLABORATOR)){
                if ($owner['role'] != Inscription::OWNER && $colaborator['role'] != Inscription::COLABORATOR){
                    if(Request::isJson()) {
                        return Response::json([
                            'flash' => Lang::get('errors.accessDenied')
                        ], 401);
                    }else{
                        App::abort(401, Lang::get('errors.accessDenied'));
                    }
                }
            }
        }else{
            if(Request::isJson()) {
                return Response::json([
                    'flash' => Lang::get('login.adminRequired')
                ], 401);
            }else{
                App::abort(401, Lang::get('login.adminRequired'));
            }
        }
    }
});
Route::filter('contestInscriptionCheck', function($route){
    if(!Auth::check()){
        if(Request::isJson()) {
            return Response::json([
                'flash' => Lang::get('login.loginRequired')
            ], 401);
        }else{
            $contestCode = $route->getParameter('contest');
            return Redirect::to(url($contestCode.'#/'));
        }
    }else{
        $contestCode = $route->getParameter('contest');
        $query = Contest::where('code', '=', $contestCode);
        /** @var Contest $con */
        $con = $query->firstOrFail();
        if($con) {
            $user = Auth::user();
            if (!$user->isSuperAdmin()) {
                $inscription = $con->getUserInscription($user);
                if (!$inscription){
                    if(Request::isJson()) {
                        return Response::json([
                            'flash' => Lang::get('errors.accessDenied')
                        ], 401);
                    }else{
                        App::abort(401, Lang::get('errors.accessDenied'));
                    }
                }
            }
        }else{
            if(Request::isJson()) {
                return Response::json([
                    'flash' => Lang::get('login.adminRequired')
                ], 401);
            }else{
                App::abort(401, Lang::get('login.adminRequired'));
            }
        }
    }
});

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::guest('login');
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

Route::filter('allowOrigin', function($route, $request, $response)
{
    $response->header('access-control-allow-origin','*');
    if(Auth::check()){
        Auth::user()->UpdateLastSeen();
    }
});