<?php

use App\Services\OxoMailer;
use Carbon\Carbon;

class SiteController extends BaseController {

    public function showIndex()
    {
        $allContests = [];
        $inscriptions = [];
        $superAdmin = false;
        if(Auth::check()){
            $user = Auth::user();
            if($user->isSuperAdmin()){
                $superAdmin = true;
                $allContests = Contest::basic()->opened()->get();
            }else {
                $inscriptions = Inscription::where('user_id', $user->id)->orderBy('role')->with('Contest')->get();
            }
        }
        return View::make('site.index', array('allInscriptions' => $inscriptions, 'allContests' => $allContests, 'superAdmin' => $superAdmin));
    }
    public function showWelcome()
    {
        $clients = Client::all();
        $contests = Contest::where('public', 1)->where('inscription_start_at','<=',date('Y-m-d H:i:s'))->where('inscription_deadline1_at','>=',date('Y-m-d H:i:s'))->get();
        $slides = Slide::isPublic()->orderBy('order','asc')->get();
        return View::make('site.home', array('clients' => $clients, 'contests' => $contests, 'slides' => $slides));
    }

    public function getApplyForContest(){
        if(Auth::check()){
            $hasContests = 0;
            $superadmin = Auth::check() && Auth::user()->isSuperAdmin();
            if($superadmin) $hasContests = 1;
            else{
                $user = Auth::user();
                $contestsIds = Inscription::where('user_id', $user->id)->where('role', Inscription::OWNER)->select('contest_id')->count();
                if($contestsIds > 0 ) $hasContests = 1;
            }
            return View::make('site.form-apply-for-contest', ['hasContests' => $hasContests]);
        }else{
            return View::make('login.form');
        }
    }

    public function getLoginApplyForContest(){
        return View::make('site.login-apply-contest');
    }

    public function showPrivacy()
    {
        return View::make('site.privacy');
    }

    public function showTerms()
    {
        return View::make('site.terms');
    }

    public function postContact()
    {
        $input = Input::only('name', 'email', 'phone', 'message');

        $rules = array(
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'message' => 'required'
        );
        $validator = Validator::make($input, $rules);

        if ($validator->fails())
        {
            $messages = $validator->messages();
            return Response::json(array('errors'=>$messages));
        }
        else
        {
            $input['messageText'] = $input['message'];
            $body = View::make('emails.auth.contact', $input)->render();
            $response = OxoMailer::sendMail([
                'email_to' => Config::get('site.contact-email'),
                'subject' => Lang::get('home.contact-mail-subject'),
                'body' => $body
            ]);
            return Response::json(array('flash'=>Lang::get('home.contactus-sent')));
        }
    }

    public function setLocale($lang){
        if(in_array($lang, Config::get('app.languages'))) {
            App::setLocale($lang);
            Session::put('my.locale', $lang);
            Session::set('lang_selected', $lang);
        }
        $returnTo = Input::get("returnTo");
        //return Redirect::back()->with('message','Operation Successful !');
        //echo URL::previous() . '#'.$returnTo; exit();
        return Redirect::to(URL::previous() . '#'.$returnTo);
    }

    public function getAvailableNames(){
        $name = Input::get('name');
        $code = Input::get('code');
        if(!Contest::where('name', $name)->first()){
            $nameAvailable = true;
        }
        else $nameAvailable = false;
        if(!Contest::where('code', $code)->first()){
            $codeAvailable = true;
        }
        else $codeAvailable = false;

        return Response::json(['name' => $nameAvailable, 'code' => $codeAvailable]);
    }
}