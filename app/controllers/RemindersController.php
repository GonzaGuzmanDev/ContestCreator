<?php

use App\Services\OxoMailer;

class RemindersController extends Controller {

	/**
	 * Display the password reminder view.
	 *
	 * @return Response
	 */
	/*public function getRemind()
	{
		return View::make('password.remind');
	}*/

	/**
	 * Handle a POST request to remind a user of their password.
	 *
	 * @return \Illuminate\Http\JsonResponse
     */
	public function postRemind()
	{
	    $email = Input::only('email');

        $user = User::where('email', '=', $email['email'])->first();
        if(!$user){
            return Response::json(['flash' => Lang::get(Password::INVALID_USER)], 400);
        }
        $token = str_random(60);
        $user->setRememberToken($token);
        $user->save();

        $body = View::make(Config::get('auth.reminder.email'), ['token'=>$token])->render();
        $response = OxoMailer::sendMail([
            'email_to' => $email['email'],
            'subject' => Lang::get('reminders.subject'),
            'body' => $body
        ]);

        return Response::json(['flash' => Lang::get(Password::REMINDER_SENT)], 202);
		/*$response = Password::remind(Input::only('email'), function($message)
		{
			$message->subject(Lang::get('reminders.subject'));
		});
		switch ($response)
		{
			case Password::INVALID_USER:
				return Response::json(['flash' => Lang::get($response)], 400);
			//return Redirect::back()->with('error', Lang::get($response));

			case Password::REMINDER_SENT:
				return Response::json(['flash' => Lang::get($response)], 202);
			//return Redirect::back()->with('status', Lang::get($response));
		}*/
	}

	/**
	 * Display the password reset view for the given token.
	 *
	 * @param  string  $token
	 * @return Response
	 */
	public function getReset($token = null)
	{
		if (is_null($token)) App::abort(404);

		return View::make('login.resetpassword')->with('token', $token);
	}

	/**
	 * Handle a POST request to reset a user's password.
	 *
	 * @return \Illuminate\Http\JsonResponse
     */
	public function postReset()
	{
		$input = Input::only(
			'email', 'password', 'password_confirmation', 'token'
		);

		$rules = array(
			'email' => 'required|email',
			'password' => 'required|same:password_confirmation|alpha_num|min:6'
		);
		$validator = Validator::make($input, $rules);

		if ($validator->fails())
		{
			$messages = $validator->messages();
			return Response::json(array('errors'=>$messages));
		}
		else
		{
            $user = User::where('email', '=', $input['email'])->where('remember_token', '=', $input['token'])->first();
            if(!$user){
                return Response::json(array('error'=>Lang::get(Password::INVALID_USER)));
                //return Response::json(['flash' => Lang::get(Password::INVALID_USER)], 400);
            }
            $user->remember_token = null;
            $user->password = Hash::make($input['password']);
            $user->save();

            $body = View::make('emails.auth.reminder-done')->render();
            $response = OxoMailer::sendMail([
                'email_to' => $user->email,
                'subject' => Lang::get('reminders.subject-done'),
                'body' => $body
            ]);
            return Response::json(array('flash'=>Lang::get('reminders.success', array('url'=>URL::to('/')))));
			/*$response = Password::reset($input, function($user, $password)
			{
				$user->password = Hash::make($password);
				$user->save();
				Mail::send('emails.auth.reminder-done', array('key' => 'value'), function($message) use ($user)
				{
					$message->to($user->email, $user->name)->subject(Lang::get('reminders.subject-done'));
				});

			});
			switch ($response)
			{
				case Password::INVALID_PASSWORD:
				case Password::INVALID_TOKEN:
				case Password::INVALID_USER:
					return Response::json(array('error'=>Lang::get($response)));
				case Password::PASSWORD_RESET:
					return Response::json(array('flash'=>Lang::get('reminders.success', array('url'=>URL::to('/')))));
			}*/
		}
	}

}
