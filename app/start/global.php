<?php
use App\Services\OxoMailer;
/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/controllers',
	app_path().'/models',
	app_path().'/database/seeds',

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

Log::useFiles(storage_path().'/logs/laravel.log');

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(/**
 * @param Exception $exception
 * @param $code
 * @return mixed
 */
    function(Exception $exception, $code)
{
    if (Request::wantsJson())
    {
        // Define the response
        $response = [
            'errors' => 'Sorry, something went wrong.'
        ];

        // If the app is in debug mode
        if (Config::get('app.debug'))
        {
            // Add the exception class name, message and stack trace to response
            $response['exception'] = get_class($exception); // Reflection might be better here
            $response['message'] = $exception->getMessage();
            $response['trace'] = $exception->getTrace();
        }

        // Default response of 400
        $status = 400;

        /*// If this exception is an instance of HttpException
        if ($exception->isHttpException())
        {
            // Grab the HTTP status code from the Exception
            $status = $exception->getStatusCode();
        }*/

        // Return a JSON response with the response array and status code
        return Response::json($response, $status);
    }
	Log::error($exception);

    $users = User::where('super', 1)->select(['email','notifications'])->get()->toArray();
    $errorSended = ErrorLog::orderBy('created_at', 'desc')->limit(1)->select("created_at")->get()->toArray();

    if(!isset($errorSended[0]['created_at']) || !\Carbon\Carbon::parse($errorSended[0]['created_at'])->addHour()->gt(\Carbon\Carbon::now())){
        foreach($users as $user){
            $notif = $user['notifications'];
            if(isset($notif['errorNotifications']) && $notif['errorNotifications'] === true){
                OxoMailer::sendMail([
                    'email_to' => $user['email'],
                    'subject' => Lang::get('contest.error'),
                    'body' => $exception
                ]);
            }
        }
        $errorLog = new ErrorLog();
        $errorLog->error = $exception;
        $errorLog->save();
        return Lang::get('contest.errorNotification');
    };
});
App::fatal(/**
 * @param Exception $exception
 * @param $code
 * @return mixed
 */
    function(Exception $exception)
{
    if (Request::wantsJson())
    {
        // Define the response
        $response = [
            'errors' => 'Sorry, something went wrong.'
        ];

        // If the app is in debug mode
        if (Config::get('app.debug'))
        {
            // Add the exception class name, message and stack trace to response
            $response['exception'] = get_class($exception); // Reflection might be better here
            $response['message'] = $exception->getMessage();
            $response['trace'] = $exception->getTrace();
        }

        // Default response of 400
        $status = 400;

        /*// If this exception is an instance of HttpException
        if ($exception->isHttpException())
        {
            // Grab the HTTP status code from the Exception
            $status = $exception->getStatusCode();
        }*/

        // Return a JSON response with the response array and status code
        return Response::json($response, $status);
    }
	Log::error($exception);

    $users = User::where('super', 1)->select(['email','notifications'])->get()->toArray();
    $errorSended = ErrorLog::orderBy('created_at', 'desc')->limit(1)->select("created_at")->get()->toArray();

    if(!isset($errorSended[0]['created_at']) || !\Carbon\Carbon::parse($errorSended[0]['created_at'])->addHour()->gt(\Carbon\Carbon::now())){
        foreach($users as $user){
            $notif = $user['notifications'];
            if(isset($notif['errorNotifications']) && $notif['errorNotifications'] === true){
                OxoMailer::sendMail([
                    'email_to' => $user['email'],
                    'subject' => Lang::get('contest.error'),
                    'body' => $exception
                ]);
            }
        }
        $errorLog = new ErrorLog();
        $errorLog->error = $exception;
        $errorLog->save();
        return Lang::get('contest.errorNotification');
    };
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function()
{
	return Response::view('errors.maintenance', [], 503);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path().'/filters.php';


/*
|--------------------------------------------------------------------------
| Locale Session Persistance
|--------------------------------------------------------------------------
*/
App::setLocale(Session::get('my.locale', Config::get('app.locale')));

/*
|--------------------------------------------------------------------------
| Error handlers
|--------------------------------------------------------------------------
*/
App::missing(function(Exception $exception)
{
	//print_r($exception);exit();
	return Response::view('errors.404', array('msg'=>Lang::get("general.pageNotFound")), 404);
});