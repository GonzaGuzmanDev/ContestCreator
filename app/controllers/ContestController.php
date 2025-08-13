<?php

use Illuminate\Filesystem\Filesystem;
use App\Services\OxoMailer;
use Endroid\QrCode\QrCode;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\Snappy;
use MercadoPago\SDK;
//use Stripe\Stripe;

class ContestController extends \BaseController {

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
        $filterContest = Input::get('filterContest');
        if(empty($filterContest)){
            $filterContest = [0,1,2,3,4,5];
        }
        switch($orderBy)
        {
            case "code":
            case "name":
            case "start_at":
            case "finish_at":
            case "users":
            case "entries":
            //case "deadline1_at":
            //case "deadline2_at":
                break;
            default:
                $orderBy = "name";
                $orderDir = 'asc';
        }
        if($orderDir == false)
        {
            $orderDir = 'desc';
        }
        else
        {
            $orderDir = 'asc';
        }
        $data = Contest::select(['contests.id',
                'code', 'contests.name',
                /*'contests.created_at',
                'contests.start_at',*/
                'contests.status',
                'inscription_start_at',
                'inscription_deadline1_at',
                'inscription_deadline2_at',
                'voters_start_at',
                'voters_deadline1_at',
                'voters_deadline2_at',
                'contests.finish_at',
                'inscription_public',
                'voters_public',
                'default_lang',
                'contest_invoices.status as invoice_status',
                DB::raw('COUNT(DISTINCT inscriptions.id) as users'),
                DB::raw('COUNT(DISTINCT entry_categories.id) as entries')])
            ->whereIn('contests.status', array_values($filterContest))
            ->where(
                function($q) use ($query) {
                    $q->where('name', 'LIKE', '%'.$query.'%');
                    $q->orWhere('code', 'LIKE', '%'.$query.'%');
                }
            )
            ->leftJoin('inscriptions', 'contests.id', '=', 'inscriptions.contest_id')
            ->leftJoin('contest_invoices', 'contests.id', '=', 'contest_invoices.contest_id')
            ->leftJoin('entries', function($join){
                $join->on('contests.id', '=', 'entries.contest_id')
                ->whereNull('entries.deleted_at');
            })
            ->leftJoin('entry_categories', 'entries.id', '=', 'entry_categories.entry_id')
            ->orderBy($orderBy, $orderDir)
            ->groupBy('contests.id')
            ->paginate($pageItems, ['id', 'code', 'name', 'created_at', 'start_at', 'inscription_start_at', 'inscription_deadline1_at', 'inscription_deadline2_at', 'voters_start_at', 'voters_deadline1_at', 'voters_deadline2_at', 'finish_at', 'inscription_public', 'voters_public', 'users']);

        $pagination = [
            'last' => $data->getLastPage(),
            'page' => $data->getCurrentPage(),
            'perPage' => $data->getPerPage(),
            'total' => $data->getTotal(),
            'orderBy' => $orderBy,
            'orderDir' => $orderDir == 'asc',
            'query' => $query,
        ];
        return Response::json(['status' => 200, 'data' => $data->getItems(), 'pagination' => $pagination, 'filterContest' => $filterContest]);
	}


	/**
	 * Store a newly created resource in storage.
	 * @return Response
	 */
	/*public function store($id)
	{
        $input = Input::only('code', 'name', 'start_at', 'deadline1_at', 'deadline2_at', 'finish_at');
        $input["user_id"] =  Auth::id();
        $rules = array(
            'code' => 'required|min:2|max:16',
            'name' => 'required|min:2|max:128',
            'start_at' => 'required|date|date_format:"Y-m-d H:i:s"',
            'deadline1_at' => 'required|date|date_format:"Y-m-d H:i:s"',
            'deadline2_at' => 'date|date_format:"Y-m-d H:i:s"',
            'finish_at' => 'required|date|date_format:"Y-m-d H:i:s"',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails())
        {
            $messages = $validator->messages();
            return Response::json(array('errors'=>$messages));
        }
        else
        {
            //Contest::create($input);
            Contest::f($id)->update($input);
            return Response::json(['status' => 200, 'flash' => Lang::get('contest.contestSaved')]);
        }
	}*/


	/**
	 * Display the specified resource.
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        $contest = Contest::find($id);
        return Response::json($contest);
	}


	/**
	 * Update the specified resource in storage.
	 * @param string $id
	 * @return Response
	 */
	public function update($id = null)
	{
	    $input = Input::only('code', 'name', 'start_at', 'single_category', 'max_entries', 'public', 'inscription_public', 'inscription_register_picture', 'inscription_start_at', 'inscription_deadline1_at', 'inscription_deadline2_at', 'voters_public', 'voters_register_picture', 'voters_start_at',
            'voters_deadline1_at', 'voters_deadline2_at', 'finish_at','storage_sources_bucket', 'google_analytics_id', 'facebook_pixel_id', 'type', 'block_finished_entry', 'admin_reset_password');

        $wizardAdmin = Input::get('wizardAdmin');

	    if($wizardAdmin){
            $input['storage_sources_bucket'] = Config::get('cloud.default_storage_sources_bucket');
        }
        //return Response::json(array('type' => $input['type'] = $input['type']['type'] == "" ? $input['type'] = null : "HOLA"));
        //$input['type'] = $input['type'] == "" ? $input['type'] = null : $input['type'];
        $rules = array(
            'name' => 'required|min:2',
            'code' => 'required|min:2',
            /*'start_at' => 'required|date|date_format:"Y-m-d H:i:s"',
            'finish_at' => 'required|date|date_format:"Y-m-d H:i:s"',*/
            'single_category' => 'boolean',
            'block_finished_entry' => 'boolean',
            'admin_reset_password' => 'boolean',
            'max_entries' => 'required|numeric',
            'public' => 'boolean',
            'inscription_public' => 'boolean',
            'inscription_register_picture' => 'boolean',
            'inscription_start_at' => 'date|date_format:"Y-m-d H:i:s"',
            'inscription_deadline1_at' => 'date|date_format:"Y-m-d H:i:s"',
            'inscription_deadline2_at' => 'date|date_format:"Y-m-d H:i:s"',
            'voters_public' => 'boolean',
            'voters_register_picture' => 'boolean',
            'voters_start_at' => 'date|date_format:"Y-m-d H:i:s"',
            'voters_deadline1_at' => 'date|date_format:"Y-m-d H:i:s"',
            'voters_deadline2_at' => 'date|date_format:"Y-m-d H:i:s"',
            /*'sizes' => '',
            'limits' => '',
            'template' => '',
            'billing' => '',*/
        );
        if(Config::get('cloud.enabled')){
            $rules['storage_sources_bucket'] = 'required|min:2';
        }
        if($input['type'] == null) $input['type'] = Contest::TYPE_CONTEST;
        $input['single_category'] = $input['single_category'] == 1;
        $input['block_finished_entry'] = $input['block_finished_entry'] == 1;
        $input['admin_reset_password'] = $input['admin_reset_password'] == 1;
        if($input['inscription_public']){
            $rules = array_merge($rules, [
                'inscription_start_at' => 'required|date|date_format:"Y-m-d H:i:s"',
                'inscription_deadline1_at' => 'required|date|date_format:"Y-m-d H:i:s"',
                'inscription_deadline2_at' => 'date|date_format:"Y-m-d H:i:s"',
            ]);
        }
        if($input['inscription_register_picture'] == null){
            unset($input['inscription_register_picture']);
        }
        if($input['voters_public']){
            $rules = array_merge($rules, [
                'voters_start_at' => 'required|date|date_format:"Y-m-d H:i:s"',
                'voters_deadline1_at' => 'required|date|date_format:"Y-m-d H:i:s"',
                'voters_deadline2_at' => 'date|date_format:"Y-m-d H:i:s"',
            ]);
        }
        /*$usesBilling = false;
        if(isset($input['billing']) && isset($input['billing']['methods'])) {
            if (isset($input['billing']['methods']['transfer']) && $input['billing']['methods']['transfer']['enabled'] == 1) {
                $rules = array_merge($rules, [
                    'billing.methods.transfer.data' => 'required'
                ]);
                $usesBilling = true;
            }
            if (isset($input['billing']['methods']['check']) && $input['billing']['methods']['check']['enabled'] == 1) {
                $rules = array_merge($rules, [
                    'billing.methods.check.data' => 'required'
                ]);
                $usesBilling = true;
            }
            if (isset($input['billing']['methods']['TCO']) && $input['billing']['methods']['TCO']['enabled'] == 1) {
                $rules = array_merge($rules, [
                    'billing.methods.TCO.data.sellerId' => 'required',
                    'billing.methods.TCO.data.privateKey' => 'required',
                    'billing.methods.TCO.data.publicKey' => 'required',
                ]);
                $usesBilling = true;
            }
            if (isset($input['billing']['methods']['MP']) && $input['billing']['methods']['MP']['enabled'] == 1) {
                $rules = array_merge($rules, [
                    'billing.methods.MP.data.shortName' => 'required',
                    'billing.methods.MP.data.clientSecret' => 'required',
                    'billing.methods.MP.data.clientId' => 'required',
                    'billing.methods.MP.data.publicKey' => 'required',
                    'billing.methods.MP.data.accessToken' => 'required',
                ]);
                $usesBilling = true;
            }
            if($usesBilling){
                $rules = array_merge($rules, [
                    'billing.mainPrice' => 'required|numeric',
                    'billing.mainCurrency' => 'required|in:'.implode(",",Config::get('billing.currency')),
                ]);
            }
        }*/
        $niceNames = array('name' => Lang::get('general.name'),
            'code' => Lang::get('general.code'),
            'start_at' => Lang::get('contest.startAt'),
            'finish_at' => Lang::get('contest.finishAt'));
        $validator = Validator::make($input, $rules);
        $validator->setAttributeNames($niceNames);
        if ($validator->fails())
        {
            $messages = $validator->messages();
            return Response::json(array('errors'=>$messages));
        }
        else
        {
            /** @var Contest $con */
            /*if(isset($input['billing'])) {
                if (!isset($input['billing']['methods']['transfer']) || $input['billing']['methods']['transfer']['enabled'] != 1) unset($input['billing']['methods']['transfer']);
                if (!isset($input['billing']['methods']['check']) || $input['billing']['methods']['check']['enabled'] != 1) unset($input['billing']['methods']['check']);
                if (!isset($input['billing']['methods']['TCO']) || $input['billing']['methods']['TCO']['enabled'] != 1) unset($input['billing']['methods']['TCO']);
                if (!isset($input['billing']['methods']['MP']) || $input['billing']['methods']['MP']['enabled'] != 1) unset($input['billing']['methods']['MP']);
                if(!$usesBilling) $input['billing'] = null;
                else $input['billing'] = json_encode($input['billing']);
            }else
            $input['billing'] = null;*/
            if(isset($id)){
                $con = Contest::findOrFail($id);
                $con->update($input);
            }else{
                $input["user_id"] =  Auth::id();
                if(!isset($input["inscription_register_picture"])) $input["inscription_register_picture"] = 0;
                $input["inscription_register_picture"] =  !$input["inscription_register_picture"] ? 0 : 1;
                $input["voters_register_picture"] =  !$input["voters_register_picture"] ? 0 : 1;
                $input['status'] = Contest::STATUS_READY;
                $con = Contest::create($input);
                $types = ContestAsset::getAllTypesIds();
                foreach($types as $type){
                    $contestAsset = new ContestAsset();
                    $contestAsset->type = $type;
                    if($type == ContestAsset::INSCRIPTION_OK_EMAIL){
                        $contestAsset->name = Lang::get('contest.inscriptionEmail');
                        $contestAsset->content = Lang::get('contest.registrationEmailTemplate');
                    }
                    if($type == ContestAsset::INSCRIPTOR_INVITATION_EMAIL){
                        $contestAsset->name = Lang::get('contest.inscriptorInvitationEmail');
                        $contestAsset->content = Lang::get('contest.inscriptorInvitationEmailTemplate', ['contest' => $con->name]);
                    }
                    if($type == ContestAsset::JUDGE_INVITATION_EMAIL){
                        $contestAsset->name = Lang::get('contest.judgeInvitationEmail');
                        $contestAsset->content = Lang::get('contest.judgeInvitationEmailTemplate', ['contest' => $con->name]);
                    }
                    if($type == ContestAsset::COLLABORATOR_INVITATION_EMAIL){
                        $contestAsset->name = Lang::get('contest.collaboratorInvitationEmail');
                        $contestAsset->content = Lang::get('contest.collaboratorInvitationEmailTemplate', ['contest' => $con->name]);
                    }
                    if($type == ContestAsset::ENTRY_ERROR_EMAIL){
                        $contestAsset->name = Lang::get('contest.entryErrorEmail');
                        $contestAsset->content = Lang::get('contest.entryErrorEmailTemplate', ['contest' => $con->name]);
                    }
                    if($type == ContestAsset::ENTRY_APPROVED_EMAIL){
                        $contestAsset->name = Lang::get('contest.entryApprovedEmail');
                        $contestAsset->content = Lang::get('contest.entryApprovedEmailTemplate', ['contest' => $con->name]);
                    }
                    if($type == ContestAsset::ENTRY_FINALIZED_EMAIL){
                        $contestAsset->name = Lang::get('contest.entryFinalizedEmail');
                        $contestAsset->content = Lang::get('contest.entryFinalizedEmailTemplate', ['contest' => $con->name]);
                    }
                    if($type == ContestAsset::MEDIA_ERROR_EMAIL){
                        $contestAsset->name = Lang::get('contest.mediaErrorEmail');
                        $contestAsset->content = Lang::get('contest.mediaErrorEmailTemplate', ['contest' => $con->name]);
                    }
                    if($type == ContestAsset::OTHER_PURPOSES_MAIL){
                        $contestAsset->name = Lang::get('contest.otherPurposesEmail');
                        $contestAsset->content = Lang::get('contest.otherPurposesEmailTemplate', ['contest' => $con->name]);
                    }
                    if($type == ContestAsset::BIG_BANNER_HTML){
                        $contestAsset->name = Lang::get('contest.bigBannerHTML');
                        $view = View::make('contest.banners.big', ['contest' => $con->name]);
                        $contestAsset->content = $view->render();
                    }
                    if($type == ContestAsset::SMALL_BANNER_HTML){
                        $contestAsset->name = Lang::get('contest.smallBannerHTML');
                        $view = View::make('contest.banners.small', ['contest' => $con->name]);
                        $contestAsset->content = $view->render();
                    }
                    $contestAsset->contest_id = $con->id;
                    $contestAsset->save();
                };
            }

            if(isset($wizardAdmin)){
                if(!Inscription::where('user_id', Auth::id())->where('contest_id', $con->id)->update(['role' => Inscription::OWNER])){
                    $newOwner = new Inscription();
                    $newOwner->user_id = Auth::id();
                    $newOwner->contest_id = $con->id;
                    $newOwner->role = Inscription::OWNER;
                    $newOwner->save();
                }
                $import_contest = Input::get('import');
                if($import_contest == true){
                    Contest::where('id', $con->id)->update(['wizard_status' => null, 'status' => Contest::STATUS_WIZARD]);
                }
                else Contest::where('id', $con->id)->update(['wizard_status' => Contest::WIZARD_REGISTER_FORM, 'status' => Contest::STATUS_WIZARD]);
                //Redirect::to('/'.$con->code.'#/admin/deadlines');
            }else{
                Contest::where('id', $con->id)->update(['wizard_status' => Contest::WIZARD_FINISHED]);
            }

            return Response::json(['status' => 200, 'flash' => Lang::get('contest.contestSaved'), 'contest'=>$con]);
        }
	}


    public function postInvoice($contest){
        $params = Input::get('params');
        ContestInvoice::where('contest_id', $params['contest_id'])->delete();

        $invoice = new ContestInvoice();
        $invoice->contest_id = $params['contest_id'];
        $invoice->status = $params['status'];
        $invoice->amount = isset($params['amount']) ? $params['amount'] : '';
        $invoice->invoice_code = isset($params['invoice_code']) ? $params['invoice_code'] : '';
        $invoice->data = isset($params['data']) ? $params['data'] : '';
        $invoice->concept = isset($params['concept']) ? $params['concept'] : '';;
        $invoice->invoice_date = isset($params['invoice_date']) ? $params['invoice_date'] : '';
        $invoice->business_name = isset($params['business_name']) ? $params['business_name'] : '';
        $invoice->currency = isset($params['currency']) ? $params['currency'] : '';
        $invoice->save();
        return $params['status'];
    }

    public function saveDeadlinesData($contest)
    {
        /** @var Contest $con */
        $con = $this->getContest($contest);
        $input = Input::only('inscription_start_at', 'inscription_deadline1_at', 'inscription_deadline2_at', 'voters_start_at', 'voters_deadline1_at', 'voters_deadline2_at', 'inscription_public', 'voters_public','inscription_register_picture','voters_register_picture', 'start_at', 'finish_at');

        $rules = array(
            'inscription_public' => 'boolean',
            'inscription_register_picture' => 'boolean',
            'inscription_start_at' => 'date|date_format:"Y-m-d H:i:s"',
            'inscription_deadline1_at' => 'date|date_format:"Y-m-d H:i:s"',
            'inscription_deadline2_at' => 'date|date_format:"Y-m-d H:i:s"',
            'voters_public' => 'boolean',
            'voters_register_picture' => 'boolean',
            'voters_start_at' => 'date|date_format:"Y-m-d H:i:s"',
            'voters_deadline1_at' => 'date|date_format:"Y-m-d H:i:s"',
            'voters_deadline2_at' => 'date|date_format:"Y-m-d H:i:s"',
            'start_at' => 'date|date_format:"Y-m-d H:i:s"',
            'finish_at' => 'date|date_format:"Y-m-d H:i:s"',
        );
        if($input['inscription_public'] || $input['inscription_start_at'] != "" || $input['inscription_deadline1_at'] != ""){
            $rules = array_merge($rules, [
                'inscription_start_at' => 'required|date|date_format:"Y-m-d H:i:s"',
                'inscription_deadline1_at' => 'required|date|date_format:"Y-m-d H:i:s"',
                'inscription_deadline2_at' => 'date|date_format:"Y-m-d H:i:s"',
            ]);
        }
        /*if($input['inscription_register_picture'] == null){
            unset($input['inscription_register_picture']);
        }*/
        if($input['voters_public'] || $input['voters_start_at'] != "" || $input['voters_deadline1_at'] != ""){
            $rules = array_merge($rules, [
                'voters_start_at' => 'required|date|date_format:"Y-m-d H:i:s"',
                'voters_deadline1_at' => 'required|date|date_format:"Y-m-d H:i:s"',
                'voters_deadline2_at' => 'date|date_format:"Y-m-d H:i:s"',
            ]);
        }
        $validator = Validator::make($input, $rules);
        if ($validator->fails())
        {
            $messages = $validator->messages();
            return Response::json(array('errors'=>$messages));
        }
        else
        {
            if(isset($con->wizard_status) && $con->wizard_status < Contest::WIZARD_FINISHED){
                Contest::where('id', $con->id)->update(['wizard_status' => Contest::WIZARD_FINISHED, 'status' => Contest::STATUS_COMPLETE]);

                $subject = Lang::get('contest.newContest', ["contest"=>$con->name]);
                $response = OxoMailer::sendMail([
                    'email_to' => 'eskel@oxobox.tv',
                    'subject' => $subject,
                    'body' => '',
                    //'body' => Lang::get('contest.entryNewBody', ["entry"=>$entry->id,"link"=>$link, "user"=>$user->email])
                ]);
            }
            $con->update($input);
            return Response::json(['status' => 200, 'flash' => Lang::get('contest.contestSaved'), 'contest'=>$con]);
        }
    }

    public function savePaymentsData($contest)
    {
        /** @var Contest $con */
        $con = $this->getContest($contest);
        $input = Input::only('billing');
        $rules = [];
        $usesBilling = false;
        if(isset($input['billing']) && isset($input['billing']['methods'])) {
            if (isset($input['billing']['methods']['transfer']) && $input['billing']['methods']['transfer']['enabled'] == 1) {
                $rules = array_merge($rules, [
                    'billing.methods.transfer.data' => 'required'
                ]);
                $usesBilling = true;
            }
            if (isset($input['billing']['methods']['check']) && $input['billing']['methods']['check']['enabled'] == 1) {
                $rules = array_merge($rules, [
                    'billing.methods.check.data' => 'required'
                ]);
                $usesBilling = true;
            }
            if (isset($input['billing']['methods']['creditcard']) && $input['billing']['methods']['creditcard']['enabled'] == 1) {
                $rules = array_merge($rules, [
                    'billing.methods.creditcard.data' => 'required'
                ]);
                $usesBilling = true;
            }
            if (isset($input['billing']['methods']['other']) && $input['billing']['methods']['other']['enabled'] == 1) {
                $rules = array_merge($rules, [
                    'billing.methods.other.data' => 'required'
                ]);
                $usesBilling = true;
            }
            if (isset($input['billing']['methods']['TCO']) && $input['billing']['methods']['TCO']['enabled'] == 1) {
                $rules = array_merge($rules, [
                    'billing.methods.TCO.data.sellerId' => 'required',
                    'billing.methods.TCO.data.privateKey' => 'required',
                    'billing.methods.TCO.data.publicKey' => 'required',
                ]);
                $usesBilling = true;
            }
            if (isset($input['billing']['methods']['MercadoPago']) && $input['billing']['methods']['MercadoPago']['enabled'] == 1) {
                $rules = array_merge($rules, [
                    /*'billing.methods.MercadoPago.data.shortName' => 'required',
                    'billing.methods.MercadoPago.data.clientSecret' => 'required',
                    'billing.methods.MercadoPago.data.clientId' => 'required',*/
                    'billing.methods.MercadoPago.data.accessToken' => 'required'
                    //'billing.methods.MP.data.publicKey' => 'required',
                    //'billing.methods.MP.data.accessToken' => 'required',
                ]);
                $usesBilling = true;
            }
            if (isset($input['billing']['methods']['customApi']) && $input['billing']['methods']['customApi']['enabled'] == 1) {
                $rules = array_merge($rules, [
                    'billing.methods.customApi.data.postURL' => 'required',
                    'billing.methods.customApi.data.billingId' => 'required',
                ]);
                $usesBilling = true;
            }
            if (isset($input['billing']['methods']['ClicPago']) && $input['billing']['methods']['ClicPago']['enabled'] == 1) {
                $rules = array_merge($rules, [
                    'billing.methods.ClicPago.data.productlink' => 'required',
                ]);
                $usesBilling = true;
            }
            if($usesBilling){
                $rules = array_merge($rules, [
                    'billing.mainPrice' => 'required|numeric',
                    'billing.mainCurrency' => 'required|in:'.implode(",",Config::get('billing.currency')),
                ]);
            }
        }
        $niceNames = array(
            'billing.mainPrice' => Lang::get('billing.mainPrice'),
            'billing.mainCurrency' => Lang::get('billing.mainCurrency'),
            'billing.methods.transfer.data' => Lang::get('billing.transfer.data'),
            /*'billing.methods.MercadoPago.data.shortName' => Lang::get('billing.MercadoPago.shortName'),
            'billing.methods.MercadoPago.data.clientSecret' => Lang::get('billing.MercadoPago.clientSecret'),
            'billing.methods.MercadoPago.data.clientId' => Lang::get('billing.MercadoPago.clientId'),*/
            'billing.methods.MercadoPago.data.accessToken' => Lang::get('billing.MercadoPago.accessToken'),
            'billing.methods.check.data' => Lang::get('billing.check.data'),
            'billing.methods.creditcard.data' => Lang::get('billing.creditcard.data'),
        );
        $validator = Validator::make($input, $rules);
        $validator->setAttributeNames($niceNames);
        $messages = new \Illuminate\Support\MessageBag();
        if ($validatorFailed = $validator->fails())
        {
            $messages->merge($validator->messages());
        }

        $discounts = Input::only(['discounts'])['discounts'];
        $discountsValidatorFailed = false;
        $discountRules = [];
        for($di = 0; $di < count($discounts); $di++) {
            //$discount = $discounts[$di];
            $discountRules = array_merge($discountRules, [
                $di.'.value' => 'required|numeric|min:1',
                $di.'.change' => 'required|in:'.Discount::CHANGE_PERCENTAGE.','.Discount::CHANGE_PRICE,
                $di.'.min_entries' => 'required|numeric|min:1',
                $di.'.max_entries' => 'numeric|min:1',
            ]);
        }
        $discountsValidator = Validator::make($discounts, $discountRules);
        if ($discountsValidator->fails())
        {
            $messages->merge($discountsValidator->messages());
            $discountsValidatorFailed = true;
        }
        if ($validatorFailed || $discountsValidatorFailed)
        {
            return Response::json(array('errors'=>$messages));
        }
        else
        {
            if (isset($input['billing'])) {
                if (!isset($input['billing']['methods']['transfer']) || $input['billing']['methods']['transfer']['enabled'] != 1) unset($input['billing']['methods']['transfer']);
                if (!isset($input['billing']['methods']['check']) || $input['billing']['methods']['check']['enabled'] != 1) unset($input['billing']['methods']['check']);
                if (!isset($input['billing']['methods']['creditcard']) || $input['billing']['methods']['creditcard']['enabled'] != 1) unset($input['billing']['methods']['creditcard']);
                if (!isset($input['billing']['methods']['other']) || $input['billing']['methods']['other']['enabled'] != 1) unset($input['billing']['methods']['other']);
                if (!isset($input['billing']['methods']['TCO']) || $input['billing']['methods']['TCO']['enabled'] != 1) unset($input['billing']['methods']['TCO']);
                if (!isset($input['billing']['methods']['MercadoPago']) || $input['billing']['methods']['MercadoPago']['enabled'] != 1) unset($input['billing']['methods']['MercadoPago']);
                if (!isset($input['billing']['methods']['ClicPago']) || $input['billing']['methods']['ClicPago']['enabled'] != 1) unset($input['billing']['methods']['ClicPago']);
                if (!isset($input['billing']['methods']['customApi']) || $input['billing']['methods']['customApi']['enabled'] != 1) unset($input['billing']['methods']['customApi']);
                if (!$usesBilling) $input['billing'] = null;
                else $input['billing'] = json_encode($input['billing']);
            } else
                $input['billing'] = null;
            $con->update($input);

            $savedDiscounts = [];
            for($di = 0; $di < count($discounts); $di++) {
                $discount = $discounts[$di];
                /** @var $disc Discount */
                if(isset($discount['id'])){
                    $disc = Discount::where('contest_id', $con->id)->where('id', $discount['id'])->first();
                    if(!$disc) continue;
                }else{
                    $disc = new Discount();
                    $disc->contest_id = $con->id;
                    $disc->save();
                }
                $disc->update($discount);
                $savedDiscounts[] = $disc->id;
            }
            Discount::where('contest_id', $con->id)->whereNotIn('id', $savedDiscounts)->delete();
            $con->discounts;

            if(isset($con->wizard_status) && $con->wizard_status < Contest::WIZARD_FINISHED){
                $wizardHasPayment = Input::only('wizardHasPayment');
                $wizard_config = json_decode($con->wizard_config) ? json_decode($con->wizard_config) : (object)[];
                $wizard_config->billing = $wizardHasPayment['wizardHasPayment'] == true ? 1 : 0;
                Contest::where('id', $con->id)->update(['wizard_status' => Contest::WIZARD_STYLE, 'wizard_config' => json_encode($wizard_config)]);
            }

            $con = $this->getContest($contest);

            return Response::json(['status' => 200, 'flash' => Lang::get('contest.contestSaved'), 'contest'=>$con], 200, [], JSON_NUMERIC_CHECK);
        }
    }

	/**
	 * Remove the specified resource from storage.
	 * @param  int  $contest
	 * @return Response
	 */
	public function destroy($contest)
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
            $con = $this->getContest($contest);
            $con->delete();
            return Response::json(['status' => 200, 'flash' => Lang::get('contest.contestDeleted')]);
        }
	}

    /**
     * @param $contest
     * @param string $relation
     * @param string $extraWith
     * @return Contest
     */
    private function getContest($contest, $relation = null, $extraWith = null, $role = Inscription::INSCRIPTOR){
        $query = Contest::where('code', '=', $contest);
        $conId = $query->firstOrFail();
        $query2 = Inscription::where('contest_id', '=', $conId->id);
        switch($relation){
            case "inscription":
                $query->with(['InscriptionMetadataFields' => function($query) use ($role)
                {
                    if($role != null) $query->where('role', $role);
                    $query->orderBy('order', 'asc');
                },
                'inscriptionTypes' => function($query) use ($role)
                {
                    $time = date("Y-m-d H:i:s");
                    $query->where('public', 1);
                    if($role != null) $query->where('role', $role);
                    $query->where(function($query) use ($time)
                        {
                            $query->where(function($query) use ($time) {
                                $query->where('start_at', '<', $time)
                                    ->where(function ($query) use ($time) {
                                        $query->where('deadline1_at', '>', $time)
                                            ->orWhere('deadline2_at', '>', $time);
                                    });
                            })
                            ->orWhere(function($query) use ($time)
                            {
                                $query->where('start_at','=', null)
                                    ->where('deadline1_at', '=', null)
                                    ->where('deadline2_at', '=', null);
                            });
                        })
                        ->orderBy('name', 'asc');
                },
                'InscriptionMetadataValues' => function($query)
                {
                    //$query->whereUserId(Auth::id());
                }
                ]);
                break;
            case "categories":
                $query->with([
                'categories' => function($query)
                {
                    //$query->orderBy('name', 'asc');
                },
                'childrenCategories' => function($query)
                {
                    //$query->orderBy('name', 'asc');
                }]);
                break;
            case "entry":
                $query->with(['EntryMetadataFields' => function($query)
                {
                    $query->orderBy('order', 'asc');
                },
                'categories' => function($query)
                {
                    //$query->orderBy('name', 'asc');
                },
                'childrenCategories' => function($query)
                {
                    //$query->orderBy('name', 'asc');
                }]);
                break;
            case "userfiles":
                $query->with(['contestFiles' => function($query)
                    {
                        $query->whereUserId(Auth::id())->orderBy('name', 'asc');
                    }
                ]);
                break;
        }
        if($extraWith != null){
            $query->with($extraWith);
        }
        $con = $query->firstOrFail();
        if(!$con) {
            App::abort(404, Lang::get('contest.notfound'));
        }
        return $con;
    }

    /**
     * @param $contest
     * @return array
     */
    private function getContestMainData($contest){
        $con = $this->getContest($contest, true, ['categories','childrenCategories'], null);
        $inscriptions = [];
        $allInscriptions = [];
        $allContests = [];
        $superAdmin = false;
        if(Auth::check()){
            $user = Auth::user();
            if($user->isSuperAdmin()){
                $superAdmin = true;
                $allContests = Contest::basic()->opened()->get();
            }else {
                $inscriptions = Inscription::where('user_id', $user->id)->where('contest_id', $con->id)->orderBy('role')->with('Contest', 'inscriptionMetadatas')->get();
                $allInscriptions = Inscription::where('user_id', $user->id)->orderBy('role')->with('Contest', 'inscriptionMetadatas')->get();
            }
        }
        if(isset($con->billing) && gettype($con->billing) == "string") $con->billing = json_decode($con->billing, true);
        $b = $con->billing;
        if(isset($b['methods']['TCO'])) unset($b['methods']['TCO']['data']['privateKey']);
        if(isset($b['methods']['MercadoPago'])){
            unset($b['methods']['MercadoPago']['data']['clientSecret']);
            unset($b['methods']['MercadoPago']['data']['accessToken']);
        }
        $b['discounts'] = Discount::where('contest_id', $con->id)->get();
        $con->billing = $b;

        if($superAdmin == true){
            $con->users = User::leftJoin('inscriptions', 'inscriptions.user_id', '=', 'users.id')
                ->where('inscriptions.contest_id', '=', $con->id)
                ->select('users.id as user_id', 'users.first_name', 'users.last_name', 'users.email')
                ->get();
        }

        return ['contest' => $con, 'inscriptions' => $inscriptions, 'allInscriptions' => $allInscriptions, 'allContests' => $allContests, 'superAdmin' => $superAdmin];
    }
    public function getIndex($contest){
        //$con = $this->getContest($contest, true, ['categories','childrenCategories'], null);
        return View::make('contest.index', $this->getContestMainData($contest));
    }

    public function getData($contest, $inscription=false, $role=Inscription::INSCRIPTOR){
        $con = $this->getContest($contest, $inscription, null, $role);
        return Response::json($con, 200, [], JSON_NUMERIC_CHECK);
    }

    public function getInvoiceData($contest){
        $con = $this->getContest($contest);
        $query = ContestInvoice::where('contest_id', $con->id)->get();
        return $query;
    }

    public function getEntries($contest){
        /** @var Contest $con */
        $user = Auth::user();
        $superadmin = Auth::check() && Auth::user()->isSuperAdmin();
        $con = $this->getContest($contest, false, ['childrenCategories']);
        $categoryId = Input::get('category');
        $user_id = Input::get('user_id');
        $lastEntryLoaded = Input::get('lastEntryLoaded');
        $entriesPerRow = Input::get('entriesPerRow');
        $filters = Input::get('filters');
        $loggedUserByAdmin = Input::get('loggedUserByAdmin');
        $entries = [];
        $sentInscription = Input::get('inscription');
        $inscription = $con->getUserInscription($user, $sentInscription['role'] != null ? $sentInscription['role'] : null);
        $inscriptor = $con->getUserInscription($user, Inscription::INSCRIPTOR);

        if(!$inscription && !$superadmin){
            return Response::make(Lang::get('Inscription not found'), 404);
        }

        if($superadmin || $inscription['role'] == Inscription::OWNER || $inscription['role'] == Inscription::COLABORATOR){
            $user->id = null;
        }
        if($user_id != null){
            $user->id = $user_id;
        }

        /* Entries for judges */
        if($inscription['role'] == Inscription::JUDGE){
            //$code = Input::get('code');
            $voteSession = Input::get('voteSession');
            $votingSessionId = VotingSession::where('code', $voteSession['code'])->select('id')->first();
            $votingUserId = VotingUser::where('voting_session_id', $votingSessionId->id)
                ->where('inscription_id', $inscription->id)->select('id')->first();
            $data = $con->getJudgeEntries($voteSession['code'], $inscription, Input::get('showAllEntries'));
            $userAbstains = UserAutoAbstain::where('voting_user_id', $votingUserId->id)
                ->where('voting_session_id', $votingSessionId->id)->get();

            /*** Hay autoabstenciones ***/
            if(count($userAbstains) > 0) {
                $entryIds = [];
                foreach ($data['entries'] as $entry) {
                    $entryId = Entry::where('id', $entry['id'])->select('id')->first();
                    array_push($entryIds, $entryId->id);
                }
                $entriesAbstain = Entry::whereIn('id', $entryIds)->with('EntryMetadataValuesWithFields', 'entryCategories')->get();
                //return Response::json(array('$entriesAbstain'=>$entriesAbstain, '$userAbstains'=>$userAbstains));
                foreach ($entriesAbstain as $entry) {
                    $abstain = false;
                    foreach ($entry['entry_metadata_values_with_fields'] as $entryMetadata) {
                        foreach ($userAbstains as $item) {
                            if ($entryMetadata['entry_metadata_field_id'] == $item['entry_metadata_field_id'] && strtoupper($entryMetadata['value']) == strtoupper($item['value'])) {
                                $abstain = true;
                            }
                        }
                    }
                    if ($abstain) {
                        foreach ($data['entries'] as $originalEntry) {
                            if($entry->id == $originalEntry->id){
                                $originalEntry->votes = 'abstain';
                            };
                        }
                    }
                };
            }
            if($data['entries'] == false) App::abort(404, Lang::get('general.error'));
        }
        /* Entries for admins, collaborators, inscriptors */
        else{
            $params = [
                'categoryId' => $categoryId,
                'lastEntryLoaded' => $lastEntryLoaded,
                'entriesPerRow' => $entriesPerRow,
                'filters' => $filters,
                'user_id' => $user->id,
                'loggedUserByAdmin' => $loggedUserByAdmin
            ];
            $data = $con->getAllEntries($params);
        }

        if($inscriptor){
            foreach($con->childrenCategories as $catKey => $category) {
                if(isset($category->category_config_type[0])){
                    if(!isset($inscriptor->inscription_type->id)) break;
                    $childCategories = $con->childrenCategoriesWithInscriptionType($inscriptor->inscription_type->id);
                    break;
                }
            }
            if(isset($childCategories[0])){
                $parents = [];
                $childrens = [];
                foreach($childCategories as $key => $categs) {
                    if ($categs->parent_id == NULL) {array_push($parents, $categs);}
                    else{array_push($childrens, $categs);}
                }
                $filteredCategories = $this->selectedCategories($parents, $childrens);
                $con->childrenCategories = $filteredCategories;
            }
        }

        $data['children_categories'] = $this->entriesCategoryCount($con->childrenCategories, $filters, $user->id, $con->id);

        if(isset($inscription)){
            $data['inscription'] = $inscription;
        }

        return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
    }

    public function postVotingUserEntries($contest){
        $userId = Input::get('userId');
        $votingUserId = Input::get('votingUserId');
        $voteSession  = Input::get('voteSession');
        $con = $this->getContest($contest);
        if($userId){
            $inscriptionQuery = Inscription::where('role', '=', Inscription::JUDGE)->where('contest_id', '=', $con->id)->where('user_id', '=', $userId);
            $inscription = $inscriptionQuery->with('InscriptionType')->first();
        }
        if($votingUserId){
            $votingUser = VotingUser::where('id', $votingUserId)->first();
            $inscriptionQuery = Inscription::where('id', $votingUser->inscription_id);
            $inscription = $inscriptionQuery->with('InscriptionType')->first();
        }
        $entries = $con->getJudgeEntries($voteSession, $inscription);
        return Response::json($entries, 200, [], JSON_NUMERIC_CHECK);
    }

    public function postVotingGroupEntries($contest){
        $con = $this->getContest($contest);
        $groupId = Input::get('groupId');
        $entryCatId = VotingGroupEntryCategory::where('voting_group_id', $groupId)->lists('entry_category_id');
        $entriesIds = EntryCategory::whereIn('id', $entryCatId)->lists('entry_id');
        $query = Entry::where('contest_id', $con->id)->where('status', Entry::APPROVE)->whereIn('id', $entriesIds);

        $entries = $query->with(['MainMetadata','EntryCategories'])->get()->each(function ($entry) use($entryCatId){
            /**	@var Entry $entry */
            if($entry->mainMetadata != null && count($entry->mainMetadata) != 0){
                $first = $entry->mainMetadata->first();
                if($first) $entry->name = $first->value;
                else $entry->name = Lang::get('contest.entryNoTitle');
            } else{
                $entry->name = Lang::get('contest.entryNoTitle');
            }
            $entry->main_metadata = null;
            $entry->categories_id = $entry->entryCategories()->lists('category_id');

            if(count($entryCatId)) {
                $cIds = EntryCategory::whereIn('id', $entryCatId)->where('entry_id',$entry->id);
                $entry->categories_id = $cIds->lists('category_id');
            }

        });

        return Response::json($entries, 200, [], JSON_NUMERIC_CHECK);
    }

    public function postGroupEntriesCategories($contest){
        $categories = Input::get('categories');
        $groupId = Input::get('voting_group_id');
        $newGroup = Input::get('newName');
        $deleted = Input::get('deleted');
        if($newGroup){
            VotingGroup::where('id', $groupId)->update(['name' => $newGroup]);
        }

        // TODO hay que borrar los entries que se sacan de los grupos, de los usuarios que estan en el grupo
        $votersInGroup = VotingUserVotingGroup::where('voting_group_id', $groupId)->lists('voting_user_id');

        foreach($votersInGroup as $voterInGroup){
            VotingUserEntryCategory::where('voting_user_id', $voterInGroup)->whereIn('entry_category_id', $deleted)->delete();
        }

        VotingGroupEntryCategory::where('voting_group_id',$groupId)->delete();

        //foreach($categories as $entryId => $categoryData){
        foreach($categories as $categoryData){
            foreach($categoryData['categories_id'] as $categoryId){
                //$entryCategory = EntryCategory::where('entry_id', $entryId)->where('category_id', $categoryId)->first(['id']);
                $entryCategory = EntryCategory::where('entry_id', $categoryData['id'])->where('category_id', $categoryId)->first(['id']);
                $query = new VotingGroupEntryCategory();
                $query->voting_group_id = $groupId;
                $query->entry_category_id = $entryCategory->id;
                $query->save();
            }
        }

        return Response::json(array('voting_group_id' => $groupId, 'categories' => $categories), 200, [], JSON_NUMERIC_CHECK);
    }

    public function postVotingUserEntriesCategories($contest){
        $voting_user_id = Input::get('voting_user_id');
        $categories = Input::get('categories');

        VotingUserEntryCategory::where('voting_user_id', $voting_user_id)->delete();

        foreach($categories as $categoryData){
            foreach($categoryData['categories_id'] as $categoryId){
                $entryCategory = EntryCategory::where('entry_id', $categoryData['id'])->where('category_id', $categoryId)->first(['id']);
                $query = new VotingUserEntryCategory();
                $query->voting_user_id = $voting_user_id;
                $query->entry_category_id = $entryCategory->id;
                $query->save();
            }
        }

        /*** Cambiar el mail ***/
        $inscriptionId = Input::get('inscription');
        $newMail = Input::get('newMail');
        if($newMail){
            Inscription::where('id',$inscriptionId)->update(['email' => $newMail]);
            VotingUser::where('id', $voting_user_id)->update(['status' => VotingUser::PENDING_NOTIFICATION]);
        }
        /** @var VotingUser $votingUser */
        $votingUser = VotingUser::where('id', $voting_user_id)->first();
        $votingUser->votingSession->loadJudgeProgress($votingUser);
        return Response::json(array('votingUser' => $votingUser, 'categories' => $categories, 'inscription' => $inscriptionId, 'newMail' => $newMail), 200, [], JSON_NUMERIC_CHECK);
    }

    public function postSessionEntries($contest){
        $voteSession  = Input::get('voteSession');
        $con = $this->getContest($contest);
        $query = Entry::where('contest_id', $con->id)->where('status', Entry::APPROVE);

        $voteData = VotingSession::where('code', $voteSession)->select('id', 'name')->firstOrFail();

        $votingCatsId = VotingCategory::where('voting_session_id', '=', $voteData['id'])->get();

        $voteCategories = [];
        $voteCategories = Input::get('voteCats');

        $query->whereHas('entryCategories', function($q) use ($voteCategories)
        {
            $q->whereIn('category_id',$voteCategories);
        });
        /*if(count($votingCatsId) > 0) {
            foreach ($votingCatsId as $catId) {
                array_push($voteCategories, $catId->category_id);
            }
            $query->whereHas('entryCategories', function($q) use ($voteCategories)
            {
                $q->whereIn('category_id',$voteCategories);
            });
        }*/

        $entryCategoryIds = null;

        $entries = $query->with(['MainMetadata','EntryCategories', 'categories'])->get()->each(function ($entry) use($voteCategories) {
            /**	@var Entry $entry */
            if($entry->mainMetadata != null && count($entry->mainMetadata) != 0){
                $first = $entry->mainMetadata->first();
                if($first) $entry->name = $first->value;
                else $entry->name = Lang::get('contest.entryNoTitle');
            } else{
                $entry->name = Lang::get('contest.entryNoTitle');
            }
            $entry->main_metadata = null;
            if(count($voteCategories)){
                $entry->categories_id = $entry->entryCategories()->whereIn('category_id',$voteCategories)->lists('category_id');
            }else{
                $entry->categories_id = $entry->entryCategories()->lists('category_id');
            }
        });

        return Response::json($entries, 200, [], JSON_NUMERIC_CHECK);
    }

    public function getUserEntries($contest){
        $userId = Input::get('user');
        $con = $this->getContest($contest);

        $query = Entry::where('contest_id', $con->id)->where('user_id', $userId);

        $entries = $query->with(['MainMetadata','EntryCategories','User','entryLog'])->with(['Billings' => function ($q) {
            $q->with('billingEntryCategories')->short();
        }])->get()->each(function ($entry) {
            if(isset($entry->mainMetadata) || $entry->mainMetadata!=null && count($entry->mainMetadata) != 0){
                $first = $entry->mainMetadata->first();
                if($first) $entry->name = $first->value;
                else $entry->name = Lang::get('contest.entryNoTitle');
            }else{
                $entry->name = Lang::get('contest.entryNoTitle');
            }
            $entry->mainMetadata = null;
            $entry->categories_id = $entry->entryCategories()->lists('category_id');
        });
        $data = array(
            'entries' => $entries,
        );
        return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
    }

    public function getEntryInscriptor($contest, $id){
        /** @var Contest $con */
        $user = Auth::user();
        $con = $this->getContest($contest);
        $superadmin = Auth::check() && Auth::user()->isSuperAdmin();
        $owner = $con->getUserInscription($user, Inscription::OWNER);
        $inscriptor = $con->getUserInscription($user, Inscription::INSCRIPTOR);
        if($inscriptor){
            $validateEntry = Entry::where('id', $id)
                ->where('user_id',$user->id)
                ->first();
            if(!$validateEntry) return Response::make('',404);
        }
        else{
            if(!$superadmin && !$owner && !$con->getUserInscription($user, Inscription::COLABORATOR)) {
                return Response::make(Lang::get('Inscription not found'), 404);
            }
        }
        $entry = $con->getEntry($id);

        foreach($entry['files_fields'] as $field){
            foreach($field['files'] as $file){
                if($file['status'] == ContestFile::ERROR
                    || $file['status'] == ContestFile::UPLOAD_INTERRUPTED
                    || $file['status'] == ContestFile::CANCELED){
                    $entry->errorInFiles = true;
                }
            }
        }

        $first = $entry->mainMetadata->first();
        if($first) $entry->name = $first->value;

        $entry['owner'] = $owner;
        if($owner || $superadmin) $entry['role'] = Inscription::OWNER;
        elseif($con->getUserInscription($user, Inscription::COLABORATOR)) $entry['role'] = Inscription::COLABORATOR;
        else $entry['role'] = Inscription::INSCRIPTOR;
        $entry['errors'] = $entry->Validate();
        $aux = $con->getUserInscription($user, Inscription::INSCRIPTOR);
        if(isset($aux)) $entry['inscription_type'] = $aux->inscription_type;
        else $entry['inscription_type'] = null;
        if($con->type == Contest::TYPE_TICKET){
                $entry['selectedTickets'] = $entry['categories'];
                /*foreach($entry['categories'] as $cat){
                    $cat->selected = true;
                }*/
        }
        return json_encode($entry, JSON_NUMERIC_CHECK);
    }

    /**
     * @param $contest
     * @return bool|\Illuminate\Http\Response
     */
    public function getCheckMaxEntries($contest){
        /** @var Contest $con */
        $con = $this->getContest($contest);
        return $con->reachedMaxEntries(Auth::user());
    }

    public function getEntryJudge($contest, $id, $code){
        /** @var Contest $con */
        $user = Auth::user();
        $con = $this->getContest($contest);
        $inscription = $con->getUserInscription($user, Inscription::JUDGE);
        /** @var VotingSession $votingSession */
        $votingSession = VotingSession::where('code', $code)->firstOrFail();
        /** @var VotingUser $voteUser */
        $voteUser = VotingUser::where('inscription_id', $inscription['id'])->where('voting_session_id', $votingSession->id)->firstOrFail();
        $voteCategories = [];
        if(count($votingSession->votingCategories) > 0) {
            foreach ($votingSession->votingCategories as $cat) {
                array_push($voteCategories, $cat->category_id);
            }
            $categories = EntryCategory::where('entry_id', $id)->select('category_id')->get();
            $values = [];
            foreach ($categories as $cat) {
                array_push($values, $cat['category_id']);
            }
            $entryCategoryVoteSession = VotingCategory::where('voting_session_id', $votingSession->id)->whereIn('category_id', $values)->get();

            if (sizeof($entryCategoryVoteSession) == 0) {
                return Response::make(Lang::get('Entry not in vote session'), 404);
            }
        }
        unset($votingSession->votingCategories);

        $superadmin = Auth::check() && Auth::user()->isSuperAdmin();
        $owner = $con->getUserInscription($user, Inscription::OWNER);
        if(!$superadmin && !$inscription && !$superadmin && !$owner) {
            return Response::make(Lang::get('Inscription not found'), 404);
        }

        $entry = $con->getJudgeEntry($id, $voteCategories, $votingSession, $inscription);
        $userAbstains = UserAutoAbstain::where('voting_user_id', $voteUser->id)
            ->where('voting_session_id', $votingSession->id)->get();

        $entry['votes'] = $entry->getJudgeVotes($voteUser, null, $voteCategories);
        if(count($userAbstains) > 0) {
            $entryWithFieldsValues = Entry::where('id', $entry->id)->with('EntryMetadataValuesWithFields')->get();
            //return Response::json(array('$userAbstains'=>$userAbstains, '$entrywithFieldsValues'=>$entryWithFieldsValues));
            foreach($userAbstains as $abstains){
                foreach($entryWithFieldsValues[0]['entry_metadata_values_with_fields'] as $fieldsWithValues){
                    if($fieldsWithValues['entry_metadata_field_id'] == $abstains['entry_metadata_field_id'] && $fieldsWithValues['value'] == $abstains['value']){
                        $entry['votes'] = 'abstain';
                        break 2;
                    }
                }
            }
        }

        $entry['role'] = Inscription::JUDGE;
        $entry->votingSession = $votingSession;
        $entry->name = $entry->getName();

        return json_encode($entry, JSON_NUMERIC_CHECK);
    }

    public function postEntryCategoryVote($contest){
        $code = Input::get('id');
        $votes = Input::get('vote');
        $entryId = Input::get('entryId');
        $votingUserPublic = Input::get('votingUserPublic');
        $user = Auth::user();
        $con = $this->getContest($contest);
        if($votingUserPublic) $inscription = Inscription::where('id', $votingUserPublic)->first();
        else $inscription = $con->getUserInscription($user, Inscription::JUDGE);
        /** @var VotingSession $votingSession */
        $votingSession = VotingSession::where('code', $code)->firstOrFail();
        /** @var VotingUser $votingUser */
        $votingUser = VotingUser::where('inscription_id', $inscription->id)->where('voting_session_id', $votingSession->id)->firstOrFail();
        switch($votingSession->vote_type){
            case VotingSession::METAL :
                foreach ($votes as $keyCat => $data) {
                    $entryCategory = EntryCategory::where('entry_id', $entryId)
                        ->where('category_id', $keyCat)
                        ->firstOrFail();
                    if ($data) {
                        Vote::where('voting_session_id', $votingSession['id'])
                            ->where('voting_user_id', $votingUser->id)
                            ->where('entry_category_id', $entryCategory->id)
                            ->delete();
                    }
                    $voteConfig = $votingSession->getVoteConfig();
                    if (isset($data['vote'])) {
                        $id = isset($data['vote']['id']) ? $data['vote']['id'] : null;
                        foreach($voteConfig['extra'] as $vExtra){
                            if($data['vote']['name'] == $vExtra['name']){
                                $id = $vExtra['id'];
                                $vote_float = $vExtra['score'];
                                $countPerCategory = $vExtra['countPerCategory'];
                            }
                        }
                        if($id !== null) {
                            $vote = Vote::firstOrNew(
                                array(
                                    'voting_session_id' => $votingSession->id,
                                    'voting_user_id' => $votingUser->id,
                                    'entry_category_id' => $entryCategory->id,
                                    'type' => Vote::TYPE_METAL,
                                    'vote' => $id,
                                    'vote_float' => $vote_float
                                )
                            );
                        }

                        $vote->save();
                    }
                }
                break;
            case VotingSession::YESNO :
                $countVotes = 0;
                foreach ($votes as $keyCat => $data) {
                    $responseCat = $keyCat;
                    $responseVote = isset($data['vote']) ? $data['vote'] : null;
                    /** @var EntryCategory $entryCategory */
                    $entryCategory = EntryCategory::where('entry_id', $entryId)
                        ->where('category_id', $keyCat)
                        ->firstOrFail();
                    $config = json_decode($votingSession['config']);
                    isset($config->yesPerCategory) ? $config->yesPerCategory : $config->yesPerCategory = 0;
                    if(!$config->yesPerCategory) $config->yesPerCategory = 0;
                    $voteCategory = VotingCategory::where("category_id", "=", $keyCat)
                        ->where("voting_session_id","=", $votingSession->id)->first();
                    if($voteCategory){
                        if($voteCategory->vote_config != null){
                            $voteCatConfig = json_decode($voteCategory->vote_config, true);
                            if(isset($voteCatConfig["yesPerCategory"])){
                                $config->yesPerCategory = intval($voteCatConfig["yesPerCategory"]);
                            }
                        }
                    }
                    switch($config->yesPerCategory){
                        case 0:
                            $vote = Vote::firstOrNew(array(
                            'voting_session_id' => $votingSession->id,
                            'voting_user_id' => $votingUser->id,
                            'entry_category_id' => $entryCategory->id,
                            'type' => Vote::TYPE_YESNO,
                            'criteria' => 0
                            ));
                            if(!isset($data['vote']) || (empty($data['vote']) && $data['vote'] !== 0))
                                $vote->vote_float = null;
                            else $vote->vote_float = json_encode($data['vote']);
                            $vote->save();
                            break;
                        default:
                            $entries = $con->getJudgeEntries($votingSession->code, $inscription);
                            $entriesIds = [];
                            foreach($entries['entries'] as $entry){
                                array_push($entriesIds, $entry->id);
                            }
                            $entryCategoriesIds = EntryCategory::whereIn('entry_id', $entriesIds)
                                ->where('category_id', $keyCat)
                                ->lists('id');
                            if($config->yesPerCategory == 1){
                                    foreach($entryCategoriesIds as $entryCatId){
                                        $vote = Vote::firstOrNew(array(
                                            'voting_session_id' => $votingSession->id,
                                            'voting_user_id' => $votingUser->id,
                                            'entry_category_id' => $entryCatId,
                                            'type' => Vote::TYPE_YESNO,
                                            'criteria' => 0
                                        ));
                                        if($entryCatId == $entryCategory->id){
                                            if(!isset($data['vote']) || (empty($data['vote']) && $data['vote'] !== 0))
                                                $vote->vote_float = null;
                                            else $vote->vote_float = json_encode($data['vote']);
                                        }
                                        else{
                                            if(!isset($data['vote']) || (empty($data['vote']) && $data['vote'] !== 0))
                                                $vote->vote_float = null;
                                            else $vote->vote_float = 0;
                                        }
                                        $vote->save();
                                    }
                                $countVotes = 1;
                            }
                            /*** MAS DE 1 SI POR CATEGORIA ***/
                            if($config->yesPerCategory > 1){
                                foreach($entryCategoriesIds as $entryCatId){
                                    /* Cuento la cantidad de SI que ya tiene la categoria */
                                    $votesAux = Vote::where('voting_session_id', $votingSession->id)
                                        ->where('voting_user_id', $votingUser->id)
                                        ->where('vote_float', 1)
                                        ->lists('entry_category_id');

                                    $countVotes = EntryCategory::whereIn('id', $votesAux)
                                        ->where('category_id', $keyCat)
                                        ->count();

                                    $vote = Vote::firstOrNew(array(
                                        'voting_session_id' => $votingSession->id,
                                        'voting_user_id' => $votingUser->id,
                                        'entry_category_id' => $entryCatId,
                                        'type' => Vote::TYPE_YESNO,
                                        'criteria' => 0
                                    ));
                                    if($entryCatId == $entryCategory->id){
                                        if(!isset($data['vote']) || (empty($data['vote']) && $data['vote'] !== 0)){
                                            /*$vote->vote_float = 0;
                                            $vote->save();*/
                                            $vote->delete();
                                        }
                                        else if($countVotes < $config->yesPerCategory){
                                            $vote->vote_float = json_encode($data['vote']);
                                            $vote->save();
                                        }
                                        else if($countVotes == $config->yesPerCategory){
                                            $vote->vote_float = 0;
                                            $vote->save();
                                        }
                                    }
                                }
                                if($countVotes == $config->yesPerCategory){
                                    foreach($entryCategoriesIds as $CatIds){
                                        $noVotes = Vote::firstOrNew(array(
                                            'voting_session_id' => $votingSession->id,
                                            'voting_user_id' => $votingUser->id,
                                            'entry_category_id' => $CatIds,
                                            'type' => Vote::TYPE_YESNO,
                                            'criteria' => 0
                                        ));
                                        if($noVotes->vote_float != 1){
                                            $noVotes->vote_float = 0;
                                            $noVotes->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                $votingUser->last_seen_at = date("Y/m/d H:i:s");
                $votingUser->save();
                $votingSession->loadJudgeProgress($votingUser);
                if($countVotes == $config->yesPerCategory){
                    $entries = $con->getJudgeEntries($votingSession->code, $inscription);
                    $data = array(
                        'entries' => $entries['entries'],
                        'catId' => $responseCat,
                        'vote' => $responseVote,
                        'countVotes' => $countVotes,
                        'votingUser'=>$votingUser
                    );
                }else{
                    $data = array(
                        'catId' => $responseCat,
                        'vote' => $responseVote,
                        'countVotes' => $countVotes,
                        'votingUser'=>$votingUser
                    );
                }
                return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
                break;
            case VotingSession::AVERAGE :
            case VotingSession::VERITRON :
                foreach ($votes as $keyCat => $data) {
                /** @var EntryCategory $entryCategory */
                $entryCategory = EntryCategory::where('entry_id', $entryId)
                    ->where('category_id', $keyCat)
                    ->firstOrFail();
                $config = json_decode($votingSession['config']);
                if ($data) {
                    Vote::where('voting_session_id', $votingSession['id'])
                        ->where('voting_user_id', $votingUser->id)
                        ->where('entry_category_id', $entryCategory->id)
                        ->delete();
                    if (isset($data['abstain'])) {
                        if ($data['abstain'] === true) {
                            $vote = Vote::firstOrNew(array(
                                'voting_session_id' => $votingSession->id,
                                'voting_user_id' => $votingUser->id,
                                'entry_category_id' => $entryCategory->id,
                                'type' => Vote::TYPE_SCORE
                            ));
                            $vote->vote_float = null;
                            $vote->vote = null;
                            $vote->abstain = true;
                            $vote->save();

                            if (isset($config->extra) && count($config->extra)) {
                                if (!isset($data['extra'])) continue;
                                foreach ($data['extra'] as $key => $value) {
                                    if (!isset($config->extra[$key])) continue;
                                    $extra = Vote::firstOrNew(array(
                                        'voting_session_id' => $votingSession->id,
                                        'voting_user_id' => $votingUser->id,
                                        'entry_category_id' => $entryCategory->id,
                                        'type' => Vote::TYPE_EXTRA,
                                        'criteria' => $key
                                    ));
                                    switch ($config->extra[$key]->type) {
                                        case Vote::EXTRA_CHECKBOX:
                                            $extra->vote_float = $value;
                                            if ($extra->vote_float) $extra->save();
                                            break;
                                        default:
                                            $extra->vote = trim($value);
                                            if ($extra->vote != "") $extra->save();
                                            return Response::json(['voting_session_id' => $votingSession->id,
                                        'voting_user_id' => $votingUser->id,
                                        'entry_category_id' => $entryCategory->id]);
                                            break;
                                    }
                                }
                            }

                            continue;
                        }
                    }
                    if (isset($config->usecriteria) && $config->usecriteria === true) {
                        if (isset($data['vote'])) {
                            foreach ($data['vote'] as $key => $value) {
                                if (!isset($config->criteria[$key])) continue;
                                $vote = Vote::firstOrNew(array(
                                    'voting_session_id' => $votingSession->id,
                                    'voting_user_id' => $votingUser->id,
                                    'entry_category_id' => $entryCategory->id,
                                    'type' => Vote::TYPE_SCORE,
                                    'criteria' => $key
                                ));
                                $vote->vote_float = $value;
                                $vote->save();
                            }
                        }
                    } else {
                        if(isset($data['vote']) && $data['vote'] !== null) {
                            $vote = Vote::firstOrNew(array(
                                'voting_session_id' => $votingSession->id,
                                'voting_user_id' => $votingUser->id,
                                'entry_category_id' => $entryCategory->id,
                                'type' => Vote::TYPE_SCORE,
                                'criteria' => 0
                            ));
                            $vote->vote_float = is_array($data['vote']) ? $data['vote'][0] : $data['vote'];
                            $vote->save();
                        }
                    }
                    if (isset($config->extra) && count($config->extra)) {
                        if (!isset($data['extra'])) continue;
                        foreach ($data['extra'] as $key => $value) {
                            if (!isset($config->extra[$key])) continue;
                            $extra = Vote::firstOrNew(array(
                                'voting_session_id' => $votingSession->id,
                                'voting_user_id' => $votingUser->id,
                                'entry_category_id' => $entryCategory->id,
                                'type' => Vote::TYPE_EXTRA,
                                'criteria' => $key
                            ));
                            switch ($config->extra[$key]->type) {
                                case Vote::EXTRA_CHECKBOX:
                                    $extra->vote_float = $value;
                                    if ($extra->vote_float) $extra->save();
                                    break;
                                default:
                                    $extra->vote = trim($value);
                                    if ($extra->vote != "") $extra->save();
                                    break;
                            }
                        }
                    }
                }
            }
            $votingUser->last_seen_at = date("Y/m/d H:i:s");
            $votingUser->save();
            break;
        }
        /** @var Entry $entry */
        $entry = Entry::where('id','=',$entryId)->where('contest_id','=',$con->id)->firstOrFail();
        $voteCategories = [];
        if(count($votingSession->votingCategories) > 0) {
            foreach ($votingSession->votingCategories as $cat) {
                array_push($voteCategories, $cat->category_id);
            }
        }
        $votingSession->loadJudgeProgress($votingUser);
        return Response::json(['votingUser'=>$votingUser, 'votes'=>$entry->getJudgeVotes($votingUser, $votingSession, $voteCategories)],200,[], JSON_NUMERIC_CHECK);
    }

    public function getEntryCategory($contest, $id){
        $user = Auth::user();
        $con = $this->getContest($contest);
        $superadmin = Auth::check() && Auth::user()->isSuperAdmin();
        $entry = new Entry();
        $owner = $con->getUserInscription($user, Inscription::OWNER);
        if($superadmin || $owner){
            $entry['selected_cat'] = $id;
            $entry['role'] = Inscription::OWNER;
            $entry['cat'] = $id;
            return json_encode($entry, JSON_NUMERIC_CHECK);
        }
        else{
            if (!$con->getUserInscription($user, Inscription::INSCRIPTOR)) {
                return Response::make(Lang::get('Inscription not found'), 404);
            }
            /*$categories_id = Array();
            array_push($categories_id,$id);*/
            $entry['selected_cat'] = $id;
            $entry['role'] = Inscription::INSCRIPTOR;
            $entry['cat'] = $id;
            return json_encode($entry, JSON_NUMERIC_CHECK);
        }
    }

    public function getMetadataFields($contest){
        $con = $this->getContest($contest);
        $metadataFields = EntryMetadataField::with('EntryMetadataConfigTemplate')->where('contest_id', '=', $con->id)->orderBy('order')->get();
        return json_encode($metadataFields, JSON_NUMERIC_CHECK);
    }

    public function getJudgeMetadataFields($contest){
        $con = $this->getContest($contest);
        $metadataFields = EntryMetadataField::with('EntryMetadataConfigTemplate')
            ->where('private', 0)
            ->where('contest_id', '=', $con->id)
            ->orderBy('order')
            ->get();
        return json_encode($metadataFields, JSON_NUMERIC_CHECK);
    }

    public function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function postEntry($contest){
        /** @var EntryMetadataField $cMeta */
        $con = $this->getContest($contest, "entry");
        //Chequear stage del contest para ver si se puede hacer la inscripción
        if(!Auth::check()){
            App::abort(404, Lang::get('login.loginRequired'));
        }
        $user = Auth::user();
        $rules = [];
        $metadataInput = Input::only('id','user','metadata','categories', 'columnsAndLabels');

        /** @var Entry $entry */
        if(isset($metadataInput['id'])) {
            if($user->isSuperAdmin() || $con->getUserInscription($user, Inscription::OWNER) || $con->getUserInscription($user, Inscription::COLABORATOR)){
                $entry = Entry::where('id','=',$metadataInput['id'])->where('contest_id','=',$con->id)->firstOrFail();
            }
            elseif($con->getUserInscription($user, Inscription::INSCRIPTOR))
            {
                $entry = Entry::where('id','=',$metadataInput['id'])->where('contest_id','=',$con->id)->where('user_id','=',$user->id)->firstOrFail();
            }
        }else{
            if($con->reachedMaxEntries($user)){
                return Response::json(array('error' => Lang::get('contest.reachedMaxEntries', ["number"=>$con->max_entries])));
            }
            $entry = new Entry();
            if($user->isSuperAdmin() || $con->getUserInscription($user, Inscription::OWNER) || $con->getUserInscription($user, Inscription::COLABORATOR)){
                //TODO Chequear que tenga inscripción al contest
                /** @var User $cUser */
                if(!$metadataInput['user']){
                    return Response::json(array('errors'=>array('user'=>Lang::get('user.notFound'))));
                }
                $cUser = User::where('email', $metadataInput['user']['email'])->first();
                if(!$cUser){
                    return Response::json(array('errors'=>array('user'=>Lang::get('user.notFound'))));
                }
                $entry->user_id = $cUser->id;
            }else{
                $entry->user_id = $user->id;
            }
            $entry->contest_id = $con->id;
            $entry->save();
        }

        $templatesIds = [];
        $defaultTemplate = false;
        foreach($metadataInput['categories'] as $categoryId) {
            /** @var Category $cat */
            $cat = Category::where('id',$categoryId)->where('contest_id', $con->id)->firstOrFail();
            if(!in_array($cat->template_id, $templatesIds)) array_push($templatesIds, $cat->template_id);
            if($cat->template_id == null) $defaultTemplate = true;
        }
        $metadataFields = [];
        $niceNames = [];
        $messages = [];

        foreach ($metadataInput['metadata'] as $iMetadata) {
            /*print_r($iMetadata);
            echo "<br>";*/
            /** @var EntryMetadataField $metadata */
            $metadata = EntryMetadataField::with('EntryMetadataConfigTemplate')->where('id', '=', $iMetadata['entry_metadata_field_id'])->where('contest_id', '=', $con->id)->firstOrFail();
            $required = $defaultTemplate && $metadata->required;
            $visible = $defaultTemplate;
            foreach($metadata->EntryMetadataConfigTemplate as $mdConfig){
                if(!in_array($mdConfig->template_id, $templatesIds)) continue;
                if($mdConfig->visible && !$visible) $visible = true;
                if($mdConfig->required) $required = true;
            }
            if(!$visible) continue;
            $mdRules = [];
            if ($required) {
                $mdRules[] = "required";
            }
            switch ($metadata->type) {
                case MetadataField::DATE:
                    $mdRules[] = "date";
                    break;
                case MetadataField::EMAIL:
                    $mdRules[] = "email";
                    break;
                case MetadataField::FILE:
                    $mdRules = [];
                    if($required) $mdRules[] = "numeric|between:1,10000";
                    $messages[$metadata->id.'.between'] = Lang::get('metadata.filerequired');
                    break;
                case MetadataField::TITLE:
                case MetadataField::DESCRIPTION:
                case MetadataField::TAB:
                case MetadataField::LINK:
                    //case MetadataField::RICHTEXT:
                    $mdRules = [];
                    break;
            }
            if(count($mdRules) == 0) continue;

            $rules[$metadata->id] = implode('|', $mdRules);
            if($metadata->type == MetadataField::FILE){
                //if(isset($metadataValue)) {echo (count($metadataValue->files)); }else{ echo 0; }
                $metadataFields[$metadata->id] = isset($iMetadata['files']) ? count($iMetadata['files']) : 0;
            }else{
                $metadataFields[$metadata->id] = isset($iMetadata['value']) ? $iMetadata['value'] : null;
            }
            $niceNames[$metadata->id] = $metadata->label;
        }

        $validator = Validator::make($metadataFields, $rules, $messages);
        $validator->setAttributeNames($niceNames);

        $validatorFailed = $validator->fails();

        if($entry->status == Entry::FINALIZE || $entry->status == Entry::APPROVE || $entry->status == Entry::ERROR ) {
            if ($validatorFailed) {
                $messages = $validator->messages();
                return Response::json(array('errors' => $messages));
            }
        }else{
            $entry->status = $validatorFailed ? Entry::INCOMPLETE : Entry::COMPLETE;
            $entry->save();
        }

        /** @var EntryCategory[] $currentCategories */
        $currentCategories = $entry->entryCategories;
        $savedCats = [];
        foreach($metadataInput['categories'] as $categoryId) {
            $entryCategory = EntryCategory::firstOrCreate(['entry_id'=>$entry->id,'category_id'=>$categoryId]);
            $entryCategory->entry_id = $entry->id;
            $entryCategory->category_id = $categoryId;
            $savedCats[] = $categoryId;
            $entryCategory->save();
        }
        foreach($currentCategories as $savedCat){
            if(!in_array($savedCat->category_id, $savedCats)){
                $entryCategoryPayment = BillingEntryCategory::where('category_id', '=', $savedCat->category_id)
                    ->where('entry_id', '=', $entry->id)->first();
                if($entryCategoryPayment){
                    /* TODO No debe borrar el billing directamente, esto funciona sólo
                    cuando puede haber 1 entry por categoría, y se paga 1 sólo entry */
                    if($entryCategoryPayment->billing != null) $entryCategoryPayment->billing->delete();
                    $entryCategoryPayment->delete();
                }
                $savedCat->delete();
            }
        }

        foreach($metadataInput['metadata'] as $iMetadata){
            $metadataRecord = null;
            $type = EntryMetadataField::where('id', $iMetadata['entry_metadata_field_id'])->select('type')->first();

            switch($type['type']){
                case MetadataField::MULTIPLEWITHCOLUMNS:
                    if (isset($metadataInput['columnsAndLabels'])) {
                        if (isset($metadataInput['columnsAndLabels'][$iMetadata['entry_metadata_field_id']])) {
                            EntryMetadataValue::where('entry_id', $entry->id)->where('entry_metadata_field_id', $iMetadata['entry_metadata_field_id'])->delete();
                            foreach ($metadataInput['columnsAndLabels'][$iMetadata['entry_metadata_field_id']] as $label => $values) {
                                $noValues = false;
                                if (!isset($values[0])) {
                                    foreach($values as $item => $keys){
                                        if($keys) $noValues = true;
                                    }
                                    if($noValues){
                                        $value = json_encode(['label' => $label, 'value' => $values]);
                                        $metadataRecord = new EntryMetadataValue();
                                        $metadataRecord->entry_id = $entry->id;
                                        $metadataRecord->entry_metadata_field_id = $iMetadata['entry_metadata_field_id'];
                                        $metadataRecord->value = $value;
                                        $metadataRecord->save();
                                    }
                                }
                                if($values != null && isset($values[0])){
                                    $value = json_encode(['label' => $label, 'value' => $values]);
                                    $metadataRecord = new EntryMetadataValue();
                                    $metadataRecord->entry_id = $entry->id;
                                    $metadataRecord->entry_metadata_field_id = $iMetadata['entry_metadata_field_id'];
                                    $metadataRecord->value = $value;
                                    $metadataRecord->save();
                                }
                            }
                        }
                    }
                break;
                case MetadataField::MULTIPLE:
                    /*"Multiple"*/
                    if(isset($iMetadata['value']) && is_array($iMetadata['value'])){
                        EntryMetadataValue::where('entry_id',$entry->id)->where('entry_metadata_field_id',$iMetadata['entry_metadata_field_id'])->delete();
                        if(count($iMetadata['value']) > 0) {
                            $metadataRecord = new EntryMetadataValue();
                            $metadataRecord->entry_id = $entry->id;
                            $metadataRecord->entry_metadata_field_id = $iMetadata['entry_metadata_field_id'];
                            $metadataRecord->value = json_encode($iMetadata['value']);
                            $metadataRecord->save();
                        }
                    }
                break;
                case MetadataField::FILE:
                    $metadataRecord = null;
                    if (isset($iMetadata['id']))
                        $metadataRecord = EntryMetadataValue::where('id', '=', $iMetadata['id'])->where('entry_id', '=', $entry->id)->where('entry_metadata_field_id', '=', $iMetadata['entry_metadata_field_id'])->first();
                    if (!$metadataRecord) {
                        $metadataRecord = new EntryMetadataValue();
                        $metadataRecord->entry_id = $entry->id;
                        $metadataRecord->entry_metadata_field_id = $iMetadata['entry_metadata_field_id'];
                        $metadataRecord->save();
                    }
                    if(count($iMetadata['files']) > 0) {
                        /** @var EntryMetadataFile[] $cFiles */
                        $cFiles = $metadataRecord->EntryMetadataFiles;
                        foreach ($cFiles as $cFile) {
                            $cFile->delete();
                        }

                        $i = 0;
                        foreach ($iMetadata['files'] as $file) {
                            $conFile = ContestFile::where('code','=',$file['code'])->first();
                            if (!$conFile) continue;
                            $metadataFile = new EntryMetadataFile();
                            $metadataFile->entry_metadata_value_id = $metadataRecord->id;
                            $metadataFile->contest_file_id = $conFile->id;
                            $metadataFile->order = $i;
                            $metadataFile->save();
                            $i++;
                        }
                    }else{
                        $metadataRecord->delete();
                    }
                break;
                case MetadataField::DATE:
                    if(!is_array($iMetadata['value']) && $type['type'] == MetadataField::DATE) {
                        if (isset($iMetadata['value']) && !$this->validateDate($iMetadata['value'])) {
                            $iMetadata['value'] = null;
                        }
                    }
                    $metadataRecord = EntryMetadataValue::where('id','=',$iMetadata['id'])
                        ->where('entry_id','=',$entry->id)
                        ->where('entry_metadata_field_id','=',$iMetadata['entry_metadata_field_id'])
                        ->first();
                    if($metadataRecord){
                        $metadataRecord->value = $iMetadata['value'];
                        $metadataRecord->save();
                    }else{
                        $metadataRecord = new EntryMetadataValue();
                        $metadataRecord->entry_id = $entry->id;
                        $metadataRecord->entry_metadata_field_id = $iMetadata['entry_metadata_field_id'];
                        $metadataRecord->value = $iMetadata['value'];
                        $metadataRecord->save();
                    }
                break;
                case MetadataField::SELECT:
                    $metadataRecord = EntryMetadataValue::where('id','=',$iMetadata['id'])->where('entry_id','=',$entry->id)->where('entry_metadata_field_id','=',$iMetadata['entry_metadata_field_id'])->first();
                    if($metadataRecord){
                        $metadataRecord->value = trim($iMetadata['value']);
                        $metadataRecord->save();
                    }elseif($iMetadata['value']){
                        $metadataRecord = new EntryMetadataValue();
                        $metadataRecord->entry_id = $entry->id;
                        $metadataRecord->entry_metadata_field_id = $iMetadata['entry_metadata_field_id'];
                        $metadataRecord->value = trim($iMetadata['value']);
                        $metadataRecord->save();
                    }
                break;
                case MetadataField::TEXT:
                    $iMetadata['value'] = preg_replace("/<[^>]*?(\/?)([a-z][a-z0-9]*)[^>]*?(\/?)>/i", '', $iMetadata['value']);
                case MetadataField::RICHTEXT:
                    if($type['type'] == MetadataField::RICHTEXT)
                        $iMetadata['value'] = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i", '<$1$2>', $iMetadata['value']);
                default:
                    if(isset($iMetadata['id'])){
                        $metadataRecord = EntryMetadataValue::where('id','=',$iMetadata['id'])->where('entry_id','=',$entry->id)->where('entry_metadata_field_id','=',$iMetadata['entry_metadata_field_id'])->first();
                        if($metadataRecord){
                            /*"udpate"*/
                            if(is_array($iMetadata['value'])) {
                                foreach ($iMetadata['value'] as $value) {
                                    $metadataRecord->value = $value;
                                    $metadataRecord->save();
                                }
                            }else{
                                $metadataRecord->value = $iMetadata['value'];
                                $metadataRecord->save();
                            }
                        }
                    }else{
                        /*"nuevo campo"*/
                        if(is_array($iMetadata['value'])){
                            foreach($iMetadata['value'] as $value){
                                $metadataRecord = new EntryMetadataValue();
                                $metadataRecord->entry_id = $entry->id;
                                $metadataRecord->entry_metadata_field_id = $iMetadata['entry_metadata_field_id'];
                                $metadataRecord->value = $value;
                                $metadataRecord->save();
                            }
                        }else{
                            $metadataRecord = new EntryMetadataValue();
                            $metadataRecord->entry_id = $entry->id;
                            $metadataRecord->entry_metadata_field_id = $iMetadata['entry_metadata_field_id'];
                            $metadataRecord->value = $iMetadata['value'];
                            $metadataRecord->save();
                        }
                    }
                break;
            }
        }

        $isNew = EntryLog::where('entry_id', $entry->id)->first();
        if($entry->status == Entry::INCOMPLETE && !$isNew){
            $entryLog = new EntryLog();
            $entryLog['user_id'] = $user->id;
            $entryLog['entry_id'] = $entry->id;
            $entryLog['msg'] = Lang::get('contest.creation');
            $entryLog['status'] = Entry::INCOMPLETE;
            $entryLog->save();

            $owners_ids = Inscription::where('contest_id', $con->id)->whereIn('role', array(Inscription::OWNER, Inscription::COLABORATOR))->lists('user_id');
            /** @var User[] $owners */
            $owners = User::whereIn('id', $owners_ids)->get();
            $link = url($con->code.'#/entry/'.$entry->id);
            foreach($owners as $owner){
                if(!$owner->canReceiveNotification(User::NotificationNewEntry)) continue;
                $subject = Lang::get('contest.entryNew', ["contest"=>$con->name, "entry"=>$entry->id]);
                $response = OxoMailer::sendMail([
                    'email_to' => $owner->email,
                    'subject' => $subject,
                    'body' => Lang::get('contest.entryNewBody', ["entry"=>$entry->id,"link"=>$link, "user"=>$user->email])
                ]);
            }
        }

        if($con->getUserInscription($user, Inscription::INSCRIPTOR)){
            $entry = $con->getUserEntry($user, $entry->id);
        }elseif($user->isSuperAdmin() || $con->getUserInscription($user, Inscription::OWNER) || $con->getUserInscription($user, Inscription::COLABORATOR)){
            $entry = $con->getEntry($entry->id);
        }
        $first = $entry->mainMetadata->first();
        if($first) $entry->name = $first->value;
        $entry->metadataInput = $metadataInput['metadata'];
        $entry['errors'] = $entry->Validate();
        return Response::json(array('flash'=>Lang::get('contest.entrySaved'), 'entry' => $entry, 'isNew' => $isNew, 'metadataFields'=>$metadataFields, 'validatorFailed'=>$validatorFailed, 'templatesIds'=>$templatesIds, 'rules'=>$rules), 200, [], JSON_NUMERIC_CHECK);
    }

    public function getInscriptionData($contest){
        /** @var Contest $con */
        $con = $this->getContest($contest);
        $roles = Inscription::getAllRoles();
        unset($roles[Inscription::OWNER]);
        $data = array(
            'roles' => $roles,
            'inscriptionTypes' => $con->inscriptionTypes,
            'inscriptionMetadata' => $con->InscriptionMetadataFields,
        );
        return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
    }

    public function postInscriptionData($contest){
        /** @var Contest $con */
        $con = $this->getContest($contest);

        /* Inscription Types save */
        $currentTypes = $con->inscriptionTypes;
        $types = Input::get('inscriptionTypes');
        //TODO Hacer validación
        $savedIds = [];
        $rolesTypes = [];
        $newTypes = [];
        $messages = [];
        foreach($types as $typeData){
            $rules = array(
                'name' => 'required',
                'start_at' => 'date|date_format:"Y-m-d H:i:s"',
                'deadline1_at' => 'date|date_format:"Y-m-d H:i:s"',
                'deadline2_at' => 'date|date_format:"Y-m-d H:i:s"',
            );
            $niceNames = array(
                'name' => Lang::get('general.name')
            );
            $validator = Validator::make($typeData, $rules);
            $validator->setAttributeNames($niceNames);
            if ($validator->fails())
            {
                $errors = $validator->messages();
                //$messages[$fieldData['errMsg']] = $errors;
                if(!isset($typeData['errMsg'])) $typeData['errMsg'] = "new0.".random_int(100, 9999);
                foreach(json_decode($errors) as $error){
                    foreach($error as $err){
                        $messages[$typeData['errMsg']] = isset($messages[$typeData['errMsg']]) ? $messages[$typeData['errMsg']]."  *".$err : "*".$err;
                    }
                }
                return Response::json(['errors'=>$messages, 'flash' => Lang::get('contest.inscriptionDataErrorTemplate')], 400, [], JSON_NUMERIC_CHECK);
                //return Response::json(['errors2'=>$messages, 'flash' => Lang::get('contest.inscriptionDataErrorForm')], 400, [], JSON_NUMERIC_CHECK);
                continue;
            }

            if(isset($typeData['id']) && is_int($typeData['id'])) {
                $savedIds[] = $typeData['id'];
                /** @var InscriptionType $type */
                $type = InscriptionType::find($typeData['id']);
                if ($type->contest_id != $con->id){
                    continue;
                }
            }else{
                $type = new InscriptionType();
                $type->contest_id = $con->id;
            }
            $type->name = $typeData['name'];
            $type->role = $typeData['role'];
            $type->public = isset($typeData['public']) ? $typeData['public'] == 1 : false;
            $type->start_at = isset($typeData['start_at']) ? $typeData['start_at'] : null;
            $type->deadline1_at = isset($typeData['deadline1_at']) ? $typeData['deadline1_at'] : null;
            $type->deadline2_at = isset($typeData['deadline2_at']) ? $typeData['deadline2_at'] : null;
            $type->trans = isset($typeData['trans']) ? json_encode($typeData['trans']) : null;
            if(!isset($rolesTypes[$typeData['role']])) $rolesTypes[$typeData['role']] = [];
            $type->save();
            if(isset($typeData['id'])) $newTypes[$typeData['id']] = $type->id;
            CategoryConfigType::where('inscription_type_id', '=', $type->id)->delete();
            if(isset($typeData['category_config_type'])){
                //foreach($fieldData['configs'] as $ind => $conf){
                foreach($typeData['category_config_type'] as $catId){
                    /** @var InscriptionMetadataConfigType $mdConfig */
                    $mdConfig = CategoryConfigType::firstOrNew(['inscription_type_id' => $type->id, 'category_id' => $catId]);
                    $mdConfig->save();
                }
            }
            array_push($rolesTypes[$typeData['role']], $type);
        }

        foreach($currentTypes as $cType){
            if(!in_array($cType->id, $savedIds)){
                $cType->delete();
            }
        }


        /* Inscription EntryMetadataValue save */
        $currentMD = $con->InscriptionMetadataFields;
        $metadata = Input::get('inscriptionMetadata');
        //TODO Hacer validación
        $savedIds = [];
        $order = 0;

        foreach($metadata as $fieldData){
            $rules = array(
                'label' => 'required',
                'type' => 'required',
            );
            $niceNames = array(
                'label' => Lang::get('metadata.label'),
                'type' => Lang::get('metadata.type')
            );
            $validator = Validator::make($fieldData, $rules);
            $validator->setAttributeNames($niceNames);
            if ($validator->fails())
            {
                $errors = $validator->messages();
                if(!isset($fieldData['errMsg'])) $fieldData['errMsg'] = random_int(100, 999);
                foreach(json_decode($errors) as $error){
                    foreach($error as $err){
                        $messages[$fieldData['errMsg']] = isset($messages[$fieldData['errMsg']]) ? $messages[$fieldData['errMsg']]."  *".$err : "*".$err;
                    }
                }
                return Response::json(['errors2'=>$messages, 'flash' => Lang::get('contest.inscriptionDataErrorForm')], 400, [], JSON_NUMERIC_CHECK);
                //return Response::json(['errors2'=>$messages, 'flash' => Lang::get('contest.inscriptionDataErrorForm')], 400, [], JSON_NUMERIC_CHECK);
                continue;
            }
            if(isset($fieldData['id'])) {
                $savedIds[] = $fieldData['id'];
                /** @var InscriptionMetadataField $field */
                $field = InscriptionMetadataField::find($fieldData['id']);
                if ($field->contest_id != $con->id){
                    continue;
                }
            }else{
                $field = new InscriptionMetadataField();
                $field->contest_id = $con->id;
            }
            $field->type = $fieldData['type'];
            $field->label = $fieldData['label'];
            if(isset($fieldData['description']))
                $field->description = $fieldData['description'];
            $field->trans = isset($fieldData['trans']) ? json_encode($fieldData['trans']) : null;
            $field->role = $fieldData['role'];
            $field->required = isset($fieldData['required']) && $fieldData['required'] == 1;
            $field->config = json_encode([
                'exportable' => isset($fieldData['config']['exportable']) ? $fieldData['config']['exportable'] : null,
                'important' => isset($fieldData['config']['important']) ? $fieldData['config']['important'] : null
            ]);
            switch($field->type){
                case InscriptionMetadataField::SELECT:
                case InscriptionMetadataField::MULTIPLE:
                    $field->config = json_encode([
                        'options' => $fieldData['config']['options'],
                        'exportable' => isset($fieldData['config']['exportable']) ? $fieldData['config']['exportable'] : null,
                        'important' => isset($fieldData['config']['important']) ? $fieldData['config']['important'] : null,
                    ]);
                    break;
                case EntryMetadataField::TITLE:
                    $field->config = json_encode([
                        'options' => isset($fieldData['config']['options']) ? $fieldData['config']['options'] : []
                    ]);
                    break;
                case EntryMetadataField::FILE:
                    $field->config = json_encode([
                        'min' => isset($fieldData['config']['min']) ? $fieldData['config']['min'] : 0,
                        'max' => isset($fieldData['config']['max']) ? $fieldData['config']['max'] : 0,
                        'types' => isset($fieldData['config']['types']) ? $fieldData['config']['types'] : [],
                        'important' => isset($fieldData['config']['important']) ? $fieldData['config']['important'] : null
                    ]);
                    break;
                case EntryMetadataField::MULTIPLEWITHCOLUMNS:
                    $field->config = json_encode([
                        'columns' => isset($fieldData['config']['columns']) ? $fieldData['config']['columns'] : [],
                        'labels' => isset($fieldData['config']['labels']) ? $fieldData['config']['labels'] : [],
                        'text' => isset($fieldData['config']['text']) ? $fieldData['config']['text'] : '',
                        'exportable' => isset($fieldData['config']['exportable']) ? $fieldData['config']['exportable'] : null,
                        'important' => isset($fieldData['config']['important']) ? $fieldData['config']['important'] : null,
                    ]);
                    break;
                case EntryMetadataField::LINK:
                    $field->config = json_encode([
                        'type' => isset($fieldData['config']['type']) ? $fieldData['config']['type'] : [],
                        'link' => isset($fieldData['config']['link']) ? $fieldData['config']['link'] : [],
                        'buttonText' => isset($fieldData['config']['buttonText']) ? $fieldData['config']['buttonText'] : [],
                        'hypText' => isset($fieldData['config']['hypText']) ? $fieldData['config']['hypText'] : [],
                        'important' => isset($fieldData['config']['important']) ? $fieldData['config']['important'] : null,
                    ]);
                    break;
            }
            $field->order = $order;
            $field->save();

            if(isset($fieldData['inscription_metadata_config_types'])){
                foreach($fieldData['inscription_metadata_config_types'] as $typeId => $conf){
                    /** @var InscriptionMetadataConfigType $mdConfig */
                    if(!is_int($typeId)){
                        if(isset($newTypes[$typeId]))
                            $typeId = $newTypes[$typeId];
                    }
                    if($conf != null && $typeId != 'undefined' && $typeId != null){
                        $mdConfig = InscriptionMetadataConfigType::firstOrNew(['inscription_metadata_field_id' => $field->id, 'inscription_type_id' => $typeId]);
                        $mdConfig->visible = isset($conf['visible']) ? $conf['visible'] : false;
                        $mdConfig->required = $mdConfig->visible && isset($conf['required']) ? $conf['required'] : false;
                        $mdConfig->save();
                   }
                }
            }
            $order++;
        }
        $deleteds = [];
        $currents = [];

        foreach($currentMD as $cMD){
            array_push($currents, $cMD->id);
            if(!in_array($cMD->id, $savedIds)){
                array_push($deleteds, $cMD->id);
                $cMD->delete();
            }
        }

        if(isset($con->wizard_status) && $con->wizard_status < Contest::WIZARD_FINISHED){
            $wizardHasInscriptions = Input::get('wizardHasInscriptions');
            $wizard_config = json_decode($con->wizard_config) ? json_decode($con->wizard_config) : (object)[];
            $wizard_config->inscriptions = $wizardHasInscriptions == true ? 1 : 0;;
            Contest::where('id', $con->id)->update(['wizard_status' => Contest::WIZARD_ENTRY_FORM, 'wizard_config' => json_encode($wizard_config)]);
        }
        $con = $this->getContest($contest);
        $roles = Inscription::getAllRoles();
        unset($roles[Inscription::OWNER]);
        $data = array(
            'roles' => $roles,
            'inscriptionTypes' => $con->inscriptionTypes,
            'inscriptionMetadata' => $con->InscriptionMetadataFields,
        );

        return Response::json(['status' => 200, 'flash' => Lang::get('contest.inscriptionDataSaved'),'data'=>$data, 'contest'=>$con], 200, [], JSON_NUMERIC_CHECK);
    }

    public function getContestsIds($contest){
        $con = $this->getContest($contest);
        $superadmin = Auth::check() && Auth::user()->isSuperAdmin();
        if($superadmin) $contestsInfo = Contest::select('id', 'name', 'code')->get();
        else{
            $user = Auth::user();
            $contestsIds = Inscription::where('user_id', $user->id)->where('role', Inscription::OWNER)->select('contest_id')->get();
            $contestsInfo = Contest::whereIn('id', $contestsIds->toArray())->select('id', 'name', 'code')->get();
        }
        foreach($contestsInfo as $info){
            $templates = EntryMetadataTemplate::where('contest_id', $info->id)->count();
            $info->templates = $templates;
        }
        $metadataFieldsCount = EntryMetadataField::where('contest_id', $con->id)->count('id');
        $data = array(
            'contestsIds' => $contestsInfo,
            'metadataFieldsCount' => $metadataFieldsCount,
        );
        return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
    }

    public function getCategoriesData($contest){
        /** @var Contest $con */
        $con = $this->getContest($contest, 'categories');
        $data = array(
            'categories' => $con->childrenCategories,
            'EntryMetadataTemplate' => $con->EntryMetadataTemplates,
        );
        return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
    }

    function entriesFiltersCounter($con){
        $user = Auth::user();
        $superadmin = Auth::check() && Auth::user()->isSuperAdmin();
        $inscriptor = $con->getUserInscription($user, Inscription::INSCRIPTOR);
        $owner = $con->getUserInscription($user, Inscription::OWNER);
        if($superadmin || $owner){
            $role = Inscription::OWNER;
        }
        elseif ($con->getUserInscription($user, Inscription::COLABORATOR)) {
            $role = Inscription::COLABORATOR;
        }
        elseif ($inscriptor) {
            $role = Inscription::INSCRIPTOR;
        }
        if ($con->getUserInscription($user, Inscription::COLABORATOR)) {
            $role = Inscription::COLABORATOR;
        }
        $finalTotal = 0;
        $payedEntries = 0;
        $totals = [];
        $totalBillings = [];

        $totalEntriesCategoryQuery = Entry::select('status', DB::raw('count(status) as total'))
            ->join('entry_categories', 'entries.id', '=', 'entry_categories.entry_id')
            ->where('contest_id', $con->id);

        $queryTotalMsgs = Entry::join('entry_log', 'entries.id', '=', 'entry_log.entry_id')
            ->where('contest_id', $con->id)
            ->where('entry_log.status', 5)
            ->where('read_by', 'not like', '%"'.$user->id.'"%');

        $totalBillingsQuery = Billing::select('billings.status', DB::raw('count(billing_entries_categories.id) as total'))
            ->join('billing_entries_categories', 'billing_entries_categories.billing_id', '=', 'billings.id')
            ->join('entries', 'entries.id', '=', 'billing_entries_categories.entry_id')
            ->where('billings.contest_id', $con->id)
            ->whereNull('billings.deleted_at')
            ->whereNull('entries.deleted_at')
            ->whereNull('billing_entries_categories.deleted_at');

        if($superadmin || $owner || $role == Inscription::COLABORATOR){
            $totalMsgs = $queryTotalMsgs->count();
            $queryCheckedEntries = Entry::where('check', 1)->count();
        }
        else{
            $totalEntriesCategoryQuery->where('entries.user_id', $user->id);
            $totalBillingsQuery->where('billings.user_id', $user->id);
            $totalMsgs = $queryTotalMsgs->where('entries.user_id', $user->id)->count();
            $queryCheckedEntries = null;
        }

        $totalEntriesCategory = $totalEntriesCategoryQuery->groupBy('status')->get();
        $totalBillingsData = $totalBillingsQuery->groupBy('billings.status')->get();

        foreach($totalEntriesCategory as $totalEntCat){
            $totals[$totalEntCat->status] = $totalEntCat->total;
            $finalTotal += $totalEntCat->total;
        }

        foreach($totalBillingsData as $totalbillQuery){
            $totalBillings[$totalbillQuery->status] = $totalbillQuery->total;
            $payedEntries = $payedEntries + $totalbillQuery->total;
        }

        $data = array(
            'totalEntriesCategory' => $totals,
            'finalTotal' => $finalTotal,
            'totalBillings' => $totalBillings,
            'totalMsgs' => $totalMsgs,
            'payedEntries' => $payedEntries,
            'totalCheck' => $queryCheckedEntries
        );

        return $data;
    }

    public function getCategoriesDataByCode($contest){
        /** @var Contest $con */
        $con = $this->getContest($contest, false, 'childrenCategories');
        $user = Auth::user();
        $superadmin = Auth::check() && Auth::user()->isSuperAdmin();
        $owner = $con->getUserInscription($user, Inscription::OWNER);
        $inscriptor = $con->getUserInscription($user, Inscription::INSCRIPTOR);
        $childCategories = [];
        $conCategories = $con->categories;

        if($superadmin || $owner){
            $role = Inscription::OWNER;
        }
        elseif ($con->getUserInscription($user, Inscription::COLABORATOR)) {
            $role = Inscription::COLABORATOR;
        }
        elseif ($inscriptor) {
            $role = Inscription::INSCRIPTOR;
            foreach($con->childrenCategories as $catKey => $category) {
                if(isset($category->category_config_type[0])){
                    if(!isset($inscriptor->inscription_type->id)) break;
                    $childCategories = $con->childrenCategoriesWithInscriptionType($inscriptor->inscription_type->id);
                    break;
                }
            }
            if(isset($childCategories[0])){
                $parents = [];
                $childrens = [];
                foreach($childCategories as $key => $categs) {
                    if ($categs->parent_id == NULL) {array_push($parents, $categs);}
                    else{array_push($childrens, $categs);}
                }
                $filteredCategories = $this->selectedCategories($parents, $childrens);
                $con->childrenCategories = $filteredCategories;
            }
        }
        elseif ($judge = $con->getUserInscription($user, Inscription::JUDGE)) {
            $role = Inscription::JUDGE;
        }
        else{
            return Response::make(Lang::get('Inscription not found'), 404);
        }

        $perUser = null;

        if($superadmin || $owner || $role == Inscription::COLABORATOR){
            $perUser = $con->getEntriesPerUser();
        }

        $counters = $this->entriesFiltersCounter($con);

        $totals = $counters['totalEntriesCategory'];
        $finalTotal = $counters['finalTotal'];
        $totalBillings = $counters['totalBillings'];
        $totalMsgs = $counters['totalMsgs'];
        $payedEntries = $counters['payedEntries'];
        $totalCheck = $counters['totalCheck'];

        $totalBillings[Billing::UNPAID] = $finalTotal - $payedEntries;
        $childrenCats = $con->childrenCategories;

        $data = array(
            'categories' => $conCategories,
            'children_categories' => $childrenCats,
            'con'=> $con,
            'role' => $role,
            'filtered_categories' => $childCategories,
            'entryPerUser' => $perUser,
            'totalEntriesCategory' => $totals,
            'finalTotal' => $finalTotal,
            'totalBillings' => $totalBillings,
            'totalMsgs' => $totalMsgs,
            'totalCheck' => $totalCheck,
            'inscription' => $con->getUserInscription($user, Inscription::INSCRIPTOR),

        );
        return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
    }

    function entriesCategoryCount($childrenCat, $filters = null, $user = null, $contest_id){
        foreach($childrenCat as $child){
            if($child['final'] == 0){
                $this->entriesCategoryCount($child['childrenCategories'], $filters, $user, $contest_id);
            }
            if($child['final'] == 1){
                $query = Entry::join('entry_categories', 'entry_categories.entry_id', '=', 'entries.id');
                $unpaid = 0;
                if(count($filters['billingFilters']) > 0) {
                    if(in_array(Billing::UNPAID, $filters['billingFilters'])){
                        $unpaid = Entry::where('contest_id', $contest_id)
                            ->join('entry_categories', 'entry_categories.entry_id', '=', 'entries.id')
                            ->where('entry_categories.category_id', $child->id)
                            ->whereDoesntHave('billingEntryCategories')
                            ->count();
                    }
                    $query->join('billing_entries_categories', 'entries.id', '=', 'billing_entries_categories.entry_id')
                        ->join('billings', 'billings.id', '=', 'billing_entries_categories.billing_id')
                        ->whereNull('billings.deleted_at')
                        ->whereNull('billing_entries_categories.deleted_at')
                        ->whereIn('billings.status', $filters['billingFilters'])
                        ->where('billing_entries_categories.category_id', $child->id);
                }

                $query->where('entry_categories.category_id', $child->id);

                if($user){
                    $query->where('entries.user_id', $user);
                }
                if($filters['query'] != ''){
                    $myArray = preg_split("/[\s,]+/", $filters['query'] );
                    foreach($myArray as $value){
                        if(is_numeric($value)){
                            $query->where('entries.id', 'LIKE', '%' . $value . '%');
                        }
                        else{
                            $query->whereHas('EntryMetadataValuesWithFields2', function($sq) use ($filters){
                                $sq->where('entry_metadata_values.value', 'LIKE',  '%' . $filters['query'] . '%');
                            });
                        }
                    }
                }
                if(!empty($filters['filterMetadata'])){
                    foreach($filters['filterMetadata'] as $filterMetadata) {
                        $query->whereHas('EntryMetadataValuesWithFields2', function ($q) use ($filterMetadata) {
                            $q->where('entry_metadata_values.value', 'LIKE', '%' . $filterMetadata['value'] . '%');
                            $q->where('entry_metadata_values.entry_metadata_field_id', $filterMetadata['id']);
                        });
                    }
                }
                if(count($filters['statusFilters']) > 0){
                    $query->whereIn('entries.status', $filters['statusFilters']);
                }
                if($filters['messageFilters']){
                    $query->whereHas('entryLog', function(){
                    });
                }
                $total = in_array(Billing::UNPAID, $filters['billingFilters'] ? $filters['billingFilters'] : []) ? $query->count() + $unpaid : $query->count();

                $child['entriesCount'] = $total;
                $child['test'] = $unpaid;
                $child['entriesRows'] = [];
                $child['lastEntryShown'] = false;
                $child['lastEntryLoaded'] = 0;
                $child['loading'] = false;
            }
        }

        $childrenCat = $this->parentEntriesCount($childrenCat);

        return $childrenCat;
    }

    function parentEntriesCount($childrenCat){
        foreach($childrenCat as $child){
            $total = 0;
            if($child['final'] == 0){
                foreach($child['childrenCategories'] as $aux){
                    $total += $aux['entriesCount'];
                }
                $child['entriesCount'] = $total;
            }
        }
        return $childrenCat;
    }

    function selectedCategories($parents, $childrens){
        foreach($parents as $key => $par){
            $par['children_categories'] = [];
            $newParents = [];
            $newChilds = [];
            foreach($childrens as $child){
                if($par->id == $child->parent_id){
                    array_push($newParents,$child);
                }
                else{
                    array_push($newChilds,$child);
                }
                $par['children_categories'] = $newParents;
            }
            $parents[$key] = $par;
            $this->selectedCategories($newParents, $newChilds);
        }
        return $parents;
    }

    public function verifyCategories($categories){
        foreach($categories as $cat) {
            if($cat['children_categories']){
                $recursiveError = $this->verifyCategories($cat['children_categories']);
                if($recursiveError != false) return $recursiveError;
            }
            $rules = array(
                'name' => 'required',
            );
            $niceNames = array(
                'name' => Lang::get('general.name'),
            );
            $validator = Validator::make($cat, $rules);
            $validator->setAttributeNames($niceNames);
            if ($validator->fails())
            {
                $errors = $validator->messages();
                if(!isset($cat['errMsg'])) $cat['errMsg'] = random_int(100, 999);
                foreach(json_decode($errors) as $error){
                    foreach($error as $err){
                        $messages[$cat['errMsg']] = isset($messages[$cat['errMsg']]) ? $messages[$cat['errMsg']]."  *".$err : "*".$err;
                    }
                }
                return Response::json(['errors'=>$messages, 'flash' => Lang::get('contest.CategoriesDataErrorForm')], 400, [], JSON_NUMERIC_CHECK);
            }
        }
        return false;
    }

    public function postCategoriesData($contest){
        /** @var Contest $con */
        /** @var Category $category */
        $con = $this->getContest($contest, 'categories');

        /* Inscription Types save */
        $currentCategories = $con->categories;
        $categories = Input::get('categories');
        //TODO Hacer validación
        $savedIds = [];
        $errors = $this->verifyCategories($categories);
        if($errors != false) return $errors;
        /*foreach($categories as $cat) {
            $rules = array(
                'name' => 'required',
            );
            $niceNames = array(
                'name' => Lang::get('general.name'),
            );
            $validator = Validator::make($cat, $rules);
            $validator->setAttributeNames($niceNames);
            if ($validator->fails())
            {
                $errors = $validator->messages();
                if(!isset($cat['errMsg'])) $cat['errMsg'] = random_int(100, 999);
                foreach(json_decode($errors) as $error){
                    foreach($error as $err){
                        $messages[$cat['errMsg']] = isset($messages[$cat['errMsg']]) ? $messages[$cat['errMsg']]."  *".$err : "*".$err;
                    }
                }
                return Response::json(['errors'=>$messages, 'flash' => Lang::get('contest.CategoriesDataErrorForm')], 400, [], JSON_NUMERIC_CHECK);
                //return Response::json(['errors2'=>$messages, 'flash' => Lang::get('contest.inscriptionDataErrorForm')], 400, [], JSON_NUMERIC_CHECK);
                continue;
            }
        }*/
        $this->saveCategoriesList($con, null, $categories, $savedIds);
        foreach($currentCategories as $cCat){
            if(!in_array($cCat->id, $savedIds)){
                $cCat->delete();
            }
        }

        if($con->wizard_status == Contest::WIZARD_CATEGORIES){
            Contest::where('id', $con->id)->update(['wizard_status' => Contest::WIZARD_PAYMENT_FORM]);
        }

        $con = $this->getContest($contest, 'categories');
        $data = [
            'categories' => $con->childrenCategories,
        ];

        return Response::json(['status' => 200, 'flash' => Lang::get('contest.categoriesDataSaved'),'data'=>$data, 'extra'=>$categories, 'contest'=>$con], 200, [], JSON_NUMERIC_CHECK);
    }

    /**
     * @param Contest $con
     * @param Category $parent
     * @param mixed $categories
     * @param mixed $savedIds
     */
    public function saveCategoriesList($con, $parent, $categories, &$savedIds){
        $order = 0;
        $langs = Config::get('app.languages');
        foreach($categories as $categoryData){
            if(isset($categoryData['id'])) {
                $savedIds[] = $categoryData['id'];
                $category = Category::find($categoryData['id']);
                if ($category->contest_id != $con->id){
                    continue;
                }
            }else{
                $category = new Category();
                $category->contest_id = $con->id;
            }
            $category->name = $categoryData['name'];
            $category->description = isset($categoryData['description']) ? $categoryData['description'] : null;
            $category->template_id = isset($categoryData['template_id']) ? intval($categoryData['template_id']) : null;
            $category->price = isset($categoryData['price']) && $categoryData['price']!='' ? floatval($categoryData['price']) : null;
            $category->order = $order;
            $category->parent_id = $parent == null? null : $parent->id;
            $category->final = !isset($categoryData['children_categories']) || count($categoryData['children_categories']) == 0;
            if(isset($categoryData['trans'])){
                $categoryTrans = array();
                foreach($categoryData['trans'] as $langKey => $langVals){
                    if(in_array($langKey, $langs)){
                        $categoryTrans[$langKey] = $langVals;
                    }
                }
                $category->trans = json_encode($categoryTrans, JSON_FORCE_OBJECT);
            }
            $category->save();

            CategoryConfigType::where('category_id', '=', $category->id)->delete();
            if(isset($categoryData['category_config_type'])){
                //foreach($fieldData['configs'] as $ind => $conf){
                foreach($categoryData['category_config_type'] as $typeId){
                    /** @var CategoryConfigType $mdConfig */
                    $mdConfig = CategoryConfigType::firstOrNew(['category_id' => $category->id, 'inscription_type_id' => $typeId]);
                    $mdConfig->save();
                }
            }
            $order++;
            if($categoryData['children_categories'] && count($categoryData['children_categories'])){
                $this->saveCategoriesList($con, $category, $categoryData['children_categories'], $savedIds);
            }
        }
    }

    public function getEntriesData($contest){
        /** @var Contest $con */
        $con = $this->getContest($contest, 'entry');
        $data = array(
            'EntryMetadataTemplate' => $con->EntryMetadataTemplates,
            'EntryMetadataField' => $con->EntryMetadataFields,
        );
        return json_encode($data, JSON_NUMERIC_CHECK);
    }

    public function postEntriesData($contest){
        /** @var Contest $con */
        $con = $this->getContest($contest, 'entry');

        /* Metadata Templates save */
        $currentTemplates = $con->EntryMetadataTemplates;
        $templates = Input::get('EntryMetadataTemplates');
        //TODO Hacer validación
        $savedIds = [];
        $newTypes = [];
        $messages = [];
        foreach($templates as $templateData){
            $rules = array(
                'name' => 'required'
            );
            $niceNames = array(
                'name' => Lang::get('general.name')
            );
            $validator = Validator::make($templateData, $rules);
            $validator->setAttributeNames($niceNames);
            if ($validator->fails())
            {
                $errors = $validator->messages();
                if(!isset($templateData['errMsg'])) $templateData['errMsg'] = random_int(100, 999);
                foreach(json_decode($errors) as $error){
                    foreach($error as $err){
                        $messages[$templateData['errMsg']] = isset($messages[$templateData['errMsg']]) ? $messages[$templateData['errMsg']]."  *".$err : "*".$err;
                    }
                }
                return Response::json(['errors'=>$messages, 'flash' => Lang::get('contest.entriesFormErrorTemplates')], 400, [], JSON_NUMERIC_CHECK);
                //return Response::json(['errors2'=>$messages, 'flash' => Lang::get('contest.inscriptionDataErrorForm')], 400, [], JSON_NUMERIC_CHECK);
                continue;
            }
            if(isset($templateData['id']) && is_int($templateData['id'])) {
                $savedIds[] = $templateData['id'];
                /** @var InscriptionType $template */
                $template = EntryMetadataTemplate::find($templateData['id']);
                if ($template->contest_id != $con->id){
                    continue;
                }
            }else{
                $template = new EntryMetadataTemplate();
                $template->contest_id = $con->id;
            }
            $template->name = $templateData['name'];
            $template->trans = isset($templateData['trans']) ? json_encode($templateData['trans']) : null;
            $template->save();
            if(isset($templateData['id'])) $newTypes[$templateData['id']] = $template->id;
            Category::where('template_id', '=', $template->id)->update(['template_id' => null]);
            if(isset($templateData['categories_ids'])){
                //foreach($fieldData['configs'] as $ind => $conf){
                foreach($templateData['categories_ids'] as $catId){
                    /** @var Category $cat */
                    $cat = Category::find($catId);
                    if($cat){
                        $cat->template_id = $template->id;
                        $cat->save();
                    }
                }
            }
        }

        foreach($currentTemplates as $cTemplate){
            if(!in_array($cTemplate->id, $savedIds)){
                $cTemplate->delete();
            }
        }

        /* Inscription EntryMetadataValue save */
        $currentMD = $con->EntryMetadataFields;
        $metadata = Input::get('EntryMetadataField');
        //TODO Hacer validación
        $savedIds = [];
        $order = 0;
        foreach($metadata as $fieldData){
            $rules = array(
                'label' => 'required',
                'type' => 'required'
            );
            $niceNames = array(
                'label' => Lang::get('metadata.label'),
                'type' => Lang::get('metadata.type')
            );
            $validator = Validator::make($fieldData, $rules);
            $validator->setAttributeNames($niceNames);
            if ($validator->fails())
            {
                $errors = $validator->messages();
                if(!isset($fieldData['errMsg'])) $fieldData['errMsg'] = random_int(100, 999);
                foreach(json_decode($errors) as $error){
                    foreach($error as $err){
                        $messages[$fieldData['errMsg']] = isset($messages[$fieldData['errMsg']]) ? $messages[$fieldData['errMsg']]."  *".$err : "*".$err;
                    }
                }
                return Response::json(['errors2'=>$messages, 'flash' => Lang::get('contest.entriesFormError')], 400, [], JSON_NUMERIC_CHECK);
                //return Response::json(['errors2'=>$messages, 'flash' => Lang::get('contest.inscriptionDataErrorForm')], 400, [], JSON_NUMERIC_CHECK);
                continue;
            }
            if(isset($fieldData['id'])) {
                $savedIds[] = $fieldData['id'];
                /** @var EntryMetadataField $field */
                $field = EntryMetadataField::find($fieldData['id']);
                if ($field->contest_id != $con->id){
                    continue;
                }
            }else{
                $field = new EntryMetadataField();
                $field->contest_id = $con->id;
            }
            $field->type = $fieldData['type'];
            $field->label = $fieldData['label'];
            if(isset($fieldData['description']))
                $field->description = $fieldData['description'];
            $field->trans = isset($fieldData['trans']) ? json_encode($fieldData['trans']) : null;
            $field->required = isset($fieldData['required']) && $fieldData['required'] == 1;
            $field->private = isset($fieldData['private']) && $fieldData['private'] == 1;
            switch($field->type){
                case EntryMetadataField::TEXT:
                    $field->config = json_encode([
                        'exportable' => isset($fieldData['config']['exportable']) ? $fieldData['config']['exportable'] : null,
                        'important' => isset($fieldData['config']['important']) ? $fieldData['config']['important'] : null,
                    ]);
                    break;
                case EntryMetadataField::TEXTAREA:
                    $field->config = json_encode([
                        'characters' => isset($fieldData['config']['characters']) ? $fieldData['config']['characters'] : null,
                        'exportable' => isset($fieldData['config']['exportable']) ? $fieldData['config']['exportable'] : null,
                        'important' => isset($fieldData['config']['important']) ? $fieldData['config']['important'] : null,
                    ]);
                    break;
                case EntryMetadataField::RICHTEXT:
                    $field->config = json_encode([
                        'characters' => isset($fieldData['config']['characters']) ? $fieldData['config']['characters'] : null,
                        'exportable' => isset($fieldData['config']['exportable']) ? $fieldData['config']['exportable'] : null,
                        'important' => isset($fieldData['config']['important']) ? $fieldData['config']['important'] : null,
                    ]);
                    break;
                case EntryMetadataField::NUMBER:
                case EntryMetadataField::EMAIL:
                    $field->config = json_encode([
                        'exportable' => isset($fieldData['config']['exportable']) ? $fieldData['config']['exportable'] : null,
                        'important' => isset($fieldData['config']['important']) ? $fieldData['config']['important'] : null,
                    ]);
                    break;
                case EntryMetadataField::DATE:
                    $field->config = json_encode([
                        'minDate' => isset($fieldData['config']['minDate']) ? $fieldData['config']['minDate'] : null,
                        'maxDate' => isset($fieldData['config']['maxDate']) ? $fieldData['config']['maxDate'] : null,
                        'exportable' => isset($fieldData['config']['exportable']) ? $fieldData['config']['exportable'] : null,
                        'important' => isset($fieldData['config']['important']) ? $fieldData['config']['important'] : null,
                    ]);
                    break;
                case EntryMetadataField::SELECT:
                    $field->config = json_encode([
                        'options' => isset($fieldData['config']['options']) ? $fieldData['config']['options'] : [],
                        'exportable' => isset($fieldData['config']['exportable']) ? $fieldData['config']['exportable'] : null,
                        'important' => isset($fieldData['config']['important']) ? $fieldData['config']['important'] : null,
                    ]);
                    break;
                case EntryMetadataField::TITLE:
                $field->config = json_encode([
                    'options' => isset($fieldData['config']['options']) ? $fieldData['config']['options'] : [],
                    'exportable' => isset($fieldData['config']['exportable']) ? $fieldData['config']['exportable'] : null,
                    'important' => isset($fieldData['config']['important']) ? $fieldData['config']['important'] : null,
                ]);
                break;
                case EntryMetadataField::MULTIPLE:
                    $field->config = json_encode([
                        'options' => isset($fieldData['config']['options']) ? $fieldData['config']['options'] : [],
                        'horizontal' => isset($fieldData['config']['horizontal']) ? $fieldData['config']['horizontal'] : '',
                        'exportable' => isset($fieldData['config']['exportable']) ? $fieldData['config']['exportable'] : null,
                        'important' => isset($fieldData['config']['important']) ? $fieldData['config']['important'] : null,
                    ]);
                    break;
                case EntryMetadataField::FILE:
                    $field->config = json_encode([
                        'min' => isset($fieldData['config']['min']) ? $fieldData['config']['min'] : 0,
                        'max' => isset($fieldData['config']['max']) ? $fieldData['config']['max'] : 0,
                        'types' => isset($fieldData['config']['types']) ? $fieldData['config']['types'] : [],
                        'important' => isset($fieldData['config']['important']) ? $fieldData['config']['important'] : null,
                        'exportable' => isset($fieldData['config']['exportable']) ? $fieldData['config']['exportable'] : null,
                    ]);
                    break;
                case EntryMetadataField::MULTIPLEWITHCOLUMNS:
                    $field->config = json_encode([
                        'columns' => isset($fieldData['config']['columns']) ? $fieldData['config']['columns'] : [],
                        'labels' => isset($fieldData['config']['labels']) ? $fieldData['config']['labels'] : [],
                        'text' => isset($fieldData['config']['text']) ? $fieldData['config']['text'] : '',
                        'exportable' => isset($fieldData['config']['exportable']) ? $fieldData['config']['exportable'] : null,
                        'important' => isset($fieldData['config']['important']) ? $fieldData['config']['important'] : null,
                    ]);
                    break;
                case EntryMetadataField::LINK:
                    $field->config = json_encode([
                        'type' => isset($fieldData['config']['type']) ? $fieldData['config']['type'] : [],
                        'link' => isset($fieldData['config']['link']) ? $fieldData['config']['link'] : [],
                        'buttonText' => isset($fieldData['config']['buttonText']) ? $fieldData['config']['buttonText'] : [],
                        'hypText' => isset($fieldData['config']['hypText']) ? $fieldData['config']['hypText'] : [],
                        'exportable' => isset($fieldData['config']['exportable']) ? $fieldData['config']['exportable'] : null,
                        'important' => isset($fieldData['config']['important']) ? $fieldData['config']['important'] : null,
                    ]);
                    break;
            }
            $field->order = $order;
            $field->save();

            if(isset($fieldData['entry_metadata_config_template'])){
                foreach($fieldData['entry_metadata_config_template'] as $conf){
                    /** @var InscriptionMetadataConfigType $mdConfig */
                    $templateId = $conf['template_id'];
                    if(!is_int($templateId)){
                        if(isset($newTypes[$templateId]))
                            $templateId = $newTypes[$templateId];
                    }
                    if($conf != null && $templateId != 'undefined' && $templateId != null){
                        $mdConfig = EntryMetadataConfigTemplate::firstOrNew(['entry_metadata_field_id' => $field->id, 'template_id' => $templateId]);
                        $mdConfig->visible = isset($conf['visible']) ? $conf['visible'] : false;
                        $mdConfig->required = $mdConfig->visible && isset($conf['required']) ? $conf['required'] : false;
                        $mdConfig->save();
                    }
                }
            }
            /*foreach($fieldData['entry_metadata_config_category'] as $catConfigData){
                if(isset($catConfigData['id'])) {
                    $conf = EntryMetadataConfigCategory::find($catConfigData['id']);
                    if ($conf->entry_metadata_field_id != $field->id){
                        continue;
                    }
                }else{
                    $conf = new EntryMetadataConfigCategory();
                    $conf->entry_metadata_field_id = $field->id;
                    //TODO Chequear que el id de la categoría perteneza a este contest
                    $conf->category_id = $catConfigData['category_id'];
                }
                $conf->hidden = $catConfigData['hidden'];
                $conf->required = !$conf->hidden && $catConfigData['required'];
                if(!$conf->hidden && $conf->required == $field->required){
                    if($conf->exists) $conf->delete();
                }else{
                    $conf->save();
                }
            }*/
            $order++;
        }
        foreach($currentMD as $cMD){
            if(!in_array($cMD->id, $savedIds)){
                $cMD->delete();
            }
        }

        if(isset($con->wizard_status) && $con->wizard_status < Contest::WIZARD_FINISHED){
            $wizardHasEntries = Input::get('wizardHasEntries');
            $wizard_config = json_decode($con->wizard_config);
            $wizard_config->entries = $wizardHasEntries == true ? 1 : 0;
            Contest::where('id', $con->id)->update(['wizard_status' => $wizardHasEntries == true ? Contest::WIZARD_CATEGORIES : Contest::WIZARD_STYLE, 'wizard_config' => json_encode($wizard_config)]);
        }

        $con = $this->getContest($contest, 'entry');
        $data = [
            'EntryMetadataTemplate' => $con->EntryMetadataTemplates,
            'EntryMetadataField' => $con->EntryMetadataFields
        ];

        return Response::json(['status' => 200, 'flash' => Lang::get('contest.entriesDataSaved'),'data'=>$data, 'contest'=>$con], 200, [], JSON_NUMERIC_CHECK);
    }

    public function getHome($contest){
        $con = $this->getContest($contest);
        $inscription = $judge = $colaborator = $owner = null;
        if(Auth::check()) {
            $colaborator = $con->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $inscription = $con->getUserInscription(Auth::user(), Inscription::INSCRIPTOR);
            $judge = $con->getUserInscription(Auth::user(), Inscription::JUDGE);
            $owner = $con->getUserInscription(Auth::user(), Inscription::OWNER);
        }

        $permits = $colaborator['permits'];
        $superadmin = Auth::check() && Auth::user()->isSuperAdmin();

        return View::make('contest.home', ['contest' => $con, 'inscription'=>$inscription['role'], 'colaborator'=>$colaborator, 'judge'=>$judge['role'], 'owner' =>$owner['role'], 'permits'=>$permits, 'superadmin'=>$superadmin]);
    }


    public function getSignup($contest){
        $con = $this->getContest($contest);
        if(Auth::user()) {
            return View::make('contest.updateInscription', ['contest' => $con]);
        }else{
            $inscriptor = $judge = false;
        }
        $inscriptionMessage = $con->getAsset(ContestAsset::NEW_INSCRIPTION_MESSAGE);
        $judgeMessage = $con->getAsset(ContestAsset::NEW_JUDGE_INSCRIPTION_MESSAGE);
        return View::make('contest.signup', ['contest' => $con, 'inscriptionMessage' => $inscriptionMessage, 'judgeMessage' => $judgeMessage, 'permits'=>null, 'registered' => $judge != null || $inscriptor != null]);
    }
    public function getUpdateInscription($contest){
        $con = $this->getContest($contest);
        return View::make('contest.updateInscription', ['contest' => $con]);
    }
    public function getTerms($contest){
        $con = $this->getContest($contest);
        return View::make('contest.terms', ['contest' => $con]);
    }

    //*************************************** Dinamic Banners ***************************************/
    public function getBigBanner($contest){
        $con = $this->getContest($contest);
        if(!Auth::user()) {
            $banner = "Location: https://www.oxoawards.com/IAB2019/asset/1859";
        }else{
            $inscriptor = $con->getUserInscription(Auth::user(), Inscription::INSCRIPTOR);
            if(isset($inscriptor['inscription_type_id']) && $inscriptor['inscription_type_id'] == 53){
                $banner = "Location: https://www.oxoawards.com/IAB2019/asset/1925";
            }
            else{
                $banner = "Location: https://www.oxoawards.com/IAB2019/asset/1859";
            }
        }
        header($banner);
    }

    public function getSmallBanner($contest){
        $con = $this->getContest($contest);
        if(!Auth::user()) {
            $banner = "Location: https://www.oxoawards.com/IAB2019/asset/1858";
        }else{
            $inscriptor = $con->getUserInscription(Auth::user(), Inscription::INSCRIPTOR);
            if(isset($inscriptor['inscription_type_id']) && $inscriptor['inscription_type_id'] == 53){
                $banner = "Location: https://www.oxoawards.com/IAB2019/asset/1927";
            }
            else{
                $banner = "Location: https://www.oxoawards.com/IAB2019/asset/1858";
            }
        }
        header($banner);
    }
    //*****************************************************************************************
    public function getFileUrl($contest, $cfvId){
        $con = $this->getContest($contest);
        $conFVersion = ContestFileVersion::where('id', $cfvId)->first();

        $playableFile = "Location: ".$conFVersion->getURL();
        header($playableFile);
        /*$con = $this->getContest($contest);
        $user = Auth::user();
        $publicMedia = false;

        $conFVersion = ContestFileVersion::where('id', $cfvId)->first();

        if(!$user) {
            $collection = Collection::where('contest_id', $con->id)->get()->toArray();
            if(sizeof($collection > 0)){
                foreach($collection as $col){
                    $entryCategory = EntryMetadataFile::where('entry_metadata_files.contest_file_id', $conFVersion->contest_file_id)
                        ->join('entry_metadata_values', 'entry_metadata_values.id', '=', 'entry_metadata_files.entry_metadata_value_id')
                        ->join('entry_categories', 'entry_categories.id', '=', 'entry_metadata_values.entry_id')
                        ->select('entry_categories.id as id')
                        ->first();

                    $isInCollection = VotingShortlist::where('voting_session_id', $col['voting_session_id'])
                        ->where('entry_category_id', $entryCategory['id'])
                        ->first();

                    if(isset($isInCollection->id)){
                        $publicMedia = true;
                    }
                }
            }
            if(!$publicMedia)
                return Redirect::to('/');
            else
            if($con->id != 257){
                return Response::make('', 404);
            }
        }
        else{
            $colaborator = $con->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $inscriptor = $con->getUserInscription(Auth::user(), Inscription::INSCRIPTOR);
            $judge = $con->getUserInscription(Auth::user(), Inscription::JUDGE);
            $owner = $con->getUserInscription(Auth::user(), Inscription::OWNER);

            if(!$colaborator && !$inscriptor && !$judge && !$owner && !$user->isSuperAdmin()){
                return false;
            }
            if($inscriptor){
                $isMyFile = ContestFile::where('id', $conFVersion->contest_file_id)
                    ->where('user_id', $user->id)->first();
                if(!$isMyFile){
                    return false;
                }
            }
        }

        $playableFile = "Location: ".$conFVersion->getURL();
        header($playableFile);*/
    }
    //****************************************************************************************
    public function getEntriesView($contest){
        $con = $this->getContest($contest);
        $user = Auth::user();
        $colaborator = $con->getUserInscription(Auth::user(), Inscription::COLABORATOR);
        $inscriptor = $con->getUserInscription(Auth::user(), Inscription::INSCRIPTOR);
        $judge = $con->getUserInscription(Auth::user(), Inscription::JUDGE);
        $owner = $con->getUserInscription(Auth::user(), Inscription::OWNER);
        $permits = $colaborator['permits'];

        if(!$con->isContestClosed() && !$user->isSuperAdmin() && !$owner) return View::make('contest.closed', ['contest' => $con]);
        if($con->isAdmin())
            return View::make('contest.entries', ['contest' => $con, 'permits' => true]);
        elseif($con->isColaborator($permits, Contest::VIEWER)
            || $con->isColaborator($permits, Contest::SIFTER)
            || $con->isColaborator($permits, Contest::EDIT)
            || $con->isColaborator($permits, Contest::ADMIN)
            || $inscriptor
            || $judge
            || $owner
            || $user->isSuperAdmin())
            return View::make('contest.entries', ['contest' => $con, 'permits' => $permits]);
        else
            return $this->getHome($contest);
    }
    public function getPagesView($contest){
        $con = $this->getContest($contest);
        return View::make('contest.pages', ['contest' => $con]);
    }
    public function getAssetsView($contest){
        $con = $this->getContest($contest);
        return View::make('contest.assets', ['contest' => $con]);
    }
    public function getFilesView($contest){
        $con = $this->getContest($contest);
        $colaborator = $con->getUserInscription(Auth::user(), Inscription::COLABORATOR);
        $permits = $colaborator['permits'];
        return View::make('contest.files', ['contest' => $con, 'permits' => $permits]);
    }

    public function getTechView($contest){
        $con = $this->getContest($contest);
        $colaborator = $con->getUserInscription(Auth::user(), Inscription::COLABORATOR);
        $permits = $colaborator['permits'];
        if($con->isAdmin()) {
            return View::make('contest.tech', ['contest' => $con, 'permits' => true]);
        }elseif($con->isColaborator($permits, Contest::ADMIN) || $con->isColaborator($permits, Contest::TECH)){
            return View::make('contest.tech', ['contest' => $con, 'permits' => $permits]);
        }
        return View::make('contest.tech', ['contest' => $con, 'permits' => $permits]);
    }

    public function getTechFilesPanelView($contest){
        $con = $this->getContest($contest);
        return View::make('files.panelTech', ['contest' => $con]);
    }
    public function getFilesPanelView($contest){
        $con = $this->getContest($contest);
        return View::make('files.panel', ['contest' => $con]);
    }
    public function getEntryView($contest){
        $con = $this->getContest($contest);
        $user = Auth::user();
        $colaborator = $con->getUserInscription(Auth::user(), Inscription::COLABORATOR);
        $permits = $colaborator['permits'];
        $owner = $con->getUserInscription(Auth::user(), Inscription::OWNER);
        if($con->isContestClosed() == 0 && !$user->isSuperAdmin() && !$owner) return View::make('contest.closed', ['contest' => $con]);
        if($con->isAdmin()) {
            return View::make('contest.entry', ['contest' => $con, 'permits' => true]);
        }else{
            return View::make('contest.entry', ['contest' => $con, 'permits' => $permits]);
        }
    }
    public function getVoteSessionView($contest){
        $con = $this->getContest($contest);
        return View::make('contest.voteSession', ['contest' => $con]);
    }

    public function getVoteView($contest){
        $con = $this->getContest($contest);
        return View::make('contest.vote', ['contest' => $con]);
    }

    public function getUserInscription($contest){
        $con = $this->getContest($contest);
        if(!Auth::check()){
            App::abort(200, Lang::get('login.loginRequired'));
        }
        $user = Auth::user();
        $inscription = $con->getUserInscription($user);
        if($inscription){
            return $inscription->toJson();
        }
        return '';
    }
    public function postUserInscription($contest){
        /*** Si es registro e inscripcion, creo al usuario ***/
        $userData = Input::only('email', 'first_name', 'last_name', 'new_password', 'repeat_password', 'accept2', 'captcha', 'role','inscriptionType', 'newRecord');
        $failed1 = false;
        $role = $userData['role'];
        $con = $this->getContest($contest, true, null, $role);
        $errors1 = new \Illuminate\Support\MessageBag();
        if($userData['email'] != null){
            $rules = array(
                'email' => 'required|email|unique:users,email',
                'first_name' => 'required|min:2',
                'last_name' => 'required|min:2',
                'new_password' => 'required|same:repeat_password|alpha_num|min:6',
                'accept2' => 'required',
                'captcha' => 'required|captcha'
            );
            if($con->inscription_register_picture && $role == Inscription::INSCRIPTOR){
                $rules['profilePicture'] = 'mimes:jpeg,bmp,png';
            }
            if($con->voters_register_picture && $role == Inscription::JUDGE){
                $rules['profilePicture'] = 'mimes:jpeg,bmp,png';
            }
            $messages = array(
                'accept.required' => Lang::get('login.acceptReq'),
                //'email.unique' => Lang::get('login.emailUniqueRemember'),
                'captcha' => Lang::get('login.captchaWrong')
            );
            $validator = Validator::make($userData, $rules, $messages);
            if ($failed1 = $validator->fails())
            {
                $messages = $validator->messages();
                return Response::json(array('errors'=>$messages, 'captchaUrl'=>Captcha::img()));
            }
        }
        else{
            //Chequear stage del contest para ver si se puede hacer la inscripción
            if(!Auth::check()){
                App::abort(404, Lang::get('login.loginRequired'));
            }
            $user = Auth::user();
        }

        $rules = [];
        $inputsIds = [];
        $inputsIds[] = 'role';
        $rolesIds = [];
        if($con->inscription_public) $rolesIds[] = Inscription::INSCRIPTOR;
        if($con->voters_public) $rolesIds[] = Inscription::JUDGE;
        if(count($rolesIds)) $rules['role'] = 'in:'.implode(',',$rolesIds);
        if(count($con->inscriptionTypes()) > 0){
            $inputsIds[] = 'inscriptionType';
            $rules['inscriptionType'] = 'exists:inscription_types,id,contest_id,'.$con->id;
        }
        $niceNames = [];
        foreach($con->InscriptionMetadataFields as $metadata){
            $isRequired = false;
            if(isset($userData['inscriptionType'])){
                $valid = false;
                foreach($metadata->inscription_metadata_config_types as $types){
                    if($types['inscription_type_id'] == $userData['inscriptionType']){
                        if($types['required'] == 1){
                            $metadata->required = 1;
                        }
                        if(!$types['inscription_type_id'] == $userData['inscriptionType']) {
                            $valid = true;
                        }
                    }
                    if($valid == false) continue;
                }
            }
            $mdRules = [];
            //if($metadata->id == 519) return $metadata;
            if($metadata->required || $isRequired){
                $mdRules[] = "required";
            }
            switch($metadata->type){
                case InscriptionMetadataField::DATE:
                    $mdRules[] = "date";
                    break;
                case InscriptionMetadataField::EMAIL:
                    $mdRules[] = "email";
                    break;
            }
            if(count($mdRules)) $rules[$metadata->id] = implode('|',$mdRules);
            $inputsIds[] = $metadata->id;
            $niceNames[$metadata->id] = $metadata->label;
        }
        $input = Input::only($inputsIds);

        $validator = Validator::make($input, $rules);
        $validator->setAttributeNames($niceNames);
        $errors2 = new \Illuminate\Support\MessageBag();
        if ($failed2 = $validator->fails())
        {
            $messages = $validator->messages();
            return Response::json(array('errors'=>$messages, 'captchaUrl'=>Captcha::img()));

        }
        if ($failed1 || $failed2)
        {
            $messages = array_replace_recursive($errors1->getMessages(), $errors2->getMessages());
            return Response::json(array('errors'=>$messages, 'captchaUrl'=>Captcha::img()));
        }
        else {
            if ($userData['email'] != null) {
                $userData['password'] = Hash::make($userData['new_password']);
                $userData['active'] = 1;
                $user = User::create($userData);
                //if(!Config::get('registration.allowUnverified')){
                //$user->verifyEmail();
                //}
                $response = array('flash' => Lang::get('login.registerOk', array('email' => $user->email)));
                if (Config::get('registration.autologin') && Config::get('registration.allowUnverified')) {
                    Auth::login($user);
                    $response['user'] = Auth::user()->getArrayData();
                }
                Auth::login($user);

                if(($con->inscription_register_picture && $role == Inscription::INSCRIPTOR)
                    || ($con->voters_register_picture && $role == Inscription::JUDGE)){
                    $savePath = storage_path('profile_pictures/'.Auth::id().'.jpg');
                    $profilePicture = Input::file('profilePicture');
                    $tmpfname = $profilePicture->getRealPath();
                    Image::convert()->recipeAndSave($tmpfname, $savePath, 'profile');
                    Image::convert()->recipeAndSave($tmpfname, $savePath, 'profile.thumb');
                    Image::convert()->recipeAndSave($tmpfname, $savePath, 'profile.preview');
                }
            }

            //Log::info($userData);
            if($userData['newRecord'] == "true"){
                $inscription = new Inscription(['contest_id' => $con->id, 'user_id' => $user->id]);
                $inscription->contest_id = $con->id;
                $inscription->user_id = $user->id;
                $inscription->inscription_type_id = $input['inscriptionType'];
                $inscription->role = $role;
                $inscription->save();
                $owners_ids = Inscription::where('contest_id', $con->id)->whereIn('role', array(Inscription::OWNER, Inscription::COLABORATOR))->lists('user_id');
                /** @var User[] $owners */
                $owners = User::whereIn('id', $owners_ids)->get();

                $link = url($con->code.'/#/admin/inscription/'.$inscription->id);
                $body = View::make('emails.contest.new-inscription',
                    ['user'=>$user, 'role'=>$role, 'inscription'=>$inscription, "contest"=>$con->name, "link"=>$link])->render();
                foreach($owners as $owner){
                    if(!$owner->canReceiveNotification(User::NotificationNewUser)) continue;
                    $subject =
                        $role == Inscription::JUDGE
                            ? Lang::get('contest.newJudgeSubject', ["contest"=>$con->name])
                            : Lang::get('contest.newInscriptorSubject', ["contest"=>$con->name]);
                    /*$response = OxoMailer::sendMail([
                        'email_to' => $owner->email,
                        'subject' => $subject,
                        'body' => $body
                    ]);*/
                }
            }else{
                $inscription = Inscription::where('contest_id', $con->id)->where('user_id', $user->id)->where('role', $role)->first();
            }
            unset($input['inscriptionType']);
            unset($input['role']);

            foreach ($input as $key => $val) {
                if ($val == null) continue;
                if (!is_array($val)) $val = [$val];
                $type = InscriptionMetadataField::where('id', $key)->select('type')->first();

                if($type['type'] == MetadataField::MULTIPLE) {
                    //$val = json_encode($val);
                    if(is_array($val)){
                        $multipleVal = array_map('intval', explode(',', $val[0]));
                        $matchThese = ['inscription_id' => $inscription->id, 'inscription_metadata_field_id' => $key];
                        InscriptionMetadataValue::where($matchThese)->delete();
                        if(count($multipleVal) > 0) {
                            $inscriptionMetadata = new InscriptionMetadataValue();
                            $inscriptionMetadata->inscription_id = $inscription->id;
                            $inscriptionMetadata->inscription_metadata_field_id = $key;
                            $inscriptionMetadata->value = json_encode($multipleVal);
                            $inscriptionMetadata->save();
                        }
                    }
                }
                else{
                    $matchThese = ['inscription_id' => $inscription->id, 'inscription_metadata_field_id' => $key];
                    InscriptionMetadataValue::where($matchThese)->delete();
                    foreach ($val as $v) {
                        $inscriptionMetadata = new InscriptionMetadataValue();
                        $inscriptionMetadata->inscription_id = $inscription->id;
                        $inscriptionMetadata->inscription_metadata_field_id = $key;
                        $inscriptionMetadata->value = $v;
                        $inscriptionMetadata->save();
                    }
                }
            }
            $inscription = Inscription::with('Contest', 'inscriptionMetadatas')->find($inscription->id);
            if ($userData['email'] != null) {
                return Response::json(array('flash' => Lang::get('contest.inscriptionCreated'), 'user' => $user, 'inscription' => $inscription, 'update' => $userData['newRecord']));
            }
            else
            {
                if($userData['newRecord'] == true){
                    return Response::json(array('flash' => Lang::get('contest.inscriptionCreated'), 'user' => $user, 'inscription' => $inscription, 'update' => $userData['newRecord']));
                }else {
                    return Response::json(array('flash' => Lang::get('contest.inscriptionUpdated'), 'user' => $user, 'inscription' => $inscription, 'update' => $userData['newRecord']));
                }
            }
        }
    }

    public function inscriptionExists($contest){
        $con = $this->getContest($contest);
        $exist = Inscription::where('user_id', '=', Input::get('id'))->where('contest_id', '=', $con->id)->get();
        $returnTo = null;
        if($con->inscription_public){
            $returnTo = 1;
        }else{
            $returnTo = 3;
        }
        return Response::json(array('data' => $exist,'returnTo' => $returnTo));
    }

    public function getInviteCode($contest, $inviteCode){
        $con = $this->getContest($contest);
        /** @var VotingUser $invite */
        $invite = VotingUser::whereHas('votingSession', function($q) use ($con){
            $q->where('contest_id', $con->id);
        })->where('invitation_key',$inviteCode)->first();
        if(!$invite) return Redirect::to('/'.$con->code.'/');
        if($invite->status == VotingUser::NOTIFIED) $invite->status = VotingUser::VISITED_PAGE;
        $invite->last_seen_at = date("Y/m/d H:i:s");
        $invite->save();
        $allowRegister = $invite->inscription->user_id == null;
        return View::make('contest.invites.index', ['contest' => $con, 'invite' => $invite, 'allowRegister' => $allowRegister]);
    }

    public function getInviteKeyIndex($contest){
        $con = $this->getContest($contest);
        return View::make('contest.invites.index-key', ['contest' => $con]);
    }
    public function getInviteHome($contest){
        $con = $this->getContest($contest);
        /** @var VotingUser $invite */
        return View::make('contest.invites.home', ['contest' => $con]);
    }
    public function getInviteKey($contest){
        $con = $this->getContest($contest);
        /** @var VotingUser $invite */
        return View::make('contest.invites.key', ['contest' => $con]);
    }
    public function getInviteReject($contest){
        $con = $this->getContest($contest);
        /** @var VotingUser $invite */
        return View::make('contest.invites.reject', ['contest' => $con]);
    }

    public function inviteLogin($contest, $inviteCode){
        $con = $this->getContest($contest);
        /** @var VotingUser $invite */
        $invite = VotingUser::whereHas('votingSession', function($q) use ($con){
            $q->where('contest_id', $con->id);
        })->where('invitation_key',$inviteCode)->firstOrFail();
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
                    $user = Auth::user();
                }else{
                    return Response::json(['flash' => Lang::get('login.emailNotVerified')], 403);
                }
            }else{
                $user = Auth::user();
            }
        }else{
            return Response::json(['flash' => Lang::get('login.authFailed')], 403);
        }
        if($invite->inscription->user_id != null){
            if($invite->inscription->user_id != $user->id){
                return Response::json(['flash' => Lang::get('login.inviteAuthFailed')], 403);
            }
        }else {
            $invite->inscription->user_id = $user->id;
        }
        $invite->inscription->email = null;
        $invite->inscription->save();
        $invite->invitation_key = null;
        $invite->last_seen_at = date("Y/m/d H:i:s");
        $invite->status = VotingUser::ACCEPTED;
        $invite->save();
        return Response::json(['user' => $user->getArrayData()],202);
    }
    public function inviteRegister($contest, $inviteCode){
        $con = $this->getContest($contest);
        /** @var VotingUser $invite */
        $invite = VotingUser::whereHas('votingSession', function($q) use ($con){
            $q->where('contest_id', $con->id);
        })->where('invitation_key',$inviteCode)->firstOrFail();

        if($invite->inscription->user_id != null){
            return Response::json(['flash' => Lang::get('login.authFailed')], 403);
        }
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
            return Response::json(array('errors'=>$messages, 'captchaUrl'=>Captcha::img()), 403);
        }
        $input['password'] = Hash::make($input['new_password']);
        $input['active'] = 1;
        $user = User::create($input);
        $user->verifyEmail();
        Auth::login($user);
        /*$response = array('flash' => Lang::get('login.registerOk', array('email' => $user->email)));
        if(Config::get('registration.autologin') && Config::get('registration.allowUnverified')) {
            Auth::login($user);
            $response['user'] = Auth::user()->getArrayData();
        }
        return Response::json($response);*/

        $invite->inscription->user_id = $user->id;
        $invite->inscription->email = null;
        $invite->inscription->save();
        $invite->invitation_key = null;
        $invite->last_seen_at = date("Y/m/d H:i:s");
        $invite->status = VotingUser::ACCEPTED;
        $invite->save();
        return Response::json(['user' => $user->getArrayData()],202);
    }
    public function inviteReject($contest, $inviteCode){
        $con = $this->getContest($contest);
        /** @var VotingUser $invite */
        $invite = VotingUser::whereHas('votingSession', function($q) use ($con){
            $q->where('contest_id', $con->id);
        })->where('invitation_key',$inviteCode)->firstOrFail();

        $invite->last_seen_at = date("Y/m/d H:i:s");
        $invite->status = VotingUser::REJECTED;
        $invite->save();
        return Response::json(['flash' => Lang::get('voting.inviteRejected')],202);
    }
    public function inviteKeyLogin($contest){
        $con = $this->getContest($contest);
        /** @var VotingSessionKey $inviteKey */
        $inviteKey = VotingSessionKey::whereHas('votingSession', function($q) use ($con){
            $q->where('contest_id', $con->id);
        })->where('key',Input::get('key'))->first();

        if(!$inviteKey){
            return Response::json(['flash' => Lang::get('login.inviteKeyAuthFailed')], 404);
        }
        if($inviteKey->votingUser){
            $user = $inviteKey->votingUser->inscription->user;
        }else{
            $user = User::create([
                'first_name' => $inviteKey->email ? $inviteKey->email : Lang::get('voting.keyInviteJudgeFirstName'),
                'last_name' => Lang::get('voting.keyInviteJudgeLastName'),
                'email'=>$inviteKey->key.'@'.$con->code.".oxoawards.com",
                'password'=>Hash::make($inviteKey->key),
                'active' => 1
            ]);
            $inscription = Inscription::create([
                'user_id' => $user->id,
                'contest_id' => $con->id,
                'role' => Inscription::JUDGE
            ]);
            $votingUser = VotingUser::create([
                'voting_session_id' => $inviteKey->votingSession->id,
                'inscription_id' => $inscription->id,
                'status' => VotingUser::ACCEPTED
            ]);
            if($inviteKey->votingGroup) {
                VotingUserVotingGroup::create([
                    'voting_user_id' => $votingUser->id,
                    'voting_group_id' => $inviteKey->votingGroup->id
                ]);
            }
            $inviteKey->voting_user_id = $votingUser->id;
            $inviteKey->save();
        }
        Auth::login($user);
        return Response::json(['user' => $user->getArrayData()],202);
    }

    public function postAutoAbstains($contest, $votingCode){
        $con = $this->getContest($contest);
        $fields = Input::get('fields');
        $votingSession = VotingSession::where('code', '=', $votingCode)->where('contest_id','=',$con->id)->firstOrFail();
        AutoAbstain::where('voting_session_id', $votingSession->id)->delete();
        foreach($fields as $field){
            $autoAbastains = new AutoAbstain();
            $autoAbastains->voting_session_id = $votingSession->id;
            $autoAbastains->metadata_field_id = $field;
            $autoAbastains->save();
        }
    }

    public function postVotingSessionSendInvites($contest, $votingCode){
        $con = $this->getContest($contest);
        $judges = Input::get('judge');
        $withCodes = Input::get('code');
        if(!$con) return Response::make('Contest not found', 400);
        /** @var VotingSession $votingSession */
        $votingSession = VotingSession::where('code', '=', $votingCode)->where('contest_id','=',$con->id)->firstOrFail();

        /** @var VotingUser[] $pendingUsers */
        if($judges){
            $judgesIds = [];
            foreach($judges as $judge){
                array_push($judgesIds, $judge['id']);
            };
            $pendingUsers = VotingUser::where('voting_session_id', $votingSession->id)->whereIn('id', $judgesIds)->get();
        }
        else $pendingUsers = VotingUser::where('voting_session_id', $votingSession->id)->where('status', VotingUser::PENDING_NOTIFICATION)->get();

        foreach($pendingUsers as $user){
            if($user->status == 1) $user->status = VotingUser::RESEND;
            else $user->status = VotingUser::NOTIFIED;
            if($user->invitation_key == null){
                $token = str_random(60);
                $user->invitation_key = $token;
            }else{
                $token = $user->invitation_key;
            }
            $user->save();


            $userName = Inscription::where('inscriptions.id', $user->inscription_id)
                        ->join('users', 'inscriptions.user_id', '=', 'users.id')
                        ->select('users.first_name', 'users.last_name')
                        ->first();

            $name = isset($userName->first_name) || isset($userName->last_name)
                ? ( (isset($userName->first_name) ? $userName->first_name : "") . " "
                    . (isset($userName->last_name) ? $userName->last_name : "")
                ) : (isset($user->inscription->invitename) ? $user->inscription->invitename : "");

            if($withCodes == true){
                $sessionKey = VotingSessionKey::where('voting_session_id', $votingSession->id)
                    ->where('email', $user->getEmail())
                    ->first();

                if($sessionKey){
                    $sessionKey = $sessionKey->key;
                }
                if(!$sessionKey){
                    $sessionKey = VotingSessionKey::createSimpleKey();
                    VotingSessionKey::create(['voting_session_id'=>$votingSession->id, 'voting_group_id'=>null, 'key'=>$sessionKey, 'email'=>$user->getEmail()]);
                }
            }

            $replaces = [
                $con->name,
                URL::to('/'.$con->code.'/invite/'.$token),
                URL::to('/'.$con->code.'/invite/'.$token)."#reject",
                isset($userName->first_name) ? $userName->first_name : "",
                isset($userName->last_name) ? $userName->last_name : "",
                $name,
                isset($sessionKey) ? $sessionKey : null,
                isset($sessionKey) ? url("/".$con->code."/invite-key") : null
            ];

            $body = ContestAsset::where('contest_id', $con->id)->where('type', ContestAsset::JUDGE_INVITATION_EMAIL)->select('content')->firstOrFail();
            $body->content = str_replace([':contest', ':link',':rejectlink', ':firstname', ':lastname', ':name', ':code', ':invite'], $replaces, $body->content);
            $response = OxoMailer::sendMail([
                'email_to' => $user->getEmail(),
                'subject' => Lang::get('voting.inviteSubject',['name' => $con->name]),
                'body' => $body->content
            ]);
        }

        $votingSession = VotingSession::judges()->where('id', '=', $votingSession->id)->firstOrFail();
        $votingSession->loadJudgesProgress();
        return Response::json(['status' => 200, 'msg'=> Lang::get("voting.invitesSent"), 'voting' => $votingSession]);
    }

    public function postSendNewsletter($contest){
        $data = Input::get('data');
        $body = $data['newsletter']['email_body'];

        foreach($data['users'] as $pendingUser){
            if($pendingUser['status'] == NewsletterUser::PENDING_NOTIFICATION){
                $response = OxoMailer::sendMail([
                    'reply_to' => $data['newsletter']['reply_to'],
                    'email_to' => $pendingUser['email'],
                    'subject' => $data['newsletter']['subject'],
                    'body' => $body
                ]);
                NewsletterUser::where('newsletter_id', $data['newsletter']['id'])
                    ->where('email', $pendingUser['email'])->update(['status' => NewsletterUser::NOTIFIED]);
            }
        }

        $remainingEmails = NewsletterUser::where('newsletter_id', $data['newsletter']['id'])->where('status', NewsletterUser::PENDING_NOTIFICATION)->count();

        if($remainingEmails == 0){
            Newsletter::where('id', $data['newsletter']['id'])->update(['status' => Newsletter::STATUS_SEND]);
        }

        return Response::json(['status' => 200, 'flash'=> Lang::get("voting.invitesSent")]);
    }

    // Update de registro a contest
    public function postUpdateUserInscription($contest){
        $user = Auth::user();
        $con = $this->getContest($contest, true, null);
        if($con->getUserInscription($user, Inscription::INSCRIPTOR)) $role = Inscription::INSCRIPTOR;
        if($con->getUserInscription($user, Inscription::JUDGE)) $role = Inscription::JUDGE;
        //Chequear stage del contest para ver si se puede hacer la inscripción
        if(!Auth::check()){
            App::abort(404, Lang::get('login.loginRequired'));
        }
        /*$inscriptionType = Input::get('inscriptionType');
        $test = Input::all();
        return Response::json($test);*/

        $user = Auth::user();
        $rules = [];
        $inputsIds = [];
        $inputsIds[] = 'role';
        $rolesIds = [];
        if($con->inscription_public) $rolesIds[] = Inscription::INSCRIPTOR;
        if($con->voters_public) $rolesIds[] = Inscription::JUDGE;
        if(count($rolesIds)) $rules['role'] = 'in:'.implode(',',$rolesIds);
        if(count($con->inscriptionTypes()) > 0){
            $inputsIds[] = 'inscriptionType';
            $rules['inscriptionType'] = 'exists:inscription_types,id,contest_id,'.$con->id;
        }
        $niceNames = [];
        foreach($con->InscriptionMetadataFields as $metadata){
            $mdRules = [];
            if($metadata->required){
                $mdRules[] = "required";
            }
            switch($metadata->type){
                case InscriptionMetadataField::DATE:
                    $mdRules[] = "date";
                    break;
                case InscriptionMetadataField::EMAIL:
                    $mdRules[] = "email";
                    break;
            }
            if(count($mdRules)) $rules[$metadata->id] = implode('|',$mdRules);
            $inputsIds[] = $metadata->id;
            $niceNames[$metadata->id] = $metadata->label;
        }

        $input = Input::all();
        $checkInput = [];
        foreach($input as $iData){
            $checkInput[$iData['id']] = !is_array($iData['value']) ? $iData['value'] : implode('',$iData['value']);;
        }

        $inscMatchs = ['contest_id' => $con->id, 'user_id'=>$user->id, 'role'=>$role];
        $inscription = Inscription::where($inscMatchs)->firstOrFail();

        $validator = Validator::make($checkInput, $rules);
        $validator->setAttributeNames($niceNames);
        if ($validator->fails())
        {
            return Response::json(array('errors'=>$validator->messages(), 'input'=>$input));
        }
        else {
            unset($input['inscriptionType']);
            unset($input['role']);

            foreach ($input as $key => $val) {
                $matchThese = ['inscription_id' => $inscription->id, 'inscription_metadata_field_id' => $val['id']];
                if (isset($val['value'])) {
                    if (!is_array($val['value'])) $val['value'] = [$val['value']];
                    if (sizeof($val['value']) > 0 || $val['value'] == null) {
                        InscriptionMetadataValue::where($matchThese)->delete();
                        if ($val['value'] == null) continue;
                        foreach ($val['value'] as $v) {
                            $inscriptionMetadata = new InscriptionMetadataValue();
                            $inscriptionMetadata->inscription_id = $inscription->id;
                            $inscriptionMetadata->inscription_metadata_field_id = $val['id'];
                            $inscriptionMetadata->value = $v;
                            $inscriptionMetadata->save();
                        }
                    } else {
                        foreach ($val['value'] as $v) {
                            if ($v == null) continue;
                            if (InscriptionMetadataValue::where($matchThese)->count() > 0) {
                                InscriptionMetadataValue::where($matchThese)->update(array('value' => $v));
                            } else {
                                $inscriptionMetadata = new InscriptionMetadataValue();
                                $inscriptionMetadata->inscription_id = $inscription->id;
                                $inscriptionMetadata->inscription_metadata_field_id = $val['id'];
                                $inscriptionMetadata->value = $v;
                                $inscriptionMetadata->save();
                            }
                        }
                    }
                }
            }
            $inscription = Inscription::with('Contest', 'inscriptionMetadatas')->find($inscription->id);
            return Response::json(array('flash' => Lang::get('contest.inscriptionUpdated'), 'user' => $user, 'inscription' => $inscription, 'metadata' => $input, "inputsIds" => $inputsIds, 'update' => true));
        }
    }

    public function getStyleData($contest){
        /** @var $con Contest */
        $con = $this->getContest($contest);
        return Response::json($con->contestAssets);
    }

    public function getPaymentsData($contest){
        /** @var $con Contest */
        $con = $this->getContest($contest);
        $arr = $con->toArray();
        $data = [
            'billing' => $arr['billing'],
            'discounts' => $con->discounts,
        ];
        return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
    }

    public function saveHomeData($contest){
        /** @var $con Contest */
        $con = $this->getContest($contest);
        $homeData = Input::get('homeData');
        if (!isset($homeData['type'])) {
            return Response::json(array('errors' => array('htmlcontent' => Lang::get('contest.saveHomeHtmlError'))));
        }
        $contestAsset = ContestAsset::firstOrCreate(['contest_id' => $con->id, 'type' => $homeData['type']]);
        $contestAsset->contest_id = $con->id;
        $contestAsset->type = $homeData['type'];
        $contestAsset->content = $homeData['html'];
        $labels = ContestAsset::getAllTypes();
        $contestAsset->name = $labels[$contestAsset->type];
        $contestAsset->save();
        return Response::json(['status' => 200, 'saveHomeOk' => true]);
    }

    public function saveAsset($contest){
        /** @var $con Contest */
        $con = $this->getContest($contest);
        $input = Input::only(['type','content']);
        if (!isset($input['type'])) {
            return Response::json(array('errors' => array('htmlcontent' => Lang::get('contest.saveHomeHtmlError'))));
        }
        $type = $input['type'];
        $contestAsset = ContestAsset::firstOrCreate(['contest_id' => $con->id, 'type' => $type]);
        $contestAsset->contest_id = $con->id;
        $contestAsset->type = $type;
        $contestAsset->content = $input['content'];
        $labels = ContestAsset::getAllTypes();
        if(isset($labels[$contestAsset->type])) {
            $contestAsset->name = $labels[$contestAsset->type];
        }
        $contestAsset->save();
        return Response::json(['status' => 200, 'saveHomeOk' => true]);
    }

    public function saveStyles($contest){
        /** @var $con Contest */
        $con = $this->getContest($contest);
        $input = Input::only(['style','custom_style']);
        $rules = array(
            'style' => 'required|numeric'
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $errors = $validator->messages();
            return Response::json(array('errors'=>$errors), 400);
        }else {
            $con->style = intval($input['style']);
            $con->custom_style = $input['custom_style'];
            $con->save();
        }
        return Response::json(['status' => true], 200);
    }

    public function finishWizard($contest){
        $con = $this->getContest($contest);
        Contest::where('id', $con->id)->update(['wizard_status'=>contest::WIZARD_DATES]);

        Return Response::json(['status'=>'Created OK'], 200);
    }

    public function reEncode($contest){
        $fileId = Input::get('id');
        $params = Input::get('param');
        $con = $this->getContest($contest);
        ContestFile::where('contest_id', $con->id)->where('id', $fileId)->update(['status' => ContestFile::QUEUED]);
        $data = ['status' => ContestFileVersion::QUEUED, 'config' => $params == null ? null : json_encode($params)];
        if(Config::get('cloud.enabled')){
            $data['cloud_encoder_id'] = null;
        }
        ContestFileVersion::where('contest_file_id', $fileId)->where('source','=', 0)->update($data);
        $contestFile = ContestFile::where('contest_id', $con->id)->where('id', $fileId)->first();
        ContestFile::executeEncoder($contestFile->contest_id, $contestFile->type);
        return Response::make('Re-Encoding in queue', 200);
    }

    public function createVersion($contest){
        $fileId = Input::get('id');
        $formatId = Input::get('format');
        $con = $this->getContest($contest);
        /** @var ContestFile $contestFile */
        $contestFile = ContestFile::where('contest_id', $con->id)->where('id', $fileId)->first();
        $contestFile->update(['status' => ContestFile::QUEUED]);
        /** @var Format $format */
        $format = Format::where('id','=',$formatId)->where('type','=',$contestFile->type)->first();
        ContestFileVersion::create(['format_id'=>$format->id, 'extension'=>$format->extension, 'contest_file_id' => $fileId, 'status' => ContestFileVersion::QUEUED, 'storage_bucket'=>Config::get('cloud.streaming_bucket')]);
        $contestFile = ContestFile::where('contest_id', $con->id)->with('ContestFileVersions')->where('id', $fileId)->first();
        ContestFile::executeEncoder($contestFile->contest_id, $contestFile->type);
        return Response::json(['status' => 200, 'file' => $contestFile]);
    }

    public function makeThumbs($contest){
        $fileId = Input::get('id');
        $con = $this->getContest($contest);
        /** @var ContestFile $contestFile */
        $contestFile = ContestFile::where('contest_id', $con->id)->where('id', $fileId)->firstOrFail();
        /** @var ContestFileVersion $contestFileVersion */
        $contestFileVersion = ContestFileVersion::where('source', '=', '1')->where('contest_file_id', '=', $contestFile->id)->firstOrFail();
        if($contestFile->type == Format::VIDEO || $contestFile->type == Format::AUDIO) {
            $mediaInfo = FFM::getMediaInfo($contestFileVersion->getPath(), 'json');
            $contestFileVersion->duration = $mediaInfo['format']['duration'];
            foreach($mediaInfo['streams'] as $stream) {
                if ($stream['codec_type'] == 'video') {
                    $contestFileVersion->sizes = $stream['width']."x".$stream['height'];
                }
            }
        }
        if($contestFile->type == Format::IMAGE) {
            $contestFileVersion->sizes = Image::convert()->getSizes($contestFileVersion->getPath());
        }
        $contestFileVersion->save();
        $contestFileVersion->createThumb();
        return Response::json(['status' => 200, 'file' => $contestFile]);
    }


    public function saveFile($contest){
        /** @var $con Contest */
        $con = $this->getContest($contest);
        if(Input::get('tech') != true){
            $file = ContestFile::where('contest_id', '=', $con->id)
                ->where('user_id', '=', Auth::id())
                ->where('id', '=', Input::get('id'))
                ->first();
        }else{
            $file = ContestFile::where('contest_id', '=', $con->id)
                ->where('id', '=', Input::get('id'))
                ->first();
        }
        if ($file){
            $file->name = Input::get('name');
            $file->save();
            return Response::make('File renamed', 200);
        }else{
            return Response::make('File not found', 400);
        }
    }
    public function getFiles($contest){
        /** @var $contest Contest */
        $pagination = Input::only('page','perPage','query','sortBy','sortInverted','selectedTypes','user','inEntries','metadataFields','statusFilters', 'encodeErrorFiles');
        $tech = Input::get('tech');
        $downloadAll = Input::get('downloadAll');
        $contest = $this->getContest($contest, "userfiles");
        $inscription = $contest->getUserInscription(Auth::user());
        $user = Auth::user();
        if($inscription && $inscription->role == Inscription::INSCRIPTOR){
            $tech = false;
        }elseif($user->isSuperAdmin() || ($inscription && ($inscription->role == Inscription::OWNER || $inscription->role == Inscription::COLABORATOR))){
            if(isset($pagination['user'])) {
                $user = User::where('email', $pagination['user']['email'])->first();
                if (!$user) {
                    return Response::json(array('errors' => array('user' => Lang::get('user.notFound'))));
                }
            }
        }
        $filesData = $contest->getUserFiles($user, $pagination, $tech, $downloadAll);
        $filesData['superAdmin'] = $user->isSuperAdmin();
        return Response::json($filesData);
    }

    public function exportFiles($contest){
        $pagination = [];
        $countFiles = null;
        $con = $this->getContest($contest);
        $filesArray = [];
        $filesKeys = ['entry_id', 'metadata_field', 'file_id','extension', 'file_name','file_status'];
        $query = Entry::where('contest_id', $con->id)->orderBy('id');
        $entries = $query->with(['FilesFields'])->get();

        foreach($entries as $entry){
            foreach($entry->files_fields as $fileField){
                foreach($fileField->files as $files){
                    //return $files->contest_file_versions[0]->extension;;
                    $aux['entry_id'] = $entry->id;
                    $metadata = EntryMetadataField::where('id', $fileField->entry_metadata_field_id)->select('label')->first();
                    $aux['metadata_field'] = $metadata['label'];
                    $aux['file_id'] = $files->id;
                    $aux['extension'] = $files->contest_file_versions[0]->extension;
                    $aux['file_name'] = $files->name;
                    switch($files->status){
                        case ContestFile::QUEUED: $aux['file_status'] = "Queued"; break;// = 0;
                        case ContestFile::ENCODING: $aux['file_status'] = "Encoding"; break;// = 1;
                        case ContestFile::ENCODED: $aux['file_status'] = "Encoded"; break;// = 2;
                        case ContestFile::ERROR: $aux['file_status'] = "Error"; break;// = 3;
                        case ContestFile::UPLOADING: $aux['file_status'] = "Uploading"; break;// = 4;
                        case ContestFile::CANCELED: $aux['file_status'] = "Canceled"; break;// = 5;
                        case ContestFile::UPLOAD_INTERRUPTED: $aux['file_status'] = "Upload Interrupted"; break;// = 6;
                    }
                    array_push($filesArray, $aux);
                    $aux = [];
                }
            }
        }

        return Excel::create($con->code."-FILES-".date('d-m-Y'), function($excel) use($filesArray,$filesKeys) {
            $excel->sheet('entries', function($sheet) use($filesArray,$filesKeys){
                $sheet->fromArray(array($filesKeys), null, 'A1', false, false
                );
                $sheet->fromArray($filesArray, null, 'A1', false, false);
            });
            $alphabet = range('A', 'Z');
        })->download('xls');
    }


    public function getAllInscriptionsData($contest) {
        /** @var Contest $con */
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest not found', 400);
        $pageItems = 20;
        $page = (int) Input::get('page');
        $page = max($page, 1);
        $query = Input::get('query');
        if ($page > 0) Paginator::setCurrentPage($page);
        $orderBy = Input::get('orderBy');
        $orderDir = Input::get('orderDir');
        $all = Input::get('all');
        $filterInscriptor = Input::get('inscriptor');
        $filterJudge = Input::get('judge');
        $filterOwner = Input::get('owner');
        $filterColaborator = Input::get('colaborator');
        $inscriptorTypes = Input::get('filterType');
        $filterJudgeType = Input::get('filterJudgeType');

        if($all) $pageItems = 1000000;
        switch($orderBy) {
            case "first_name":
            case "last_name":
            case "email":
            case "name":
            case "role":
            case "entries_count":
            case "deadline1_at":
            case "deadline2_at":
                break;
            default:
                $orderBy = "email";
                $orderDir = 'asc';
        }
        $roles = [];

        if($filterInscriptor == 1) array_push($roles, Inscription::INSCRIPTOR);
        if($filterJudge == 1) array_push($roles, Inscription::JUDGE);
        if($filterOwner == 1) array_push($roles, Inscription::OWNER);
        if($filterColaborator == 1) array_push($roles, Inscription::COLABORATOR);

        if(empty($roles)) $roles = [Inscription::INSCRIPTOR, Inscription::JUDGE, Inscription::OWNER, Inscription::COLABORATOR];

        if($orderDir == false) $orderDir = 'desc';
        else $orderDir = 'asc';

        $inscription_types = InscriptionType::where('contest_id', $con->id)->select('id','role', 'name')->get();

        $data = Inscription::select(array('inscriptions.id', 'users.first_name', 'users.email', 'users.last_name', 'inscription_types.name', 'inscriptions.role', 'inscriptions.deadline1_at', 'inscriptions.deadline2_at', DB::raw('COUNT(DISTINCT entry_categories.id) as entries_count'), 'inscriptions.inscription_type_id'))
            ->join('users', 'inscriptions.user_id', '=', 'users.id')
            ->leftJoin('inscription_types', 'inscriptions.inscription_type_id', '=', 'inscription_types.id')
            ->leftJoin('entries', function($join) use ($con){
                $join->on('users.id', '=', 'entries.user_id');
                $join->on(DB::raw('entries.deleted_at IS NULL'), DB::raw(''), DB::raw(''));
                $join->on(DB::raw('entries.contest_id = '.$con->id), DB::raw(''), DB::raw(''));
            })
            ->leftJoin('entry_categories', 'entries.id', '=', 'entry_categories.entry_id')
            ->where('inscriptions.contest_id', '=', $con->id)
            ->whereIn('inscriptions.role', $roles)
            ->where(
                function($q) use ($inscriptorTypes, $roles){
                    if(!empty($inscriptorTypes) && in_array(Inscription::INSCRIPTOR, $roles)) $q->whereIn('inscriptions.inscription_type_id', $inscriptorTypes)->where('inscriptions.role', Inscription::INSCRIPTOR);
                }
            )
            ->where(
                function($q) use ($filterJudgeType, $roles){
                    if(!empty($filterJudgeType) && in_array(Inscription::JUDGE, $roles)) $q->whereIn('inscriptions.inscription_type_id', $filterJudgeType)->where('inscriptions.role', Inscription::JUDGE);
                }
            )

            ->where(
                function($q) use ($query) {
                    $q->where('users.first_name', 'LIKE', '%'.$query.'%');
                    $q->orWhere('users.last_name', 'LIKE', '%'.$query.'%');
                    $q->orWhere('users.email', 'LIKE', '%'.$query.'%');
                }
            )
            ->groupBy("users.id")
            ->orderBy($orderBy, $orderDir)
            ->paginate($pageItems, ['inscriptions.id', 'users.first_name', 'users.email', 'users.last_name', 'inscription_types.name', 'inscriptions.role', 'inscriptions.deadline1_at', 'inscriptions.deadline2_at', 'entries_count']);
        $pagination = [
            'last' => $data->getLastPage(),
            'page' => $data->getCurrentPage(),
            'perPage' => $data->getPerPage(),
            'total' => $data->getTotal(),
            'orderBy' => $orderBy,
            'orderDir' => $orderDir == 'asc',
            'query' => $query,
            'inscriptor' => $filterInscriptor,
            'judge' => $filterJudge,
            'owner' => $filterOwner,
            'colaborator' => $filterColaborator,
        ];

        return Response::json(['status' => 200, 'data' => $data->getItems(), 'allRoles' => Inscription::getAllRoles(), 'query' => $query, 'pagination' => $pagination, 'inscriptionTypes' => $inscription_types]);
    }

    public function getInscription($contest, $inscriptionId=NULL) {
        /** @var Contest $con */
        $con = $this->getContest($contest, 'inscription');
        $inscription = Inscription::with(['user', 'inscriptionType'])->find($inscriptionId);
        $inscriptions = Inscription::where('id', $inscriptionId)->where('contest_id', $con->id)->orderBy('role')->with(['Contest','inscriptionMetadatas'])->get();
        if(!$inscription) {
            $inscription = new Inscription;
        }
        //$roles = Inscription::getAllRoles();
        $roles = $con->getAllRolesData();
        return Response::json(['status' => 200, 'inscriptionId' => $inscriptionId, 'inscriptions' => $inscriptions, 'inscription' => $inscription, 'showPermits' => Inscription::COLABORATOR, 'new' => $inscriptionId == NULL, 'allRoles' => $roles], 200, [], JSON_NUMERIC_CHECK);
    }

    public function getInscriptionForm($contest){
        $userId = Input::get('user_id');
        $con = $this->getContest($contest, 'inscription');
        $inscription = Inscription::where('user_id', $userId)
            ->where('contest_id', $con->id)
            ->orderBy('role')
            ->with(['inscriptionMetadatas'])
            ->first();

        $inscription['notes'] = Note::where('inscription_id', $inscription['id'])->select('id','msg','created_at')->get();

        return $inscription;
    }

    public function postInscription($contest, $inscriptionId=NULL) {
        /** @var $con Contest */
        $con = $this->getContest($contest);
        $new = false;
        $inscription = Input::get('inscription');
        $newInscription = Input::get('newInscription');
        $inscriptionId = Input::get('inscriptionId');
        $user = Input::get('user');
        $newUserData = Input::get('newUser');
        $inscriptorHasTypes = Input::get('inscriptorHasTypes');
        $JudgeHasTypes = Input::get('JudgeHasTypes');

        /* Es una invitacion, creo al usuario */
        if($newUserData != null){
            $newUser = new User();
            $newUser->email = $inscription['user'];
            $newUser->first_name = $newUserData['firstName'];
            $newUser->last_name = $newUserData['lastName'];
            $newUser->password = Hash::make($newUserData['password']);
            $newUser->active = 1;
            if($newUser->save()){
                $newInvitation = new Invitation();
                $newInvitation->contest_id = $con->id;
                $newInvitation->email = $inscription['user'];
                $newInvitation->password = $newUserData['password'];
                if(!$newInvitation->save()){
                    $newUser->delete();
                    return Response::json(array('flash'=> 'contest.errorCreatingInvitation'));
                }

            }else{
                return Response::json(array('flash'=> 'contest.errorCreatingUser'));
            }

            $hasInscription = Inscription::where('email', $inscription['user'])->whereNull('user_id')->get();

            foreach($hasInscription as $inscriptionExists){
                $update = new Inscription();
                $update->exists = true;
                $update->id = $inscriptionExists['id'];
                $update->user_id = $newUser->id;
                $update->save();
            }

            $inscription['user'] = array();
            $inscription['user']['id'] = $newUser['id'];
            $inscription['user']['first_name'] = $newUserData['firstName'];
            $inscription['user']['last_name'] = $newUserData['lastName'];
            $inscription['user']['email'] = $inscription['user'];
        }
        $rules = array(
            'role' => 'required',//exist!!!
            'start_at' => 'date|date_format:"Y-m-d H:i:s"',
            'deadline1_at' => 'date|date_format:"Y-m-d H:i:s"',
            'deadline2_at' => 'date|date_format:"Y-m-d H:i:s"',
        );

        if(isset($inscription['role'])){
            if(($inscription['role'] == Inscription::INSCRIPTOR || $inscription['role'] == Inscription::JUDGE) && $inscriptorHasTypes == 1 && $JudgeHasTypes == 1){
                $rules['inscription_type_id'] = 'required';
            }
        }
        $validator = Validator::make($inscription, $rules);
        if ($validator->fails()) {
            $errors = $validator->messages();
            return Response::json(array('errors'=>$errors));
        }

        if($newInscription){
            $userRoles = Inscription::where('user_id', '=', isset($inscription['user']['user_id']) ? $inscription['user']['user_id'] : $inscription['user']['id'])
                ->where('contest_id', '=', $con->id)
                ->get();

            foreach($userRoles as $roles){
                //array_push($dataRoles, $roles['role']);
                switch($roles['role']){
                    case Inscription::JUDGE: if( $inscription['role'] == Inscription::JUDGE ){
                        return Response::json(array('error'=> Lang::get('contest.userInscriptionCreated')));
                    }
                        break;
                    case Inscription::INSCRIPTOR: if( $inscription['role'] == Inscription::INSCRIPTOR ) {
                        return Response::json(array('error' => Lang::get('contest.errorNewInscriptionInscriptor'), 'role' => $roles['role']));
                    }
                        break;
                    case Inscription::OWNER: return Response::json(array('errors'=>true, 'flash'=> Lang::get('contest.errorNewInscriptionOwner')));
                        break;
                    case Inscription::COLABORATOR:
                        if( $inscription['role'] == Inscription::COLABORATOR ) {
                            return Response::json(array('error' => Lang::get('contest.errorNewInscriptionColaborator'), 'roles' => $roles['role']));
                        }
                        break;
                }
            }
        }

        /** @var $inscriptionCheck Inscription */
        $userId = isset($inscription['user']['id']) ? $inscription['user']['id'] : $inscription['user']['user_id'];
        //$userInscription = Inscription::where('role', '=', $inscription['role'])->where('contest_id', '=', $con->id)->where('user_id', '=', $userId)->first();
        $userInscription = Inscription::where('id', '=', $inscriptionId)->first();

        if($newInscription){
            $userInscription = new Inscription();
        }

        if ($inscription['role'] == Inscription::COLABORATOR) {
            $inscription['permits'] = json_encode(
                [
                    'admin' => isset($inscription['permits']['admin']) ? $inscription['permits']['admin'] : false,
                    'edit' => isset($inscription['permits']['edit']) ? ( $inscription['permits']['viewer'] == true ? $inscription['permits']['edit'] : false ) : false,
                    'billing' => isset($inscription['permits']['billing']) ? $inscription['permits']['billing'] : false,
                    'tech' => isset($inscription['permits']['tech']) ? $inscription['permits']['tech'] : false,
                    'sifter' => isset($inscription['permits']['sifter']) ? ( $inscription['permits']['viewer'] == true ? $inscription['permits']['sifter'] : false ) : false,
                    'design' => isset($inscription['permits']['design']) ? $inscription['permits']['design'] : false,
                    'viewer' => isset($inscription['permits']['viewer']) ? $inscription['permits']['viewer'] : false,
                    'voting' => isset($inscription['permits']['voting']) ? $inscription['permits']['voting'] : false,
                ],
                JSON_FORCE_OBJECT);
        }else{
            $inscription['permits'] = NULL;
        }
        $userInscription->contest_id = $con->id;
        $userInscription->user_id = $userId;

        $userInscription->save();
        $userInscription->update($inscription);
        $userInscription = Inscription::with(['user', 'inscriptionType'])->find($userInscription->id);
        return Response::json(array('flash'=> $new ? Lang::get('contest.userInscriptionCreated') : Lang::get('contest.userInscriptionSaved'), 'inscription' => $userInscription, 'data' => $inscription));
    }

    public function destroyInscription($contest, $inscriptionId) {
        /** @var $con Contest */
        $con = $this->getContest($contest, 'inscription');
        if(!$con) return Response::make('Contest not found', 400);
        Inscription::destroy($inscriptionId);
        return Response::json(array('flash'=>Lang::get('contest.userInscriptionDeleted')));
    }

    /**** Manejo de las páginas estáticas del concurso ****/
    /**
     * Devuelve la lista de las página estáticas asociadas al concurso.
     * @param string $contest
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllPagesData($contest) {
        /** @var Contest $con */
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest not found', 400);
        $pageItems = 20;
        $page = (int) Input::get('page');
        $page = max($page, 1);
        $query = Input::get('query');
        if ($page > 0) Paginator::setCurrentPage($page);
        $orderBy = Input::get('orderBy');
        $orderDir = Input::get('orderDir');
        switch($orderBy) {
            case "id":
            case "name":
                break;
            default:
                $orderBy = "id";
                $orderDir = 'asc';
        }
        if ($orderDir == false) $orderDir = 'desc';
        else $orderDir = 'asc';
        $data = ContestAsset::staticPage()->where('contest_id', '=', $con->id)->where('name', 'LIKE', '%'.$query.'%')->orderBy($orderBy, $orderDir)->paginate($pageItems, ['id', 'contest_id', 'type', 'name', 'code', 'content']);
        $pagination = [
            'last' => $data->getLastPage(),
            'page' => $data->getCurrentPage(),
            'perPage' => $data->getPerPage(),
            'total' => $data->getTotal(),
            'orderBy' => $orderBy,
            'orderDir' => $orderDir == 'asc',
            'query' => $query,
        ];
        return Response::json(['status' => 200, 'data' => $data->getItems(), 'query' => $query, 'pagination' => $pagination]);
    }

    /**
     * Devuelve el ContestAsste o crea uno vacío con el tipo definido como STATIC_PAGE
     * @param $contest
     * @param null $pageId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function getPage($contest, $pageId=NULL) {
        /** @var Contest $con */
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest not found', 400);
        $page = ContestAsset::find($pageId);
        $filters = null;
        if(!$page) {
            $page = new ContestAsset();
            $page->type = ContestAsset::STATIC_PAGE;
        }else{
            $entriesIds = "";
            $config = json_decode($page->config);
            if(count($config->filters) > 0){
                $filters = $config->filters;
            }
            $entries = PublicEntry::where('page_id', $pageId)->select('entry_id')->get()->toArray();
            foreach($entries as $entry){
                $entriesIds .= $entry['entry_id'].",";
            }
        }
        return Response::json(['status' => 200, 'page' => $page, 'entriesIds' => $entriesIds, 'filters' => $filters, 'new' => $pageId == NULL]);
    }

    /**
     * Devuelve el ContestAsste o crea uno vacío con el tipo definido como STATIC_PAGE
     * @param $contest
     * @param null $pageId
     * @return array
     */
    public function getPageContents($contest, $pageId=NULL, $lastEntryLoaded = NULL) {
        /** @var Contest $con */
        $con = $this->getContest($contest);
        if(!$con) App::abort(404, Lang::get('contest.notfound'));
        /** @var ContestAsset $page */
        $page = ContestAsset::where("contest_id","=",$con->id)->where("code","=",$pageId)->first();
        if(!$page || $page->type != ContestAsset::STATIC_PAGE){
            App::abort(404, Lang::get('general.pageNotFound'));
        }
        $data = [];
        $config = json_decode($page->config);

        $entries = PublicEntry::where('page_id', $page->id)->select('entry_id')->get()->toArray();

        if($entries || (isset($config->filters) && count($config->filters) > 0)){
            $entriesIds = [];
            $publicEntries = [];

            if(isset($config->filters) && count($config->filters) > 0){
                if(!$lastEntryLoaded){
                    $lastEntryLoaded = 0;
                    $data['totalEntries'] = Entry::whereIn('status', $config->filters)->where('contest_id', $con->id)->count();
                }
                $publicEntries = Entry::whereIn('status', $config->filters)->where('contest_id', $con->id)
                    ->with(['MainMetadata','filesFields','importantFields'])
                    ->skip($lastEntryLoaded)
                    ->take(24)
                    ->get();
            }else{
                foreach($entries as $entry){
                    array_push($entriesIds,$entry['entry_id']);
                }
                if(!$lastEntryLoaded){
                    $lastEntryLoaded = 0;
                    $data['totalEntries'] = Entry::whereIn('id', $entriesIds)->where('contest_id', $con->id)->count();
                }
                $publicEntries = Entry::whereIn('id', $entriesIds)->where('contest_id', $con->id)
                    ->with(['MainMetadata','filesFields','importantFields'])
                    ->skip($lastEntryLoaded)
                    ->take(24)
                    ->get();
            }
            foreach($publicEntries as $entry){
                if(isset($entry->mainMetadata) || $entry->mainMetadata!=null && count($entry->mainMetadata) != 0){
                    $first = $entry->mainMetadata->first();
                    if($first) $entry->name = $first->value;
                    else $entry->name = Lang::get('contest.entryNoTitle');
                }else{
                    $entry->name = Lang::get('contest.entryNoTitle');
                }
                $name = User::where('id', $entry->user_id)->select('first_name','last_name')->first();
                $entry->userName = $name->first_name." ".$name->last_name;
                $entry->user_id = null;
                unset($entry->user_id);
                $entry->contest_id = null;
                unset($entry->contest_id);
                unset($entry->status);
                $entry->status = null;
                $entry->categories_id = $entry->entryCategories()->lists('category_id');
            }

            $data['entries'] = $publicEntries;
        }
        //$data = $this->getContestMainData($contest);
        $data['content'] = $page->content;
        return $data;
    }

    /**
     * Salva/Crea ContestAsset del tipo STATIC_PAGE
     * @param $contest
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function postPage($contest) {
        /** @var Contest $con */
        $con = $this->getContest($contest);
        $new = false;
        if(!$con) return Response::make('Contest not found', 400);
        $page = Input::get('page');
        $entries = Input::get('entries');
        $filters = Input::get('filters');
        if (isset($page['id'])) {
            $staticPage = ContestAsset::find($page['id']);
            if (!$staticPage) return Response::json(['status' => 400, 'errors' => ['page' => Lang::get('contest.pageError')]]);
        } else {
            $staticPage = new ContestAsset;
            $new = true;
        }
        if (!isset($page['name'])) return Response::json(['status' => 400, 'errors' => ['name' => Lang::get('contest.nameError')]]);
        $staticPage->contest_id = $con->id;
        // Fuerzo a que sean del tipo 4 (STATIC_PAGE)
        $staticPage->type = ContestAsset::STATIC_PAGE;
        $staticPage->name = $page['name'];
        $staticPage->code = ContestAsset::getCode($page['name']);
        $staticPage->content = isset($page['content']) ? $page['content'] : "";
        $staticPage->save();

        /** The page has entries to show **/
        if(isset($filters) && count($filters) > 0){
            $staticPage->config = json_encode(["filters" => $filters]);
            $staticPage->save();
        }
        else{
            $staticPage->config = json_encode(["filters" => []]);
            $staticPage->save();
        }
        if(isset($entries)){
            PublicEntry::where('page_id', $staticPage->id)->delete();
            foreach($entries as $entry){
                if(isset($filters) && count($filters) > 0){
                    $entryStatus = Entry::where('contest_id', $con->id)->whereIn('status', $filters)->first();
                    if($entryStatus) continue;
                }
                $public_entry = new PublicEntry();
                $public_entry->entry_id = $entry['id'];
                $public_entry->page_id = $staticPage->id;
                $public_entry->save();
            }
        }

        return Response::json(array('flash'=> $new ? Lang::get('contest.pageCreated') : Lang::get('contest.pageSaved'), 'page' => $staticPage));
    }

    /**
     * Borra el ContestAsset del tipo STATIC_PAGE
     * @param $contest
     * @param $pageId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroyPage($contest, $pageId) {
        /** @var $con Contest */
        $con = $this->getContest($contest);
        if(!$contest) return Response::make('Contest not found', 400);
        ContestAsset::destroy($pageId);
        return Response::json(array('flash'=> Lang::get('contest.pageDeleted')));
    }

    /**** Manejo de las páginas estáticas del concurso ****/
    /**
     * Devuelve la lista de las página estáticas asociadas al concurso.
     * @param string $contest
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllAssetsData($contest) {
        /** @var Contest $con */
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest not found', 400);
        $pageItems = 20;
        $page = (int) Input::get('page');
        $page = max($page, 1);
        $query = Input::get('query');
        if ($page > 0) Paginator::setCurrentPage($page);
        $orderBy = Input::get('orderBy');
        $orderDir = Input::get('orderDir');
        switch($orderBy) {
            case "id":
            case "name":
                break;
            default:
                $orderBy = "id";
                $orderDir = 'asc';
        }
        if ($orderDir == false) $orderDir = 'desc';
        else $orderDir = 'asc';
        $data = ContestAsset::staticAsset()->where('contest_id', '=', $con->id)->where('name', 'LIKE', '%'.$query.'%')->orderBy($orderBy, $orderDir)->paginate($pageItems, ['id', 'contest_id', 'type', 'name', 'code', 'content_type','extension']);
        $pagination = [
            'last' => $data->getLastPage(),
            'page' => $data->getCurrentPage(),
            'perPage' => $data->getPerPage(),
            'total' => $data->getTotal(),
            'orderBy' => $orderBy,
            'orderDir' => $orderDir == 'asc',
            'query' => $query,
        ];
        return Response::json(['status' => 200, 'data' => $data->getItems(), 'query' => $query, 'pagination' => $pagination]);
    }

    /**
     * Borra el ContestAsset del tipo GENERAL_FILE
     * @param $contest
     * @param $assetId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws Exception
     */
    public function destroyAsset($contest, $assetId) {
        /** @var $con Contest */
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest not found', 400);
        /** @var ContestAsset $asset */
        $asset = ContestAsset::where('contest_id','=',$con->id)->where('id','=',$assetId)->firstOrFail();
        if($asset) {
            Cloud::Instance()->DeleteFileFromGCStorage($asset->getRelativePath(), $asset->getBucket());
            $asset->delete();
        }
        return Response::json(array('flash'=> Lang::get('contest.assetDeleted')));
    }

    /**
     * Script para actualizar los banners de los festivales
     */
    public function fixContestsBanners(){
        $contests = Contest::all();
        echo "<pre>";
        echo count($contests)." contests\n";
        $execute = false;
        $bannersTypes = [ContestAsset::BIG_BANNER, ContestAsset::SMALL_BANNER];
        foreach ($contests as $contest){
            echo "Fixing contest ".$contest->name." (".$contest->code.")\n";
            foreach ($bannersTypes as $type) {
                echo "Fixing banner type $type: ";
                $contestAsset = ContestAsset::where("contest_id", "=", $contest->id)->where("type", "=", $type)->first();
                $bannerFound = false;
                if ($contestAsset){
                    if($contestAsset->content != null) {
                        echo "Banner found. Uploading... ";
                        $contestAsset->extension = "jpg";
                        $image = base64_decode($contestAsset->content);
                        try {
                            if($execute) Cloud::Instance()->UploadFileToGCStorage($contestAsset->getRelativePath(), $contestAsset->getBucket(), $image);
                            echo "Done. ";
                        } catch (Google_Exception $e) {
                            echo "Error uploading banner to GCS. " . $e->getMessage();
                            continue;
                        }
                        echo "Changing type... ";
                        $contestAsset->type = ContestAsset::GENERAL_FILE;
                        $contestAsset->content_type = Format::getMimeType("jpg");
                        $contestAsset->content = null;
                        $bannerFound = true;
                        if($execute) $contestAsset->save();
                        echo "Done. ";
                    }else{
                        try {
                            $contestAsset->delete();
                        } catch (Exception $e) {
                        }
                        echo "Banner not found. ";
                    }
                }else{
                    echo "Banner not found. ";
                }
                echo "\nCreating HTML banner... ";
                $bannerHTMLAsset = new ContestAsset();
                $bannerHTMLAsset->type = $type == ContestAsset::BIG_BANNER ? ContestAsset::BIG_BANNER_HTML : ContestAsset::SMALL_BANNER_HTML;
                $bannerHTMLAsset->name = $type == ContestAsset::BIG_BANNER ? Lang::get('contest.bigBannerHTML') : Lang::get('contest.smallBannerHTML');
                if ($bannerFound) {
                    $link = URL::to("/".$contest->code."/").($type == ContestAsset::BIG_BANNER ? "#entries":"#home");
                    $imgUrl = $contestAsset->getURL($contest->code);
                    $bannerHTMLAsset->content = "<a href='$link'><img src='$imgUrl' style='width:100%;'/></a>";
                }else {
                    if ($type == ContestAsset::BIG_BANNER) {
                        $view = View::make('contest.banners.big', ['contest' => $contest->name]);
                        $bannerHTMLAsset->content = $view->render();
                    }elseif ($type == ContestAsset::SMALL_BANNER) {
                        $view = View::make('contest.banners.small', ['contest' => $contest->name]);
                        $bannerHTMLAsset->content = $view->render();
                    }
                }
                $bannerHTMLAsset->contest_id = $contest->id;
                if($execute) $bannerHTMLAsset->save();
                echo "Banner ready.\n";
            }
            echo "Contest Done\n";
        }
        echo "\n\nDONE!!!\n";
        echo "</pre>";
        exit();
    }

    /**** Manejo de las páginas estáticas del concurso ****/
    /**
     * Devuelve la lista de las página estáticas asociadas al concurso.
     * @param string $contest
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBillings($contest) {
        /** @var Contest $con */
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest not found', 400);
        $colaborator = $con->getUserInscription(Auth::user(), Inscription::COLABORATOR);
        $owner = $con->getUserInscription(Auth::user(), Inscription::OWNER);
        if(!$owner && (!$con->isColaborator(isset($colaborator->permits) ? $colaborator->permits : null, Contest::BILLING) && !$con->isColaborator(isset($colaborator->permits) ? $colaborator->permits : null, Contest::ADMIN)) && !Auth::user()->isSuperAdmin()){
            return Response::make('Authorization required', 400);
        }
        $pageItems = 20;
        $page = (int) Input::get('page');
        $page = max($page, 1);
        $query = Input::get('query');
        if ($page > 0) Paginator::setCurrentPage($page);
        $orderBy = Input::get('orderBy');
        $filters = Input::get('filters');
        $orderDir = Input::get('orderDir');
        switch($orderBy) {
            case "id":
            case "method":
            case "created_at":
            case "paid_at":
                break;
            case "name":
                //$orderBy = "first_name";
                break;
            default:
                $orderBy = "paid_at";
                $orderDir = false;
        }
        if ($orderDir == false) $orderDir = 'desc';
        else $orderDir = 'asc';
        /*$users = User::where('first_name', 'LIKE', '%'.$query.'%')
            ->orWhere('last_name', 'LIKE', '%'.$query.'%')
            ->orWhere('email', 'LIKE', '%'.$query.'%')->lists('id');*/
        $users = DB::table('inscriptions')
            ->join('users', 'inscriptions.user_id', '=', 'users.id')
            ->where('inscriptions.contest_id', '=', $con->id)
            ->where(
                function($q) use ($query) {
                    $q
                        ->where('users.first_name', 'LIKE', '%'.$query.'%')
                        ->orWhere('users.last_name', 'LIKE', '%'.$query.'%')
                        ->orWhere('users.email', 'LIKE', '%'.$query.'%');
                }
            )->lists('inscriptions.user_id');

        $data = Billing::where('contest_id', '=', $con->id)
            ->with(['billingEntryCategories','billingEntryCategories.entry'])
            ->with(['user'])
            ->where(
                function($q) use ($filters, $users, $query){
                    if($filters){
                        $q->whereIn('status', $filters);
                    }
                    if ($query != '') {
                        $q->where(function($q) use ($users, $query) {
                            $q->whereIn('user_id', $users);
                            $q->orWhereHas('billingEntryCategories', function ($q) use ($query) {
                                if ($query != '') $q->where('entry_id', 'LIKE', '%' . $query . '%');
                            });
                        });
                    }
                }
            );
        $data = $data->orderBy($orderBy, $orderDir)->paginate($pageItems);
        foreach($data->getItems() as $billing){
            foreach($billing->billing_entry_categories as $bec) {
                $entry = $bec->entry;
                if(!$entry) continue;
                if($entry->deleted_at != null) $bec->entry_deleted = true;
                if ($entry->mainMetadata != null && count($entry->mainMetadata) != 0) {
                    $first = $entry->mainMetadata->first();
                    if($first) $entry->name = $first->value;
                    else $entry->name = Lang::get('contest.entryNoTitle');
                } else {
                    $entry->name = Lang::get('contest.entryNoTitle');
                }
                if($con->type == Contest::TYPE_TICKET){
                    $bec->tickets = Ticket::where('billing_entry_category_id', $bec->id)->count();
                }
                $entry->main_metadata = null;
            }
        }
        foreach($data as $item){
            $item->inscription = Inscription::where('inscriptions.user_id',"=", $item->user_id)
                ->join('inscription_types','inscriptions.inscription_type_id',"=", 'inscription_types.id')
                ->where('inscriptions.role', 1)
                ->get();
        }
        //where('transaction_id', 'LIKE', '%'.$query.'%')->
        $pagination = [
            'last' => $data->getLastPage(),
            'page' => $data->getCurrentPage(),
            'perPage' => $data->getPerPage(),
            'total' => $data->getTotal(),
            'orderBy' => $orderBy,
            'orderDir' => $orderDir == 'asc',
            'query' => $query,
        ];
        return Response::json(['status' => 200, 'data' => $data->getItems(), 'query' => $query, 'pagination' => $pagination, 'filters'=>$filters]);
    }

    /**
     * Devuelve el Billing o crea uno vacío con el tipo definido como STATIC_PAGE
     * @param $contest
     * @param null $billId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function getBill($contest, $billId=NULL) {
        /** @var Contest $con */
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest not found', 400);
        /** @var Inscription $colaborator */
        /** @var Inscription $owner */
        $colaborator = $con->getUserInscription(Auth::user(), Inscription::COLABORATOR);
        $owner = $con->getUserInscription(Auth::user(), Inscription::OWNER);
        if(!$owner && (!$con->isColaborator(isset($colaborator->permits) ? $colaborator->permits : null, Contest::BILLING) && !$con->isColaborator(isset($colaborator->permits) ? $colaborator->permits : null, Contest::ADMIN)) && !Auth::user()->isSuperAdmin()){
            return Response::make('Authorization required', 400);
        }
        $bill = Billing::where('contest_id', $con->id)->with(['user','billingEntryCategories','billingEntryCategories.entry'])->findOrFail($billId);
        foreach($bill->billing_entry_categories as $bec) {
            $entry = $bec->entry;
            if(!$entry) continue;
            if($entry->deleted_at != null) $bec->entry_deleted = true;
            if ($entry->mainMetadata != null && count($entry->mainMetadata) != 0) {
                $first = $entry->mainMetadata->first();
                if($first) $entry->name = $first->value;
                else $entry->name = Lang::get('contest.entryNoTitle');
            } else {
                $entry->name = Lang::get('contest.entryNoTitle');
            }
            $entry->main_metadata = null;
            if($con->type == Contest::TYPE_TICKET){
                $bec->tickets = Ticket::where('billing_entry_category_id', $bec->id)->count();
            }
        }
        return Response::json(['status' => 200, 'bill' => $bill]);
    }

    /**
     * Devuelve el Billing o crea uno vacío con el tipo definido como STATIC_PAGE
     * @param $contest
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function postBillStatus($contest) {
        /** @var Contest $con */
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest not found', 400);
        $inscriptor = null;

        /** @var Inscription $colaborator */
        /** @var Inscription $owner */
        $colaborator = $con->getUserInscription(Auth::user(), Inscription::COLABORATOR);
        $owner = $con->getUserInscription(Auth::user(), Inscription::OWNER);
        if(!$owner && (!$con->isColaborator(isset($colaborator->permits) ? $colaborator->permits : null, Contest::BILLING) && !$con->isColaborator(isset($colaborator->permits) ? $colaborator->permits : null, Contest::ADMIN)) && !Auth::user()->isSuperAdmin()){
            return Response::make('Authorization required', 400);
        }
        $billData = Input::only(['bill']);
        $input = Input::only(['status']);
        if(!isset($billData['bill']['id'])) return Response::make('Bill not found', 400);
        $billId = $billData['bill']['id'];
        /** @var Billing $bill */
        $bill = Billing::where('contest_id', $con->id)->with('user')->findOrFail($billId);
        $rules = array(
            'status' => 'required|in:'.Billing::STATUS_PENDING.','.Billing::STATUS_SUCCESS.','.Billing::STATUS_ERROR.','.Billing::STATUS_PROCESSING
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails())
        {
            $messages = $validator->messages();
            return Response::json(array('errors'=>$messages));
        }
        $bill->status = $input['status'];
        if($bill->status == Billing::STATUS_SUCCESS){
            $bill->paid_at = \Carbon\Carbon::now();
            $bill->paid = $bill->price;
        }
        $bill->save();
        if($bill->status == Billing::STATUS_SUCCESS){
            if($con->id == 234){ /* HARDCODE IAB 2021*/
                $inscriptor = Inscription::where('user_id', $bill->user_id)
                                    ->where('contest_id', $con->id)
                                    ->select('inscription_type_id')
                                    ->first();

                if(isset($inscriptor->inscription_type_id) && $inscriptor->inscription_type_id == 95){
                    $emailSend = true;
                    $subject = Lang::get('contest.briefMaterialIAB', ["contest"=>$con->name]);
                    $response = OxoMailer::sendMail([
                        'email_to' => $bill->user->email,
                        'subject' => $subject,
                        'body' => Lang::get('contest.briefMaterialIABBody', [])
                    ]);

                };
            }
        }

        return Response::json(['status' => 200, 'bill' => $bill, '$emailSend' => isset($emailSend) ? true : false, '$inscriptor' => $inscriptor]);
    }

    public function reportPaymentCustomApi($contest, $billCode = null){
        $con = $this->getContest($contest);
        $txtfile = storage_path("reportpayCustomApi-".$con->code.".txt");
        file_put_contents($txtfile, "--------------------------------------------------------------\n", FILE_APPEND | LOCK_EX);
        $log = true;
        if($log) file_put_contents($txtfile, date("Y-m-d H:i:s")."\n".print_r(Input::all(), true)."\n", FILE_APPEND | LOCK_EX);
        if(!$con){
            if($log) file_put_contents($txtfile, "ERROR\n", FILE_APPEND | LOCK_EX);
            file_put_contents($txtfile, "--------------------------------------------------------------\n", FILE_APPEND | LOCK_EX);
            return Response::make('Contest not found', 400);
        }
        $input = Input::all();
        $billingId = $input['billingId'];
        $status = $input['paymentStatus'];
        $bill = Billing::where('contest_id', $con->id)
            ->where('id',$billingId)
            ->firstOrFail();
        if(!$bill){
            if($log) file_put_contents($txtfile, "ERROR\n", FILE_APPEND | LOCK_EX);
            file_put_contents($txtfile, "--------------------------------------------------------------\n", FILE_APPEND | LOCK_EX);
            return Response::make('error', 400);
        }
        $bill->status = $status;
        $bill->save();

        if($log){
            file_put_contents($txtfile, "user_id => ".print_r($bill->user_id, true)."\n", FILE_APPEND | LOCK_EX);
            file_put_contents($txtfile, "SUCCESS\n", FILE_APPEND | LOCK_EX);
        }
        file_put_contents($txtfile, "--------------------------------------------------------------\n", FILE_APPEND | LOCK_EX);
        return Response::make('success', 200);
    }

    /**
     * Devuelve el Billing o crea uno vacío con el tipo definido como STATIC_PAGE
     * @param $contest
     * @param string $billCode
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @var Contest $con
     */
    public function reportPayment($contest) {
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest not found', 400);

        $billingConf = $con->getBillingData();
        $MPData = $billingConf['methods']['MercadoPago']['data'];

        $txtfile = storage_path("reportpay.txt");
        $log = true;
        if($log) file_put_contents($txtfile, date("Y-m-d H:i:s")."\n".print_r(Input::all(), true)."\n", FILE_APPEND | LOCK_EX);

        MercadoPago\SDK::setAccessToken($MPData['accessToken']);

        $merchant_order = null;

        switch($_GET["topic"]) {
            case "payment":
                if($log) file_put_contents($txtfile, "PAYMENT\n", FILE_APPEND | LOCK_EX);

                try {
                    $payment = MercadoPago\Payment::find_by_id($_GET["id"]);
                    // Get the payment and the corresponding merchant_order reported by the IPN.
                    if($payment !== null)
                        $merchant_order = MercadoPago\MerchantOrder::find_by_id($payment->order->id);
                    //if($log) file_put_contents($txtfile, "merchant_order => ".print_r($merchant_order,true)."\n", FILE_APPEND | LOCK_EX);
                }catch (MercadoPagoException $exception){
                    if($log) file_put_contents($txtfile, $exception->getMessage(), FILE_APPEND | LOCK_EX);
                }

                if ($log) file_put_contents($txtfile, "POINT A\n", FILE_APPEND | LOCK_EX);

                if (isset($merchant_order->payments) && !empty($merchant_order->payments)) {
                    if ($log) file_put_contents($txtfile, "POINT B\n", FILE_APPEND | LOCK_EX);

                    if($log) file_put_contents($txtfile, "MERCHANT ORDER PAYMENTS => ".print_r($merchant_order->payments,true)."\n", FILE_APPEND | LOCK_EX);

                    $paid_amount = 0;
                    $rejected = false;
                    $processing = false;

                    foreach ($merchant_order->payments as $payment) {
                        switch($payment->status){
                            case "approved":
                                $paid_amount += $payment->transaction_amount;
                                break;
                            case "refunded":
                            case "charged_back":
                            case "cancelled":
                            case "rejected":
                                if($payment->status_detail == "cc_rejected_call_for_authorize") $processing = true;
                                else $rejected = true;
                                break;
                            case "pending":
                            case "in_process":
                            case "in_mediation":
                                $processing = true;
                                break;
                        }
                    }

                    if($log) file_put_contents($txtfile, "merchant_order->preference_id => ".print_r($merchant_order->preference_id,true)."\n", FILE_APPEND | LOCK_EX);

                    $bill = Billing::where('contest_id', $con->id)
                        //->where('id',$merchant_order_info['response']['external_reference'])
                        ->where('transaction_id', $merchant_order->preference_id)
                        ->first();

                    if($log) file_put_contents($txtfile, "BILL => ".print_r($bill,true)."\n", FILE_APPEND | LOCK_EX);

                    if ($bill) {
                        // If the payment's transaction amount is equal (or bigger) than the merchant_order's amount you can release your items
                        $bill->paid = $paid_amount;
                        if ($paid_amount >= $merchant_order->total_amount) {
                            $bill->status = Billing::STATUS_SUCCESS;
                            file_put_contents($txtfile, "PAGADO\n", FILE_APPEND | LOCK_EX);
                            //TODO Notify user and admins??
                        } else if ($rejected) {
                            $bill->status = Billing::STATUS_ERROR;
                            file_put_contents($txtfile, "REJECTED\n", FILE_APPEND | LOCK_EX);
                        } else if ($processing) {
                            $bill->status = Billing::STATUS_PROCESSING;
                            file_put_contents($txtfile, "PROCESSING\n", FILE_APPEND | LOCK_EX);
                        } else if ($paid_amount > 0) {
                            $bill->status = Billing::STATUS_PARTIALLY_PAID;
                            file_put_contents($txtfile, "PARTIALLY PAID\n", FILE_APPEND | LOCK_EX);
                        }
                        //$bill->payment_data = json_encode($pdata);
                        $bill->save();
                    }
                }

            break;

            /*case "merchant_order":
                if($log) file_put_contents($txtfile, "MERCHANT ORDER\n", FILE_APPEND | LOCK_EX);
                $merchant_order = MercadoPago\MerchantOrder::find_by_id($_GET["id"]);
                break;*/
        }

        if($log) file_put_contents($txtfile, "POINT END\n", FILE_APPEND | LOCK_EX);

        return Response::make('OK', 200);

        /*$mp = new MP($MPData['clientId'], $MPData['clientSecret']);

        if($log) file_put_contents($txtfile, "POINT A\n", FILE_APPEND | LOCK_EX);

        $sandbox = Config::get('billing.MercadoPagoSandbox');
        $mp->sandbox_mode($sandbox);

        if($log) file_put_contents($txtfile, "POINT B\n", FILE_APPEND | LOCK_EX);
        if (!isset($input["id"], $input["topic"]) || !ctype_digit($input["id"])) {
            return Response::make('Bad request', 400);
        }

        if($log) file_put_contents($txtfile, "POINT C\n", FILE_APPEND | LOCK_EX);
        $merchant_order_info = null;
        if(isset($_GET["topic"]) && $_GET["topic"] == 'payment'){
            if($log) file_put_contents($txtfile, "POINT D1\n", FILE_APPEND | LOCK_EX);
            try {
                $payment_info = $mp->get(($sandbox ? "/sandbox" : "") . "/collections/notifications/" . $input["id"]);
                $merchant_order_info = $mp->get(($sandbox?"/sandbox":"")."/merchant_orders/" . $payment_info["response"]["collection"]["merchant_order_id"]);
                // Get the merchant_order reported by the IPN.
                if($log) file_put_contents($txtfile, "payment_info => ".print_r($payment_info,true)."\n", FILE_APPEND | LOCK_EX);
            }catch (MercadoPagoException $exception){
                if($log) file_put_contents($txtfile, $exception->getMessage(), FILE_APPEND | LOCK_EX);
            }
            if($log) file_put_contents($txtfile, "POINT D12\n", FILE_APPEND | LOCK_EX);
        } else if(isset($_GET["topic"]) && $_GET["topic"] == 'merchant_order'){
            if($log) file_put_contents($txtfile, "POINT D2\n", FILE_APPEND | LOCK_EX);
            try {
                $merchant_order_info = $mp->get(($sandbox?"/sandbox":"")."/merchant_orders/" . $input["id"]);
            }catch (MercadoPagoException $exception){
                if($log) file_put_contents($txtfile, $exception->getMessage(), FILE_APPEND | LOCK_EX);
            }
        }

        if ($merchant_order_info != null) {
            if($log) file_put_contents($txtfile, "merchant_order_info => ".print_r($merchant_order_info,true)."\n", FILE_APPEND | LOCK_EX);
        }
        if ($merchant_order_info != null && $merchant_order_info["status"] == 200) {
            if($log) file_put_contents($txtfile, "POINT E\n", FILE_APPEND | LOCK_EX);
            if($log) file_put_contents($txtfile, "merchant_order_info => ".print_r($merchant_order_info,true)."\n", FILE_APPEND | LOCK_EX);
            $bill = Billing::where('contest_id', $con->id)
                ->where('id',$merchant_order_info['response']['external_reference'])
                ->firstOrFail();

            $pdata = json_decode($bill->payment_data, true);
            $pdata = array_merge(['payments_data'=>[]], $pdata);
            if(!array_key_exists('payments_data', $pdata)) $pdata['payments_data'] = [];

            // If the payment's transaction amount is equal (or bigger) than the merchant_order's amount you can release your items
            $paid_amount = 0;

            $rejected = false;
            $processing = false;
            foreach ($merchant_order_info["response"]["payments"] as  $payment) {
                switch($payment['status']){
                    case "approved":
                        $paid_amount += $payment['transaction_amount'];
                        break;
                    case "refunded":
                    case "charged_back":
                    case "cancelled":
                    case "rejected":
                        if($payment["status_detail"] == "cc_rejected_call_for_authorize") $processing = true;
                        else $rejected = true;
                        break;
                    case "pending":
                    case "in_process":
                    case "in_mediation":
                        $processing = true;
                        break;
                }
                $pdata['payments_data'][$payment['id']] = $payment;
            }

            $bill->paid = $paid_amount;
            if($paid_amount >= $merchant_order_info["response"]["total_amount"]){
                $bill->status = Billing::STATUS_SUCCESS;
                //TODO Notify user and admins??
            } else if($rejected){
                $bill->status = Billing::STATUS_ERROR;
            } else if($processing){
                $bill->status = Billing::STATUS_PROCESSING;
            } else if($paid_amount > 0){
                $bill->status = Billing::STATUS_PARTIALLY_PAID;
                //print_r("Not paid yet. Do not release your item.");
            }

            $bill->payment_data = json_encode($pdata);
            $bill->save();
        }
        if($log) file_put_contents($txtfile, "POINT F\n", FILE_APPEND | LOCK_EX);
        return Response::make('OK', 200);*/
    }

    public function reportPaymentStripe($contest, $billCode = null)
    {
        Stripe::setApiKey('');
        $con = $this->getContest($contest);
        if (!$con) return Response::make('Contest not found', 400);

        $txtfile = storage_path("reportpay.txt");

        $log = true;
        if ($log) file_put_contents($txtfile, date("Y-m-d H:i:s") . "\n" . print_r(Input::all(), true) . "\n", FILE_APPEND | LOCK_EX);
        if ($log) file_put_contents($txtfile, "PAGADO CON STRIPE YEAH\n", FILE_APPEND | LOCK_EX);

        // You can find your endpoint's secret in your webhook settings
        $endpoint_secret = 'whsec_8DTU2hYH07LrJIbkMwl9y184UFLmXDXK';

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        // Handle the checkout.session.completed event
        if ($event->type == 'checkout.session.completed') {
            if ($log) file_put_contents($txtfile, "PAGADO CON STRIPE SESSION COMPLETED YEAH\n", FILE_APPEND | LOCK_EX);
            $session = $event->data->object;

            // Fulfill the purchase...
            $payment = Billing::where('payment_data', $session->payment_intent)->first();
            if($payment){
                switch($session->payment_status){
                    case "paid":
                        $payment->status = Billing::STATUS_SUCCESS;
                        $payment->save();
                        break;
                    case "unpaid":
                        $payment->status = Billing::UNPAID;
                        $payment->save();
                        break;
                    case "no_payment_required":
                        break;
                }
            }
        }

        if ($log) file_put_contents($txtfile, "PAGADO CON STRIPE RETURN OK\n", FILE_APPEND | LOCK_EX);

        return Response::make('OK', 200);
    }

    public function reportPaymentClicpagoGet($contest) {
        return Response::make('OK', 200);
    }
    /**
     * Devuelve el Billing o crea uno vacío con el tipo definido como STATIC_PAGE
     * @param $contest
     * @return \Illuminate\Http\JsonResponse|\Illuminmyapate\Http\Response
     */
    public function reportPaymentClicpago($contest, $code) {
        $con = $this->getContest($contest);
        $bill = Billing::where('code','=',$code)->where('contest_id','=',$con->id)->firstOrFail();
        $transaction_id = mcrypt_decrypt(MCRYPT_BLOWFISH, Billing::ClicPagoDescryptKey, base64_decode(Input::get('clicpagoEncryptedTxId')), MCRYPT_MODE_ECB);

        $bill->status = Billing::STATUS_SUCCESS;
        $bill->transaction_id = trim($transaction_id);
        $bill->save();
        return Response::make('OK', 200);
    }

    public function getEntryBillingStatusPage($contest, $entryId, $billingStatus){
        $con = $this->getContest($contest);
        if(!Auth::check()){
            return Redirect::to('/#home');
        }
        $user = Auth::user();
        /** @var Entry $entry */
        if($user->isSuperAdmin() || $con->getUserInscription($user, Inscription::OWNER) || $con->getUserInscription($user, Inscription::COLABORATOR)){
            $entry = Entry::where('id','=',$entryId)->where('contest_id','=',$con->id)->firstOrFail();
        }
        elseif($con->getUserInscription($user, Inscription::INSCRIPTOR))
        {
            $entry = Entry::where('id','=',$entryId)->where('contest_id','=',$con->id)->where('user_id','=',$user->id)->firstOrFail();
        }

        //$preference_id = Input::get('preference_id');
        //$bill_id = Input::get('external_reference');
        $bill = $entry->billings[0];
        /*$bill = Billing::where('contest_id', $con->id)
            ->where('transaction_id', $preference_id)
            ->where('id',$bill_id)
            ->with('user')
            ->with('billingEntryCategories')
            ->firstOrFail();*/
        return View::make('contest.billing.redirect-index', ['contest' => $con, 'entry'=>$entry, 'bill'=>$bill, 'billingStatus'=>$billingStatus]);
    }

    public function getEntryBillingRedirectHome($contest){
        $con = $this->getContest($contest);
        /** @var VotingUser $invite */
        return View::make('contest.billing.home', ['contest' => $con]);
    }
    /**
     *
     * @param $contest
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function postBill($contest) {
        /** @var Contest $con */
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest not found', 400);

        /** @var Inscription $colaborator */
        /** @var Inscription $owner */
        $colaborator = $con->getUserInscription(Auth::user(), Inscription::COLABORATOR);
        $owner = $con->getUserInscription(Auth::user(), Inscription::OWNER);
        if(!$owner && (!$colaborator || !$con->isColaborator($colaborator->permits, Contest::BILLING)) && !Auth::user()->isSuperAdmin()){
            return Response::make('Authorization required', 400);
        }
        $input = Input::only('bill');
        $billData = $input['bill'];
        if(!isset($billData['id'])) return Response::make('Bill not found', 400);
        $billId = $billData['id'];
        /** @var Billing $bill */
        $bill = Billing::where('contest_id', $con->id)->with('user')->findOrFail($billId);
        $total = 0;
        foreach($billData['billing_entry_categories'] as $bCat){
            /** @var BillingEntryCategory $bCatRow */
            $bCatRow = BillingEntryCategory::where('billing_id', $billId)->where('category_id', $bCat['category_id'])->where('entry_id', $bCat['entry_id'])->first();
            $bCatRow->price = $bCat['price'];
            $total += $bCatRow->price;
            $bCatRow->save();
        }
        //$bill->price = $total;
        $bill->price = $billData['price'];
        $bill->save();
        $bill = Billing::where('contest_id', $con->id)->with(['user','billingEntryCategories','billingEntryCategories.entry'])->findOrFail($billId);
        return Response::json(['status' => 200, 'bill' => $bill]);
    }

    public function getUserPayments($contest){
        /** @var $contest Contest */
        $contest = $this->getContest($contest);
        $pagination = Input::only('page','perPage','query','sortBy','sortInverted','selectedTypes','user','statusFilters');
        $inscription = $contest->getUserInscription(Auth::user(), Inscription::INSCRIPTOR);
        $user = Auth::user();
        if($inscription){
            $pageItems = 20;
            $page = (int) Input::get('page');
            $page = max($page, 1);
            $query = Input::get('query');
            if ($page > 0) Paginator::setCurrentPage($page);
            $orderBy = Input::get('orderBy');
            $filters = Input::get('filters');
            $orderDir = Input::get('orderDir');
            switch($orderBy) {
                case "id":
                case "method":
                case "created_at":
                case "paid_at":
                    break;
                case "name":
                    //$orderBy = "first_name";
                    break;
                default:
                    $orderBy = "paid_at";
                    $orderDir = false;
            }
            if ($orderDir == false) $orderDir = 'desc';
            else $orderDir = 'asc';
            $users = [Auth::id()];

            $data = Billing::where('contest_id', '=', $contest->id)
                ->with(['billingEntryCategories','billingEntryCategories.entry'])
                ->with(['user'])
                ->where(
                    function($q) use ($filters, $users, $query){
                        if($filters){
                            $q->whereIn('status', $filters);
                        }
                        if (count($users)) {
                            $q->whereIn('user_id', $users);
                        }
                        if ($query != '') {
                            $q->where(function($q) use ($users, $query) {
                                $q->orWhereHas('billingEntryCategories', function ($q) use ($query) {
                                    if ($query != '') $q->where('entry_id', 'LIKE', '%' . $query . '%');
                                });
                            });
                        }
                    }
                );
            $data = $data->orderBy($orderBy, $orderDir)->paginate($pageItems);
            foreach($data->getItems() as $billing){
                foreach($billing->billing_entry_categories as $bec) {
                    $entry = $bec->entry;
                    if(!$entry) continue;
                    if($entry->deleted_at != null) $bec->entry_deleted = true;
                    if ($entry->mainMetadata != null && count($entry->mainMetadata) != 0) {
                        $first = $entry->mainMetadata->first();
                        if($first) $entry->name = $first->value;
                        else $entry->name = Lang::get('contest.entryNoTitle');
                    } else {
                        $entry->name = Lang::get('contest.entryNoTitle');
                    }
                    $entry->main_metadata = null;
                }
            }
            foreach($data as $item){
                $item->inscription = Inscription::where('inscriptions.user_id',"=", $item->user_id)
                    ->join('inscription_types','inscriptions.inscription_type_id',"=", 'inscription_types.id')
                    ->where('inscriptions.role', 1)
                    ->get();
            }
            //where('transaction_id', 'LIKE', '%'.$query.'%')->
            $pagination = [
                'last' => $data->getLastPage(),
                'page' => $data->getCurrentPage(),
                'perPage' => $data->getPerPage(),
                'total' => $data->getTotal(),
                'orderBy' => $orderBy,
                'orderDir' => $orderDir == 'asc',
                'query' => $query,
            ];
            return Response::json(['status' => 200, 'data' => $data->getItems(), 'query' => $query, 'pagination' => $pagination, 'filters'=>$filters]);
        }
    }

    /**
     * Devuelve el Billing o crea uno vacío con el tipo definido como STATIC_PAGE
     * @param $contest
     * @param null $billId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function getUserPayment($contest, $billId=NULL) {
        /** @var Contest $con */
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest not found', 400);
        $bill = Billing::where('contest_id', $con->id)->where('user_id', Auth::id())->with(['user','billingEntryCategories','billingEntryCategories.entry'])->findOrFail($billId);
        foreach($bill->billing_entry_categories as $bec) {
            $entry = $bec->entry;
            if(!$entry) continue;
            if($entry->deleted_at != null) $bec->entry_deleted = true;
            if ($entry->mainMetadata != null && count($entry->mainMetadata) != 0) {
                $first = $entry->mainMetadata->first();
                if($first) $entry->name = $first->value;
                else $entry->name = Lang::get('contest.entryNoTitle');
            } else {
                $entry->name = Lang::get('contest.entryNoTitle');
            }
            $entry->main_metadata = null;
        }
        return Response::json(['status' => 200, 'bill' => $bill]);
    }

    /**
     * @param $contestCode
     * @return \Illuminate\View\View
     */
    public function getPaymentsView($contestCode){
        $contest = $this->getContest($contestCode);
        $colaborator = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
        $permits = $colaborator['permits'];
        if($contest->isInscriptor()) {
            return View::make('admin.contests.form-billing', ['contest' => $contest, 'admin' => false, 'superadmin' => false, 'permits' => $permits]);
        }else{
            App::abort(404);
        }
    }

    public function getPaymentView($contestCode){
        $contest = $this->getContest($contestCode);
        $colaborator = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
        $permits = $colaborator['permits'];
        if($contest->isInscriptor()) {
            return View::make('admin.contests.form-bill', ['contest' => $contest, 'admin' => false, 'superadmin' => false, 'permits' => $permits]);
        }else{
            App::abort(404);
        }
    }

    /**
     * Retorna una lista de usuario para el autocomplete.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersData() {
        $term = Input::get('term');
        $contestId = Input::get('contest');
        $roles = null;
        if($contestId != null){
            $allUsers = User::where('users.email', 'LIKE', '%'.$term.'%')
                ->leftJoin('inscriptions', 'inscriptions.user_id', '=', 'users.id')
                ->where('inscriptions.contest_id', '=', $contestId)
                ->where('inscriptions.role', '!=', Inscription::JUDGE )
                ->select('users.id as user_id', 'users.first_name', 'users.last_name', 'users.email')
                ->get();
        }
        else{
            $allUsers = User::where('email', 'LIKE', '%'.$term.'%')
                ->select('id as user_id', 'first_name', 'last_name', 'email')
                ->get();
            if(isset($allUsers['user_id'])){
                $roles = Inscription::where('user_id', '=', $allUsers['user_id']);
            }
        }
        return Response::json(['status' => 200, 'data' => $allUsers, 'roles' => $roles]);
    }

    /**
     * Post del formulario de votaciones
     * @param $contest
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */

    public function postVotingSessionList($contest){
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest not found', 400);
        $pageItems = 20;
        $page = (int) Input::get('page');
        $page = max($page, 1);
        $query = Input::get('query');
        if ($page > 0) Paginator::setCurrentPage($page);
        $orderBy = Input::get('orderBy');
        $orderDir = Input::get('orderDir');
        switch($orderBy) {
            case "name":
            case "contest_id":
            case "vote_type":
            case "start_at":
            case "finish_at":
                break;
            default:
                $orderBy = "start_at";
                $orderDir = 'asc';
        }
        if($orderDir == false) $orderDir = 'desc';
        else $orderDir = 'asc';
        $data = DB::table('voting_sessions')
            ->where('contest_id', '=', $con->id)
            ->where('name', 'LIKE', '%'.$query.'%')
            ->orderBy($orderBy, $orderDir)
            ->paginate($pageItems, ['code', 'name', 'vote_type', 'start_at', 'finish_at']);
        $voteTypes = VotingSession::getAllVoteTypes();
        $pagination = [
            'last' => $data->getLastPage(),
            'page' => $data->getCurrentPage(),
            'perPage' => $data->getPerPage(),
            'total' => $data->getTotal(),
            'orderBy' => $orderBy,
            'orderDir' => $orderDir == 'asc',
            'query' => $query,
        ];
        $voting = [];
        $votingSession = $data->getItems();
        foreach($votingSession as $key => $items){
            //$voting[$key] = VotingSession::judges()->where('code','=',$items->code)->where('contest_id','=',$con->id)->firstOrFail();
            $voting[$key] = VotingSession::where('code','=',$items->code)->where('contest_id','=',$con->id)->firstOrFail();
            if($voting[$key]->public == 1){
                $voting[$key] = 0;
                continue;
            }
            $voting[$key]->loadJudgesProgress();
            $items = json_decode($voting[$key]);
            $totalProgress = 0;
            foreach($items->voting_users as $judge){
                if($judge->progress->total == 0) continue;
                $totalProgress += $judge->progress->votes / $judge->progress->total;
            };
            if(count($voting[$key]->voting_users) > 0){
                $totalProgress = round(($totalProgress/count($voting[$key]->voting_users)*100 * 100)/100);
                $voting[$key] = $totalProgress;
            }else{
                $voting[$key] = 0;
            }
        }

        return Response::json(['status' => 200, 'data' => $votingSession, 'query' => $query, 'pagination' => $pagination, 'voteTypes' => $voteTypes, 'votersProgress' => $voting]);
    }

    function cmp($a, $b)
    {
        if ($a->id == $b->id) {
            return 0;
        }
        return ($a->id < $b->id) ? -1 : 1;
        // Orden anterior: Por nombre de entry
        // return strcmp($a->name, $b->name);
    }

    public function postVotingSession($contest) {
        /** @var $con Contest */
        $con = $this->getContest($contest);
        $new = false;
        $votingCategories = Input::get('votingCategories');
        $updatedJudges = Input::get('updatedJudges');
        $id = Input::get('voting.code');
        $voting_categories_data = Input::get('voting.voting_categories');
        $voting = Input::only('voting.name', 'voting.vote_type', 'voting.config', 'voting.start_at', 'voting.finish_at', 'voting.finish_at2', 'voting.public', 'voting.publicAnonymous');
        $rules = array(
            'name' => 'required',//exist!!!
            'vote_type' => 'required',
            'start_at' => 'required|date|date_format:"Y-m-d H:i:s"',
            'finish_at' => 'required|date|date_format:"Y-m-d H:i:s"',
            'public' => 'boolean',
            'publicAnonymous' => 'boolean'
        );

        $validator = Validator::make($voting['voting'], $rules);
        if ($validator->fails())
        {
            $messages = $validator->messages();
            return Response::json(array('errors'=>$messages));
        }

        if($id != null){
            $votingSession = VotingSession::where('code', '=', $id)->where('contest_id','=',$con->id)->firstOrFail();
            $voting['voting']['public'] = intval($voting['voting']['public']) == 1;
            $voting['voting']['publicAnonymous'] = intval($voting['voting']['publicAnonymous']) == 1;
            if(isset($voting['voting']['config'])){
                if(isset($voting['voting']['config']['oxoMeeting']) && $voting['voting']['config']['oxoMeeting'] === 1){
                    $voting['voting']['config']['oxoMeetingLink'] = 'oxoMeet-'.$votingSession['code'];
                    if(isset($voting['voting']['config']['oxoMeetingPassword'])){
                        $voting['voting']['config']['oxoMeetingPassword'] === "" ? $voting['voting']['config']['oxoMeetingPassword'] = str_random(10) : "";
                    }else{
                        $voting['voting']['config']['oxoMeetingPassword'] = str_random(10);
                    }
                }
                $voting['voting']['config'] = json_encode($voting['voting']['config']);
            }
            $votingSession->update($voting['voting']);
            VotingCategory::where('voting_session_id', '=', $votingSession->id)->delete();
            foreach($votingCategories as $categoryId)
            {
                //TODO Validar ID de categoría con el contest
                $voteCategory = VotingCategory::firstOrCreate(['voting_session_id'=> $votingSession->id,'category_id'=>$categoryId]);
                foreach ($voting_categories_data as $voteCatData){
                    if($voteCatData['category_id'] == $categoryId){
                        if(isset($voteCatData['vote_config']) && $voteCatData['vote_config'] != null){
                            $voteCategory->vote_config = json_encode($voteCatData['vote_config']);
                            $voteCategory->save();
                            break;
                        }
                    }
                }
            }
            $shortList = VotingShortlist::where('voting_session_id', $votingSession->id)->select('entry_category_id')->get()->toArray();
        } else{
            $votingSession = new VotingSession();
            $votingSession->name = $voting['voting']['name'];
            $votingSession->vote_type = $voting['voting']['vote_type'];
            if(isset($voting['voting']['config'])){
                $votingSession->config = json_encode($voting['voting']['config']);
            }
            else $votingSession->config = null;
            $votingSession->contest_id = $con->id;
            $votingSession->start_at = $voting['voting']['start_at'];
            $votingSession->finish_at = $voting['voting']['finish_at'];
            $votingSession->finish_at2 = $voting['voting']['finish_at2'];
            if($voting['voting']['public']) $votingSession->public = $voting['voting']['public'] == 1;
            if($voting['voting']['publicAnonymous']) $votingSession->publicAnonymous = $voting['voting']['publicAnonymous'] == 1;
            else $votingSession->public = false;
            $votingSession->code = VotingSession::createCode();
            $votingSession->save();
            foreach($votingCategories as $categoryId)
            {
                //TODO Validar ID de categoría con el contest
                $voteCategory = VotingCategory::firstOrCreate(['voting_session_id'=> $votingSession->id,'category_id'=>$categoryId]);
                foreach ($voting_categories_data as $voteCatData){
                    if($voteCatData['category_id'] == $categoryId){
                        if(isset($voteCatData['vote_config']) && $voteCatData['vote_config'] != null){
                            $voteCategory->vote_config = json_encode($voteCatData['vote_config']);
                            $voteCategory->save();
                            break;
                        }
                    }
                }
            }
            $shortList = VotingShortlist::where('voting_session_id', $votingSession->id)->select('entry_category_id')->get()->toArray();
        }
        foreach($updatedJudges as $judgeId => $groupsIds)
        {
            $votingUser = VotingUser::where('voting_session_id', $votingSession->id)->where('id',$judgeId)->firstOrFail();
            VotingUserVotingGroup::where('voting_user_id', $votingUser->id)->delete();
            foreach($groupsIds as $groupId) {
                VotingUserVotingGroup::firstOrCreate(['voting_user_id' => $votingUser->id, 'voting_group_id' => $groupId]);
            }
        }
        $votingSession = VotingSession::judges()->where('id', '=', $votingSession->id)->where('contest_id','=',$con->id)->firstOrFail();
        $votingSession->loadJudgesProgress();

        foreach($votingSession->voting_groups as $key => $group){
            $votingSession->voting_groups[$key]->countEntries = VotingGroupEntryCategory::where('voting_group_id', $group->id)->count();
        }

        $votingSession->autoAbstains = AutoAbstain::where('voting_session_id', $votingSession->id)
            ->lists('voting_session_id');

        return Response::json(array('shortList' => $shortList, 'contest_id' => $con->id, 'voting' => $votingSession, 'flash'=>Lang::get('contest.votingSessionSaved'), 'votingCategories' => $votingCategories), 200, [], JSON_NUMERIC_CHECK);
    }

    public function postShortList($contest){
        $con = $this->getContest($contest);
        $id = Input::get('votingId');
        $shortList = Input::get('shortList');
        $votingSession = VotingSession::where('code', '=', $id)->where('contest_id','=',$con->id)->firstOrFail();
        VotingShortlist::where('voting_session_id', $votingSession->id)->delete();
        foreach($shortList as $value){
            $voting = new VotingShortlist();
            $voting->voting_session_id = $votingSession->id;
            $voting->entry_category_id = $value;
            $voting->save();
        }
    }


    public function postVotingSessionNewGroup($contest) {
        /** @var $con Contest */
        $con = $this->getContest($contest);
        $id = Input::get('code');

        $votingSession = VotingSession::where('code', '=', $id)->where('contest_id','=',$con->id)->firstOrFail();

        $group = VotingGroup::create(['name'=>Lang::get('voting.newGroupName', ['num' => count($votingSession->votingGroups) + 1]), 'voting_session_id' => $votingSession->id]);
        return Response::json($group, 200, [], JSON_NUMERIC_CHECK);
    }

    public function postVotingSessionInvites($contest, $votingCode){
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest not found', 400);
        /** @var VotingSession $votingSession */
        $votingSession = VotingSession::where('code', '=', $votingCode)->where('contest_id','=',$con->id)->firstOrFail();
        $groupId = Input::get('group');
        $group = null;
        if($groupId != null){
            $group = VotingGroup::where('voting_session_id', $votingSession->id)->where('id',$groupId)->firstOrFail();
        }
        $emails = Input::get('emails');
        $emailsLines = explode("\n", str_replace(["\r",","],"\n", $emails));
        $goodEmails = [];
        $emailsNames = [];
        foreach ($emailsLines as $emailLine) {
            if($emailLine != ''){
                $emailLine = str_replace("\t"," ",$emailLine);
                $spacePos = strpos($emailLine, " ");
                if($spacePos !== false){
                    $email = substr($emailLine, 0, $spacePos);
                    $name = substr($emailLine, $spacePos + 1);
                }else{
                    $email = $emailLine;
                    $name = "";
                }
                $validator = Validator::make(
                    array(
                        'email' => $email
                    ),
                    array(
                        'email' => 'required|email'
                    )
                );
                if (!$validator->fails())
                {
                    if(!in_array($email, $goodEmails)) array_push($goodEmails, $email);
                    $emailsNames[$email] = $name;
                }
            }
        }

        if(count($goodEmails) == 0) {
            return Response::json(['status' => 200, 'errors' => Lang::get("contest.votingNoGoodEmails")]);
        }

        foreach ($goodEmails as $email) {
            /** @var User $juser */
            /** @var Inscription $inscription */
            $juser = User::where('email','=',$email)->first(['id','first_name','last_name','email']);
            if($juser){
                $inscription = $con->getUserInscription($juser, Inscription::JUDGE);
                if(!$inscription){
                    $inscription = new Inscription();
                    $inscription->role = Inscription::JUDGE;
                    $inscription->user_id = $juser->id;
                    $inscription->contest_id = $con->id;
                    $inscription->save();
                }
            }else{
                $inscription = Inscription::firstOrNew(['email'=>$email,'role' => Inscription::JUDGE,'contest_id'=>$con->id]);
                //$inscription->role = Inscription::JUDGE;
                //$inscription->email = $email;
                //$inscription->contest_id = $con->id;
                $name = $emailsNames[$email];
                $inscription->invitename = $name;
                $inscription->save();
            }
            $votingUser = VotingUser::where('voting_session_id','=',$votingSession->id)->where('inscription_id','=',$inscription->id)->first();
            if(!$votingUser) {
                $votingUser = new VotingUser();
                $votingUser->voting_session_id = $votingSession->id;
                $votingUser->inscription_id = $inscription->id;
                $votingUser->invitation_key = str_random(60);
                $votingUser->save();
            }
            if($group){
                VotingUserVotingGroup::firstOrCreate(['voting_group_id'=>$group->id, 'voting_user_id'=>$votingUser->id]);
            }
        }

        $votingSession = VotingSession::judges()->where('id', '=', $votingSession->id)->firstOrFail();
        $votingSession->loadJudgesProgress();
        return Response::json(['status' => 200, 'msg'=> Lang::get("contest.votingJudgesCreated"), 'voting' => $votingSession]);
    }

    public function getVotingKeys($contest, $votingCode){
        $con = $this->getContest($contest, false);
        /** @var VotingSession $votingSession */
        $votingSession = VotingSession::where('code','=',$votingCode)->where('contest_id','=',$con->id)->firstOrFail();
        $groupId = Input::get('group');
        $simple = Input::get('simple');
        if($groupId != null){
            VotingGroup::where('voting_session_id', $votingSession->id)->where('id',$groupId)->firstOrFail();
        }

        $keys = [];
        for($i = 0; $i < 10; $i++){
            if($simple) $key = VotingSessionKey::createSimpleKey();
            else $key = VotingSessionKey::createKey();
            VotingSessionKey::create(['voting_session_id'=>$votingSession->id, 'voting_group_id'=>$groupId, 'key'=>$key]);
            $keys[] = $key;
        }

        return Response::json(array('keys' => $keys), 200, [], JSON_NUMERIC_CHECK);
    }

    /**
     * Borra el VotingSession
     * @param $contest
     * @param $votingId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroyVotingSession($contest, $votingCode) {
        /** @var $con Contest */
        $con = $this->getContest($contest);
        if(!$contest) return Response::make('Contest not found', 400);
        VotingSession::where('code', '=', $votingCode)->where('contest_id','=',$con->id)->delete();
        return Response::json(array('flash'=> Lang::get('contest.votingSessionDeleted')));
    }

    /**
     * Borra un grupo de una sesion de votacion
     * @param $groupId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroyVotingGroup($contest,$groupId) {
        if(!$groupId) return Response::make('group not found', 400);
        VotingGroup::where('id', '=', $groupId)->delete();
        return Response::json(array('flash'=> Lang::get('contest.votingGroupDeleted'), 'groupId' => $groupId));
    }

    /**
     * Borra el VotingSession
     * @param $contest
     * @param $votingId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroyVotingSessionJudge($contest, $votingCode, $judgeId) {
        /** @var $con Contest */
        $con = $this->getContest($contest);
        if(!$contest) return Response::make('Contest not found', 400);
        $votingSession = VotingSession::where('code', '=', $votingCode)->where('contest_id','=',$con->id)->firstOrFail();
        $votingUser = VotingUser::where('id','=',$judgeId)->where('voting_session_id','=', $votingSession->id)->firstOrFail();
        $votingUser->delete();
        return Response::json(array('flash'=> Lang::get('contest.VoteJudgeDeleted')));
    }

    public function destroyNewsletterUser($contest, $newsletterId, $userEmail) {
        /** @var $con Contest */
        NewsletterUser::where('newsletter_id', $newsletterId)->where('email', $userEmail)->delete();

        return Response::json(array('flash'=> Lang::get('contest.NewsletterUserDeleted')));
    }

    /**
     * Get del formulario de votaciones
     * @param $contest
     * @param $votingId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function getVoting($contest, $votingCode=NULL){
        $voteTypes = VotingSession::getAllVoteTypes();
        $con = $this->getContest($contest, false, ['categories','childrenCategories']);
        $data = [];
        foreach($voteTypes as $voteType => $label){
            $data[$voteType] = array(
                'id'	=> $voteType,
                'label'	=> $label,
            );
        };
        $metadataFields = EntryMetadataField::where('contest_id', $con->id)->get()->toArray();
        $superadmin = Auth::check() && Auth::user()->isSuperAdmin();
        $contestVotingSessionsIds = VotingSession::where('contest_id', $con->id)->select('id')->get()->toArray();
        $listOfShortLists = VotingShortlist::whereIn('voting_shortlists.voting_session_id', $contestVotingSessionsIds)
            ->select('voting_sessions.name', 'voting_sessions.id')->groupBy('voting_session_id')
            ->join('voting_sessions','voting_sessions.id', '=', 'voting_shortlists.voting_session_id')->get();
        $exportTemplates = [];
        if($votingCode != null){
            /** @var VotingSession $voting */
            $votingSession = VotingSession::where('code','=',$votingCode)->where('contest_id','=',$con->id)->firstOrFail();
            if($votingSession->id == 602) $voting = $votingSession;
            else $voting = VotingSession::judges()->where('code','=',$votingCode)->where('contest_id','=',$con->id)->firstOrFail();
            $shortList = VotingShortlist::where('voting_session_id', $voting->id)->select('entry_category_id')->get()->toArray();
            $voting->loadJudgesProgress();
            foreach($voting->voting_groups as $key => $group){
                $voting->voting_groups[$key]->countEntries = VotingGroupEntryCategory::where('voting_group_id', $group->id)->count();
            }

            $voting->metadata_fields_abstain = AutoAbstain::where('voting_session_id', $voting->id)->lists('metadata_field_id');
            $autoAbstains = EntryMetadataValue::whereIn('entry_metadata_field_id', $voting->metadata_fields_abstain)
                ->with(['EntryMetadataField'])
                ->select('value','id', 'entry_metadata_field_id')
                ->groupBy('value','entry_metadata_field_id')->get();
            //return $autoAbstains;
            $sessionAutoAbstains = array();
            foreach($autoAbstains as $abstains){
                if(!isset($sessionAutoAbstains[$abstains->entry_metadata_field->label])) $sessionAutoAbstains[$abstains->entry_metadata_field->label] = [];
                array_push($sessionAutoAbstains[$abstains->entry_metadata_field->label], $abstains);
            }
            $voting->autoAbstains = $sessionAutoAbstains;

            $voteCategories = VotingCategory::select('category_id')->where('voting_session_id', '=', $voting->id)->lists('category_id');

            /*$childCategories = [];
            foreach($con->childrenCategories as $catKey => $category) {
                $childCategories = $con->childrenCategoriesWithVotingSession($voting);
            }
            if(isset($childCategories[0])){
                $parents = [];
                $childrens = [];
                foreach($childCategories as $key => $categs) {
                    if ($categs->parent_id == NULL) {array_push($parents, $categs);}
                    else{array_push($childrens, $categs);}
                }
                $filteredCategories = $this->selectedCategories($parents, $childrens);
                $con->childrenCategories = $filteredCategories;
            }*/

            $exportTemplatesAux = ExportResult::where('contest_id', $con->id)
                ->where('voting_session_code', $votingCode)
                ->select('config')
                ->groupBy('type')
                ->get()
                ->toArray();

            $exportTemplates = [];
            foreach($exportTemplatesAux as $type => $template){
                $config = json_decode($template['config']);
                if(isset($config)){
                    foreach(json_decode($template['config']) as $value){
                        if($value == null) continue;
                        if(!isset($exportTemplates[$type])) $exportTemplates[$type] = [];
                        array_push($exportTemplates[$type], $value);
                    }
                }
            }
            $autoAbstains = AutoAbstain::where('voting_session_id', $voting->id)
                ->lists('metadata_field_id');

            $user = Auth::user();
            $colaborator = $con->getUserInscription($user, Inscription::COLABORATOR);

            return Response::json(array('colaborator' => $colaborator, 'exportTemplates' => $exportTemplates, 'listOfShortLists' => $listOfShortLists, 'shortList' => $shortList, 'superadmin' => $superadmin, 'metadataFields' => $metadataFields, 'voteTypes'=> $data, 'voting' => $voting, 'categories' => $con->childrenCategories, 'voteCategories' => $voteCategories, 'autoAbstains' => $autoAbstains), 200, [], JSON_NUMERIC_CHECK);
        }
        else{
            return Response::json(array('exportTemplates' => $exportTemplates, 'listOfShortLists' => $listOfShortLists, 'superadmin' => $superadmin, 'voteTypes'=> $data, 'metadataFields' => $metadataFields, 'categories' => $con->childrenCategories, 'voteCategories' => []), 200, [], JSON_NUMERIC_CHECK);
        }
    }

    public function getVotingJudges($contest, $votingCode){
        $con = $this->getContest($contest, false, ['categories','childrenCategories']);
        $voting = VotingSession::judges()->where('code','=',$votingCode)->where('contest_id','=',$con->id)->firstOrFail();
        $voting->loadJudgesProgress();
        return Response::json(array('judges' => $voting->votingUsers), 200, [], JSON_NUMERIC_CHECK);
    }

    public function getVotingResults($contest, $votingCode){
        $con = $this->getContest($contest, false);
        $fromShortList = Input::get('fromShortLists');
        /** @var VotingSession $votingSession */
        $votingSession = VotingSession::where('code','=',$votingCode)->where('contest_id','=',$con->id)->firstOrFail();
        $entries = $votingSession->GetAllEntriesResults();
        return Response::json(array('results' => $entries), 200, [], JSON_NUMERIC_CHECK);
    }

    public function getVoteSessionsList($contest){
        $con = $this->getContest($contest);
        $user = Auth::user();
        $inscription = $con->getUserInscription($user, Inscription::JUDGE);
        $votingSessions = [];
        if($inscription) {
            /** @var VotingSession[] $votingSessions */
            $votingSessions = VotingSession::where('contest_id', '=', $con->id)
                ->where('start_at', '<' , \Carbon\Carbon::now())
                ->where('finish_at', '>' , \Carbon\Carbon::now())
                ->where(function ($query) use ($inscription){
                    $query->where('public', '=', 1)
                        ->orWhereHas('votingUsers', function ($query) use ($inscription){
                            $query->where('inscription_id', '=', $inscription->id);
                        });
                })->with(['votingUsers' => function ($query) use ($inscription){
                    $query->where('inscription_id', '=', $inscription->id);
                }])->get();
            foreach($votingSessions as $session){
                $session->loadJudgesProgress();
                $session->judge = count($session->votingUsers) ? $session->votingUsers[0] : null;
                unset($session->votingUsers);
                unset($session->votingCategories);
                $session->metadata_fields_abstain = AutoAbstain::where('voting_session_id', $session->id)->lists('metadata_field_id');
                $autoAbstains = EntryMetadataValue::whereIn('entry_metadata_field_id', $session->metadata_fields_abstain)
                    ->with(['EntryMetadataField'])
                    ->select('value','id', 'entry_metadata_field_id')
                    ->groupBy('value','entry_metadata_field_id')->get();
                //return $autoAbstains;
                $sessionAutoAbstains = array();
                foreach($autoAbstains as $abstains){
                    if(!isset($sessionAutoAbstains[$abstains->entry_metadata_field->label])) $sessionAutoAbstains[$abstains->entry_metadata_field->label] = [];
                    array_push($sessionAutoAbstains[$abstains->entry_metadata_field->label], $abstains);
                }
                $session->autoAbstains = $sessionAutoAbstains;

                if($session->judge){
                    $session->userAutoAbstains = UserAutoAbstain::where('voting_session_id', $session->id)
                        ->where('voting_user_id', $session->judge->id)
                        ->get();
                }
            }
        }
        return Response::json(array('votingSessions'=> $votingSessions), 200, [], JSON_NUMERIC_CHECK);
    }

    public function getVoteData($contest, $code=NULL){
        $con = $this->getContest($contest, false, 'childrenCategories');
        $user = Auth::user();
        $superadmin = Auth::check() && Auth::user()->isSuperAdmin();
        $inscription = $con->getUserInscription($user, Inscription::JUDGE);
        if($inscription || $superadmin) {
            /** @var VotingSession $votingSession */
            $votingSession = VotingSession::where('code', $code)->first();
            if($votingSession->start_at > \Carbon\Carbon::now() || $votingSession->finish_at < \Carbon\Carbon::now())
                return Response::make(Lang::get('VoteSession closed'), 404);

            //$config = VotingSession::where('code', $code)->select('config')->first();
            $parentShortlist = $votingSession->parentShortlist();
            /** @var VotingUser $votingUser */
            $votingUser = VotingUser::where('inscription_id', $inscription['id'])->where('voting_session_id', $votingSession['id'])->first();
            if(!$votingUser){
                if($votingSession['public'] == 1){
                    $votingUser = new VotingUser();
                    $votingUser->inscription_id = $inscription['id'];
                    $votingUser->voting_session_id = $votingSession['id'];
                    $votingUser->status = VotingUser::ACCEPTED;
                    $votingUser->save();
                }
            }
            if(!$votingUser || !$votingSession){
                return Response::make(Lang::get('VoteSession not found 1'), 404);
            }

            $config = json_decode($votingSession['config']);
            if(isset($config->percentage)){
                $percentagesData = array(
                    'judge' => $votingUser->id,
                    'inscription_id' => $inscription->id,
                    'voteSessionCode' => $code,
                    'voting_session_id' => $votingSession['id'],
                    'entry_metadata_fields' => [],
                    'percentage' => $config->percentage
                );
                /* $$ REVISAR, SOLO SI HAY DISTRIBUCION DE ENTRIES, 1 SOLA VEZ
                  if($this->checkVotingPercentages($con, $percentagesData) == true){
                    $con = null;
                    $con = $this->getContest($contest, false, 'childrenCategories');
                }*/
            }

            $votingSession->loadJudgeProgress($votingUser, Input::get('showAllEntries'));
            $childCategories = [];
            foreach($con->childrenCategories as $catKey => $category) {
                $childCategories = $con->childrenCategoriesWithVotingSession($votingSession);
            }
            if(isset($childCategories[0])){
                $parents = [];
                $childrens = [];
                foreach($childCategories as $key => $categs) {
                    if ($categs->parent_id == NULL) {array_push($parents, $categs);}
                    else{array_push($childrens, $categs);}
                }
                $filteredCategories = $this->selectedCategories($parents, $childrens);
                $con->childrenCategories = $filteredCategories;
            }

            $data = array(
                'votingUser' => $votingUser,
                'children_categories' => $con->childrenCategories,
                'votingSession' => $votingSession,
                'role' => Inscription::JUDGE,
                'inscription' => $inscription
            );

            if($parentShortlist) $data['parentShortlist'] = $parentShortlist->lists('entry_category_id');
            return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
        }
        else{
            return Response::make(Lang::get('VoteSession not found'), 404);
        }
    }
    public function postShortlistParent($contest, $code=NULL){
        $con = $this->getContest($contest, false);
        $user = Auth::user();
        $superadmin = Auth::check() && Auth::user()->isSuperAdmin();
        $inscription = $con->getUserInscription($user, Inscription::JUDGE);
        $entryCategoryId = Input::get('entryCategoryId');
        $inShortlist = Input::get('shortList');
        if($inscription || $superadmin) {
            /** @var VotingSession $votingSession */
            $votingSession = VotingSession::where('code', $code)->first();
            //TODO Validar mejor la participación del juez con esta sesión de votación
            $confDec = json_decode($votingSession->config);
            if(isset($confDec->shortListConfig) && count($confDec->shortListConfig) > 0) {
                foreach ($confDec->shortListConfig as $votingSessionId) {
                    if($inShortlist){
                        $old = VotingShortlist::where('voting_session_id', $votingSessionId)->where('entry_category_id', $entryCategoryId)->first();
                        if(!$old){
                            $voting = new VotingShortlist();
                            $voting->voting_session_id = $votingSessionId;
                            $voting->entry_category_id = $entryCategoryId;
                            $voting->save();
                        }
                    }else{
                        VotingShortlist::where('voting_session_id', $votingSessionId)->where('entry_category_id', $entryCategoryId)->delete();
                    }
                }
            }
        }
        else{
            return Response::json(['error'=>Lang::get('VoteSession not found 3')], 404);
        }
    }

    public function postUserRole($contest){
        $user = Auth::user();
        $superadmin = Auth::check() && Auth::user()->isSuperAdmin();
        $con = $this->getContest($contest);
        $inscription = $con->getUserInscription($user);
        return Response::json(array('data' => $inscription->role));
    }

    public function postRemoveEntryFromCategory(){
        $category_id = Input::get('category_id');
        $entry_id = Input::get('entry_id');
        EntryCategory::where('category_id', '=', $category_id)->where('entry_id', $entry_id)->delete();
        return Response::json(array('status' => 'Entry succesfully deleted from category'));
    }

    public function postCancelPayment(){
        $billId = Input::get('billId');
        Billing::where('id', $billId)->delete();
        BillingEntryCategory::where('billing_id', $billId)->delete();
    }

    public function postInscriptionId($contest){
        $user = Auth::user();
        if(!$user) return null;
        $con = $this->getContest($contest);
        $role = Input::get('role');
        $inscription = $con->getUserInscription($user, $role);

        if($inscription) {
            return Response::json(array('id' => $inscription->id, 'role' => $role, 'inscription_type' => $inscription->inscription_type_id));
        }else{
            return null;
            //return Response::json(array('errors'=>Lang::get('contest.userInscriptionNotFound')));
        }
    }
    /*
    public function postInvitationId($contest){
        $con = $this->getContest($contest);
        $data = Input::get('invitation');
        $id = Input::get('invitation.id');

        if($id != null) {
            $invitationId = InvitationId::where('id', '=', $id);
            $invitationId->update($data);
        }
        else{
            $invitationId = new InvitationId();
            $invitationId->contest_id = $con->id;
            $invitationId->name = $data['name'];
            $invitationId->subject = $data['subject'];
            $invitationId->content = $data['content'];
            $invitationId->save();
        }
        return Response::json(array("data" => $data, 'flash'=>Lang::get('contest.invitationSaved')));

    }*/

    public function getInvitationsData($contest){
        $con = $this->getContest($contest);
        $data = InvitationId::where('contest_id', '=', $con->id)->get();
        return Response::json(array('data' => $data));
    }

    #pagination
    public function postInvitationData($contest){
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest not found', 400);
        $pageItems = 20;
        $page = (int) Input::get('page');
        $page = max($page, 1);
        $query = Input::get('query');
        if ($page > 0) Paginator::setCurrentPage($page);
        $orderBy = Input::get('orderBy');
        $orderDir = Input::get('orderDir');
        switch($orderBy) {
            case "id":
            case "name":
            case "subject":
            case "content":
                break;
            default:
                $orderBy = "id";
                $orderDir = 'asc';
        }
        if($orderDir == false) $orderDir = 'desc';
        else $orderDir = 'asc';
        $data = InvitationId::where('contest_id', '=', $con->id)
            ->orderBy($orderBy, $orderDir)
            ->paginate($pageItems, ['id', 'name', 'subject', 'content']);
        $pagination = [
            'last' => $data->getLastPage(),
            'page' => $data->getCurrentPage(),
            'perPage' => $data->getPerPage(),
            'total' => $data->getTotal(),
            'orderBy' => $orderBy,
            'orderDir' => $orderDir == 'asc',
            'query' => $query,
        ];
        return Response::json(['status' => 200, 'data' => $data->getItems(), 'query' => $query, 'pagination' => $pagination]);
    }

    public function getInvitationData($contest, $id=null){
        $con = $this->getContest($contest);
        $data = InvitationId::where('id', '=', $id)->get();
        return Response::json(array("data" => $data));
    }

    /*
    * Borra la invitacion
    * @param $contest
    * @param $id
    * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
    */
    public function destroyInvitation($contest, $id) {
        /** @var $con Contest */
        $con = $this->getContest($contest);
        if(!$contest) return Response::make('Contest not found', 400);
        InvitationId::destroy($id);
        return Response::json(array('flash'=> Lang::get('contest.InvitationDeleted')));
    }

    public function postUploadFile()
    {
        $input = Input::all();

        $request = new \Flow\Request();
        $destination = storage_path() . '/files/' . $input['flowFilename'];
        $config = new \Flow\Config(array(
            'tempDir' => storage_path() . '/files'
        ));
        $file = new \Flow\File($config, $request);
        $response = Response::make('', 200);

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!$file->checkChunk()) {
                return Response::make('', 404);
            }
        } else {
            if ($file->validateChunk()) {
                $file->saveChunk();
            } else {
                return Response::make('', 400);
            }
        }
        if ($file->validateFile() && $file->save($destination)) {
            $response = Response::make('pass some success message to flow.js', 200);
        }

        return Response::json(array('input' => $input, 'response' => $response));
    }

    public function postImportUserList($contest){
        $con = $this->getContest($contest);

        $fileName = Input::get('fileName');
        $createPassword = Input::get('createPassword');
        $errorMails = array();
        Excel::load(storage_path() . '/files/' . $fileName, function($rows) use($con, $createPassword, $errorMails){
            $result = $rows->get();
            $notCreated = array();
            foreach($result as $key => $value){
                if($value->email == null || $value->nombre == null || $value->apellido == null){
                    array_push($notCreated,$value->email);
                    continue;
                }
                $pass = substr(md5(rand(0, 1000000)), 0, 7);
                $invitation = new Invitation();
                $invitation->contest_id = $con->id;
                $invitation->email = $value->email;
                if($createPassword == 1) {
                    $user = new User();
                    $user->first_name = $value->nombre;
                    $user->last_name = $value->apellido;
                    $user->email = $value->email;
                    $user->password = Hash::make($pass);
                    $invitation->password = $pass;
                }
                if(!$invitation->save()) array_push($errorMails, $value->email);
                else{
                    if($createPassword == 1 && !($user->where('email', '=', $value->email)->count() > 0)){
                        $user->save();
                    }
                }
            }
        });
        return Response::json(array('flash'=> Lang::get('contest.invitationsCreated'), 'filename' => $fileName, 'errorMails' => $errorMails));
    }

    public function exportCredits($contest){
        set_time_limit(0);
        $con = $this->getContest($contest);
        //$values = [361,362,363,378,379,380,400,401,115,409,410]; Effie Latam
        $values = [416,417,418,433,434,435,436,439,440,441,442];

        $credits = EntryMetadataValue::select(array('entry_id', 'entry_metadata_field_id', 'entry_metadata_fields.config','value'))
            ->rightJoin('entries', 'entries.id','=','entry_metadata_values.entry_id')
            ->join('entry_metadata_fields', 'entry_metadata_fields.id','=','entry_metadata_values.entry_metadata_field_id')
            ->whereIn('entry_metadata_values.entry_metadata_field_id', $values)
            ->where('entries.contest_id', $con->id)->get();

        $count = 0;
        $creditsArray = [];
        foreach($credits as $index => $credit){
            $metaField = '';
            switch($credit->entry_metadata_field_id){
                /* Effie Latam*/
                case 416: $metaField = 'Título del caso';break;
                case 417: $metaField = 'Agencia';break;
                case 418: $metaField = 'Anunciante';break;
                case 433: $metaField = '*Créditos Individuales';break;
                case 434: $metaField = 'Créditos Agencia principal';break;
                case 435: $metaField = 'Créditos Cliente';break;
                case 436: $metaField = 'Créditos Agencias Contribuyentes';break;
                case 439: $metaField = 'Sector de la industria';break;
                case 440: $metaField = 'Objetivos del caso';break;
                case 441: $metaField = '* Sumario del caso';break;
                case 442: $metaField = '* Declaración de Eficacia';break;
            }
            $label = '';
            $finalValue = '';
            if(json_decode($credit->value) != null){
                $data = json_decode($credit->value);
                if(!isset($data->value)){
                    $finalValue = $data;
                }
                else{
                    foreach($data->value as $key => $value){
                        $aux = $key.' = '.$value."\t";
                        $finalValue.= $aux." - ";
                    }
                }
            }else{
                $finalValue = $credit->value;
            }

            array_push($creditsArray, array($credit->entry_id,$metaField,$finalValue));
        }
        return Excel::create($con->code."-creditos-".date('d-m-Y'), function($excel) use($creditsArray) {
            $excel->sheet('creditos', function($sheet) use($creditsArray){
                $sheet->fromArray(array(
                    array('entry', 'campo', 'value')
                ), null, 'A1', false, false
                );
                $sheet->setAutoFilter('A1:C1');
                $sheet->fromArray($creditsArray, null, 'A1', false, false);
            });
        })->download('xls');
    }

    function avgCriterios($results, $criterios, $config, $excludeCrits){
        for ($i = 0; $i < $criterios; $i++) {
            if (!isset($results[$config['criteria'][$i]['name']])) break;
            $excludeCrits[$config['criteria'][$i]['name']] += $results[$config['criteria'][$i]['name']];
        }
        return $excludeCrits;
    }

    public function postExportJudges($contest, $votingCode){
        $con = $this->getContest($contest);
        $judges = VotingSession::judges()->where('code','=',$votingCode)->where('contest_id','=',$con->id)->firstOrFail();
        $judges->loadJudgesProgress();
        foreach($judges->voting_groups as $key => $group){
            $judges->voting_groups[$key]->countEntries = VotingGroupEntryCategory::where('voting_group_id', $group->id)->count();
        }
        $data = [];
        $voting_users = $judges->voting_users;
        foreach($voting_users as $judge){
            try{
                $judge->inscription->email;
            }catch (Exception $e){
                continue;
            }
            if(!$judge->inscription->email){
                $inscription = Inscription::where('id', $judge->inscription->id)->firstOrFail();
                $user = User::where('id', $inscription->user_id)->firstOrFail();
                $judge->inscription->email = $user->email;
            }
            $status = '';
            switch($judge->status){
            case VotingUser::PENDING_NOTIFICATION: $status = Lang::get('voting.pendingNotification'); break;
            case VotingUser::NOTIFIED: $status = Lang::get('voting.notified'); break;
            case VotingUser::VISITED_PAGE: $status = Lang::get('voting.visitedPage'); break;
            case VotingUser::REJECTED: $status = Lang::get('voting.rejectedInvitation'); break;
            case VotingUser::ACCEPTED: $status = Lang::get('voting.accepted'); break;
            }
            $link = '';
            if($judge->invitation_key){
                $link = url("/".$con->code."/invite")."/".$judge->invitation_key;
            }
            $votingUserGroup = $votingGroupName = null;
            $votingUserGroup = VotingUserVotingGroup::where('voting_user_id', $judge['id'])->first();
            if($votingUserGroup) $votingGroupName = VotingGroup::where('id', $votingUserGroup->voting_group_id)->select('name')->first();
            array_push($data, array($judge['id'], $votingGroupName ? $votingGroupName->name : null, $judge->inscription->id, $judge->inscription->email, $link, $status));
        }
        return Excel::create($con->code."-Jurados-".date('d-m-Y'), function($excel) use($data) {
            $excel->sheet('Jurados', function($sheet) use($data){
                $sheet->fromArray(array(array('Id juez', 'grupo', 'Id inscripción','Email del juez', 'Link invitación', 'Estado invitación')), null, 'A1', false, false);
                $sheet->fromArray($data, null, 'A1', false, false);
            });
        })->download('xls');
    }

    public function saveExportTemplate($contest){
        $exportTemplate = Input::get('bulks');
        $voting_session_code = Input::get('voting_code');
        $con = $this->getContest($contest, false);
        foreach($exportTemplate as $type => $template){
            ExportResult::where('contest_id', $con->id)
            ->where('voting_session_code', $voting_session_code)
            ->where('type', $type)
            ->delete();

            $exportResults = new ExportResult();
            $exportResults->contest_id = $con->id;
            $exportResults->voting_session_code = $voting_session_code;
            $exportResults->type = $type;
            $exportResults->config = json_encode($template);
            $exportResults->save();
        }
        return Response::json(array('flash' => Lang::get('voting.success')));
    }

    public function exportResults($contest){
        set_time_limit(0);
        $tildes=array('á','é','í','ó','ú');
        $vocales=array('a','e','i','o','u');

        $fields = Input::get('fields');
        $votingCode = Input::get('votingCode');
        $exportType = Input::get('type');
        $hideEntryNotVoted = Input::get('hideEntryNotVoted');

        //return Response::json(["VALUE" =>$hideEntryNotVoted]);

        $con = $this->getContest($contest, false);
        $votingSession = VotingSession::where('code','=',$votingCode)->where('contest_id','=',$con->id)->firstOrFail();

        /**************************************************************************************************************/
        $entries = $votingSession->GetAllEntriesResults();
        $config = $votingSession->getVoteConfig();
        if(isset($config['criteria'])) $criterios = count($config['criteria']);
        else $criterios = 0;
        /**************************************************************************************************************/

        $entriesData = [];
        $categories_list = [];

        /***** Armo el array de la cabecera del excel con los campos exportables ********/
        $entriesDataKeys = array('Entry_id', 'Nombre', 'Categoria', 'Final', 'Premio','Abstenciones', 'Inscriptor', 'Jueces', 'Votaron');
        $exportFields = EntryMetadataField::where('contest_id', $con->id)
            ->whereIn('id', $fields)
            ->orderBy('order')
            ->get()
            ->toArray();

        foreach($exportFields as $field){
            array_push($entriesDataKeys, $field['id'].'-'.$field['label']);
        }

        array_push($entriesDataKeys, 'FilesNames','files');

        $votingSession->vote_type == VotingSession::YESNO ? array_push($entriesDataKeys, 'Total SI', 'Total NO') : null;

        for ($i = 0; $i < $criterios; $i++) {
            if(!isset($config['criteria'][$i]['name'])) continue;
            $config['criteria'][$i]['name'] = str_replace($tildes,$vocales,$config['criteria'][$i]['name']);
            array_push($entriesDataKeys, $config['criteria'][$i]['name']);
        }

        //return $entriesDataKeys;
        $lfcr = chr(10) . chr(13);
        /** @var Entry $entry */

        foreach($entries as $entry) {
            /******* Tomo las categorias con sus categorias padres ********/
            $entriesSheetCriterias = [];
            $entriesSheetExtras = [];
            $entry->exportables = $entry->getExportables()->whereIn('CM.id', $fields)->get()->toArray();
            foreach ($entry->categories as $key => $category) {
                if(!in_array($category->id, $entry->categories_id)) unset($entry->categories[$key]);
                $entriesDataAux = [];
                $cat = $parent = '';
                $cat = $category['name'];
                foreach($entriesDataKeys as $keys){
                    $entriesDataAux[$keys] = '-';
                };
                while ($category['parent_id'] != null) {
                    $aux = Category::select('name', 'parent_id')->where('id', $category['parent_id'])->first();
                    $parent = $aux['name'] . '>>' . $parent;
                    $category['parent_id'] = $aux['parent_id'];
                }

                for ($i = 0; $i < $criterios; $i++) {
                    if(!isset($config['criteria'][$i]['name'])) continue;
                    $config['criteria'][$i]['name'] = str_replace($tildes,$vocales,$config['criteria'][$i]['name']);
                    $entriesSheetCriterias[$config['criteria'][$i]['name']] = null;
                }

                $inscriptorData = User::where('id', $entry['user_id'])
                    ->select('first_name', 'last_name', 'email')
                    ->get();

                $entriesDataAux['Inscriptor'] = $inscriptorData[0]['email']." (".$inscriptorData[0]['first_name']." ".$inscriptorData[0]['last_name'].")";

                $final = $voted = '-';
                if(isset($entry['votes'])){
                    $catNotFound = true;
                    foreach($entry['votes'] as $categ => $votes){
                        $final = $voted = '-';
                        $hasValue = false;
                        if(isset($votes['vote']) && $votingSession->vote_type == VotingSession::METAL){
                            foreach($votes['vote'] as $value){
                                foreach($value as $key => $item){
                                    if($key == 'name') $hasValue = true;
                                }
                            }
                        }
                        else $hasValue = true;
                        if($categ == $category->id && $hasValue){
                            $catNotFound = false;
                            $votes = $entry['votes'][$category->id];
                            if(isset($votes['abstains']))
                                $abstain = $votes['abstains'];
                            $judges = $votes['judges'];
                            $voted = $votes['total'];
                            if(isset($votes['final']))
                                $final = $votes['final'];
                            if($votingSession->vote_type == VotingSession::VERITRON) {
                                if (isset($votes['yesPerc']))
                                    $yesPerc = $votes['yesPerc'];
                                if (isset($votes['noCount']))
                                    $noCount = $votes['noCount'];
                            }
                            if((isset($votes['totalYes']) || isset($votes['totalNo'])) && $votingSession->vote_type == VotingSession::YESNO){
                                if($votes['totalYes'] > $votes['totalNo']) $final = Lang::get('voting.yes');
                                if($votes['totalYes'] < $votes['totalNo']) $final = Lang::get('voting.no');
                                if($votes['totalYes'] == $votes['totalNo']) $final = Lang::get('voting.tie');
                                $totalYes = $votes['totalYes'];
                                $totalNo = $votes['totalNo'];
                            }
                            if($votingSession->vote_type == VotingSession::METAL){
                                $metalAux = $votes['vote'];
                                $final = 0;
                                foreach($metalAux as $vote){
                                    if(count((array)$metalAux) === 1){
                                        $final = $vote->score;
                                        $entriesDataAux['Premio'] = $vote->name;
                                    }
                                    else{
                                        $final = $final + $vote->score;
                                    }
                                }
                            }
                            if(isset($votes['vote']) && sizeof($votes['vote']) > 0 && $criterios > 0){
                                foreach ($votes['vote'] as $key => $vote) {
                                    if(!isset($config['criteria'][$key]['name'])) continue;
                                    $config['criteria'][$key]['name'] = str_replace($tildes,$vocales,$config['criteria'][$key]['name']);
                                    $entriesSheetCriterias[$config['criteria'][$key]['name']] = $vote;
                                };
                            }
                            if(isset($config['extra']) && ($votingSession->vote_type == VotingSession::AVERAGE || $votingSession->vote_type == VotingSession::VERITRON)) {
                                foreach ($config['extra'] as $configExtra) {
                                    $configExtra['name'] = str_replace($tildes, $vocales, $configExtra['name']);
                                    $entriesSheetExtras[$configExtra['name']] = null;
                                }
                            }

                            if(isset($votes['extra'])) {
                                foreach ($votes['extra'] as $key => $extra) {
                                    $config['extra'][$key]['name'] = str_replace($tildes, $vocales, $config['extra'][$key]['name']);
                                    $entriesSheetExtras[$config['extra'][$key]['name']] = $extra;
                                };
                            }

                            $voters = [];
                            foreach (json_decode($entry['EntryCategories']) as $entryCat) {
                                $voters = Vote::where('entry_category_id', $entryCat->id)
                                    ->where('votes.voting_session_id', $votingSession->id)
                                    //->where('type', 0)
                                    ->join('voting_users', 'votes.voting_user_id', '=', 'voting_users.id')
                                    ->join('inscriptions', 'voting_users.inscription_id', '=', 'inscriptions.id')
                                    ->join('users', 'inscriptions.user_id', '=', 'users.id')
                                    ->select('users.email', 'users.first_name', 'users.last_name')
                                    ->groupBy('users.email')
                                    ->get();
                            }
                            $voterUsers = '';
                            foreach ($voters as $votersData) {
                                $voterUsers .= $votersData['first_name'] . ' ' . $votersData['last_name'] . '(' . $votersData['email'] . ')' . ' - ';
                            }

                            $categories_list[$entry->id.$category->id] = $parent.$cat;
                            $entriesDataAux['Entry_id'] = $entry->id;
                            $entriesDataAux['Nombre'] = $entry['name'];
                            $entriesDataAux['Categoria'] = $parent.$cat;
                            foreach($entriesSheetCriterias as $key => $entriesCriterias){
                                $entriesDataAux[$key] = $entriesCriterias;
                            }
                            if(isset($totalYes)) $entriesDataAux['Total SI'] = isset($totalYes) ? $totalYes : null;
                            if(isset($totalNo)) $entriesDataAux['Total NO'] = isset($totalNo) ? $totalNo : null;
                            $entriesDataAux['Final'] = isset($final) ? $final : null;
                            /*if($votingSession['id'] == 90 || $votingSession['id'] == 91 || $votingSession['id'] == 73){
                                $entriesDataAux['Premio'] = '-';
                                if($final > 0.8 && $final < 1.64){
                                    $entriesDataAux['Premio'] = 'Bronce';
                                }
                                if($final > 1.65 && $final < 2.54){
                                    $entriesDataAux['Premio'] ='Plata';
                                }
                                if($final > 2.55 && $final < 3){
                                    $entriesDataAux['Premio'] = 'Oro';
                                }
                            }*/
                            if($votingSession->vote_type == VotingSession::VERITRON) {
                                $entriesDataAux['% SI'] = isset($yesPerc) ? $yesPerc : null;
                                $entriesDataAux['NO'] = isset($noCount) ? $noCount : null;
                            }
                            if(isset($abstain)) $entriesDataAux['Abstenciones'] = isset($abstain) ? $abstain : null;
                            $entriesDataAux['Jueces'] = isset($judges) ? $judges : null;
                            $entriesDataAux['Votaron'] = isset($voted) ? intval($voted) : null;
                            //$entriesDataAux['Quienes votaron'] = $voterUsers;
                            foreach($entriesSheetExtras as $key => $entriesExtras) {
                                $entriesDataAux[$key] = $entriesExtras;
                            }

                            $fileInfo = '';
                            $filesNames = '';
                            if($exportType == "jsonExport"){
                                $exportIds = [];
                                foreach($entry->exportables as $exportData){
                                    array_push($exportIds, $exportData['id']);
                                }
                                $entriesDataAux['files'] = array();
                                foreach($entry->files_fields as $fileField){
                                    $fieldName = EntryMetadataField::where('id', $fileField->entry_metadata_field_id)
                                        ->select('label')
                                        ->get();
                                    if(in_array($fileField->entry_metadata_field_id, $exportIds)){
                                        foreach($fileField->files as $files){
                                            $fileName = $files->name;
                                            foreach($files->contest_file_versions as $file){
                                                if($file->source == 1){
                                                    $fileId = isset($file->id) ? $file->id : ' - ';
                                                    $file->name = $fileName. $lfcr;
                                                    $file->fieldName = $fieldName[0]['label'];
                                                    //$file['File Name'] = $fileId.'.'.$fileExt.$lfcr;
                                                    array_push($entriesDataAux['files'], $file);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            else{
                                foreach($entry->files_fields as $fileField){
                                    foreach($fileField->files as $files){
                                        $fileName = $files->name;
                                        foreach($files->contest_file_versions as $file){
                                            if($file->source == 1){
                                                $fileId = isset($file->id) ? $file->id : ' - ';
                                                $fileExt = isset($file->extension) ? $file->extension : ' - ';
                                                $fileSize = isset($file->size) ? $file->size : ' - ';
                                                $fileSizes = isset($file->sizes) ? $file->sizes : ' - ';
                                                $fileDuration = isset($file->duration) ? $file->duration : ' - ';
                                                $fileInfo = 'Name : '. $fileName. $lfcr .
                                                    'File Name : ' . $fileId.'.'.$fileExt.$lfcr.
                                                    'Id : ' . $fileId.$lfcr.
                                                    'Extension : ' . $fileExt.$lfcr.
                                                    'Size : ' . $fileSize.$lfcr.
                                                    'Sizes : ' . $fileSizes.$lfcr.
                                                     'Duration : ' . $fileDuration .$lfcr.
                                                    '---------------------------------'.$lfcr.
                                                    $fileInfo.$lfcr;
                                                $filesNames .= $fileId.'.'.$fileExt." ";
                                            }
                                        }
                                    }
                                }
                                $entriesDataAux['FilesNames'] = $filesNames;
                                $entriesDataAux['files'] = $fileInfo;
                            }

                            foreach($entry->exportables as $exportables){
                                $entriesDataAux[$exportables['id'].'-'.$exportables['label']] = $exportables['value'];
                            }

                            //array_push($entriesData,$entriesDataAux);
                        }
                    }
                    if($catNotFound){
                        $categories_list[$entry->id.$category->id] = $parent.$cat;
                        $entriesDataAux['Categoria'] = $parent.$cat;
                        if (isset($entry->exportables)) {
                            foreach($entry->exportables as $exportables){
                                $entriesDataAux[$exportables['id'].'-'.$exportables['label']] = $exportables['value'];
                                //$entriesDataAux[$exportables['label']] = $exportables['value'];
                            }
                        }
                    }
                    if($hideEntryNotVoted == true){
                        //return Response::json(['Final' => $entriesDataAux['Final']]);
                        if(isset($entriesDataAux['Final']) && $entriesDataAux['Final'] != '-'){
                            $entriesDataAux['Entry_id'] = $entry->id;
                            $entriesDataAux['Nombre'] = $entry['name'];
                            array_push($entriesData,$entriesDataAux);
                        }
                    }else{
                        $entriesDataAux['Entry_id'] = $entry->id;
                        $entriesDataAux['Nombre'] = $entry['name'];
                        array_push($entriesData,$entriesDataAux);
                    }
                }
            }
        }

        //EXPORT JSON
        if($exportType == "jsonExport"){
            $JsonResponse = [];
            $aux =[];
            foreach($entriesData as $keyValue => $value){
                if($value['Premio'] != "-" && $value['Premio'] != "Sin Premio"){
                    foreach($value as $key => $item){
                        $pos = strrpos($key, '-');
                        if($pos){
                            $index = substr($key, $pos+1);
                            $aux[$index] = $item;
                        }else{
                            $aux[$key] = $item;
                        }
                    }
                array_push($JsonResponse, $aux);
                $aux =[];
                }
            }
            return $JsonResponse;
        }

        // EXPORT DOC
        if($exportType == "doc"){
            $enter = "\n";
            $contestScript = "GUION $enter $enter";
            $count = 1;
            $script_entries = [];
            /* Ordeno el array de entries */
            $orderedEntries = array();
            foreach($entriesData as $key => $item){
                $orderedEntries[$item['Categoria']][$key] = $item;
            }
            ksort($orderedEntries);
            foreach($orderedEntries as $entries){
                /* Ordeno los sub-arrays por premios */
                $sortPrices = array();
                foreach ($entries as $key => $row) {
                    $sortPrices[$key] = $row['Final'];
                }
                array_multisort($sortPrices, SORT_DESC, $entries);

                foreach($entries as $value){
                    unset($value['name']);
                    unset($value['parent_id']);
                    unset($value['Final']);
                    unset($value['Abstenciones']);
                    unset($value['Inscriptor']);
                    unset($value['Jueces']);
                    unset($value['Votaron']);
                    unset($value['FilesNames']);
                    unset($value['files']);


                    array_push($script_entries, $value);
                    /*foreach($value as $key => $item){
                        $pos = strrpos($key, '-');
                        if($pos){
                            $index = substr($key, $pos+1);
                            $aux[$index] = $item;
                        }else{
                            $aux[$key] = $item;
                        }
                    }*/

                    /*unset($aux['name']);
                    unset($aux['parent_id']);
                    unset($aux['Final']);
                    unset($aux['Abstenciones']);
                    unset($aux['Inscriptor']);
                    unset($aux['Jueces']);
                    unset($aux['Votaron']);
                    unset($aux['FilesNames']);
                    unset($aux['files']);
                    array_push($script_entries, $aux);*/

                    /*if($value['Premio'] != '-' && $value['Premio'] != 'Mencion'){
                        foreach($value as $key => $item){
                            $pos = strrpos($key, '-');
                            if($pos){
                                $index = substr($key, $pos+1);
                                $aux[$index] = $item;
                            }else{
                                $aux[$key] = $item;
                            }
                        }
                        return $aux;
                        $auxScript =
                                     "Id: ".$aux['Entry_id'].$enter.
                                     "Premio: ".$aux['Premio'].$enter.
                                     "Categoria: ".$aux['Categoria'].$enter.
                                     "Titulo: ".$aux['Nombre'].$enter.
                                     "Título de la campaña: ".$aux['Título de la campaña'].$enter.
                                     "Agencia: ".$aux['Agencia'].$enter.
                                     "Universidad: ".$aux['Universidad / Escuela'].$enter.
                                     "Anunciante: ".$aux['Anunciante'].$enter.$enter;
                        $contestScript = $contestScript." ".$auxScript;
                        //$count ++;
                    }*/
                }
                //$contestScript = $contestScript.$enter.'---------------------------------------------------------'.$enter.$enter;
            }

            //return $script_entries;

            return View::make('contest.static.contestScript', array('entries' => $script_entries));

            //return $contestScript;
        }

        $votes = Vote::where('votes.voting_session_id', $votingSession->id)
            ->leftJoin('entry_categories', 'votes.entry_category_id','=','entry_categories.id')
            ->leftJoin('voting_users', 'voting_users.id','=','votes.voting_user_id')
            ->leftJoin('inscriptions', 'inscriptions.id','=','voting_users.inscription_id')
            ->leftJoin('users', 'users.id','=','inscriptions.user_id')
            ->select('users.email', 'users.first_name', 'users.last_name', 'entry_categories.entry_id', 'votes.vote', 'votes.vote_float', 'votes.criteria','votes.type', 'votes.abstain', 'entry_categories.category_id')
            ->orderBy('entry_id','type')
            ->get()
            ->toArray();

        //TODO indices dinamicos, sin hardcodeos para criterios, titulos etc
        $extrasData = [];
        $critVotes = [];
        $extrasVotes = [];
        $total = null;

        if($votingSession->vote_type == VotingSession::AVERAGE || $votingSession->vote_type == VotingSession::VERITRON)
        {
            foreach($votes as $key => $extra) {
                if($extra['type'] == 1){
                    $value = "";
                    if($extra['vote_float'] == 1) $value = "Si";
                    $extrasVotes[$config['extra'][$extra['criteria']]['name']] = $extra['vote'] == null ? $value : $extra['vote'];
                }
                if($extra['type'] == 0){
                    $abstain = $extra['abstain'];
                    switch($abstain){
                        case 0: $abstain = null;
                            $critVotes[$extra['criteria']] = $extra['vote_float'];
                            break;
                        case 1: $abstain = "Abstencion"; break;
                    }
                }
                if(!isset($votes[$key+1]) || $votes[$key+1]['entry_id'] != $extra['entry_id'] || $votes[$key+1]['email'] != $extra['email']){
                    if ($criterios > 0){
                    for ($i = 0; $i < $criterios; $i++) {
                        if (!isset($critVotes[$i])) break;
                        //$weight = $config['criteria'][$i]['weight'];
                        $weight = isset($config) && isset($config['criteria'][$i]['weight']) ? $config['criteria'][$i]['weight'] : null;
                        $weight = $weight != null && $weight != '' ? floatval($weight) / 100 : 1 / $criterios;
                        $total += $weight * $critVotes[$i];
                    }
                    }else{
                        $total = isset($critVotes[0]) ? $critVotes[0] : 0;
                    }
                    $data = array('Email' => $extra['email'],
                        'Nombre' => $extra['first_name'],
                        'Apellido' => $extra['last_name'],
                        'entry_id' => $extra['entry_id'],
                        'Categoria' => isset($categories_list[$extra['entry_id'].$extra['category_id']]) ? $categories_list[$extra['entry_id'].$extra['category_id']] : "",
                        'category_id' => $extra['category_id'],
                        'Judge Weighted Score' => $total != null ? $total : null,
                        'abstenciones' => $abstain);

                    if(isset($config['criteria'])) {
                        for ($i = 0; $i < $criterios; $i++) {
                            if(!isset($config['criteria'][$i]['name'])) break;
                            $config['criteria'][$i]['name'] = str_replace($tildes, $vocales, $config['criteria'][$i]['name']);
                            $data[$config['criteria'][$i]['name']] = null;
                        }
                        foreach ($critVotes as $i => $vote) {
                            if(!isset($config['criteria'][$i]['name'])) break;
                            if (!isset($critVotes[$i]) || !isset($config['criteria'][$i])) break;
                            $config['criteria'][$i]['name'] = str_replace($tildes, $vocales, $config['criteria'][$i]['name']);
                            $data[$config['criteria'][$i]['name']] = $vote;
                        }
                    }

                    if(isset($config['extra'])) {
                        foreach($config['extra'] as $configExtra){
                            $data[$configExtra['name']] = null;
                        }
                    }
                    foreach($extrasVotes as $label => $extraVote){
                        $data[$label] = $extraVote;
                    }
                    array_push($extrasData,$data);
                    $total = null;
                    $critVotes = [];
                    $extrasVotes = [];
                }
            }
            if(isset($extrasData[0])) {
                $extrasDataKeys = array_keys($extrasData[0]);
            }else{
                $extrasDataKeys = [];
            }
            $groupedEntries =[];
            foreach($extrasData as $key => $item)
            {
                if (array_key_exists('entry_id', $item)) {
                    $groupedEntries[$item['entry_id'].$item['category_id']][$key] = $item;
                }
            }
            ksort($groupedEntries, SORT_NUMERIC);

            $excludes = [];
            $includes = [];
            $excludesDetail = [];

            /********** Armo el array para el export de Extras LATAM EFFIE **********/
            if(strpos(strtolower($con->name), 'effie') !== false){
                foreach($groupedEntries as $excludeResults){
                    $groupResults = [];
                    $ExcludeTotal = $includeTotal = 0;
                    $excludeCrits = $includeCrits = [];
                    for ($i = 0; $i < $criterios; $i++) {
                        $excludeCrits[$config['criteria'][$i]['name']] = '';
                        $includeCrits[$config['criteria'][$i]['name']] = '';
                    }
                    /***
                        Tomo solo los registros que tengan todos los votos (sin abstenciones)
                        los guardo en $groupResults
                    ***/
                    foreach($excludeResults as $key => $item){
                        $canVote = true;
                        for ($i = 0; $i < $criterios; $i++) {
                            if($item[$config['criteria'][$i]['name']] == null && $item['abstenciones'] == null) $canVote = false;
                        }
                        if($canVote == true){
                            array_push($groupResults, $item);
                        }
                    }

                    $totalColumn = array_column($groupResults, 'Judge Weighted Score');
                    $minTotal = $maxTotal = null;
                    $totalColumn = array_filter($totalColumn);
                    if(isset($totalColumn) && (!empty($totalColumn))){
                        $minTotal = min(array_diff($totalColumn, array(null)));
                        $maxTotal = max($totalColumn);
                    }

                    /*Maximo y minimo de cada criterio */
                    for ($i = 0; $i < $criterios; $i++) {
                        $totalCrit[$config['criteria'][$i]['name']] = array_column($groupResults, $config['criteria'][$i]['name']);
                        //if(isset($totalCrit[$config['criteria'][$i]['name']][0])) {
                        $totalCrit[$config['criteria'][$i]['name']] = array_filter($totalCrit[$config['criteria'][$i]['name']]);
                        if(isset($totalCrit[$config['criteria'][$i]['name']]) && !empty($totalCrit[$config['criteria'][$i]['name']])) {
                            $minCrit[$config['criteria'][$i]['name']] = min(array_diff($totalCrit[$config['criteria'][$i]['name']], array(null)));
                            $maxCrit[$config['criteria'][$i]['name']] = max($totalCrit[$config['criteria'][$i]['name']]);
                        }
                    }

                    /* Tomo todos los totales (por criterios) excluyendo el minimo y el maximo */
                    $max = false;
                    $min = false;
                    $abstainsCount = 0;
                    $allVoters = sizeof($groupResults);
                    $includesAux = [];
                    $excludesDetailAux = [];

                    foreach($groupResults as $results){
                        /***** Excludes ****/
                        switch($results['Judge Weighted Score']){
                            case $minTotal: if($min != true){
                                            $min = true;
                                            }else{
                                                $ExcludeTotal += $results['Judge Weighted Score'];
                                                $excludeCrits = $this->avgCriterios($results, $criterios, $config, $excludeCrits);
                                                $results = array_slice($results, 0, 4, true) + array("categoria" => $categories_list[$results['entry_id'].$results['category_id']]) + array_slice($results, 4, count($results) - 1, true);
                                                array_push($excludesDetailAux, $results);
                                            }
                                            break;
                            case $maxTotal: if($max != true){
                                                $max = true;
                                            }else{
                                                $ExcludeTotal += $results['Judge Weighted Score'];
                                                $excludeCrits = $this->avgCriterios($results, $criterios, $config, $excludeCrits);
                                                $results = array_slice($results, 0, 4, true) + array("categoria" => $categories_list[$results['entry_id'].$results['category_id']]) + array_slice($results, 4, count($results) - 1, true);
                                                array_push($excludesDetailAux, $results);
                                            }
                                            break;
                            case null:      $results = array_slice($results, 0, 4, true) + array("categoria" => $categories_list[$results['entry_id'].$results['category_id']]) + array_slice($results, 4, count($results) - 1, true);
                                            //array_push($excludesDetailAux, $results);
                                            $abstainsCount ++;
                                            break;
                            default:        $ExcludeTotal += $results['Judge Weighted Score'];
                                            $excludeCrits = $this->avgCriterios($results, $criterios, $config, $excludeCrits);
                                            //isset($categories_list[$results['entry_id'].$results['category_id']]) ? $categories_list[$results['entry_id'].$results['category_id']] : null;
                                            $results = array_slice($results, 0, 4, true) + array("categoria" => $categories_list[$results['entry_id'].$results['category_id']]) + array_slice($results, 4, count($results) - 1, true);
                                            array_push($excludesDetailAux, $results);
                                            break;
                        }
                        /**** Includes *****/
                        $includeTotal += $results['Judge Weighted Score'];
                        $includeCrits = $this->avgCriterios($results, $criterios, $config, $includeCrits);

                        $results = array_slice($results, 0, 4, true) + array("categoria" => isset($categories_list[$results['entry_id'].$results['category_id']]) ? $categories_list[$results['entry_id'].$results['category_id']] : null) + array_slice($results, 4, count($results) - 1, true);
                        $results = array_slice($results, 0, 5, true) + array("jueces" => sizeof($groupResults)) + array_slice($results, 5, count($results) - 1, true);

                        for ($i = 0; $i < $criterios; $i++) {
                            $results[$config['criteria'][$i]['name'].' Score avg'] = null;
                        }
                        array_push($includesAux, $results);
                    }

                    $includeVoters = $allVoters - $abstainsCount;
                    $includeVoters == 0 ? $includeAverage = '' : $includeAverage = floatval($includeTotal) / intval($includeVoters);
                    $includesAux[0]['Total Weighted Score Avg.'] = $includeAverage;
                    for ($i = 0; $i < $criterios; $i++) {
                        $includesAux[0][$config['criteria'][$i]['name'].' Score avg'] = intval($includeVoters) != 0 ? floatval($includeCrits[$config['criteria'][$i]['name']]) / intval($includeVoters) : 0;
                    }
                    foreach($includesAux as $aux){
                        array_push($includes, $aux);
                    }
                    /**** Excluyo el maximo y el minimo de los jueces *****/
                    $excludeVoters = sizeof($groupResults) - ( 2 + $abstainsCount);
                    for ($i = 0; $i < $criterios; $i++) {
                        $excludeCritAverage[$config['criteria'][$i]['name']] = intval($excludeVoters) != 0 ? floatval($excludeCrits[$config['criteria'][$i]['name']]) / intval($excludeVoters) : 0;
                    }
                    /* Porcentaje de las piezas menos las excluidas (minimo y maximo) */
                    $excludeAverage = intval($excludeVoters) != 0 ? floatval($ExcludeTotal) / intval($excludeVoters) : 0;

                    foreach($excludesDetailAux as $key => $item){
                        $excludesDetailAux[$key] = array_slice($excludesDetailAux[$key], 0, 5, true) + array("# of Judges - Excludes" => $excludeVoters) + array_slice($excludesDetailAux[$key], 4, count($excludesDetailAux[$key]) - 1, true);
                    }
                    if(!$groupResults) continue;
                    $excludesAux = array('entry_id' => $groupResults[0]['entry_id'],
                        'category' => isset($categories_list[$groupResults[0]['entry_id'].$groupResults[0]['category_id']]) ? $categories_list[$groupResults[0]['entry_id'].$groupResults[0]['category_id']] : "",
                        'Excludes - Total Weighted Score Avg.' => $excludeAverage,
                        'minTotal' => $minTotal,
                        'maxTotal'=>$maxTotal);
                    for ($i = 0; $i < $criterios; $i++) {
                        $excludesAux[$config['criteria'][$i]['name'].' Score Avg'] = isset($excludeCritAverage[$config['criteria'][$i]['name']]) ? $excludeCritAverage[$config['criteria'][$i]['name']] : null;
                        $excludesAux[$config['criteria'][$i]['name'].' Min'] = isset($minCrit[$config['criteria'][$i]['name']]) ? $minCrit[$config['criteria'][$i]['name']] : null;
                        $excludesAux[$config['criteria'][$i]['name'].' Max'] = isset($maxCrit[$config['criteria'][$i]['name']]) ? $maxCrit[$config['criteria'][$i]['name']] : null;
                        $excludesDetailAux[0][$config['criteria'][$i]['name'].' Score Avg'] = $excludeCritAverage[$config['criteria'][$i]['name']];
                    }
                    $excludesDetailAux[0]['Excludes - Total Weighted Score Avg.'] = $excludeAverage;

                    array_push($excludes, $excludesAux);
                    $minCrit = [];
                    $maxCrit = [];
                    $excludeCrits = [];

                    if(isset($excludesDetailAux) && !empty($excludesDetailAux)){
                        foreach($excludesDetailAux as $aux){
                            array_push($excludesDetail, $aux);
                        }
                    }
                    $excludesDetailAux = null;
                    unset($excludesDetailAux);
                }
            }

            isset($excludesDetail[0]) ? $excludeDetailKeys = array_keys($excludesDetail[0]) : $excludeDetailKeys = null;
            isset($excludes[0]) ? $excludeKeys = array_keys($excludes[0]) : $excludeKeys = [];
            isset($includes[0]) ? $includeKeys = array_keys($includes[0]) : $includeKeys = [];
        }

        if(!isset($excludesDetail)) $excludesDetail = [];
        if(!isset($includes)) $includes = [];
        if(!isset($extrasDataKeys)) $extrasDataKeys = [];
        //if(!isset($entriesDataKeys)) $entriesDataKeys = [0,1];
        if(!isset($excludes)) $excludes = [];
        if(!isset($includeKeys)) $includeKeys = [];
        if(!isset($excludeKeys)) $excludeKeys = [];
        if(!isset($excludeDetailKeys)) $excludeDetailKeys = [];

        $filename = "Resultados-".$con->code."-".date('Y-m-d').".xls";

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        $alphabet = range('A', 'Z');

        foreach($entriesDataKeys as $key => $value){
            $pos = strrpos($value, '-');
            if($pos)
                $entriesDataKeys[$key] = substr($value, $pos+1);
        }

        /*** Arma el sheet de entries ***/
        $objPHPExcel->setActiveSheetIndex(0);
        $entries = $objPHPExcel->getActiveSheet(0);
        $entries->setTitle("Entries");
        $entries->fromArray(array($entriesDataKeys), null, 'A1', false, false);
        $entries->fromArray($entriesData, null, 'A2', false, false);
        /*if(sizeof($entriesDataKeys)-1 >= count($alphabet)){
            if($alphabet[count($alphabet)-sizeof($entriesDataKeys)])
                $toCell = "A".$alphabet[count($alphabet)-sizeof($entriesDataKeys)];
            else
                $toCell = "B".$alphabet[sizeof($entriesDataKeys)-1-count($alphabet)];
        }else{
            $toCell = $alphabet[sizeof($entriesDataKeys)-1];
        }
        $entries->setAutoFilter('A1:'.$toCell.'1');*/
        //$entries->getStyle('G2')->getAlignment()->setWrapText(true);
        //$entries->getStyle('A1:'.$toCell.'9999')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setWrapText(true);
        //$entries->getStyle('G')->getAlignment()->setWrapText(true);

        if(($votingSession->vote_type == VotingSession::AVERAGE || $votingSession->vote_type == VotingSession::VERITRON)){
            /*** Arma el sheet de extras ***/
            $extras = $objPHPExcel->createSheet(1);
            $objPHPExcel->setActiveSheetIndex(1);
            $extras->setTitle("Detalles");
            $extras->fromArray(array($extrasDataKeys), null, 'A1', false, false);
            $extras->fromArray($extrasData, null, 'A2', false, false);
            if (count($extrasDataKeys)) {
                $toCell = $alphabet[sizeof($extrasDataKeys) - 1];
                $extras->setAutoFilter('A1:' . $toCell . '1');
            }
        }

        if(($votingSession->vote_type == VotingSession::AVERAGE || $votingSession->vote_type == VotingSession::VERITRON)
            && strpos(strtolower($con->name), 'effie') !== false){
            //-------------------------------------------------------------------------
            /*** Arma el sheet de excludes ***/
            if(count($excludes)){
                $excludesPHP = $objPHPExcel->createSheet(2);
                $objPHPExcel->setActiveSheetIndex(2);
                $excludesPHP->setTitle("Excludes");
                $excludesPHP->fromArray(array($excludeKeys), null, 'A1', false, false);
                $excludesPHP->fromArray($excludes, null, 'A2', false, false);
                if(count($excludeKeys)) {
                    $toCell = $alphabet[sizeof($excludeKeys) - 1];
                    $excludesPHP->setAutoFilter('A1:' . $toCell . '1');
                }
            }
            //-------------------------------------------------------------------------
            /*** Arma el sheet de includes ***/
            if(count($includes)){
                $includesPHP = $objPHPExcel->createSheet(3);
                $objPHPExcel->setActiveSheetIndex(3);
                $includesPHP->setTitle("Includes");
                $includesPHP->fromArray(array($includeKeys), null, 'A1', false, false);
                $includesPHP->fromArray($includes, null, 'A2', false, false);
                if(count($includeKeys)) {
                    $toCell = $alphabet[sizeof($includeKeys) - 1];
                    $includesPHP->setAutoFilter('A1:' . $toCell . '1');
                }
            }
            //-------------------------------------------------------------------------------
            if(count($excludesDetail)){
                $excludeDetailsPHP = $objPHPExcel->createSheet(3);
                $objPHPExcel->setActiveSheetIndex(3);
                $excludeDetailsPHP->setTitle("Excludes Details");
                $excludeDetailsPHP->fromArray(array($excludeDetailKeys), null, 'A1', false, false);
                $excludeDetailsPHP->fromArray($excludesDetail, null, 'A2', false, false);
                if(count($excludeDetailKeys)) {
                    $toCell = $alphabet[sizeof($excludeDetailKeys) - 1];
                    $excludeDetailsPHP->setAutoFilter('A1:' . $toCell . '1');
                }
            }
        }

        // Redirect output to a client’s web browser (Excel5)
        $path = storage_path('exports/');
        $fs = new Filesystem();
        Log::info($path);
        if (!$fs->exists($path)) {
            App::abort(404, Lang::get('contest.FolderNotFound'));
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$path.$filename.'"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($path.$filename);
        header('Content-Range: bytes ' . filesize($path.$filename));
        readfile($path.$filename);
        exit;
    }

    public function showQrCode($contest, $ticketCode){
        $con = $this->getContest($contest);

        $validTicket = Ticket::where('code', $ticketCode)->first();
        if(!$validTicket)
            return Response::json(array('Error' => 'The QR code is invalid'));

        //TODO Validar codigo de ticket
        $qrCode = new QrCode();
        $qrCode->setText(url('/').'/'.$con->code.'/t/'.$ticketCode)->setSize(100)->setPadding(10)->setErrorCorrection('low')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0));
        //header('Content-Type: '.$qrCode->getContentType());
        header('Content-Type: image/png');
        imagepng($qrCode->getImage());
        exit();
    }

    public function exportEntriesData($contest){
        set_time_limit(0);
        $catIndex = Lang::get('contest.categories');
        $userIndex = Lang::get('contest.inscriptor');
        $entryStatusIndex = Lang::get('contest.entry.status');
        $billIndex = Lang::get('contest.billingStatus');
        $indexTitle = Lang::get('contest.title');
        $con = $this->getContest($contest);
        $superadmin = Auth::check() && Auth::user()->isSuperAdmin();

        $votingSession = VotingSession::where('contest_id', $con->id)
            ->where('vote_type', VotingSession::METAL)
            ->first();

        $query = Entry::where('contest_id', $con->id)
                ->whereNull('deleted_at')
                ->orderBy('id');
        //$entries = $query->with(['EntryMetadataValuesWithFields','categories','User','FilesFields'])->with(['Billings' => function ($q) {
        $entries = $query->with(['EntryMetadataValuesWithFields2','categories','User'])->with(['Billings' => function ($q) {
            $q->with('billingEntryCategories')->short();
        }])->get();


        $keyCount = 0;
        $entriesData = [];
        $keys = [];

        /***** Armo el array de la cabecera del excel con los campos exportables ********/
        $entriesKeys = array('id', $catIndex, $indexTitle, $userIndex, $entryStatusIndex, $billIndex, 'Fecha de creacion', 'Fecha de pago', 'Premio', 'Tipo de inscripcion');
        $fields = EntryMetadataField::where('contest_id', $con->id)->get()->toArray();

        $count = 0;
        foreach($fields as $field){
            if(isset($field['config'])){
                foreach($field['config'] as $title => $config){
                    if($title == 'exportable' && $config == 1){
                        array_push($entriesKeys, $field['id'].'-'.$field['label']);
                    }
                }
            }
        }
        unset($fields);
        $fields = null;

        $fields = InscriptionMetadataField::where('contest_id', $con->id)->get()->toArray();
        foreach($fields as $field){
            if(isset($field['config'])){
                foreach($field['config'] as $title => $config){
                    if($title == 'exportable' && $config == 1){
                        array_push($entriesKeys, $field['id'].'-'.$field['label']);
                    }
                }
            }
        }
        unset($fields);
        $fields = null;


        /********* Guardo los datos de cada entry en un array para exportar, tengo que ordenarlos como las cabeceras **********/
        $catIndex = Lang::get('contest.categories');
        $userIndex = Lang::get('contest.inscriptor');
        $entryStatusIndex = Lang::get('contest.entry.status');
        $billIndex = Lang::get('contest.billingStatus');
        $indexTitle = Lang::get('contest.title');
        $con = $this->getContest($contest);
        $superadmin = Auth::check() && Auth::user()->isSuperAdmin();
        $keyCount = 0;
        $entriesData = [];
        $keys = [];

        foreach($entries as $entry) {
            if(isset($entry->billings)|| $entry->billings!=null && count($entry->billings)!= 0){
                foreach($entry->billings as $bill){
                    $entry->$billIndex = Billing::getStatusName($bill->status);
                    $entry['Fecha de pago'] = $bill->paid_at;
                }
            }
            else{
                $entry->$billIndex = Lang::get('billing.status.waiting');
            }
            if(isset($entry->mainMetadata) || $entry->mainMetadata!=null && count($entry->mainMetadata) != 0){
                $first = $entry->mainMetadata->first();
                if($first) $entry->$indexTitle = $first->value;
                else $entry->$indexTitle = Lang::get('contest.entryNoTitle');
            }else{
                $entry->$indexTitle = Lang::get('contest.entryNoTitle');
            }
            $entry->billings = null;
            $entry->mainMetadata = null;
            unset($entry->billings);
            unset($entry->mainMetadata);
            $finalValue = null;
            $exportables = [];
            if(isset($entry->EntryMetadataValuesWithFields) || $entry->EntryMetadataValuesWithFields!=null && count($entry->EntryMetadataValuesWithFields) != 0){
                foreach($entry->EntryMetadataValuesWithFields as $item){
                    if(isset($item->config) && isset(json_decode($item->config)->exportable)){
                        if(json_decode($item->config)->exportable == 1){
                            if($item->type == EntryMetadataField::MULTIPLEWITHCOLUMNS || $item->type == EntryMetadataField::MULTIPLE){
                                if($item->type == EntryMetadataField::MULTIPLE){
                                    $multipleValue = "";
                                    try{
                                    foreach(json_decode($item->value) as $arrayValue){
                                        $multipleValue .= json_decode($item->config)->options[$arrayValue]." - ";
                                    }
                                    }catch(Exception $e){
                                        continue;
                                    }
                                    $exportables[$item->entry_metadata_field_id.'-'.$item->label] = $multipleValue;
                                }
                                if($item->type == EntryMetadataField::MULTIPLEWITHCOLUMNS){
                                    if(json_decode($item->value) != null){
                                        $dataObj = json_decode($item->value);
                                        if(!isset($dataObj->value)){
                                            $finalValue = $dataObj;
                                        }
                                        else{
                                            foreach($dataObj->value as $key => $value){
                                                $aux = $key.' = '.$value."\t";
                                                $finalValue.= $dataObj->label." - ".$aux." - "."\n";
                                            }
                                        }
                                        if(isset($exportables[$item->label])){
                                            if($finalValue != null){
                                                $exportables[$item->entry_metadata_field_id.'-'.$item->label] .= $finalValue;
                                            }
                                        }
                                        else{
                                            if($finalValue != null){
                                                $exportables[$item->entry_metadata_field_id.'-'.$item->label] = $finalValue;
                                            }
                                        }
                                    }
                                }
                            }else{
                                $finalValue = null;
                                if($item->type == entryMetadataField::FILE){
                                    $filesNames = '- ';
                                    foreach($item->files as $file){
                                        $filesNames .= $file->name." - \r";
                                    }
                                    $exportables[$item->entry_metadata_field_id.'-'.$item->label] =  $filesNames;
                                }
                                else $exportables[$item->entry_metadata_field_id.'-'.$item->label] = $item->value;
                            }
                        }
                    }
                }
            }
            $entry->EntryMetadataValuesWithFields = null;
            unset($entry->EntryMetadataValuesWithFields);
            $entry['exportables'] = $exportables;
            $entry->mainMetadata = null;
            if($votingSession) {
                $entry->votes = $entry->getJudgeVotes(null, $votingSession, null); /*** con null en vez de $voteCategories devuelve todo ****/
                $entry->voteSession = $votingSession;
            }
            if(isset($entry->categories) || $entry->categories!=null && count($entry->categories) != 0) {
                foreach ($entry->categories as $cats) {
                    $cat = $parent = '';
                    $cat = $cats->name;
                    while ($cats->parent_id != null) {
                        $aux = Category::select('name', 'parent_id')->where('id', $cats->parent_id)->first();
                        $parent = $aux['name'] . '>>' . $parent;
                        $cats->parent_id = $aux['parent_id'];
                    }

                    foreach($entriesKeys as $keys){
                        $data[$keys] = '-';
                    };
                    $data['id'] = $entry->id;
                    $data[$catIndex] = $parent.$cats->name;
                    $data[$indexTitle] = $entry->$indexTitle;
                    $data[$userIndex] = $entry->user->email;
                    $inscriptor = Inscription::where('user_id', $entry->user->id)
                        ->where('contest_id', $con->id)
                        ->where('role', Inscription::INSCRIPTOR)
                        ->first();

                    if($inscriptor){
                        $ins_meta_and_fields = InscriptionMetadataField::where('contest_id', '=', $con->id)
                            ->leftJoin('inscription_metadata_values', 'inscription_metadata_fields.id', '=', 'inscription_metadata_values.inscription_metadata_field_id')
                            ->where('inscription_metadata_values.inscription_id', $inscriptor->id)
                            ->get();

                        //$exportables = [];
                        foreach($ins_meta_and_fields as $metadata){
                            if(isset(json_decode($metadata->config)->exportable)){
                                if(json_decode($metadata->config)->exportable == 1){
                                    $exportables[$metadata->inscription_metadata_field_id."-".$metadata->label] = $metadata->value;
                                }
                            }
                        }
                    }
                    $entry['exportables'] = $exportables;

                    if(isset($inscriptor->inscription_type_id) && $inscriptor->inscription_type_id != null){
                        $inscription_type = InscriptionType::where('id', $inscriptor->inscription_type_id)->first();
                        $data["Tipo de inscripcion"] = $inscription_type->name;
                    }
                    $data[$entryStatusIndex] = Entry::getStatusName($entry->status);
                    $data[$billIndex] = $entry->$billIndex;
                    $data['Fecha de pago'] = $entry['Fecha de pago'];
                    $data['Fecha de creacion'] = $entry->created_at;
                    if (isset($entry->exportables)) {
                        foreach ($entry->exportables as $key => $value) {
                            $data[$key] = $value;
                        }
                    }

                    if(isset($entry['votes'])){
                        foreach($entry['votes'] as $categ => $votes){
                            if($categ == $cats->id){
                                foreach($votes as $vote){
                                    foreach($vote as $voteKey => $voteValue){
                                        if($voteKey == 'name') {
                                            $data['Premio'] = $voteValue;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $fileInfo = '';
                    $filesNames = '';
                    if( $superadmin ){
                        foreach($entry->files_fields as $fileField){
                            foreach($fileField->files as $files){
                                $fileName = $files->name;
                                foreach($files->contest_file_versions as $file){
                                    if($file->source == 1){
                                        $fileId = isset($file->id) ? $file->id : ' - ';
                                        $fileExt = isset($file->extension) ? $file->extension : ' - ';
                                        $fileSize = isset($file->size) ? $file->size : ' - ';
                                        $fileSizes = isset($file->sizes) ? $file->sizes : ' - ';
                                        $fileDuration = isset($file->duration) ? $file->duration : ' - ';
                                        $fileInfo = 'Name : '. $fileName."\n" .
                                            'File name : ' . $fileId.'.'.$fileExt."\n".
                                            /*'Id : ' . $fileId."\n".
                                            'Extension : ' . $fileExt."\n".
                                            'Size : ' . $fileSize."\n".
                                            'Sizes : ' . $fileSizes."\n".
                                            'Duration : ' . $fileDuration ."\n".*/
                                            '---------------------------------'."\n".
                                            $fileInfo."\n";
                                        $filesNames .= " ".$fileId.'.'.$fileExt;
                                    }
                                }
                            }
                        }
                        $data['files'] = $fileInfo;
                        $data['Files Names'] = $filesNames;
                    }
                    array_push($entriesData, $data);
                }
            }
        }
        $entries = null;
        unset($entries);
        $votingSession = null;
        unset($votingSession);


        /**** INSCRIPTORES *****/
        $query2 = Inscription::where('contest_id',$con->id)->where('role', Inscription::INSCRIPTOR)->groupBy('user_id');
        $inscriptors = $query2->with('user', 'insMetaAndFields')->get();

        $inscriptorsData = [];
        $keys = [];

        $fields = InscriptionMetadataField::where('contest_id', $con->id)->get()->toArray();
        $entryOrTicket = $con->type == Contest::TYPE_CONTEST ? 'Cantidad de inscripciones' : 'Cantidad de tickets';
        $inscriptorsKeys = array('Nombre', 'Apellido', 'E-mail', $entryOrTicket, 'Total a Pagar','Tipo de inscripcion');
        foreach($fields as $field){
            if(isset($field['config'])){
                foreach($field['config'] as $title => $config){
                    if($title == 'exportable' && $config == 1){
                        array_push($inscriptorsKeys, $field['id'].'-'.$field['label']);
                    }
                }
            }
        }
        unset($fields);
        $fields = null;

        foreach($inscriptors as $inscriptor) {
            $concatValue = [];
            foreach($inscriptorsKeys as $keys){
                $concatValue[$keys] = '-';
            };

            $ins_meta_and_fields = InscriptionMetadataField::where('contest_id', '=', $con->id)
                ->leftJoin('inscription_metadata_values', 'inscription_metadata_fields.id', '=', 'inscription_metadata_values.inscription_metadata_field_id')
                ->where('inscription_metadata_values.inscription_id', $inscriptor->id)
                ->get();

            $exportables = [];

            foreach($ins_meta_and_fields as $metadata){
                if(isset(json_decode($metadata->config)->exportable)){
                    if(json_decode($metadata->config)->exportable == 1){
                        if(is_array(json_decode($metadata->value))){// && sizeof(json_decode($metadata->value)) > 1){
                            $multipleValue = "";
                            //return json_decode($metadata->config)->options;
                            //return $metadata->value;
                            foreach(json_decode($metadata->value) as $arrayValue){
                                $multipleValue .= json_decode($metadata->config)->options[$arrayValue]." - ";
                            };
                            $exportables[$metadata->inscription_metadata_field_id."-".$metadata->label] = $multipleValue;
                        }else{
                            $exportables[$metadata->inscription_metadata_field_id."-".$metadata->label] = $metadata->value;
                        }
                    }
                }
            }

            $inscriptor->exportables = $exportables;
            if($inscriptor->user != null) $inscriptor->Casos = Entry::where('contest_id', $con->id)->where('user_id', $inscriptor->user->id)->count();

            if(isset($inscriptor->user)){
                $concatValue['Nombre'] = $inscriptor->user->first_name;
                $concatValue['Apellido'] = $inscriptor->user->last_name;
                $concatValue['E-mail'] = $inscriptor->user->email;
                $inscriptorEntriesIds = [];
                $inscriptorEntries = Entry::where('contest_id', $con->id)
                    ->where('user_id', $inscriptor->user->id)
                    ->select('id')
                    ->get()
                    ->toArray();
                foreach($inscriptorEntries as $ids){
                    array_push($inscriptorEntriesIds, $ids['id']);
                }
                if($con->type == Contest::TYPE_CONTEST)
                    $countEntries = EntryCategory::whereIn('entry_id', $inscriptorEntriesIds)->count();

                if($con->type == Contest::TYPE_TICKET){
                    $entryCatIds = EntryCategory::whereIn('entry_id', $inscriptorEntriesIds)->select('id')->get()->toArray();
                    $countEntries = Ticket::whereIn('entry_category_id', $entryCatIds)->count();
                }


                $concatValue[$entryOrTicket] = $countEntries;

                $userEntries = Entry::where('contest_id', $con->id)
                    ->where('user_id', $inscriptor->user->id)
                    ->join('entry_categories', 'entries.id', '=', 'entry_categories.entry_id')
                    ->select('entries.id', 'entry_categories.category_id')
                    ->get();

                $totalPaid = 0;
                $totalDebt = 0;
                $totalToPay = 0;

                foreach($userEntries as $ent){
                    $paid = BillingEntryCategory::where('entry_id', $ent->id)
                            ->where('category_id', $ent->category_id)
                            ->orderBy('created_at', 'desc')
                            ->first();
                    $categoryPrice = Category::where('id', $ent->category_id)->select('price')->first();

                    if($con->billing){
                        if(!$paid){
                            if(!$categoryPrice->price){
                                $totalDebt = $totalDebt + intval(json_decode($con->billing)->mainPrice);
                            }
                            else{
                                $totalDebt = $totalDebt + intval($categoryPrice->price);
                            }
                        }else{
                            $status = Billing::where('id', $paid->billing_id)->select('status')->first();
                            if($status['status'] == Billing::STATUS_SUCCESS){
                                $totalPaid = $totalPaid + intval($paid->price);
                            }
                            else $totalDebt = $totalDebt + intval($paid->price);
                        }
                    }
                }

                if($totalPaid > 0){
                    $totalToPay = "Pagado: ".$totalPaid. " - ";
                }
                if($totalDebt > 0){
                    $totalToPay > 0 ? $totalToPay .= "Debe: ".$totalDebt : $totalToPay = "Debe: ".$totalDebt;
                }
                $concatValue['Total a Pagar'] = $totalToPay;

                $inscription_type = InscriptionType::where('id', $inscriptor->inscription_type_id)->first();
                if(isset($inscriptor->inscription_type_id) && $inscriptor->inscription_type_id != null){
                    $concatValue["Tipo de inscripcion"] = $inscription_type->name;
                }
            }else{
                $concatValue = [];
            }
            $auxValue = '';

            if(isset($inscriptor->exportables)){
                $auxValue = null;
                foreach($inscriptor->exportables as $key => $value){
                    $concatValue[$key] = $value;
                }
            }

            if(!empty($concatValue)){
                array_push($inscriptorsData, $concatValue);
            }
        }

        foreach($inscriptorsKeys as $key => $value){
            $pos = strrpos($value, '-');
            if($pos)
                $inscriptorsKeys[$key] = substr($value, $pos+1);
        }

        /*foreach($inscriptorsKeys as $key){
            array_push($entriesKeys, $key);
        }*/

        foreach($entriesKeys as $key => $value){
            $pos = strrpos($value, '-');
            if($pos)
                $entriesKeys[$key] = substr($value, $pos+1);
        }

        array_push($entriesKeys, 'Files Names', 'files');

        return Excel::create($con->code."data".date('d-m-Y'), function($excel) use($inscriptorsKeys, $entriesKeys, $entriesData, $inscriptorsData) {
            if(sizeof($entriesData) > 0){
                $excel->sheet('entries', function($sheet) use($entriesKeys, $entriesData){
                    $sheet->fromArray(array($entriesKeys), null, 'A1', false, false
                    );
                    $sheet->fromArray($entriesData, null, 'A1', false, false);
                });
                $alphabet = range('A', 'Z');
                //return sizeof($entriesKeys);
                /*if(sizeof($entriesKeys)-1 >= count($alphabet)){
                    $toCell = "A".$alphabet[sizeof($entriesKeys)-1-count($alphabet)];
                }else{
                    $toCell = $alphabet[sizeof($entriesKeys)-1];
                }
                $excel->getActiveSheet()->setAutoFilter('A1:'.$toCell.'1');
                $excel->getActiveSheet()->getStyle('A1:'.$toCell.'9999')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setWrapText(true);*/
            }
            if(sizeof($inscriptorsData) > 0){
                $excel->sheet('inscriptores', function($sheet) use($inscriptorsData, $inscriptorsKeys){
                    $sheet->fromArray(array($inscriptorsKeys),null, 'A1', false, false);
                    $sheet->fromArray($inscriptorsData, null, 'A1', false, false);
                });
            }
        })->download('xls');
    }

    public function getUserDataEntry($contest){
        $user = Auth::user();
        $con = $this->getContest($contest);
        $roles = Inscription::where('user_id','=', $user->id)->where('contest_id', '=', $con->id)->select('role')->get();
        return Response::json(array('DATA'=> $roles));
    }

    public function getAdminInformation($contest){
        $con = $this->getContest($contest);
        $user = Auth::user();
        $superadmin = Auth::check() && Auth::user()->isSuperAdmin();

        $tickets = [];
        $inscriptions = Inscription::where('contest_id', '=', $con->id)
            ->where('deleted_at', null)
            ->select(array(DB::raw('COUNT(DISTINCT inscriptions.user_id) as total')))->get();

        if($con->getUserInscription($user, Inscription::OWNER) || $superadmin
            || $con->getUserInscription($user, Inscription::COLABORATOR)) {
            $files = ContestFile::where('contest_id', $con->id)->count();
            $entries = Entry::select('status',DB::raw('count(status) as total'))
                ->join('entry_categories', 'entries.id', '=', 'entry_categories.entry_id')
                ->where('contest_id', $con->id)
                ->groupBy('status')->get();

            if($con->type == Contest::TYPE_TICKET) {
                $tickets = Ticket::join('entry_categories', 'tickets.entry_category_id', '=', 'entry_categories.id')
                    ->join('entries', 'entries.id', '=', 'entry_categories.entry_id')
                    ->where('entries.contest_id', $con->id)
                    ->count();
            }

            $billings = Billing::select(array(DB::raw('sum(price) as totalBilling'), DB::raw('count(status) as total'),'status', 'currency'))
                ->where('contest_id', $con->id)
                ->groupBy('status')
                ->get();
        }else {
            $files = ContestFile::where('contest_id', $con->id)->where('user_id', $user->id)->count();
            $entries = Entry::select('status',DB::raw('count(status) as total'))
                ->join('entry_categories', 'entries.id', '=', 'entry_categories.entry_id')
                ->where('contest_id', $con->id)
                ->where('user_id', $user->id)
                ->groupBy('status')->get();

            if($con->type == Contest::TYPE_TICKET) {
                $tickets = Ticket::join('entry_categories', 'tickets.entry_category_id', '=', 'entry_categories.id')
                    ->join('entries', 'entries.id', '=', 'entry_categories.entry_id')
                    ->where('entries.contest_id', $con->id)
                    ->where('entries.user_id', $user->id)
                    ->count();
            }

            $billings = Billing::select(array(DB::raw('count(status) as total'),'status'))
                ->where('contest_id', $con->id)
                ->where('user_id', $user->id)
                ->groupBy('status')
                ->get();
        }
        $events = $con->getEvents();

        return Response::json(array('contest' => $con->code, 'tickets' => $tickets, 'inscriptions' => $inscriptions, 'billing' => $billings, 'entries' => $entries, 'files' => $files, 'events' => $events, 'allRoles' => Inscription::getAllRoles()));
    }

    public function postPayTickets($contest){
        $user = Auth::user();
        $con = $this->getContest($contest);
        $tickets = Input::get('tickets');
        //return $tickets;
        $totalAmount = 0;

        $billing = $con->getBillingData();
        $billCatsPricesInfo = "<br><u> Entradas</u>: <br>";
        foreach($tickets as $ticket){
            $totalAmount = $totalAmount + $ticket['totalPriceTickets'];
            $ticketPrice = $ticket['totalTickets'] > 1 ? $ticket['totalTickets']." x ".$ticket['price']." (".$ticket['totalPriceTickets'].") ".$billing['mainCurrency'] : $ticket['totalPriceTickets']." ".$billing['mainCurrency'];
            $billCatsPricesInfo = $billCatsPricesInfo.
            "<br> <u>Entrada</u>: ". $ticket['name'].
            " - <u>Precio</u>: ".$ticketPrice;
        }

        $billCatsPricesInfo = $billCatsPricesInfo."<br><br> <u>Total</u>: ".$totalAmount." ".$billing['mainCurrency'];

        $entry = new Entry();
        $entry->user_id = $user->id;
        $entry->contest_id = $con->id;
        $entry->status = Entry::APPROVE;
        $entry->save();

        /** TODO  PAYMENT METHODS (MP) **/
        if (!!$billing) {
            $method = Input::get('method');
            $bill = new Billing();
            $bill->code = Billing::createCode();
            $bill->contest_id = $con->id;
            $bill->user_id = $user->id;
            $bill->method = $method;
            $bill->status = Billing::STATUS_PENDING;
            $bill->price = $totalAmount;
            $bill->currency = $billing['mainCurrency'];
            $bill->save();
        }

        $data = [
            'con'                   => $con,
            'type'                  => Contest::TYPE_TICKET,
            'method'                => $method,
            'bill'                  => $bill,
            'entry'                 => $entry,
            'billCatsPricesInfo'    => $billCatsPricesInfo
            /*'entries'               => $entries,
            'entriesIterator'       => $entriesIterator,
            'total'                 => $total,
            'billCats'              => $billCats,
            'totalCategories'       => $totalCategories*/
        ];

        $bill = $this->processPayment($data);

        $ticketCodes = [];
        $ticketCodesEmail = [];
        $ticketCodesBody = "";
        foreach($tickets as $ticket){
            $billEntryCat = new BillingEntryCategory();
            $billEntryCat->billing_id = $bill->id;
            $billEntryCat->entry_id = $entry->id;
            $billEntryCat->category_id = $ticket['id'];
            $billEntryCat->price = $ticket['totalPriceTickets'];
            $billEntryCat->save();

            $entryCategory = new EntryCategory();
            $entryCategory->category_id = $ticket['id'];
            $entryCategory->entry_id = $entry->id;
            $entryCategory->save();

            for($i = 0; $i < $ticket['totalTickets'];$i++ ){
                $ticketCode = User::getRandomCode();
                $saveTicket = new Ticket();
                $saveTicket->entry_category_id = $entryCategory->id;
                $saveTicket->code = $ticketCode;
                $saveTicket->billing_entry_category_id = $billEntryCat->id;
                $saveTicket->save();

                $catName = EntryCategory::join('categories', 'categories.id', '=', 'entry_categories.category_id')
                    ->where('entry_categories.category_id', $entryCategory->category_id)
                    ->select('categories.name', 'categories.id')
                    ->first();

                $qrCode = new QrCode();
                $qrCode->setText(url('/').'/'.$con->code.'/t/'.$ticketCode)->setSize(100)->setPadding(10)->setErrorCorrection('low')
                    ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0));
                $imageUri = $qrCode->getDataUri();

                array_push($ticketCodes, ['code' => $ticketCode, 'qr' => $imageUri, 'name' => $catName->name]);
                array_push($ticketCodesEmail, ['code' => $ticketCode, 'qr' => url($con->code.'/qrCode/'.$ticketCode.".png"),
                    'name' => $catName->name, 'ticketId' => $catName->id]);
            }
        }

        //return View::make('emails.contest.new-ticket', ['user'=>$user, "tickets"=>json_encode($ticketCodesEmail)])->render();
        $body = View::make('emails.contest.new-ticket', ['user'=>$user, "tickets"=>json_encode($ticketCodesEmail), 'contest' => $con])->render();

        //return $body;
        /*** Send Email ***/
        $response = OxoMailer::sendMail([
            'email_to' => $user->email,
            'subject' => Lang::get('oxoTicket.transactionOkSubject',['contest' => $con->name]),
            'body' => $body
        ]);

        return Response::json(array('code' => $ticketCodes, 'transaction' => true, 'session' => isset($bill->session) ? $bill->session : null ));
    }

    public function getTicketCheck($contest, $ticket){
        $con = $this->getContest($contest);
        $colaborator = $owner = null;
        if(Auth::check()) {
            $colaborator = $con->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $owner = $con->getUserInscription(Auth::user(), Inscription::OWNER);
        }
        $permits = $colaborator['permits'];
        $superadmin = Auth::check() && Auth::user()->isSuperAdmin();

        if(!$colaborator && !$owner && !$superadmin) return Response::json(['colaborator' => $colaborator, 'owner' => $owner, 'superadmin' => $superadmin]);

        $validateTickets = Ticket::where('code', $ticket)->first();
        if($validateTickets){
            $entry = Ticket::join('entry_categories', 'tickets.entry_category_id', '=', 'entry_categories.id')
                ->join('entries', 'entries.id', '=', 'entry_categories.entry_id')
                ->join('categories', 'entry_categories.category_id', '=', 'categories.id')
                ->where('tickets.id', $validateTickets->id)
                ->select('entries.status', 'categories.name', 'categories.description')
                ->first();
            if($entry->status != Entry::APPROVE){
                $validTicket = Ticket::NOT_PAYED;
                return View::make('contest.tickets-check', ['ticketStatus' => $validTicket, 'contest' => $con, 'info' => $entry->name.' - '.$entry->description]);
            }
            $checked = TicketCheck::select('created_at', DB::raw('count(*) as total'))
                        ->where('ticket_id', $validateTickets->id)
                        ->groupBy(DB::raw('Date(created_at)'))
                        ->orderBy('created_at', 'desc')
                        ->get();

            $ticketCheck = new TicketCheck();
            $ticketCheck->ticket_id = $validateTickets->id;
            $ticketCheck->save();

            if(isset($checked) && sizeof($checked) > 0){
                return View::make('contest.tickets-check', ['dateChecked' => $checked, 'times' => sizeof($checked),'ticketStatus' => Ticket::ALREADY_CHECKED, 'contest' => $con, 'info' => $entry->name.' - '.$entry->description]);
            }
            $validTicket = Ticket::VALID;
        }
        else $validTicket = Ticket::INVALID;
        return View::make('contest.tickets-check', ['ticketStatus' => $validTicket,  'contest' => $con, 'info' => $entry->name.' - '.$entry->description]);
    }

    public function processPayment($data){
        $con = isset($data['con']) ? $data['con'] : null;
        $type = isset($data['type']) ? $data['type'] : null;
        $method = isset($data['method']) ? $data['method'] : null;
        $bill = isset($data['bill']) ? $data['bill'] : null;
        $onlyPay = isset($data['onlyPay']) ? $data['onlyPay'] : null;
        $entry = isset($data['entry']) ? $data['entry'] : null;
        $billCatsPricesInfo = isset($data['billCatsPricesInfo']) ? $data['billCatsPricesInfo'] : null;
        $entries = isset($data['entries']) ? $data['entries'] : null;
        $entriesIterator = isset($data['entriesIterator']) ? $data['entriesIterator'] : 1;
        $total = isset($data['total']) ? $data['total'] : 0;
        $billCats = isset($data['billCats']) ? $data['billCats'] : [];
        $totalCategories = isset($data['totalCategories']) ? $data['totalCategories'] : null;

        $ids = "";
        $billing = $con->getBillingData();
        $owners_ids = Inscription::where('contest_id', $con->id)->whereIn('role', array(Inscription::OWNER, Inscription::COLABORATOR))->lists('user_id');
        /** @var User[] $owners */
        $owners = User::whereIn('id', $owners_ids)->get();
        $entryInfo = "";
        $entryData = $con->getEntry($entry->id);
        if($entriesIterator == sizeof($entries)) {
            foreach($entries as $ent){
                $ids .= $ent['id'] . " - ";
            }
        }

        $link = url($con->code.'#/admin/billing/bill/'.$bill->id);
        switch ($method) {
            case Billing::METHOD_TRANSFER:
            case Billing::METHOD_CHECK:
            case Billing::METHOD_CREDITCARD:
                if(!$onlyPay || $entry->IsPaid()){
                    foreach($entryData['entry_metadata_values'] as $data){
                        if($data['type'] == MetadataField::FILE){
                            $entryInfo = $entryInfo."<u>".$data['fieldName']."</u><br>";
                            foreach($data['files'] as $file){
                                $type = "";
                                switch($file['type']){
                                    case 0: $type = "(VIDEO)"; break;
                                    case 1: $type = "(IMAGEN)"; break;
                                    case 2: $type = "(AUDIO)"; break;
                                    case 3: $type = "(DOCUMENTO)"; break;
                                    case 4: $type = "(OTRO)"; break;
                                }
                                $entryInfo = $entryInfo."<b> Archivo : </b>".$file['name']." - Tipo: ".$type."<br>";
                            }
                        }
                        else{
                            if(is_string($data['fieldName']) && is_string($data['value']))
                                $entryInfo = $entryInfo."<b><u>".$data['fieldName']."</u>: </b>".$data['value']."<br>";
                        }
                    }
                }

                break;
            case Billing::METHOD_MP:
                //Credenciales
                $billingConf = $con->getBillingData();
                $MPData = $billingConf['methods']['MercadoPago']['data'];
                MercadoPago\SDK::setAccessToken($MPData['accessToken']);
                $entryUrl = url($con->code."/entry/".$entry->id."/bs/");
                foreach ($billCats as $bCat) {
                    $payer = new MercadoPago\Payer();
                    $payer->name = $entry->user->first_name;
                    $payer->surname = $entry->user->last_name;
                    $payer->email = $entry->user->email;
                    $payer->date_created = $entry->user->created_at;

                    $bCat->entry_id = $entry->id;
                    // Crea un objeto de preferencia
                    $preference = new MercadoPago\Preference();

                    // Crea un ítem en la preferencia
                    $item = new MercadoPago\Item();
                    $item->title = $bCat->category->name . " - #" . $bCat->entry_id . " " . $bCat->entry->getName(); //Title of what you are paying for. It will be displayed in the payment process.;
                    $item->quantity = 1;
                    $item->unit_price = floatval($bCat->price);
                    $item->currency_id = $bill->currency; // Available currencies at: https://api.mercadopago.com/currencies
                    $preference->items = array($item);
                    /*$preference->back_urls =  [
                        "success" => $entryUrl."/".Billing::STATUS_SUCCESS,
                        "failure" => $entryUrl."/".Billing::STATUS_ERROR,
                        "pending" => $entryUrl."/".Billing::STATUS_PENDING,
                    ];
                    $preference->auto_return = "approved";*/
                    $preference->save();

                    $bill->transaction_id = $preference->id;
                    $bill->payment_data = json_encode(['url' => $preference->init_point]);
                    $bill->save();
                    $bill->redirectUrl = $preference->init_point;
                }
                break;
                /*$MPData = $billing['methods']['MercadoPago']['data'];
                $mp = new MP($MPData['clientId'], $MPData['clientSecret']);

                $mp->sandbox_mode(Config::get('billing.MercadoPagoSandbox'));

                // Documentación sobre Preferences de MercadoPago
                // https://www.mercadopago.com.ar/developers/es/api-docs/basic-checkout/checkout-preferences/
                $entryUrl = url($con->code."/entry/".$entry->id."/bs/");
                //$notificationUrl = url("/api/contest/".$con->code."/report-payment/".$bill->code);
                $notificationUrl = url("/api/contest/".$con->code."/report-payment/");
                $preference_data = [
                    "items" => [],
                    "payer" =>  [
                        "name" => $entry->user->first_name,
                        "surname" => $entry->user->last_name,
                        "email" => $entry->user->email,
                        "date_created" => $entry->user->created_at,
                    ],
                    "back_urls" =>  [
                        "success" => $entryUrl."/".Billing::STATUS_SUCCESS,
                        "failure" => $entryUrl."/".Billing::STATUS_ERROR,
                        "pending" => $entryUrl."/".Billing::STATUS_PENDING,
                    ],
                    "auto_return" => "approved",
                    "notification_url" => $notificationUrl,
                    "external_reference" => $bill->id,
                ];
                foreach ($billCats as $bCat) {
                    $bCat->entry_id = $entry->id;
                    //$billCat->category_id = $cat->id;
                    //$billCat->price = $cat->getPrice();
                    //$billCat->save();
                    $preference_data['items'][] = [
                        "id" => $bCat->id,
                        "title" => $bCat->category->name." - #".$bCat->entry_id." ".$bCat->entry->getName(), //Title of what you are paying for. It will be displayed in the payment process.
                        "currency_id" => $bill->currency, // Available currencies at: https://api.mercadopago.com/currencies
                        "quantity" => 1,
                        "unit_price" => floatval($bCat->price)
                    ];
                }
                $preference = $mp->create_preference($preference_data);
                $redirectUrl = $preference['response'][Config::get('billing.MercadoPagoSandbox') ? 'sandbox_init_point' : 'init_point'];
                $bill->transaction_id = $preference['response']['id'];
                $bill->payment_data = json_encode(['url'=>$redirectUrl]);
                $bill->save();
                $bill->redirectUrl = $redirectUrl;
                break;*/
            case Billing::METHOD_TCO:
                $paymentData = Input::get('payment');
                $TCODAta = $billing['methods']['TCO']['data'];
                Twocheckout::privateKey($TCODAta['privateKey']);
                Twocheckout::sellerId($TCODAta['sellerId']);
                Twocheckout::sandbox(Config::get('billing.TCOSandbox'));
                try {
                    $charge = Twocheckout_Charge::auth(array(
                        "merchantOrderId" => $bill->id,
                        "token" => Input::get('token'),
                        "currency" => $bill->currency,
                        "total" => $bill->price,
                        // Options: https://www.2checkout.com/documentation/payment-api/create-sale
                        "billingAddr" => array(
                            "name" => Auth::user()->fullName(),
                            "addrLine1" => 'Manuela Pedraza 2174 B',
                            "city" => 'Capital Federal',
                            "state" => 'BA',
                            "zipCode" => '1429',
                            "country" => 'Argentina',
                            "email" => Auth::user()->email,
                            //"phoneNumber" => '555-555-5555'
                        )
                    ));

                    if ($charge['response']['responseCode'] == 'APPROVED') {
                        $bill->status = Billing::STATUS_SUCCESS;
                        $bill->paid_at = \Carbon\Carbon::now();
                        $bill->paid = $bill->price;
                        $bill->transaction_id = $charge['response']['transactionId'];
                    }
                } catch (Twocheckout_Error $e) {
                    //$bill->status = Billing::STATUS_ERROR;
                    //$bill->error = $e->getMessage();
                    //$bill->save();
                    $bill->delete();
                    return Response::json(['msg' => $e->getMessage()]);
                }
                $bill->payment_data = json_encode($paymentData);
                $bill->save();

                break;
            case Billing::CUSTOM_API:
                $CPData = $billing['methods']['customApi']['data'];
                //$entryUrl = url($con->code."/entry/".$entry->id."/bs/".Billing::STATUS_PENDING);
                $entryUrl = url($con->code);
                $notificationUrl = url("/api/contest/".$con->code."/report-payment/customApi/".$bill->code);

                $postURL = $CPData['postURL'];
                $billingId = $CPData['billingId'];
                $numberOfEntries = $CPData['numberOfEntries'];
                switch($bill->status){
                    case 0: $paymentStatus = "STATUS_PENDING"; break;
                    case 1: $paymentStatus = "STATUS_SUCCESS"; break;
                    case 2: $paymentStatus = "STATUS_ERROR"; break;
                }

                $pData = [
                    'postURL' => $postURL,
                    'billingId' => $bill->id,
                    'numberOfEntries' => $totalCategories,
                    'paymentStatus' => $paymentStatus,
                    'billingIdName' => $CPData['billingId'],
                    'numberEntriesName' => $CPData['numberOfEntries'],
                    'paymentStatusName' => $CPData['paymentStatus'],
                ];

                $bill->payment_data = json_encode($pData);
                $bill->save();

                $bill->payment_data = json_encode($pData);
                break;
            case Billing::METHOD_STRIPE:
                $products = "producto";
                $entryUrl = url($con->code."/entry/".$entry->id."/bs/");
                Stripe::setApiKey('');
                foreach ($billCats as $bCat) {
                    $products = $products." + ".$bCat->category->name;
                }
                $session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => $products,
                            ],
                            'unit_amount' => $bill->price,
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => $entryUrl."/".Billing::STATUS_SUCCESS,
                    'cancel_url' => $entryUrl."/".Billing::STATUS_PENDING,
                ]);
                $bill->payment_data = json_encode($session->payment_intent);
                $bill->save();
                $bill->session = $session;
                //return $response->withJson([ 'id' => $session->id ])->withStatus(200);
            break;
        }
        switch ($type) {
            case Contest::TYPE_CONTEST:
                if($entriesIterator == sizeof($entries)) {
                    $userInfo = "<u>Participante</u>: <br>"
                        . "<u>Identificador de pago</u>: " . $bill->id
                        . "<br><u>Nombre</u>: " . $entryData['user']['first_name'] . " " . $entryData['user']['last_name']
                        . "<br> <u>Email</u>:" . $entryData['user']['email'];
                    $subject = Lang::get('contest.payedEntry', ["contest" => $con->name, "entry" => $ids]);
                    $billCatsPricesInfo = $billCatsPricesInfo . "<br> <u>Total</u>: " .
                        $total . " " . $entryData['billings'][0]['currency'] .
                        "<br><u>Forma de pago</u>: " . $entryData['billings'][0]['method'];

                    $body = View::make('emails.contest.new-payment',
                        ['userInfo'=>$userInfo, "type"=>$type, "entry"=>$ids, 'billCatsPricesInfo'=>$billCatsPricesInfo, 'entryInfo'=>$entryInfo, "contest"=>$con->name, "link"=>$link])->render();

                    foreach($owners as $owner){
                        if(!$owner->canReceiveNotification(User::NotificationEntryPaid)) continue;
                        $response = OxoMailer::sendMail([
                            'email_to' => $owner->email,
                            'subject' => $subject,
                            'body' => $body
                        ]);
                    }
                }
            case Contest::TYPE_TICKET:
                $userInfo = "<u>Comprador</u>: <br>"
                    . "<u>Identificador de pago</u>: " . $bill->id
                    . "<br><u>Nombre</u>: " . $entryData['user']['first_name'] . " " . $entryData['user']['last_name']
                    . "<br> <u>Email</u>:" . $entryData['user']['email'];
                $subject = Lang::get('oxoTicket.payedTicket', ["contest" => $con->name, "entry" => $entry->id]);

                $body = View::make('emails.contest.new-payment',
                    ['userInfo'=>$userInfo, "type"=>$type, "entry"=>$entry->id, 'billCatsPricesInfo'=>$billCatsPricesInfo, 'entryInfo'=>$entryInfo, "contest"=>$con->name, "link"=>$link])->render();

                foreach($owners as $owner){
                    if(!$owner->canReceiveNotification(User::NotificationEntryPaid)) continue;
                    $response = OxoMailer::sendMail([
                        'email_to' => $owner->email,
                        'subject' => $subject,
                        'body' => $body
                    ]);
                }
        }
        return $bill;
    }

    function isEntryNotPaid($entry){
        $categories_ids = [];
        $countPayed = 0;
        $countNoPayed = 0;
        foreach($entry['entry_categories'] as $category){
            $payed = Entry::join('billing_entries_categories', 'billing_entries_categories.entry_id', '=', 'entries.id')
                ->where('billing_entries_categories.entry_id', $entry['id'])
                ->where('billing_entries_categories.category_id', $category['category_id'])
                ->whereNull('billing_entries_categories.deleted_at')
                ->count();
            if($payed == 0){
                array_push($categories_ids, $category['category_id']);
                $entry['categories_id'] = $categories_ids;
                $entry['mustPay'] = true;
                $countNoPayed++;
            }
            if($payed > 0){
                $countPayed++;
            }
        }

        $response = [
            'entry' => $entry,
            'countPayed' => $countPayed,
            'countNoPayed' => $countNoPayed,
        ];

        return $response;
    }

    public function filterChangeEntryStatus($contest){
        $user = Auth::user();
        $con = $this->getContest($contest);
        $superadmin = Auth::check() && Auth::user()->isSuperAdmin();
        $owner = $con->getUserInscription($user, Inscription::OWNER);
        $entries = Input::get('entries');
        $status = Input::get('status');
        $onlyPay = Input::get('onlyPay');
        // Devuelve todos los entries seleccionados, ya que no hay impedimentos para que sean puestos en error
        $responseEntries = [];
        $countPayed = 0;
        $countNoPayed = 0;

        foreach($entries as $entry){
            if($onlyPay == true){
                $entry['mustPay'] = true;
                $data = $this->isEntryNotPaid($entry);
                $countPayed = $countPayed + $data['countPayed'];
                $countNoPayed = $countNoPayed + $data['countNoPayed'];
                $entry = $data['entry'];
                array_push($responseEntries, $entry);
            }
            switch ($status){
                // Volver a completo
                case Entry::COMPLETE :
                case entry::APPROVE:
                    if($entry['status'] == Entry::FINALIZE){
                        array_push($responseEntries, $entry);
                    }
                break;
                // Finalizar
                case Entry::FINALIZE:
                    if($entry['status'] == Entry::COMPLETE || $entry['status'] == Entry::ERROR
                    || ($entry['status'] == Entry::APPROVE && ($superadmin || $owner))){
                    $data = $this->isEntryNotPaid($entry);
                    $countPayed = $countPayed + $data['countPayed'];
                    $countNoPayed = $countNoPayed + $data['countNoPayed'];
                    $entry = $data['entry'];
                    array_push($responseEntries, $entry);
                }
                break;
            }
        }

        return Response::json(['entries' => $status == Entry::ERROR ? $entries : $responseEntries,
            'bulkBillings' => isset($bulkBillings) ? $bulkBillings : [],
            'countPayed' => $countPayed,
            'countNoPayed' => $countNoPayed,
            'onlyPay' => $onlyPay
        ]);
    }

    public function postCheckFinalizedEntry($contest){
        $entryId = Input::get('id');
        $entry = Entry::where('id', $entryId)->first();
        //return Response::json(['id'=>$entryId, 'entry'=>$entry]);
        if($entry->check === null) $entry->check = true;
        else $entry->check = !$entry->check;
        $entry->save();
        return Response::json(['entry' => $entry], 200);
    }

    public function postEntryStatus($contest){
        /** @var TODO: 2checkout sell */
        $user = Auth::user();
        $con = $this->getContest($contest);
        $entries = Input::get('entry');
        //if(!isset($entry[0])) $entryId = $entry['id'];
        $status = Input::get('status');
        $onlyPay = Input::get('onlyPay');
        $message = Input::get('message');
        $discount = Input::get('discountValue');
        $notPayedCount = Input::get('notPayed');
        $returnEntries = [];
        $billing = false;
        $bill = null;
        $totalCategories = 0;

        $priceWithDiscount = 0;
        if($discount>0){
            $discountsArray = Discount::where('contest_id', $con->id)->get();
            foreach($discountsArray as $disc){
                if($disc['min_entries'] <= $notPayedCount){
                    if($disc['max_entries'] >= $notPayedCount || $disc['max_entries'] == null){
                        $priceWithDiscount = $disc['value'];
                        $discountId = $disc['id'];
                    }
                }
            };
        }

        $entriesIterator = 1;
        $total = 0;
        $moreThanOne = false;
        foreach($entries as $entryParam) {
            /** @var Entry $entry */
            $entryId = $entryParam['id'];
            $entry = Entry::where('contest_id', '=', $con->id)->where('id', '=', $entryId)->first();
            if (!$entry) {
                //return Response::json(['entry'=>$entry]);
                return Response::json(['msg' => Lang::get('contest.entryNotFound'),'entryId'=>$entryId, 'entries'=>$entries]);
            }
            $owner = $con->getUserInscription($user, Inscription::OWNER);
            $colaborator = $con->getUserInscription($user, Inscription::COLABORATOR);
            $inscriptor = $con->getUserInscription($user, Inscription::INSCRIPTOR);
            if($inscriptor){
                $inscription_type_price = $inscriptor->inscription_type != null ? $inscriptor->inscription_type->price : null;
            }else{
                $inscription_type_price = null;
            }
            if (!$owner && !$colaborator && !Auth::user()->isSuperAdmin()) {
                if (!$inscriptor || $entry->user_id != $user->id) {
                    return Response::json(['msg' => Lang::get('contest.entryNotFound')]);
                }
            }
            $currentStatus = $entry->status;
            switch ($currentStatus) {
                case Entry::APPROVE:
                    if (!$owner && !$colaborator && !Auth::user()->isSuperAdmin()) {
                        return Response::json(['entry' => $entry]);
                    }
                    break;
            }

            $owners_ids = Inscription::where('contest_id', $con->id)->whereIn('role', array(Inscription::OWNER, Inscription::COLABORATOR))->lists('user_id');
            /** @var User[] $owners */
            $owners = User::whereIn('id', $owners_ids)->get();

            switch ($status) {
                case Entry::ERROR:
                    if (!$owner && !$colaborator && !Auth::user()->isSuperAdmin()) {
                        return Response::json(['entry' => $entry]);
                    }

                    break;
                case Entry::APPROVE:
                    if (!$onlyPay) break;
                case Entry::COMPLETE:
                    if (!$onlyPay) break;
                case Entry::INCOMPLETE:
                    if (!$onlyPay) break;
                case Entry::FINALIZE:
                    if ($currentStatus == Entry::APPROVE && !$onlyPay) break;
                    $method = Input::get('method');
                    if ($entry->IsPaid() || !$method){
                        break;
                    }
                    $billing = $con->getBillingData();
                    if (!!$billing) {
                        if(!$bill){
                            $bill = new Billing();
                            $bill->code = Billing::createCode();
                            $bill->contest_id = $con->id;
                            $bill->user_id = $entry->user->id;
                            $bill->comments = $message;
                            $bill->method = $method;
                            $bill->status = Billing::STATUS_PENDING;
                            if($priceWithDiscount>0){
                                $bill->price = $priceWithDiscount*$notPayedCount;
                            }
                            else $bill->price = $inscription_type_price ? $inscription_type_price : $entry->getTotalPrice();
                            $bill->currency = $billing['mainCurrency'];
                            $bill->save();
                        }
                        else{
                            $moreThanOne = true;
                        }

                        $billCats = [];
                        if($entriesIterator == 1) $billCatsPricesInfo = "<br><u> Categorias</u>: <br>";
                        foreach ($entry->categories as $cat) {
                            if (!$entry->mustPayCategory($cat)) continue;
                            $billCat = new BillingEntryCategory();
                            $billCat->billing_id = $bill->id;
                            $billCat->entry_id = $entry->id;
                            $billCat->category_id = $cat->id;
                            if($priceWithDiscount>0){
                                $billCat->price = $priceWithDiscount;
                                $billCat->original_price = $cat->getPrice();
                                $billCat->discount_id = $discountId;
                            }
                            else $billCat->price = $cat->getPrice();
                            $billCat->save();
                            $billCats[] = $billCat;
                            $catInfo = Category::where('id', $cat->id)->select('name')->first();
                            $billCatsPricesInfo = $billCatsPricesInfo."<br> <u>Categoria</u>: ".$catInfo['name']." - <u>Precio</u>: ".$cat->getPrice()." ".$bill->currency;
                            $total = $billCat->price + $total;
                            $totalCategories++;
                        }
                        if($moreThanOne == true){
                            $billAux = Billing::where('id', $bill->id);
                            $billAux->update(['price' => $total]);
                        }
                        if($entriesIterator == sizeof($entries)){
                            $data = [
                                'con'                   => $con,
                                'type'                  => Contest::TYPE_CONTEST,
                                'method'                => $method,
                                'bill'                  => $bill,
                                'onlyPay'               => $onlyPay,
                                'entry'                 => $entry,
                                'billCatsPricesInfo'    => $billCatsPricesInfo,
                                'entries'               => $entries,
                                'entriesIterator'       => $entriesIterator,
                                'total'                 => $total,
                                'billCats'              => $billCats,
                                'totalCategories'       => $totalCategories
                            ];

                            $bill = $this->processPayment($data);
                        }
                        $entriesIterator++;
                    }
                break;
            }

            $entry = Entry::where('contest_id', '=', $con->id)->where('id', '=', $entryId);

            //**************************************************
            //***** Ssnd emails with the new entry status ******
            //**************************************************
            if (!$onlyPay) {
                $entryUpdate = [];
                $entryUpdate['status'] = $status;
                if (isset($message)) {
                    $entryUpdate['error'] = $message;
                }
                $entry->update($entryUpdate);
                /** Entry status notifications */
                $thisEntry = Entry::where('contest_id', '=', $con->id)->where('id', '=', $entryId)->first();
                switch($status){
                    case Entry::FINALIZE:
                        //Send mail to contest owner
                        $replaces = [
                            $con->name,
                            $thisEntry->id,
                            $thisEntry->getName(),
                            url($con->code.'/#/entry/'.$thisEntry->id),
                            $thisEntry->user->fullName(),
                            isset($message) ? $message : ""
                        ];
                        $body = ContestAsset::where('contest_id', $con->id)->where('type', ContestAsset::ENTRY_FINALIZED_EMAIL)->select('content')->first();
                        if(!empty($body))
                            $body->content = str_replace([':contest', ':entry', ':title', ':link', ':name', ':message'], $replaces, $body->content);
                        else $body = new ContestAsset();
                        foreach($owners as $owner) {
                            if(!$owner->canReceiveNotification(User::NotificationEntryFinalized)) continue;
                            $subject = Lang::get('contest.entryFinalizedSubject', ["contest" => $con->name, "entry" => $thisEntry->id]);
                            $response = OxoMailer::sendMail([
                                'email_to' => $owner->email,
                                'subject' => $subject,
                                'body' => $body->content
                            ]);
                        }
                        break;
                    case Entry::APPROVE:
                        //Send mail to user
                        if($thisEntry->user->canReceiveNotification(User::NotificationEntryApproved)) {
                            $replaces = [
                                $con->name,
                                $thisEntry->id,
                                $thisEntry->getName(),
                                url($con->code . '/#/entry/' . $thisEntry->id),
                                $thisEntry->user->fullName(),
                                isset($message) ? $message : ""
                            ];
                            $body = ContestAsset::where('contest_id', $con->id)->where('type', ContestAsset::ENTRY_APPROVED_EMAIL)->select('content')->firstOrFail();
                            $body->content = str_replace([':contest', ':entry', ':title', ':link', ':name', ':message'], $replaces, $body->content);
                            $subject = Lang::get('contest.entryApprovedSubject', ["contest" => $con->name, "entry" => $thisEntry->id]);
                            $response = OxoMailer::sendMail([
                                'email_to' => $thisEntry->user->email,
                                'subject' => $subject,
                                'body' => $body->content
                            ]);
                        }
                        break;
                    case Entry::ERROR:
                        if($thisEntry->user->canReceiveNotification(User::NotificationEntryError)) {
                            $replaces = [
                                $con->name,
                                $thisEntry->id,
                                $thisEntry->getName(),
                                url($con->code . '/#/entry/' . $thisEntry->id),
                                $thisEntry->user->fullName(),
                                isset($message) ? $message : ""
                            ];
                            $body = ContestAsset::where('contest_id', $con->id)->where('type', ContestAsset::ENTRY_ERROR_EMAIL)->select('content')->firstOrFail();
                            $body->content = str_replace([':contest', ':entry', ':title', ':link', ':name', ':message'], $replaces, $body->content);
                            $subject = Lang::get('contest.entryErrorSubject', ["contest" => $con->name, "entry" => $thisEntry->id]);
                            $response = OxoMailer::sendMail([
                                'email_to' => $thisEntry->user->email,
                                'subject' => $subject,
                                'body' => $body->content
                            ]);
                        }
                        //Send mail to user
                        break;
                }
            }
            //**************************************************
            //     End entry status emails
            //**************************************************
            $entryLog = new EntryLog();
            $entryLog['user_id'] = $user->id;
            $entryLog['entry_id'] = $entryId;
            if (isset($message)) {
                $entryLog['msg'] = $message;
            }
            $entryLog['status'] = isset($status) ? $status : 0;
            $entryLog->save();
            $entry = Entry::where('contest_id', '=', $con->id)->where('id', '=', $entryId)->basic()->firstOrFail();
            array_push($returnEntries, $entry);
        }
        /***** Final del foreach de entries, para bulks y para acciones individuales ****/
        if($billing) {
            if($bill->status == Billing::STATUS_SUCCESS) {
                return Response::json([
                    'success' => Lang::get('billing.success'),
                    'title' => Lang::get('billing.successtitle'),
                    'entry' => $entry,
                    'returnEntries' => $returnEntries
                ], 200, [], JSON_NUMERIC_CHECK);
            }elseif($bill->status == Billing::STATUS_PENDING) {
                if($method == Billing::METHOD_MP) {
                    if (isset($bill->redirectUrl)) {
                        return Response::json([
                            'success' => Lang::get('billing.MercadoPago.continuelink', ['url' => $bill->redirectUrl]),
                            'title' => Lang::get('billing.successtitle'),
                            'entry' => $entry,
                            'returnEntries' => $returnEntries
                        ], 200, [], JSON_NUMERIC_CHECK);
                    } else {
                        return Response::json([
                            'success' => Lang::get('billing.pending'),
                            'title' => Lang::get('billing.successtitle'),
                            'entry' => $entry,
                            'returnEntries' => $returnEntries
                        ], 200, [], JSON_NUMERIC_CHECK);
                    }
                }
                if($method == Billing::CUSTOM_API) {
                    return Response::json([
                        'success' => Lang::get('billing.customApi.continuelink', json_decode($bill->payment_data, true)),
                        'title' => Lang::get('billing.successtitle'),
                        'entry' => $entry,
                        'returnEntries' => $returnEntries
                    ], 200, [], JSON_NUMERIC_CHECK);
                }
            }
        }

        $counters = $this->entriesFiltersCounter($con);

        $totals = $counters['totalEntriesCategory'];
        $finalTotal = $counters['finalTotal'];
        $totalBillings = $counters['totalBillings'];
        $payedEntries = $counters['payedEntries'];

        $totalBillings[Billing::UNPAID] = $finalTotal - $payedEntries;

        return Response::json(['entry'=>$entry,
            'returnEntries' => $returnEntries,
            'billing' =>$billing,
            'billy' => $bill,
            'redirectUrl' => isset($bill->redirectUrl) ? $bill->redirectUrl : null,
            'totalEntriesCategory' => $totals,
            'finalTotal' => $finalTotal,
            'totalBillings' => $totalBillings,
            'session' => isset($bill->session) ? $bill->session : null],
            200, [], JSON_NUMERIC_CHECK);
    }

    public function postFileStatus($contest){
        $fileId = Input::get('fileId');
        $status = Input::get('status');
        $message = Input::get('message');
        $user = Auth::user();
        $file = ContestFile::where('id', '=', $fileId);
        $fileUpdate['tech_status'] = $status;
        if(isset($message)){$fileUpdate['description'] = $message;}
        if($status == ContestFile::TECH_OK){$fileUpdate['description'] = null;}
        $file->update($fileUpdate);
        $contestFileLog = new ContestFileLog();
        $contestFileLog['contest_file_id'] = $fileId;
        $contestFileLog['status'] = $status;
        if(isset($message)){
            $contestFileLog['msg'] = $message;
        }
        $contestFileLog['user_id'] = $user->id;
        $contestFileLog->save();
        $file = ContestFile::where('id', '=', $fileId)->first();
        return Response::json($file);
    }

    public function getEntryLog($contest, $entryId = null, $status = null){
        $user = Auth::user();
        if($entryId == null)
            $entryId = Input::get('id');
        $status = Input::get('status');
        /** @var EntryLog[] $entryLog */
        $query = EntryLog::where('entry_log.entry_id', '=', $entryId)
            ->with('user')
            ->orderBy('entry_log.created_at', 'ASC');
        if($status != null) $query->where('status', $status);
        $entryLog = $query->get();

        foreach($entryLog as $key => $log){
            $readBy = $log->read_by == null ? [] : json_decode($log->read_by);
            if(!in_array($user->id, $readBy)){
                array_push($readBy, $user->id);
            }
            $log->read_by = json_encode($readBy);
            $log->save();
        }

        return Response::json(array('entryLog' => $entryLog, 'entryId' => $entryId));

    }

    public function postEntryMessage($contest){
        $user = Auth::user();
        $message = Input::get('message');
        $entryId = Input::get('entryId');
        /* TODO Validar ID del entry con el user, que sea el inscriptor del entry, o un owner, colaborator o superadmin */
        $entry = Entry::where('id', '=', $entryId)->firstOrFail();
        $con = $this->getContest($contest);
        $readBy = [];
        if($message != null){
            $entryLog = new EntryLog();
            $entryLog['user_id'] = $user->id;
            $entryLog['entry_id'] = $entryId;
            $entryLog['msg'] = $message;
            $entryLog['status'] = Entry::ENTRY_MESSAGE;
            array_push($readBy, $user->id);
            $entryLog['read_by'] = json_encode($readBy);
            $entryLog->save();
            if($entryLog->id){
                $entryLog = EntryLog::where('entry_log.entry_id', '=', $entryId)
                    ->with('user')
                    ->orderBy('entry_log.created_at', 'ASC')
                    ->get();

                $userOwner = Entry::where('id', $entryId)->first();
                $OwnerEmail = User::where('id', $userOwner->user_id)->first();

                /*** Email para el propietario del entry ***/
                $link = url($con->code.'#/entry/'.$entry->id);
                $body = View::make('emails.contest.new-message',
                    ['user'=>$user->fullName(), 'entry'=>$entry->getName(), "contest"=>$con->name, "message"=>$message, "link"=>$link])->render();
                if($OwnerEmail->canReceiveNotification(User::NotificationNewMessage)) {
                    $mailToReceiver = OxoMailer::sendMail(['email_to' => $OwnerEmail->email,
                        'subject' => Lang::get('contest.newMessageReceived', ["entry" => $entryId, "contest" => $con->name]),
                        'body' => $body]);
                }

                $bodySent = View::make('emails.contest.new-message-sent',
                    ['entry'=>$entry->getName(), "contest"=>$con->name, "message"=>$message, "link"=>$link])->render();
                /*** Email para quien dejo el mensaje ***/
                if($user->canReceiveNotification(User::NotificationNewMessage)) {
                    $mailToWriter = OxoMailer::sendMail(['email_to' => $user->email,
                        'subject' => Lang::get('contest.newMessageSentSubject', ["entry" => $entryId, "contest" => $con->name]),
                        'body' => $bodySent]);
                }

                return Response::json(['status' => 200, 'flash' => Lang::get('contest.messageOk'), 'entryLog' => $entryLog]);
            };
        }
    }

    public function postAdminNote(){
        $user = Auth::user();
        $note = Input::get('note');
        $inscriptionId = Input::get('inscriptionId');
        $action = Input::get('action');
        $actionId = Input::get('id');
        switch ($action){
            case 0: //EDIT
                $adminNote = Note::where('id', $actionId)->firstOrFail();
                $adminNoteUpdate['msg'] = $note;
                $adminNote->update($adminNoteUpdate);
                if($adminNote->id){
                    $adminNote = Note::where('inscription_id', '=', $inscriptionId)
                        ->select('msg','created_at', 'id')
                        ->orderBy('created_at', 'ASC')
                        ->get();
                    return Response::json(['status' => 200, 'flash' => Lang::get('contest.messageOk'), 'note' => $adminNote]);
                };
                 break;
            case 1: // DELETE
                $adminNote = Note::where('id', $actionId)->firstOrFail();
                $adminNote->delete();
                $adminNote = Note::where('inscription_id', '=', $inscriptionId)
                    ->select('msg','created_at', 'id')
                    ->orderBy('created_at', 'ASC')
                    ->get();
                return Response::json(['status' => 200, 'flash' => Lang::get('contest.messageOk'), 'note' => $adminNote]);
                break;
            default: if($note != null){
                $adminNote = new Note();
                $adminNote['inscription_id'] = $inscriptionId;
                $adminNote['msg'] = $note;
                $adminNote->save();
                if($adminNote->id){
                    $adminNote = Note::where('inscription_id', '=', $inscriptionId)
                        ->select('msg','created_at', 'id')
                        ->orderBy('created_at', 'ASC')
                        ->get();
                    return Response::json(['status' => 200, 'flash' => Lang::get('contest.messageOk'), 'note' => $adminNote]);
                };
            };
        }
    }

    public function deleteEntry($contest){
        $con = $this->getContest($contest);
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
            $id = Input::get('id');
            /** @var Entry $entry */
            $entry = Entry::where('id', '=', $id)->firstOrFail();

            $superadmin = Auth::check() && Auth::user()->isSuperAdmin();
            $owner = $con->getUserInscription(Auth::user(), Inscription::OWNER);
            if(!$superadmin && !$owner && !$con->getUserInscription(Auth::user(), Inscription::COLABORATOR)) {
                if(!$con->getUserInscription(Auth::user(), Inscription::INSCRIPTOR) || $entry->user_id != Auth::id()){
                    return Response::make(Lang::get('contest.entryNotFound'), 404);
                }
            }
            if($entry){
                BillingEntryCategory::where('entry_id', $entry->id)->delete();
                $entry->delete();
            }
            return Response::json(['status' => 200, 'flash' => Lang::get('contest.entryDeleted')]);
        }
    }

    public function getAsset($contest, $id){
        /** @var $contest Contest */
        $contest = $this->getContest($contest);
        $contestAsset = ContestAsset::where('contest_id','=',$contest->id)->where('id','=', $id)->firstOrFail();
        return Redirect::to($contestAsset->getCloudURL());
    }

    public function postNewAsset($contest){
        /** @var $contest Contest */
        $contest = $this->getContest($contest);
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
            $contestAsset = new ContestAsset();
            $contestAsset->contest_id = $contest->id;
            $contestAsset->type = ContestAsset::GENERAL_FILE;
            //$contestAsset->content = base64_encode($imagedata);
            $name = Input::get('flowFilename');
            $contestAsset->name = $name;
            $contestAsset->extension = pathinfo($name, PATHINFO_EXTENSION);
            $contestAsset->content_type = Format::getMimeType(pathinfo($name, PATHINFO_EXTENSION));
            $contestAsset->save();
            $filecontents = file_get_contents($tmpfname);
            Cloud::Instance()->UploadFileToGCStorage($contestAsset->getRelativePath(), $contestAsset->getBucket(), $filecontents);
            $response = Response::make('Upload successful', 200);
        } else {
            //$response = Response::make('Give me more!', 200);
            // This is not a final chunk, continue to upload
        }
        return $response;
    }

    public function addFile($contest){
        set_time_limit(0);
        /** @var $contest Contest */
        $contest = $this->getContest($contest);
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
        $tmpfname = tempnam(Config::get('upload.chunksmerge'), rand());
        if ($file->validateFile() && $file->save($tmpfname)) {
            //Se crea el archivo completo y se guarda en un temporal
            $fileName = pathinfo(Input::file('file')->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = strtolower(pathinfo(Input::file('file')->getClientOriginalName(), PATHINFO_EXTENSION));
            //Creamos el ContestFile y todos los ContestFileVersions necesarios
            $cfile = ContestFile::make($contest, $tmpfname, $fileName, $extension, Auth::user());
            ContestFile::executeEncoder($cfile->contest_id, $cfile->type);
            $cfile->contestFileVersions;
            $cfile->user = Input::get('userId');
            //$response = Response::make(json_decode($file), 200);
            return Response::json($cfile);
        } else {
            //$response = Response::make('Give me more!', 200);
            // This is not a final chunk, continue to upload
        }
        return $response;
    }

    public function newFile($contest){
        $contest = $this->getContest($contest);
        $filesData = Input::get('files');
        Log::info(print_r($filesData, true));
        $response = ['uploadedFiles'=>[],'uploadedVersions'=>[],'uploadUris'=>[]];
        for($i=0;$i<count($filesData);$i++){
            $filesData[$i]['name'];
            $size = $filesData[$i]['size'];
            $fileName = pathinfo($filesData[$i]['name'], PATHINFO_FILENAME);
            $extension = pathinfo($filesData[$i]['name'], PATHINFO_EXTENSION);
            $cfile = ContestFile::make($contest, null, $fileName, strtolower($extension), Auth::user(), $size);
            $cfile->contestFileVersions;
            //$cfile->user = Input::get('userId');
            $cfile->user = Auth::id();
            $source = $cfile->GetSource();
            $source->status = ContestFileVersion::UPLOADING;
            $source->uploaded_at = \Carbon\Carbon::now();
            $source->save();
            $response['uploadedFiles'][$fileName.".".$extension] = $cfile;
            $response['uploadedVersions'][$fileName.".".$extension] = $source->id;
            $response['uploadUris'][$fileName.".".$extension] = Cloud::Instance()->getGCRequestUri($source->getRelativePath(), $size, $source->storage_bucket);
        }
        $response['success'] = true;
        return Response::json($response);
    }

    public function fileUploaded($contest){
        $con = $this->getContest($contest);
        $fileVersionId = Input::get('id');
        /** @var ContestFileVersion $fileVersion */
        $fileVersion = ContestFileVersion::where('id','=',$fileVersionId)->firstOrFail();
        if($fileVersion->contestFile->user_id != Auth::id()){
            return false;
        }
        if($fileVersion->contestFile->type == Format::DOCUMENT || $fileVersion->contestFile->type == Format::OTHER)
            $fileVersion->contestFile->status = ContestFile::ENCODED;
        else $fileVersion->contestFile->status = ContestFile::QUEUED;
        $fileVersion->contestFile->save();
        $fileVersion->status = ContestFileVersion::AVAILABLE;
        $fileVersion->percentage = 100;
        $fileVersion->save();
        ContestFile::executeEncoder($fileVersion->contestFile->contest_id, $fileVersion->contestFile->type);
        return Response::json($fileVersion->contestFile);
    }

    public function uploadProgress($contest){
        $con = $this->getContest($contest);
        $fileVersionId = Input::get('id');
        $cfilev = ContestFileVersion::where('id','=',$fileVersionId)->firstOrFail();
        if($cfilev->contestFile->user_id != Auth::id()){
            return false;
        }
        $cfilev->status = ContestFileVersion::UPLOADING;
        $cfilev->percentage = Input::get('progress') * 100;
        $cfilev->uploaded_at = \Carbon\Carbon::now();
        $cfilev->save();
        return Response::json($cfilev->contestFile);
    }
    public function uploadCanceled($contest){
        $con = $this->getContest($contest);
        $fileVersionId = Input::get('id');
        $cfilev = ContestFileVersion::where('id','=',$fileVersionId)->firstOrFail();
        if($cfilev->contestFile->user_id != Auth::id()){
            return false;
        }
        $file = $cfilev->contestFile;
        foreach($file->contestFileVersions as $version){
            $version->deleteFiles();
        }
        $file->delete();
        return Response::json(array('flash'=>Lang::get('contest.contestFileUploadCanceled')));
    }
    public function uploadError($contest){
        $con = $this->getContest($contest);
        $fileVersionId = Input::get('id');
        $cfilev = ContestFileVersion::where('id','=',$fileVersionId)->firstOrFail();
        if($cfilev->contestFile->user_id != Auth::id()){
            return false;
        }
        $file = $cfilev->contestFile;
        $file->status = ContestFile::UPLOAD_INTERRUPTED;
        $file->save();
        $cfilev->status = ContestFileVersion::UPLOAD_INTERRUPTED;
        $cfilev->save();
        return Response::json(array('flash'=>Lang::get('contest.contestFileUploadError')));
    }

    public function contestStatusRequest($contest){
        $con = Input::get('contest');
        if(!isset($con))
            $con = $this->getContest($contest);
        else{
            $con = $this->getContest($con['code']);
        }
        $superAdmin = Input::get('admin');

        $status = Input::get('status');

        $owners = User::where('super', 1)->select('email')->get();

        if($status == Contest::STATUS_COMPLETE){
            $con->status = Contest::STATUS_COMPLETE;
            $con->save();
            return Response::json(array('status' => Contest::STATUS_COMPLETE));
        }


        if($status == Contest::STATUS_CLOSED){
            $con->status = Contest::STATUS_CLOSED;
            $con->save();
            foreach($owners as $super) {
                $response = OxoMailer::sendMail([
                    'email_to' => $super->email,
                    'subject' => 'Festival Cerrado: '.$con->name,
                    'body' => 'El siguiente Festival: "'.$con->name.'" ha sido cerrado. <br> Link: '.url("/".$con->code."/"),
                ]);
            }
            return Response::json(array('status' => Contest::STATUS_CLOSED));
        }

        if($status == Contest::STATUS_BANNED){
            $con->status = Contest::STATUS_BANNED;
            $con->save();
            foreach($owners as $super) {
                $response = OxoMailer::sendMail([
                    'email_to' => $super->email,
                    'subject' => 'Festival Baneado: '.$con->name,
                    'body' => 'El siguiente Festival: "'.$con->name.'" ha sido baneado. <br> Link: '.url("/".$con->code."/"),
                ]);
            }
            return Response::json(array('status' => Contest::STATUS_BANNED));
        }

        if($con->status == Contest::STATUS_COMPLETE){
            if(!$superAdmin) {
                $subject = Lang::get('contest.newContest', ["contest" => $con->name]);
                foreach($owners as $super) {
                    $response = OxoMailer::sendMail([
                        'email_to' => $super->email,
                        'subject' => $subject,
                        'body' => 'El contest: "'.$con->name.'" ha sido creado. <br> link: '.url("/".$con->code."/"),
                    ]);
                }
                $wizard_config = json_decode($con->wizard_config) ? json_decode($con->wizard_config) : (object)[];
                $wizard_config->habilitationRequest = 1;
                Contest::where('id', $con->id)->update(['wizard_config' => json_encode($wizard_config)]);
                return;
            }
            if($superAdmin){
                $con->status = Contest::STATUS_READY;
                $con->save();
                return Response::json(array('status' => Contest::STATUS_READY));
            }
        }

        if($con->status == Contest::STATUS_READY){
            $con->status = Contest::STATUS_PUBLIC;
            $con->save();
            foreach($owners as $super) {
                $response = OxoMailer::sendMail([
                    'email_to' => (string)$super->email,
                    'subject' => 'Festival Publico: '.$con->name,
                    'body' => 'El siguiente Festival: "'.$con->name.'" esta en estado Publico. <br> Link: '.url("/".$con->code."/"),
                ]);
            }
            return Response::json(array('status' => Contest::STATUS_PUBLIC));
        }
    }

    public function deleteInscription($contest){
        $user = Auth::user();
        $con = $this->getContest($contest);
        $role = Input::get('role');
        $inscription = $con->getUserInscription($user, $role);
        if($inscription) {
            $inscription->delete();
            return Response::json(array('flash'=>Lang::get('contest.userInscriptionDeleted')));
        }else{
            return Response::json(array('errors'=>Lang::get('contest.userInscriptionNotFound')));
        }
    }
    public function deleteFile($contest){
        /** @var $con Contest */
        $con = $this->getContest($contest);
        $input = Input::only('captcha');
        $rules = array(
            //'captcha' => 'required|captcha'
        );
        $validator = Validator::make($input, $rules);

        if ($validator->fails())
        {
            $messages = $validator->messages();
            return Response::json(array('errors'=>$messages, 'captchaUrl'=>Captcha::img()));
        }
        else {
            /** @var ContestFile $file */
            $inscription = $con->getUserInscription(Auth::user());
            $user = Auth::user();
            if($user->isSuperAdmin() || ($inscription && ($inscription->role == Inscription::OWNER)) || Input::get('tech')){
                $file = ContestFile::where('contest_id', '=', $con->id)->where('id', '=', Input::get('id'))->first();
            }
            else $file = ContestFile::where('contest_id', '=', $con->id)->where('user_id', '=', Auth::id())->where('id', '=', Input::get('id'))->first();

            if ($file){
                foreach($file->contestFileVersions as $version){
                    $version->deleteFiles();
                }
                $file->delete();
                return Response::make('Delete successful', 200);
            }else{
                //return Response::json(array('id' => Input::get('id'), 'user_id' => Auth::id(), 'contest_id' => $con->id));
                return Response::make('File not found', 400);
            }
        }
    }

    public function loginAsInscription($inscriptionId){
        $inscriptionData = Inscription::where('id', $inscriptionId)->select('user_id')->first();
        Auth::loginUsingId($inscriptionData['user_id']);
        return Redirect::to('/#home');

    }

    function cmpstatic($a, $b)
    {
        return strcmp($a->name, $b->name);
    }

    public function exportStatic($contest, $code, $groupIndex = null, $catId = null, $entryId = null)
    {
        set_time_limit(0);
        $con = $this->getContest($contest, false, 'childrenCategories');
        $user = Auth::user();
        $superadmin = Auth::check() && Auth::user()->isSuperAdmin();
        $inscription = $con->getUserInscription($user, Inscription::COLABORATOR);
        $owner = $con->getUserInscription($user, Inscription::OWNER);
        if($owner || $inscription || $superadmin) {

            $votingSession = VotingSession::where('code', $code)->first();
        }
        else
        {
            return Response::make(Lang::get('VoteSession not found 4'), 404);
        }

        /** @var ZipArchive $zip */
        $download = $groupIndex == "download";
        if($download){
            $groupIndex = null;

            $tmpFilename = tempnam(sys_get_temp_dir(), 'myApp_');

            $zip = new ZipArchive();
            if($zip->open($tmpFilename, ZIPARCHIVE::OVERWRITE) !== true) {
                return false;
            }
        }

        /*$cats = $con->categories->sortBy(function ($cat) {
            return sprintf('%-12s%s', $cat->parent_id, $cat->order);
        });*/

        $vcats = [];
        if(count($votingSession->categories)) {
            foreach ($votingSession->categories as $ec) {
                if (!array_key_exists($ec->id, $vcats)) {
                    $vcats[$ec->id] = $ec;
                }
            }
        }else{
            foreach ($con->categoriesByName as $ec) {
                if (!array_key_exists($ec->id, $vcats)) {
                    $vcats[$ec->id] = $ec;
                }
            }
        }

        $confDec = json_decode($votingSession->config);
        $shortlistEntryCategories = [];
        if(isset($confDec->shortListConfig) && count($confDec->shortListConfig) > 0){
            $newVcats = [];
            $votingShortLists = VotingShortlist::whereIn('voting_session_id', $confDec->shortListConfig)->select('entry_category_id')->get()->toArray();
            //return $votingShortLists;
            foreach($votingShortLists as $vSL){
                $shortlistEntryCategories[] = $vSL['entry_category_id'];
            }
            $voteCategories2 = EntryCategory::whereIn('id', $votingShortLists)->select('category_id')->get()->toArray();
            //return Response::json(array('voteccat' => sizeof($voteCategories),'votecounter' =>$voteCatCounter));
            if (count($voteCategories2) > 0) {
                foreach($voteCategories2 as $vCatId){
                    $newVcats[$vCatId['category_id']] = $vcats[$vCatId['category_id']];
                }
                $vcats = $newVcats;
                //usort($vcats, "cmp");
            }
        }

        $mediaFiles = "";

        $inGroups = count($votingSession->votingGroups);
        /** @var VotingGroup[] $groups */
        if($inGroups){
            if($download || $groupIndex === null) {
                //Página de categorías
                $view = View::make('contest.static.groups', array(
                        'download' => $download,
                        'contest' => $con,
                        'groups' => $votingSession->votingGroups,
                        'votingSession' => $votingSession
                    )
                );
                if($download) {
                    $zip->addFromString("screening/index.html", $view->render());
                }else {
                    return $view;
                }
            }
            if($download || ($groupIndex !== null && $catId == null)){
                if($download) $groups = $votingSession->votingGroups;
                else $groups = [$votingSession->votingGroups[$groupIndex]];
                $gIndex = 0;
                foreach($groups as $group){
                    //Página de categorías
                    $gcats = [];
                    foreach($group->entryCategories as $ec){
                        if(!array_key_exists($ec->category_id, $gcats)){
                            $gcats[$ec->category_id] = $ec->category;
                        }
                    }

                    if(count($gcats) == 0) $gcats = $vcats;
                    foreach($gcats as $cat){
                        if($cat->final){
                            $cCat = $cat;
                            $breadCrumbs = "";
                            while($parent = $cCat->parentCategory){
                                $breadCrumbs = $parent->name.' >> '.$breadCrumbs;
                                $cCat = $parent;
                            }
                            $cat->name = $breadCrumbs.$cat->name;
                        }
                    }
                    usort($gcats, array($this, "cmpstatic"));

                    $view = View::make('contest.static.categories', array(
                            'download' => $download,
                            'contest' => $con,
                            'group' => $group,
                            'groups' => true,
                            'groupIndex' => $download ? $gIndex : $groupIndex,
                            'categories' => $gcats,
                            'votingSession' => $votingSession,
                        )
                    );
                    if ($download) {
                        $zip->addFromString("screening/".$group['name'].".html", $view->render());
                    } else {
                        return $view;
                    }
                    $gIndex++;
                }
            }
        }else{
            if($download || $catId == null){
                //Página de categorías
                $gcats = $vcats;
                foreach($gcats as $cat){
                    if($cat->final){
                        $cCat = $cat;
                        $breadCrumbs = "";
                        while($parent = $cCat->parentCategory){
                            $breadCrumbs = $parent->name.' >> '.$breadCrumbs;
                            $cCat = $parent;
                        }
                        $cat->name = $breadCrumbs.$cat->name;
                    }
                }
                usort($gcats, array($this, "cmpstatic"));

                $view = View::make('contest.static.categories', array(
                        'download' => $download,
                        'contest' => $con,
                        'groupIndex' => $groupIndex,
                        'categories' => $gcats,
                        'votingSession' => $votingSession
                    )
                );
                if ($download) {
                    $zip->addFromString("screening/index.html", $view->render());
                } else {
                    return $view;
                }
            }
        }

        if($inGroups && ($download || ($groupIndex != null && $catId != null))){
            if($download) $groups = $votingSession->votingGroups;
            else $groups = [$votingSession->votingGroups[$groupIndex]];
            $gIndex = 0;
            foreach($groups as $group){
                $gcats = [];
                $centries = [];
                if(count($group->entryCategories)) {
                    foreach ($group->entryCategories as $ec) {
                        if (!array_key_exists($ec->category_id, $gcats) && ($download || $catId == $ec->category_id)) {
                            $gcats[$ec->category_id] = $ec->category;
                        }
                        if (!array_key_exists($ec->category_id, $centries)) {
                            $centries[$ec->category_id] = [];
                        }
                        if (($download || $catId == $ec->category_id)) $centries[$ec->category_id][] = $ec->entry;
                    }
                }else{
                    foreach($con->entries as $ent){
                        foreach($ent->categories as $c) {
                            if (($download || $catId == $c->id) && array_has($vcats, $c->id) && !array_key_exists($c->id, $gcats)) {
                                $gcats[$c->id] = $c;
                            }
                            if (($download || $catId == $c->id) && array_has($vcats, $c->id) && !array_key_exists($c->id, $centries)) {
                                $centries[$c->id] = [];
                            }
                            if (($download || $entryId == null || $entryId == $ent->id) && array_has($vcats, $c->id)) $centries[$c->id][] = $ent;
                        }
                    }
                }

                if(count($gcats) == 0) $gcats = $vcats;
                foreach($gcats as $cat){
                    if($cat->final){
                        $cCat = $cat;
                        $breadCrumbs = "";
                        while($parent = $cCat->parentCategory){
                            $breadCrumbs = $parent->name.' >> '.$breadCrumbs;
                            $cCat = $parent;
                        }
                        $cat->name = $breadCrumbs.$cat->name;
                    }
                }
                if($download || $entryId == null) {
                    //echo "<pre>";print_r($gcats);exit();
                    foreach ($gcats as $cat) {
                        foreach($centries[$cat->id] as $entry){
                            $entry->name = $entry->getName();
                        };

                        usort($centries[$cat->id], array($this, "cmp"));

                        if(!$cat->final) continue;
                        if(!isset($centries[$cat->id])) $centries[$cat->id] = [];
                        $view = View::make('contest.static.category', array(
                                'download' => $download,
                                'contest' => $con,
                                'category' => $cat,
                                'group' => $group,
                                'groups' => true,
                                'groupIndex' => $download ? $gIndex : $groupIndex,
                                'entries' => $centries[$cat->id],
                                'votingSession' => $votingSession
                             )
                        );
                        if ($download) {
                            $zip->addFromString("screening/cats/".$gIndex."/".$cat->id."/index.html", $view->render());
                        } else {
                            return $view;
                        }
                    }
                }

                foreach ($centries as $cId => $ces){
                    foreach ($ces as $entry) {
                        $cat = $gcats[$cId];
                        //return array(intval($entry->id), intval($entryId));
                        if($entryId != null && intval($entry->id) != intval($entryId))
                            continue;
                        $entry->getName();
                        $view = View::make('contest.static.entry', array(
                                'download' => $download,
                                'contest' => $con,
                                'category' => $cat,
                                'group' => $group,
                                'groups' => true,
                                'groupIndex' => $download ? $gIndex : $groupIndex,
                                'votingSession' => $votingSession,
                                'entry' => $entry,
                            )
                        );

                        if ($download) {
                            $zip->addFromString("screening/cats/".$gIndex."/".$cat->id."/entries/".$entry->id.".html", $view->render());
                        } else {
                            return $view;
                        }

                        $values = $entry->EntryMetadataValues;
                        foreach($values as $val) {
                            //if (in_array($val->EntryMetadataField->id, [369, 370])) {
                                $config = json_decode($val->EntryMetadataField->config);
                                if($config->important == 1){
                                    foreach ($val->files as $file) {
                                        switch ($file->type) {
                                            case Format::VIDEO:
                                                foreach ($file->contest_file_versions as $fv) {
                                                    if ($fv->source == 0){// && ($fv->format == null || $fv->format->position = 1)) {
                                                        $mediaFiles .= $fv->id . "." . $fv->extension."\n";
                                                    }
                                                }
                                                break;
                                            default:
                                                foreach ($file->contest_file_versions as $fv) {
                                                    if ($fv->source == 0) {
                                                        $mediaFiles .= $fv->id . "." . $fv->extension."\n";
                                                    }
                                                }
                                                break;
                                        }
                                    }
                                }
                        }
                    };

                }
                $gIndex++;
            }

            $zip->addFile(public_path()."/css/bootstrap.min.css","screening/css/bootstrap.min.css");
            $zip->addFile(public_path()."/css/font-awesome.min.css","screening/css/font-awesome.min.css");
            $zip->addFile(public_path()."/css/bootstrap.slate.css","screening/css/bootstrap.slate.css");
            $zip->addFile(public_path()."/css/app.css","screening/css/app.css");
            $zip->addFile(public_path()."/css/responsive.css","screening/css/responsive.css");
            $zip->addFromString("screening/media.txt", $mediaFiles);
            // Create recursive directory iterator
            /** @var SplFileInfo[] $files */
            $fontsPath = public_path()."/fonts/";
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($fontsPath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file)
            {
                // Skip directories (they would be added automatically)
                if (!$file->isDir())
                {
                    // Get real and relative path for current file
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($fontsPath));

                    // Add current file to archive
                    $zip->addFile($filePath, "screening/fonts/".$relativePath);
                }
            }

            $zip->close();
            $archive_file_name = "screening.zip";
            header("Content-type: application/zip");
            header("Content-Disposition: attachment; filename=$archive_file_name");
            header("Content-length: " . filesize($tmpFilename));
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile("$tmpFilename");
            exit();
        }elseif(!$inGroups && ($download || $catId != null)){
            $gcats = $download ? $vcats : [];
            $centries = [];
            foreach($con->entries as $ent){
                foreach($ent->categories as $c) {
                    if (($download || $catId == $c->id) && array_key_exists($c->id,$vcats) && !array_key_exists($c->id, $gcats)) {
                        $gcats[$c->id] = $c;
                    }
                    if (($download || $catId == $c->id) && array_key_exists($c->id,$vcats) && !array_key_exists($c->id, $centries)) {
                        $centries[$c->id] = [];
                    }
                    if (($download || $entryId == null || $entryId == $ent->id) && array_key_exists($c->id,$vcats)){
                        if(count($shortlistEntryCategories) == 0){
                            $centries[$c->id][] = $ent;
                        }else{
                            foreach ($ent->entryCategories as $eCat){
                                if(in_array($eCat->id, $shortlistEntryCategories)){
                                    $centries[$c->id][] = $ent;
                                }
                            }
                        }
                    }
                }
            }

            if(count($gcats) == 0 && $catId != null){
                $gcats = [$catId => Category::where('id','=',$catId)->first()];
                $centries[$catId] = [];
            }
            if($download || $entryId == null) {
                foreach($gcats as $cat){
                    if($cat->final){
                        $cCat = $cat;
                        $breadCrumbs = "";
                        while($parent = $cCat->parentCategory){
                            $breadCrumbs = $parent->name.' >> '.$breadCrumbs;
                            $cCat = $parent;
                        }
                        $cat->name = $breadCrumbs.$cat->name;
                    }
                }
                foreach ($gcats as $cat) {
                    if(!isset($centries[$cat->id])) continue;
                    foreach($centries[$cat->id] as $entry){
                        $entry->name = $entry->getName();
                    };
                    usort($centries[$cat->id], array($this, "cmp"));

                    $view = View::make('contest.static.category', array(
                            'download' => $download,
                            'contest' => $con,
                            'category' => $cat,
                            'groupIndex' => $groupIndex,
                            'groups' => false,
                            'entries' => isset($centries[$cat->id]) ? $centries[$cat->id] : [],
                            'votingSession' => $votingSession
                        )
                    );
                    if ($download) {
                        $zip->addFromString("screening/cats/" . $cat->id . "/index.html", $view->render());
                    } else {
                        return $view;
                    }
                }
            }

            if(count($gcats) == 0){
                $gcats = $vcats;
                foreach($gcats as $cat){
                    if($cat->final){
                        $cCat = $cat;
                        $breadCrumbs = "";
                        while($parent = $cCat->parentCategory){
                            $breadCrumbs = $parent->name.' >> '.$breadCrumbs;
                            $cCat = $parent;
                        }
                        $cat->name = $breadCrumbs.$cat->name;
                    }
                }
            }
            foreach ($centries as $cId => $ces){
                foreach ($ces as $entry) {
                    $cat = $gcats[$cId];
                    if($entryId != null && intval($entry->id) != intval($entryId))
                        continue;
                    $entry->getName();
                    $view = View::make('contest.static.entry', array(
                            'download' => $download,
                            'contest' => $con,
                            'category' => $cat,
                            'groupIndex' => $groupIndex,
                            'groups' => false,
                            'votingSession' => $votingSession,
                            'entry' => $entry,
                        )
                    );

                    if ($download) {
                        $zip->addFromString("screening/cats/" . $cat->id . "/entries/".$entry->id.".html", $view->render());
                    } else {
                        return $view;
                    }

                    $values = $entry->EntryMetadataValues;
                    foreach($values as $val) {
                        //if (in_array($val->EntryMetadataField->id, [369, 370])) {
                        $config = json_decode($val->EntryMetadataField->config);
                        if($config->important == 1){
                        foreach ($val->files as $file) {
                            switch ($file->type) {
                                case Format::VIDEO:
                                    foreach ($file->contest_file_versions as $fv) {
                                        if ($fv->source == 0){// && $fv->format_id == 4) {
                                            $mediaFiles .= $fv->id . "." . $fv->extension."\n";
                                        }
                                    }
                                    break;
                                default:
                                    foreach ($file->contest_file_versions as $fv) {
                                        if ($fv->source == 0) {
                                            $mediaFiles .= $fv->id . "." . $fv->extension."\n";
                                        }
                                    }
                                    break;
                            }
                        }
                        }
                        //}
                    }
                };

            }
            $zip->addFile(public_path()."/css/bootstrap.min.css","screening/css/bootstrap.min.css");
            $zip->addFile(public_path()."/css/font-awesome.min.css","screening/css/font-awesome.min.css");
            $zip->addFile(public_path()."/css/bootstrap.slate.css","screening/css/bootstrap.slate.css");
            $zip->addFile(public_path()."/css/app.css","screening/css/app.css");
            $zip->addFile(public_path()."/css/responsive.css","screening/css/responsive.css");
            $zip->addFromString("screening/media.txt", $mediaFiles);
            // Create recursive directory iterator
            /** @var SplFileInfo[] $files */
            $fontsPath = public_path()."/fonts/";
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($fontsPath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file)
            {
                // Skip directories (they would be added automatically)
                if (!$file->isDir())
                {
                    // Get real and relative path for current file
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($fontsPath));

                    // Add current file to archive
                    $zip->addFile($filePath, "screening/fonts/".$relativePath);
                }
            }

            $zip->close();
            $archive_file_name = "screening.zip";
            header("Content-type: application/zip");
            header("Content-Disposition: attachment; filename=$archive_file_name");
            header("Content-length: " . filesize($tmpFilename));
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile("$tmpFilename");
            exit();
        }
    }

    public function customInsert()
    {
        $con = $this->getContest("latameffie2016");
        $votingSession = VotingSession::where('code', "YlvJeuBp")->firstOrFail();
        $data = [
            #1
            ['voting_users' => [154,149,53,134,75,22,52,203],'entry_categories' => [1568,2141,2342,2216,2201,2217,1705,1864,2324,1887,2034,1834,2252,2013,2343,2146,2264,2240,2036,2043,2048,1718,2125,2337]],
            #2
            ['voting_users' => [124,191,98,67,20,110,103,193,249],'entry_categories' => [1677,1780,1897,1984,2312,2153,2129,2255,1999,1636,1639,1669,1674,1691,1879,2326,2124,2030,2033,2196,2017,2027,1848,2172]],
            #3
            ['voting_users' => [150,165,66,85,30,138,68,108,236,255],'entry_categories' => [1865,1873,1915,2344,2032,2005,2081,1587,1607,1664,1714,1782,1785,1787,1795,1823,1830,1894,1921,1947,1972,2164,2177]],
            #4
            ['voting_users' => [148,151,157,122,64,39,116,42,254,268],'entry_categories' => [2183,2233,2336,1646,1862,2063,2060,2069,2225,2076,1807,1813,1815,2139,1849,1593,1961,1977,1577,1941,2040,2199,2282,1888]],
            #5
            ['voting_users' => [207,135,89,78,90,73,76,221,237,266],'entry_categories' => [2065,2297,2298,2300,2303,1711,1715,2075,2132,2133,1926,2200,2248,2306,1586,2093,2331,2279,2169,2170,1783,1626,1656]],
            #6
            ['voting_users' => [60,223,109,112,74,126,197,133,256],'entry_categories' => [1659,1672,1739,1825,1918,2010,2294,2319,2253,2259,2311,1810,2204,1569,1875,1940,1950,2084,1591,1854,2116,1653,1671]],
            #7
            ['voting_users' => [32,94,59,147,163,26,153,199,238],'entry_categories' => [1701,1784,1792,2176,2212,1647,1655,1871,1930,1596,1708,2073,2197,2205,2012,2106,1853,2056,2320,2082,1704,1597]],
            #8
            ['voting_users' => [46,82,38,97,49,174,217.216,267],'entry_categories' => [1938,1703,1861,1944,1956,1959,1982,2121,2094,2165,2191,2223,2323,2127,2350,2313,2105,2284,2305,2074,2245,2143,2308]],
            #9
            ['voting_users' => [77,158,166,119,101,131,92,104,264],'entry_categories' => [1806,1811,1816,1748,1765,1867,2053,2144,1686,1925,1709,1710,2301,1986,2115,1676,1720,1759,1903,1922,2150,2162,2195]],
            #10
            ['voting_users' => [175,180,173,41,65,102,57,208,250,257],'entry_categories' => [2210,2100,1856,1812,1777,1842,1850,2287,1666,1684,1829,1932,1969,2332,2108,2042,2014,1625,1721,1892,1898]],
            #11
            ['voting_users' => [176,177,136,164,47,55,61,139,239],'entry_categories' => [1978,1979,2327,2249,1758,2260,1628,1629,1681,1722,1736,1744,1756,1820,1822,1911,1935,2039,2128,1857,1860,2071,2031]],
            #12
            ['voting_users' => [27,54,137,93,99,189,25,209,248,258],'entry_categories' => [2059,2120,2271,1561,2050,2107,2242,1837,1841,2089,1987,1606,1641,1700,1772,1788,1797,1819,1859,1886,2175,2228,2262]],
            #13
            ['voting_users' => [],'entry_categories' => []],
            #14
            ['voting_users' => [80,185,88,130,220,127,171,118,246,259],'entry_categories' => [2267,1651,1603,1844,1846,2230,1883,1937,1891,1954,2051,2280,2321,2269,1585,1604,1726,1821,1826,2009,1712]],
            #15
            ['voting_users' => [145,63,144,186,155,142,219,143,240,261],'entry_categories' => [1713,1752,2180,1613,1614,1814,1609,1615,1617,1618,1622,1623,1953,2003,2004,2345,2083,1630,1732,1652,1661,1667,1687]],
            #16
            ['voting_users' => [113,218,45,91,181,71,56,204,260],'entry_categories' => [1737,1786,1800,1801,1824,1872,1882,1907,1976,2268,2314,2330,2126,1698,2189,2202,2237,2270,2307,1723,2296,2222,2138]],
            #17
            ['voting_users' => [],'entry_categories' => []],
            #18
            ['voting_users' => [51,214,86,95,194,21,120,83],'entry_categories' => [2110,2315,1851,1611,1992,2285,2086,1645,2137,1683,1789,1828,1906,1910,1981,2219,1665,1909,1590,2283,2304,1761,1702]],
            #19
            ['voting_users' => [205,123,84,23,111,152,196,168,241,252],'entry_categories' => [1877,1663,2338,2272,1990,2016,2256,1605,1635,1660,1817,1983,2151,2161,2184,2213,2215,2229,2329,1649,2145,1852,2024]],
            #20
            ['voting_users' => [34,182,35,156,128,50,184,146],'entry_categories' => [2095,2147,2157,2293,2346,2156,2090,2171,2257,2028,1654,1658,1885,1900,1924,2025,2179,2214,2266,1874,1908,2173,2067]],
            #21
            ['voting_users' => [211,212,24,43,162,222,183,115,242,263],'entry_categories' => [2122,2236,2221,1870,2207,2227,2114,2113,2178,2309,1751,1839,1847,2136,1608,1624,2348,2155,2131,2273,1682,1717]],
            #22
            ['voting_users' => [202,79,72,140,31,19,114,36,243,253],'entry_categories' => [1831,1858,2080,2193,2206,2224,1757,2192,1616,2265,2318,2295,1805,1620,1917,2341,1566,1592,2181,2209,1644,2088,2220]],
            #23
            ['voting_users' => [125,132,28,69,15,206,87,16,247,262],'entry_categories' => [2008,1619,1621,2035,2226,1933,1689,2015,1791,1794,1799,1920,1951,1980,2099,2231,2333,2092,2058,1707,2130,2109,2154]],
            #24
            ['voting_users' => [105,213,215,169,160,100,18,192,29,107,229],'entry_categories' => [2258,1747,1750,1610,1754,2322,2347,1638,1790,1832,1881,1943,2152,2325,2046,1716,2302,2218,2194,1767,1775,1598,1637]],
            #25
            ['voting_users' => [179,48,172,188,70,106,81,159,33,244,251],'entry_categories' => [1650,1876,2185,2211,2238,2334,2246,2190,2123,2317,2278,1570,1793,2057,1778,2167,2328,2135,1868,1880,2006]],
            #26
            ['voting_users' => [187,129,190,167,141,121,161,228],'entry_categories' => [2276,1734,1773,1627,1657,1662,1675,1760,1827,1919,2103,2203,2274,2072,2235,2254,1808,1818,2277,2119,2261,2288,1763]],
            #27
            ['voting_users' => [58,198,37,201,40,210,178,96,265],'entry_categories' => [1843,1845,1574,1835,1916,1796,1942,1970,2101,2182,2198,2251,1985,2263,2335,1766,1774,1836,1840,1678,1899,2160]],
            #28
            ['voting_users' => [195,117,62,170,44,227,230,245],'entry_categories' => [2038,2044,2158,2047,2117,2291,2208,2055,2275,1579,1724,1762,1948,2001,2066,2232]]
        ];
        $newJudgesPerGroup = 5;
        $groupId = 0;
        foreach($data as $d){
            $groupId++;
            if(count($d['entry_categories'])) {
                //Agregar jueces genéricos para obtener los links
                for($i = 0; $i < $newJudgesPerGroup; $i++){
                    $inscription = new Inscription();
                    $inscription->role = Inscription::JUDGE;
                    $inscription->contest_id = $con->id;
                    $inscription->email = "judge".($i + 1)."@group".$groupId;
                    $inscription->save();
                    $votingUser = new VotingUser();
                    $votingUser->voting_session_id = $votingSession->id;
                    $votingUser->inscription_id = $inscription->id;
                    $votingUser->invitation_key = str_random(60);
                    $votingUser->save();
                    foreach ($d['entry_categories'] as $ecat) {
                        $v = new VotingUserEntryCategory();
                        $v->voting_user_id = $votingUser->id;
                        $v->entry_category_id = $ecat;
                        $v->save();
                    }
                }
                //Agregar jueces con ids de voting users existentes
                /*foreach ($d['entry_categories'] as $ecat) {
                    foreach ($d['voting_users'] as $vu) {
                //    foreach ($d['voting_users'] as $vu) {
                        $v = new VotingUserEntryCategory();
                        $v->voting_user_id = $vu;
                        $v->entry_category_id = $ecat;
                        $v->save();
                    }
                }*/
            }
        }
        echo "listo";
    }

    public function printFile($id){
        set_include_path(get_include_path() . PATH_SEPARATOR . "/path/to/dompdf");

        require_once "dompdf_config.inc.php";

        $dompdf = new DOMPDF();

        $html = <<<'ENDHTML'
<html>
 <body>
  <h1>Hello Dompdf</h1>
 </body>
</html>
ENDHTML;

        $dompdf->load_html($html);
        $dompdf->render();

        $dompdf->stream("hello.pdf");
    }

    function cmpp($a, $b) {
        return intval($b['total']) - intval($a['total']);
    }

    public function postUserAutoAbstain($contest){
        $data = Input::get('data');

        $con = $this->getContest($contest);

        UserAutoAbstain::where('voting_user_id', $data['judge'])
            ->where('voting_session_id', $data['voting_session_id'])
            ->delete();

        /*** didn't select abstentions, save a registry without values just to know ***/
        if(!$data['entry_metadata_fields']){
            $userAutoAbstain = new UserAutoAbstain();
            $userAutoAbstain->voting_user_id = $data['judge'];
            $userAutoAbstain->voting_session_id = $data['voting_session_id'];
            $userAutoAbstain->save();
        }
        else{
            foreach($data['entry_metadata_fields'] as $item){
                $userAutoAbstain = new UserAutoAbstain();
                $userAutoAbstain->voting_user_id = $data['judge'];
                $userAutoAbstain->voting_session_id = $data['voting_session_id'];
                $userAutoAbstain->entry_metadata_field_id = $item['entry_metadata_field_id'];
                $userAutoAbstain->value = $item['value'];
                $userAutoAbstain->save();
            }
        }
        // Si quiero que figure el entry pero sin opcion de votarlo, no llamo a esta funcion
        if(isset($data['config']['percentageByJudge']) && $data['config']['percentageByJudge'] === 1 && $data['config']['percentage'] > 0){
            return $this->checkVotingPercentages($con, $data);
        }
    }

    public function checkVotingPercentages($con, $data){
        /** check if percentage entries is checked */
        if(isset($data['percentage'])) $data['config']['percentage'] = $data['percentage'];

        /*** get the session voting entries from this user ***/
        $inscriptionQuery = Inscription::where('id', $data['inscription_id']);
        $inscription = $inscriptionQuery->with('InscriptionType')->first();
        $entries = $con->getJudgeEntries($data['voteSessionCode'], $inscription);
        $entryIds = [];
        /*** iterate over the entries, to find the autoabstains ***/
        foreach($entries['entries'] as $entry){
            $id = Entry::where('id', $entry['id'])->lists('id');
            array_push($entryIds, $id[0]);
        }
        /*** get the number of entries according to the percentage ***/
        if(isset($data['config']['percentage']) && $data['config']['percentage'] > 0) {
            $roundedEntriesCount = ceil((count($entryIds) * $data['config']['percentage']) / 100);
        }

        $votingUsersIds = [];
        $votingSession = VotingSession::where('id',$data['voting_session_id'])->with('votingUsers')->first();
        $voteCategories = VotingCategory::select('category_id')->where('voting_session_id', '=', $data['voting_session_id'])->lists('category_id');

        foreach($votingSession->voting_users as $users){
            array_push($votingUsersIds, $users->id);
        }

        $entryCat = VotingUserEntryCategory::whereIn('voting_user_id', $votingUsersIds)
            ->join('entry_categories', 'entry_categories.id', '=', 'voting_user_entry_categories.entry_category_id')
            ->select('entry_categories.entry_id', DB::raw('count(*) as total'))
            ->groupBy('entry_categories.entry_id')
            ->orderBy('total', 'asc')
            ->get();

        if(isset($roundedEntriesCount)){
            $entries = Entry::whereIn('id', $entryIds)->with('EntryMetadataValuesWithFields', 'entryCategories')
                ->orderBy(DB::raw('RAND()'))->get()->each(function ($entry) use($entryCat){
                    $entry->total = 0;
                    foreach($entryCat as $item){
                        if($item->entry_id == $entry->id){
                            $entry->total = $item->total;
                        }
                    }
                })->toArray();

            usort($entries,array($this, "cmpp"));
            $entries = array_reverse($entries);
        }else{
            $entries = Entry::whereIn('id', $entryIds)->with('EntryMetadataValuesWithFields', 'entryCategories')
                ->get()->each(function ($entry) use ($voteCategories){
                foreach($entry->entry_categories as $key => $entryCats){
                    if(!in_array($entryCats->category_id, $voteCategories)){
                        unset($entry->entry_categories[$key]);
                    }
                }
            });
        }

        $counter = 0;
        foreach($entries as $entry){
            if(isset($roundedEntriesCount) && $counter > $roundedEntriesCount) continue;
            $abstain = false;
            foreach($entry['entry_metadata_values_with_fields'] as $entryMetadata){
                foreach($data['entry_metadata_fields'] as $item){
                    if ($entryMetadata['entry_metadata_field_id'] == $item['entry_metadata_field_id'] && $entryMetadata['value'] == $item['value']) {
                    //if($entryMetadata['id'] == $item['id']){
                        $abstain = true;
                    }
                }
            }
            if(!$abstain){
                foreach($entry['entry_categories'] as $category){
                    /*VotingUserEntryCategory::where('voting_user_id', $data['judge'])
                        ->where('entry_category_id', $category['id'])->delete();*/
                    $votUserEntryCat = new VotingUserEntryCategory();
                    $votUserEntryCat->voting_user_id = $data['judge'];
                    $votUserEntryCat->entry_category_id = $category['id'];
                    $votUserEntryCat->save();

                    $counter++;
                }
            }
        }

        return Response::json(['msg' => 'Success', 'response' => 200]);
    }

    public function getSelectedAutoAbstain($contest){
        $data = Input::get('data');

        $abstains = UserAutoAbstain::where('voting_user_id', $data['judge'])
            ->where('voting_session_id', $data['votingSessionId'])
            ->get()
            ->toArray();

        $response = [];
        foreach($abstains as $abstain){
            $extraData = EntryMetadataValue::where('entry_metadata_field_id', $abstain['entry_metadata_field_id'])
                ->where('value', $abstain['value'])->select('id')->first();
            $extraDataId = isset($extraData) ? $extraData->id : [];
            array_push($response, ["value"=>$abstain['value'],"id"=>$extraDataId,"entry_metadata_field_id"=>$abstain['entry_metadata_field_id'],"entry_metadata_field"=>["id"=>$abstain['entry_metadata_field_id'],"contest_id"=>72,"label"=>"Cliente","description"=>null,"trans"=>null,"type"=>1,"required"=>1,"visible"=>0,"private"=>1,"order"=>2,"config"=>["exportable"=>1]]]);
        }
        return json_encode($response);
        //return Response::json();
        //array_push($abstains, $abstainValue);
        //if(count($abstains)>0) return $abstains;
        //else return [];
    }

    public function postImportContestData($contest)
    {
        $currentContest = $this->getContest($contest);
        $selCat = Input::get('selCat');
        $selEntryForm = Input::get('selEntryForm');
        $selUserForm = Input::get('selUserForm');
        $selStyle = Input::get('selStyle');
        $selEmails = Input::get('selEmails');
        $selPayments = Input::get('selPayments');
        $selVoting = Input::get('selVoting');
        $selectedContest = Input::get('contestId');

        /*** import Categories ***/
        if($selCat){
            //Relation array for new categories
            $relationCats = [];
            //Delete all existent categories in the contest
            Category::where('contest_id', $currentContest->id)->delete();

            // Get all the categories from the imported contest
            $importedCategories = Category::where('contest_id', $selectedContest['id'])->get();

            // Create the new Categories
            foreach($importedCategories as $importCategory){
                $newCateg = $importCategory->replicate();
                $newCateg->id = null;
                if(!$selEntryForm)$newCateg->template_id = null;
                $newCateg->contest_id = $currentContest->id;
                $newCateg->save();
                $relationCats[$importCategory->id] = $newCateg->id;
            }
            // took all the New categories
            $newCategories = Category::where('contest_id', $currentContest->id)->get();

            // Replace the parents ids with the new categories ids
            foreach($newCategories as $newCateg){
                if($newCateg->parent_id != null){
                    $newCateg->parent_id = $relationCats[$newCateg->parent_id];
                    $newCateg->update();
                }
            }
        }

        /*** import Entry Forms ***/
        if($selEntryForm){
            $relationTemplates = [];
            //Delete all existent templates in the contest
            EntryMetadataTemplate::where('contest_id', $currentContest->id)->delete();

            // Took the entry metadata templates and duplicate them, with the new contest id
            $importedTemplates = EntryMetadataTemplate::where('contest_id', $selectedContest['id'])->get();
            foreach ($importedTemplates as $importedTemplate) {
                $newEntryMDTemplate = $importedTemplate->replicate();
                $newEntryMDTemplate->contest_id = $currentContest->id;
                $newEntryMDTemplate->save();
                $relationTemplates[$importedTemplate->id] = $newEntryMDTemplate->id;
            }

            //Delete all existent forms in the contest
            EntryMetadataField::where('contest_id', $currentContest->id)->delete();
            //took the entry metadata fields and duplicate them with the new contest id
            $importedMetadataFields = EntryMetadataField::where('contest_id', $selectedContest['id'])->get();
            foreach($importedMetadataFields as $importedMetadataField){
                $newMetadataField = $importedMetadataField->replicate();
                $newMetadataField->contest_id = $currentContest->id;
                $newMetadataField->save();
                $entryMetadataConfigTemplates = EntryMetadataConfigTemplate::where('entry_metadata_field_id', $importedMetadataField->id)->get();
                // If the form has templates, duplicate them
                foreach($entryMetadataConfigTemplates as $entryMetadataConfigTemplate){
                    $newEMCT = $entryMetadataConfigTemplate->replicate();
                    $newEMCT->entry_metadata_field_id = $newMetadataField->id;
                    $newEMCT->template_id = $relationTemplates[$newEMCT->template_id];
                    $newEMCT->save();
                }
            }
        }

        // the entry forms has templates && the user selected also categories, add to them the new template id
        if($selectedContest['templates'] > 0 && $selCat) {
            //return Response::json(array($relationCats, $selCat, $newCategories, ));
            foreach ($newCategories as $newCateg) {
                if ($newCateg->template_id != null) {
                    $newCateg->template_id = $relationTemplates[$newCateg->template_id];
                    $newCateg->update();
                }
            }
        }

        // users registration form
        if($selUserForm) {
            $inscriptionTemplatesRelation = [];
            // Delete all the existing rows
            InscriptionMetadataField::where('contest_id', $currentContest->id)->delete();
            InscriptionType::where('contest_id', $currentContest->id)->delete();
            //If the inscription form has templates, duplicate
            $inscriptionTemplates = InscriptionType::where('contest_id', $selectedContest['id'])->get();
            foreach($inscriptionTemplates as $inscriptionTemplate){
                $newInscriptionTemplate = $inscriptionTemplate->replicate();
                $newInscriptionTemplate->contest_id = $currentContest->id;
                $newInscriptionTemplate->save();
                // If user registration form has templates associated with categories, duplicate them and assign the new category
                $categoryConfigTypes = CategoryConfigType::where('inscription_type_id', $inscriptionTemplate->id)->get();
                foreach($categoryConfigTypes as $categoryConfigType){
                    if(isset($relationCats[$categoryConfigType->category_id])){
                        $newCategoryConfigType = $categoryConfigType->replicate();
                        $newCategoryConfigType->inscription_type_id = $newInscriptionTemplate->id;
                        $newCategoryConfigType->category_id =  $relationCats[$categoryConfigType->category_id];
                        $newCategoryConfigType->save();
                    }
                }
                $inscriptionTemplatesRelation[$inscriptionTemplate->id] = $newInscriptionTemplate->id;
            }
            // Select the imported contest rows
            $importedInscriptionFields = InscriptionMetadataField::where('contest_id', $selectedContest['id'])->get();
            foreach($importedInscriptionFields as $importedInscriptionField){
                $newInscriptionField = $importedInscriptionField->replicate();
                $newInscriptionField->contest_id = $currentContest->id;
                $newInscriptionField->save();
                if(!empty($inscriptionTemplatesRelation)){
                    $inscMCT = InscriptionMetadataConfigType::where('inscription_metadata_field_id', $importedInscriptionField->id)->get();
                    foreach($inscMCT as $insc){
                        if(isset($inscriptionTemplatesRelation[$insc->inscription_type_id])){
                            $newInsc = $insc->replicate();
                            $newInsc->inscription_metadata_field_id = $newInscriptionField->id;
                            $newInsc->inscription_type_id = $inscriptionTemplatesRelation[$insc->inscription_type_id];
                            $newInsc->save();
                        }
                    }
                }
            }
        }
        // Styles Import
        $contestData = Contest::where('id', $selectedContest['id'])->first();
        if($selStyle){
            ContestAsset::where('contest_id', $currentContest->id)->delete();
            $importedContestAssets = ContestAsset::where('contest_id', $selectedContest['id'])->get();
            foreach($importedContestAssets as $importedContestAsset){
                $newContestAsset = $importedContestAsset->replicate();
                $newContestAsset->contest_id = $currentContest->id;
                $newContestAsset->save();
            }

            $currentContest->template = $contestData->template;
            $currentContest->custom_style = $contestData->custom_style;
            $currentContest->save();
            /*if(file_exists(public_path().'/css/contests/'.$selectedContest['code'].'.css')){
                if(!File::copy(public_path().'/css/contests/'.$selectedContest['code'].'.css', public_path().'/css/contests/'.$currentContest->code.'.css')){
                    return Response::json(array('errors'=>"couldn't copy css file"));
                }
            };*/
        }
        if($selPayments){
            $currentContest->billing = $contestData->billing;
            $currentContest->save();
        }
        if($selVoting){
            VotingSession::where('contest_id', $currentContest->id)->delete();
            $voteSessions = VotingSession::where('contest_id', $selectedContest['id'])->get();
            foreach($voteSessions as $voteSession){
                $newVoteSession = $voteSession->replicate();
                $newVoteSession->contest_id = $currentContest->id;
                $newVoteSession->save();
            }
        }

        return Response::json(['status' => 200, 'flash' => Lang::get('contest.importSuccess')]);
    }

    /**
     * Genera un newsletter con sus parametros y listado de personas a quienes enviar
     * @param $contest
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function postNewsletter($contest){
        $con = $this->getContest($contest);
        $new = false;
        if(!$con) return Response::make('contest not found', 400);
        $newsletter = Input::get('newsletter');
        $selectedEmails = Input::get('selectedEmails');
        $listOfEmails = Input::get('listOfEmails');

        if(isset($newsletter['id'])){
            $newsletterModel = Newsletter::find($newsletter['id']);
            if(!$newsletterModel) return Response::json(['status' => 400, 'errors' => ['nwesletter' => Lang::get('contest.newsletterError')]]);
        } else{
            $newsletterModel = new Newsletter;
            $new = true;
        }
        if(!isset($newsletter['name'])) return Response::json(['status' => 400, 'errors' => ['name' => Lang::get('contest.nameError')]]);
        if(!isset($newsletter['subject'])) return Response::json(['status' => 400, 'errors' => ['subject' => Lang::get('contest.subjectError')]]);
        if(!isset($newsletter['email_body'])) return Response::json(['status' => 400, 'errors' => ['emailBody' => Lang::get('contest.emailBodyError')]]);

        $newsletterModel->contest_id = $con->id;
        $newsletterModel->name = $newsletter['name'];
        $newsletterModel->subject = $newsletter['subject'];
        $newsletterModel->reply_to = $newsletter['reply_to'];
        $newsletterModel->email_body = $newsletter['email_body'];
        $newsletterModel->status = Newsletter::STATUS_WAITING;
        if(isset($newsletter['send_when'])) $newsletterModel->send_when = $newsletter['send_when'];
        $newsletterModel->save();

        if($selectedEmails){
            foreach($selectedEmails as $email){
                $newsLetterTo = NewsletterUser::where('email', '=', $email['email'])->where('newsletter_id', $newsletterModel->id)->first();
                if(!$newsLetterTo){
                    $newsLetterTo = new NewsletterUser;
                    $newsLetterTo->newsletter_id = $newsletterModel->id;
                    $newsLetterTo->email = $email['email'];
                    $newsLetterTo->status = NewsletterUser::PENDING_NOTIFICATION;
                    $newsLetterTo->save();
                }
            }
        }

        if($listOfEmails){
            $emailsLines = explode("\n", str_replace(["\r",","],"\n", $listOfEmails));
            $goodEmails = [];
            $emailsNames = [];
            foreach ($emailsLines as $emailLine) {
                if($emailLine != ''){
                    $emailLine = str_replace("\t"," ",$emailLine);
                    $spacePos = strpos($emailLine, " ");
                    if($spacePos !== false){
                        $email = substr($emailLine, 0, $spacePos);
                        $name = substr($emailLine, $spacePos + 1);
                    }else{
                        $email = $emailLine;
                        $name = "";
                    }
                    $validator = Validator::make(
                        array(
                            'email' => $email
                        ),
                        array(
                            'email' => 'required|email'
                        )
                    );
                    if (!$validator->fails())
                    {
                        if(!in_array($email, $goodEmails)) array_push($goodEmails, $email);
                        $emailsNames[$email] = $name;
                    }
                }
            }

            if(count($goodEmails) == 0) {
                return Response::json(['status' => 200, 'errors' => Lang::get("contest.votingNoGoodEmails")]);
            }

            foreach ($goodEmails as $email) {
                /** @var User $juser */
                /** @var Inscription $inscription */
                $newsLetterTo = NewsletterUser::where('email','=',$email)->where('newsletter_id', $newsletterModel->id)->first();
                if(!$newsLetterTo){
                    $newsLetterTo = new NewsletterUser();
                    $newsLetterTo->email = $email;
                    $newsLetterTo->newsletter_id = $newsletterModel->id;
                    $newsLetterTo->status = NewsletterUser::PENDING_NOTIFICATION;
                    $newsLetterTo->save();
                }
            }
        }

        return Response::json(array('flash'=> $new ? Lang::get('contest.newsletterCreated') : Lang::get('contest.newsletterSaved'), 'newsletter' => $newsletterModel));
    }

    /**** Manejo de los newsletters del concurso ****/
    /**
     * Devuelve la lista de newsletters asociadas al concurso.
     * @param string $contest
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllNewslettersData($contest){
        $con = $this->getcontest($contest);
        if(!$con) return Response::make('Contest not found', 400);
        $pageItems = 20;
        $page = (int) Input::get('page');
        $page = max($page, 1);
        $query = Input::get('query');
        if ($page > 0) Paginator::setCurrentPage($page);
        $orderBy = Input::get('orderBy');
        $orderDir = Input::get('orderDir');
        switch($orderBy) {
            case "id":
            case "name":
                break;
            default:
                $orderBy = "id";
                $orderDir = 'asc';
        }
        if ($orderDir == false) $orderDir = 'desc';
        else $orderDir = 'asc';
        $data = Newsletter::where('contest_id', '=', $con->id)->where('name', 'LIKE', '%'.$query.'%')->orderBy($orderBy, $orderDir)->paginate($pageItems, ['id', 'contest_id', 'name', 'subject', 'status']);
        $pagination = [
            'last' => $data->getLastPage(),
            'page' => $data->getCurrentPage(),
            'perPage' => $data->getPerPage(),
            'total' => $data->getTotal(),
            'orderBy' => $orderBy,
            'orderDir' => $orderDir == 'asc',
            'query' => $query,
        ];
        return Response::json(['status' => 200, 'data' => $data->getItems(), 'query' => $query, 'pagination' => $pagination]);
    }

    /**
     * Devuelve el newsletter
     * @param $contest
     * @param null newsletterId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function getNewsletter($contest, $newsletterId = NULL){
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest not found', 400);
        $newsletter = Newsletter::find($newsletterId);
        $selectedEmails = NewsletterUser::where('newsletter_id', $newsletterId)->select('email', 'status')->get()->toArray();
        return Response::json(['status' => 200, 'newsletter' => $newsletter, 'selectedEmails' => $selectedEmails, 'new' => $newsletterId == NULL]);
    }

    public function getPublicAnonymousVotingSession($contest, $code = null){
        $con = $this->getContest($contest);
        $votingUser = null;
        $votingSession = VotingSession::where('code', $code)->first();
        $cookieName = 'UserVisit'.$code;
        if($votingSession->publicAnonymous != true){
            return url($con->code);
        }
        else {
            if(isset($_COOKIE[$cookieName])){
                $userCookies = $_COOKIE[$cookieName];
            }
            else{
                $userCookies = User::getRandomCode();
                $Month = 2592000 + time();
                setcookie($cookieName, $userCookies, $Month);
            }

            $user = User::where('email', $userCookies . '@' . $con->code . ".oxoawards.com")->first();
            if($user){
            $inscription = Inscription::where('user_id', $user->id)
                ->where('contest_id', $con->id)
                ->first();
            if($inscription){
                $votingUser = VotingUser::where('voting_session_id', $votingSession->id)
                    ->where('inscription_id', $inscription->id)
                    ->first();
                }
            }
            if (!$votingUser) {
                $user = User::create([
                    'first_name' => Lang::get('voting.keyInviteJudgeFirstName'),
                    'last_name' => Lang::get('voting.keyInviteJudgeLastName'),
                    'email' => $userCookies . '@' . $con->code . ".oxoawards.com",
                    'password' => Hash::make($userCookies),
                    'active' => 1
                ]);
                $inscription = Inscription::create([
                    'user_id' => $user->id,
                    'contest_id' => $con->id,
                    'role' => Inscription::JUDGE
                ]);
                $votingUser = VotingUser::create([
                    'voting_session_id' => $votingSession->id,
                    'inscription_id' => $inscription->id,
                    'status' => VotingUser::ACCEPTED
                ]);
                /*if($inviteKey->votingGroup) {
                    VotingUserVotingGroup::create([
                        'voting_user_id' => $votingUser->id,
                        'voting_group_id' => $inviteKey->votingGroup->id
                    ]);
                }*/
                //}
            }
        }
        $entries = $con->getJudgeEntries($votingSession->code, $inscription, true);
        return Response::json(array('votingUser'=>$votingUser, 'votingSession'=>$votingSession, 'entries' => $entries, 'childrenCategories' => $con->childrenCategories));
        //return View::make('contest.anonymousVote', ['votingUser'=>$votingUser, 'votingSession'=>$votingSession, 'entries' => $entries['entries']]);
    }

    public function getAnonymousVoteView($contest){
        $con = $this->getContest($contest);
        return View::make('contest.anonymous', ['contest' => $con]);
    }

    public function postExportRanking($contest){
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest Not Found', 400);

        $metadataIds = Input::get('metadataIds');
        $metadataLabel = Input::get('metadataLabel');
        $voteSession = Input::get('voteSession');
        $categories = Input::get('categories');

        $votingSession = VotingSession::where('code', $voteSession['code'])->first();

        foreach($metadataIds as $key => $metadataId){
            $query = EntryMetadataValue::where('entry_metadata_values.entry_metadata_field_id', $metadataId)
                ->join('entry_categories', 'entry_metadata_values.entry_id', '=', 'entry_categories.entry_id')
                ->join('votes', 'votes.entry_category_id', '=', 'entry_categories.id')
                ->where('votes.voting_session_id', $votingSession->id);

                if(sizeof($categories) > 1){
                    $query->whereIn('entry_categories.category_id', $categories);
                }

                $ranking[$metadataLabel[$key]] = $query->select(DB::raw("entry_metadata_values.value as Nombre"), DB::raw("SUM(votes.vote_float) as Total"))
                ->groupBy(DB::raw("TRIM(entry_metadata_values.value)"))
                ->orderBy('Total', 'DESC')
                ->get()
                ->toArray();
        }

        /****************************************************************************/
        $path = storage_path('exports/');
        $rankingsFile = new Spreadsheet();
        $sheetNumber = 0;

        foreach($ranking as $index => $rank){
            if($sheetNumber>0) $rankingsFile->createSheet($sheetNumber);
            $rankingsFile->setActiveSheetIndex($sheetNumber);
            $rankingSheet = $rankingsFile->getActiveSheet($sheetNumber);
            $rankingSheet->setTitle($index);
            $rankingSheet->fromArray(array('Nombre', 'Total'), null, 'A1', false, false);
            $rankingSheet->fromArray($rank, null, 'A2', false, false);
            $sheetNumber++;
        }

        $rankingsFileName = "Mi primer archivo.xlsx";
        /**
         * Los siguientes encabezados son necesarios para que
         * el navegador entienda que no le estamos mandando
         * simple HTML
         * Por cierto: no hagas ningún echo ni cosas de esas; es decir, no imprimas nada
         */

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $path.$rankingsFileName . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($rankingsFile, 'Xlsx');
        //$writer->save('php://output');
        $writer->save($path.$rankingsFileName);
        header('Content-Range: bytes ' . filesize($path.$rankingsFileName));
        readfile($path.$rankingsFileName);
        exit;
        /******************************************************************************/
        // Redirect output to a client’s web browser (Excel5)
        /*$path = storage_path('exports/');
        $fs = new Filesystem();
        Log::info($path);
        if (!$fs->exists($path)) {
            App::abort(404, Lang::get('contest.FolderNotFound'));
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$path.$filename.'"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($path.$filename);
        header('Content-Range: bytes ' . filesize($path.$filename));
        readfile($path.$filename);

        return Response::json(array('ranking'=>$ranking, '$metadataLabel'=>$metadataLabel));*/
        //return Response::json(array('metadataIds'=>$metadataIds, 'votesession'=>$voteSession));

        //$ranking =
    }

    public function exportPDF($contest, $id = null){
        ini_set('max_execution_time', 60);
        set_time_limit(-1);

        /*$entriesIds = explode(',', $id);

        return sizeof($entriesIds);*/

        $con = $this->getContest($contest);
        $metadatas = [];
        $metadatasInit = [];
        $fieldsWithNoValue = array(
            MetadataField::TITLE,
            MetadataField::DESCRIPTION,
            MetadataField::FILE,
            MetadataField::TAB
        );

        if(!$id){
            $tmpFilename = tempnam(sys_get_temp_dir(), 'myApp_');

            $zip = new ZipArchive();
            if($zip->open($tmpFilename, ZIPARCHIVE::OVERWRITE) !== true) {
                return false;
            }
        }
        //See if the contest has entry metadata templates
        $metadataTemplates = EntryMetadataTemplate::where('contest_id', $con->id)->first();

        // SINGLE FORM, NO TEMPLATES: get static fields just once
        if(!$metadataTemplates){
            // MetadataFields with no value field (titles, descriptions, tabs, etc
            $metadataFieldsNoValue = EntryMetadataField::where('contest_id', $con->id)
            ->whereIn('type', $fieldsWithNoValue)
            ->where('private', 0)
            ->get();

            foreach($metadataFieldsNoValue as $metaNoValue){
                $metadatasInit[intval($metaNoValue->order)] = $metaNoValue;
            }
        }
        //Entries with metadatafields with value
        $query = Entry::with('EntryMetadataValuesWithFieldsPDF','categories')
            ->where('contest_id', $con->id);

        if(isset($id)) $query = $query->where('id', $id);
        else $query->whereIn('status', [Entry::FINALIZE, Entry::APPROVE]);

        $entries = $query->get();

        foreach($entries as $entry){

            // SINGLE FORM, NO TEMPLATES
            if(!$metadataTemplates) {
                // New Array of metadata, the index is the metadatafield order
                $metadatas = $metadatasInit;
            }

            /* With Snappy PDF */
            // For each category/entry one PDF
            foreach ($entry->categories as $key => $category) {
                // WITH TEMPLATES
                if($metadataTemplates){
                    $metadatas = [];

                    $metadataFieldsNoValue = EntryMetadataConfigTemplate::where('template_id', $category['template_id'])
                        //->with('EntryMetadataField')
                        ->join('entry_metadata_fields', 'entry_metadata_config_template.entry_metadata_field_id', '=', 'entry_metadata_fields.id')
                        ->where('entry_metadata_config_template.visible', 1)
                        ->where('entry_metadata_fields.private', 0)
                        ->whereIn('entry_metadata_fields.type', $fieldsWithNoValue)
                        ->get();

                    foreach($metadataFieldsNoValue as $metaNoValue){
                        $metadatas[intval($metaNoValue->order)] = $metaNoValue;
                    }
                }

                //return $entry->entry_metadata_values_with_fields_PDF;

                $entry->entry_metadata_values_with_fields = $this->processPdfs($entry,$metadatas);

                //return $entry->entry_metadata_values_with_fields;

                $parent = '';
                $cat = $category['name'];

                // Get categories breadcrumbs
                while ($category['parent_id'] != null) {
                    $aux = Category::select('name', 'parent_id')
                        ->where('id', $category['parent_id'])
                        ->first();
                    $parent = $aux['name'] . '>>' . $parent;
                    $category['parent_id'] = $aux['parent_id'];
                }

                $categoryName = $parent.$category->name;

                try{
                    $pdf = \PDF::loadView('metadata.print-entries-pdf',['entry_metadata_values_with_fields' => $entry->entry_metadata_values_with_fields, 'entryId' => $entry->id, 'contest' => $con, 'category' => $categoryName])
                        ->setPaper('a4')
                        ->setOption('margin-bottom', 10);
                }catch (Exception $e){
                    return $e;//$entry->entry_metadata_values_with_fields;
                    //return Response::make($e);
                }
                if(isset($id)){
                    return $pdf->download($entry->id.'.pdf');
                }

                $entryName =  $entry->id;
                if(sizeof($entry->categories) > 1){
                    $numberOfFile = $key+1;
                    $entryName = $entryName."-".$numberOfFile." de ".sizeof($entry->categories);
                }

                $breadCrumb = str_replace(">>", "/", $categoryName);

                $zip->addFromString($con->name." pdfs/".$breadCrumb."/".$entryName.".pdf", $pdf->download());
            }
        }

        $zip->close();

        $archive_file_name = $con->name."-Entries-PDFs.zip";
        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=$archive_file_name");
        header("Content-length: " . filesize($tmpFilename));
        header("Pragma: no-cache");
        header("Expires: 0");
        readfile("$tmpFilename");
        exit();
    }

    function processPdfs($entry, $metadatas){
        foreach ($entry->entry_metadata_values_with_fields_pdf as $key => $field) {
            switch($field->type){
                case MetadataField::FILE:
                    if(!isset($field)) continue;
                    foreach ($field->files as $files) {
                        foreach ($files->contest_file_versions as $fv) {
                            $fv->image = $fv->getSymbolicURL();
                        }
                    }
                break;
                case MetadataField::RICHTEXT:
                    $field->value = rtrim($field->value, '<p><br></p>');
                    $field->value = rtrim($field->value, '<!--StartFragment-->');
                    $field->value = rtrim($field->value, '<!--EndFragment-->');
                break;
            }
            //if(empty($metadatas[intval($field->order)])){
            $metadatas[intval($field->order)] = $field;
            //array_push($metadatas, $field);
            //}
        }
        ksort($metadatas);
        return $metadatas;
    }

    function postMeetUserInLobby($contest){
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest Not Found', 400);

        $votingUserId = Input::get('votingUserId');

        $votingUser = VotingUser::where('id', $votingUserId)->first();
        if($votingUser->status == VotingUser::IN_LOBBY){
            $votingUser->status = VotingUser::ACCEPTED;
        }
        else $votingUser->status = VotingUser::IN_LOBBY;
        $votingUser->save();

        return Response::json(['judge'=>$votingUser]);
    }

    function getMeetUserInLobby($contest, $id){
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest Not Found', 400);
        $inLobby = false;
        $votingUser = VotingUser::where('id', $id)->first();
        if($votingUser->status == VotingUser::IN_LOBBY){
            $inLobby = true;
        }
        return Response::json(['inLobby' => $inLobby]);
    }

    function postVotingLobby($contest){
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest Not Found', 400);
        $judgeId = Input::get('judgeId');
        $votingUser = VotingUser::where('id', $judgeId)->first();
        $votingUser->status = VotingUser::ACCEPTED;
        $votingUser->save();
    }

    function getUsersInLobby($contest){
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest Not Found', 400);
        $judgeId = Input::get('votingUserId');
        $voteSessionId = Input::get('voteSessionId');
        /*if(isset($judgeId)){
            $votingUser = VotingUser::where('id', $judgeId)->first();
            if($votingUser->status === VotingUser::ACCEPTED){
                $votingUser->status = VotingUser::IN_LOBBY;
            }
            else{
                $votingUser->status = VotingUser::ACCEPTED;
            }
            $votingUser->save();
            return Response::json(array('votingUser' => $votingUser));
        }*/
        $usersInLobby = VotingUser::join('inscriptions', 'inscriptions.id', '=', 'voting_users.inscription_id')
                        ->join('users', 'inscriptions.user_id', '=', 'users.id')
                        ->where('voting_users.voting_session_id', $voteSessionId)
                        //->where('voting_users.status', VotingUser::IN_LOBBY)
                        ->select('voting_users.id','users.first_name','users.last_name', 'voting_users.status')
                        ->get()->toArray();

        return Response::json(array('usersInLobby' => $usersInLobby));
    }

    /**
     * Gets a collection
     * @param $contest
     * @param $code
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */

    public function getCollection($contest, $code = NULL){
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest not found', 400);
        $collection = Collection::where('code',$code)->first();
        $collection->metadata_config = json_decode($collection->metadata_config);
        $vote_config = VotingSession::where('id', $collection->voting_session_id)
                                                ->select('config')->first();
        $config = [];
        if(isset($vote_config->config)) $config = json_decode($vote_config->config);
        $collection->vote_config = [];
        if(isset($config->extra))
            $collection->vote_config = $config->extra;

        $collection->config = json_decode($collection->config);
        if(isset($collection->config->voteType)){
            $voteType = $collection->config->voteType;
            foreach($collection->vote_config as $vote){
                if(isset($vote->id) && in_array($vote->id, $voteType)){
                    $vote->selected = true;
                }
            }
        }

        if($collection->private == 1){
            $collection->invites = CollectionKey::where('collection_id', $collection->id)
                                                ->select('email', 'key')
                                                ->get()
                                                ->toArray();
        }

        return Response::json(array('collection' => $collection), 200, [], JSON_NUMERIC_CHECK);
    }

    /**
     * creates a new collection of entries that can be published (public or private)
     * @param $contest
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function postCollection($contest)
    {
        $con = $this->getContest($contest);
        if (!$con) return Response::make('contest not found', 400);
        $collection = Input::get('collection');

        /*$rules = array(
            'name' => 'required',
            'start_at' => 'required|date|date_format:"Y-m-d H:i:s"',
            'finish_at' => 'required|date|date_format:"Y-m-d H:i:s"',
        );

        $validator = Validator::make($collection, $rules);
        if ($validator->fails())
        {
            $messages = $validator->messages();
            return Response::json(array('errors'=>$messages));
        }*/

        if (isset($collection['id'])) {
            $collectionModel = Collection::find($collection['id']);
            if (!$collectionModel) return Response::json(['status' => 400, 'errors' => ['collection' => Lang::get('contest.collectionError')]]);
        } else {
            $collectionModel = new Collection();
            $collectionModel->contest_id = $con->id;
            $collectionModel->code = Collection::createCode();
        }
        $collectionModel->name = $collection['name'];
        $collectionModel->private = isset($collection['private']) ? $collection['private'] : 0;
        $collectionModel->voting_session_id = isset($collection['voting_session_id']) ? $collection['voting_session_id'] : null;
        $collectionModel->show_prize = isset($collection['show_prize']) ? $collection['show_prize'] : 0;
        if(isset($collection['metadata_config'])){
            $collectionModel->metadata_config = json_encode($collection['metadata_config']);
        }
        if(isset($collection['config'])){
            if(isset($collection['show_prize']) && $collection['show_prize'] == 0) $collection['config']['voteType'] = [];
            $collectionModel->config = json_encode($collection['config']);
        }
        $collectionModel->start_at = $collection['start_at'];
        $collectionModel->finish_at = $collection['finish_at'];
        $collectionModel->save();

        $collectionModel->config = json_decode($collectionModel->config);

        return Response::json(['collection' => $collectionModel]);
    }

    public function postCollectionList($contest){
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest not found', 400);
        $pageItems = 20;
        $page = (int) Input::get('page');
        $page = max($page, 1);
        $query = Input::get('query');
        if ($page > 0) Paginator::setCurrentPage($page);
        $orderBy = Input::get('orderBy');
        $orderDir = Input::get('orderDir');
        switch($orderBy) {
            case "name":
            case "start_at":
            case "finish_at":
                break;
            default:
                $orderBy = "start_at";
                $orderDir = 'asc';
        }
        if($orderDir == false) $orderDir = 'desc';
        else $orderDir = 'asc';
        $data = DB::table('collections')
            ->where('contest_id', '=', $con->id)
            ->where('name', 'LIKE', '%'.$query.'%')
            ->whereNull('deleted_at')
            ->orderBy($orderBy, $orderDir)
            ->paginate($pageItems, ['code', 'name', 'start_at', 'finish_at']);

        $pagination = [
            'last' => $data->getLastPage(),
            'page' => $data->getCurrentPage(),
            'perPage' => $data->getPerPage(),
            'total' => $data->getTotal(),
            'orderBy' => $orderBy,
            'orderDir' => $orderDir == 'asc',
            'query' => $query,
        ];

        return Response::json(['collection' => $data->getItems(), 'query' => $query, 'pagination' => $pagination]);
    }

    public function getVotingSessions($contest){
        $con = $this->getContest($contest);
        if(!$con) return Response::Make('Contest nor found', 400);
        $votingSessions = VotingSession::where('contest_id', $con->id)->get(['id', 'name']);

        return Response::json(['data' => $votingSessions]);
    }

    public function getCollectionsView($contest){
        $con = $this->getContest($contest);
        return View::make('contest.collections', ['contest' => $con]);
    }

    public function getCollectionContents($contest, $code = NULL){
        //$con = $this->getContest($contest);
        $con = $this->getContest($contest, false, ['categories','childrenCategories']);
        $entries = [];
        $categories = [];
        $time = date("Y-m-d H:i:s");
        if(!$con) return Response::make('Contest not found', 400);
        $collection = Collection::where('code',$code)->first();
        if(isset($collection->start_at) && $collection->start_at !== null){
            if($collection->start_at > $time)
                return Redirect::to('/home');
        }
        if(isset($collection->finish_at) && $collection->finish_at !== null){
            if($collection->finish_at < $time)
                return Redirect::to('/home');
        }
        if($collection->private == 1){
            $user = null;
            if(Auth::user()){
                $user = CollectionKey::where('collection_id', $collection->id)
                    ->where('email', Auth::user()->email)
                    ->first();
                if(!$user){
                    return Response::json(array('private' => true));
                }
            }
            else
                return Response::json(array('private' => true));
        }
        $collection->metadata_config = json_decode($collection->metadata_config);
        $collection->config = json_decode($collection->config);
        if(isset($collection->voting_session_id) && $collection->voting_session_id > 0){
            $votingSession = VotingSession::where('id', $collection->voting_session_id)->first();
            $collectionCategories = $votingSession->getCollectionCategories();
            $categories = $con->collectionChildrenCategories($collectionCategories);
            //$collection->config = json_decode($collection->config);
            $entries = $this->getCollectionEntries($contest, true, $votingSession, $collection->code);
        }

        $prizes = [];
        if($collection->show_prize == true && isset(json_decode($votingSession->config)->extra)){
            foreach(json_decode($votingSession->config)->extra as $prize){
                if(in_array($prize->id, $collection->config->voteType)){
                    array_push($prizes, ['id' => $prize->id, 'name' => $prize->name, 'color' => $prize->color]);
                }
            }
            array_multisort($prizes, SORT_DESC);
        }

        return Response::json(array('prizes' => $prizes, 'categories' => $categories, 'collection' => $collection, 'entries' => $entries/*, 'categories' => $con->childrenCategories*/), 200, [], JSON_NUMERIC_CHECK);
    }

    public function getCollectionEntries($contest, $firstTime = false, $vs = null, $code = null){
        $con = $this->getContest($contest);
        $categoryId = Input::get('categoryId');
        $votingSessionId = Input::get('votingSessionId');
        $pagination = Input::get('pagination');
        $voteCategories = [];

        if($code == null) $code = Input::get('code');
        $collection = Collection::where('code',$code)->first();

        $config = json_decode($collection->config);

        if(isset($vs)){
            $votingSession = $vs;
            $votingSessionId = $votingSession->id;
        }else{
            $votingSession = VotingSession::where('id', $votingSessionId)->first();
        }

        $query = Entry::where('contest_id', $con->id)
            ->where('status', Entry::APPROVE);

        if(isset($pagination)){
            if(isset($pagination['query']) && $pagination['query'] != ''){
                $query->where(function($q) use ($pagination){
                    $q->whereHas('EntryMetadataValuesWithFields2', function($sq) use ($pagination){
                        $valueTrimmed = trim($pagination['query']);
                        $sq->join('entry_metadata_fields', 'entry_metadata_values.entry_metadata_field_id','=','entry_metadata_fields.id');
                        $sq->where('entry_metadata_values.value', 'LIKE',  '%' .$valueTrimmed. '%');
                        $sq->where('entry_metadata_fields.order', 0);
                    });
                });
            }
        }

        $confDec = json_decode($votingSession->config);

        // ONLY SHORT-LIST RESULTS
        if(isset($confDec->shortListConfig) && count($confDec->shortListConfig) > 0){
            $votingSessionId = $confDec->shortListConfig;
        }else{
            $votingSessionId = [intval($votingSessionId)];
        }

        $votingShortLists = VotingShortlist::whereIn('voting_session_id', $votingSessionId);

        if(isset($categoryId) && $categoryId != null){
            $cat = Category::where('id', $categoryId)->select('final')->first();
            if($cat->final == 1) {
                $votingShortLists->join('entry_categories', 'entry_categories.id', '=', 'voting_shortlists.entry_category_id')
                    ->where('entry_categories.category_id', $categoryId);
            }
            if($cat->final != 1){
                $catIds = Category::where('parent_id', $categoryId)
                    ->lists('id');
                $catId = array_values($catIds);

                $votingShortLists->join('entry_categories', 'entry_categories.id', '=', 'voting_shortlists.entry_category_id')
                    ->whereIn('entry_categories.category_id', $catId);

            }else{
                $catId = [];
                array_push($catId,  $categoryId);
            }

            $query->whereHas('entryCategories', function ($q) use ($catId) {
                $q->whereIn('category_id', $catId);
            });
            $voteCategories = $catId;
        }

        $votingShortLists = $votingShortLists->select('entry_category_id')
            ->get()
            ->toArray();

        $voteCategoriesShortLists = EntryCategory::whereIn('id', $votingShortLists)
            ->select('category_id')
            ->groupBy('category_id')
            ->get()
            ->toArray();

        $voteCategories = $voteCategoriesShortLists;

        $query->whereHas('entryCategories', function ($q) use ($votingShortLists, $config, $pagination) {
            $q->whereIn('entry_categories.id', $votingShortLists);
            if(isset($config->voteType) && sizeof($config->voteType) > 0){
                if(sizeof($pagination['prizes']) > 0) $voteType = $voteType = $pagination['prizes'];
                else $voteType = $config->voteType;

                $q->join('votes', 'votes.entry_category_id', '=', 'entry_categories.id')
                    ->whereIn('votes.vote', $voteType)
                    ->orderBy('votes.vote');
            }
        });

        if($firstTime == true || $pagination['query'] == ""){
            $query->take(50);
        }

        $entries = $query->with(['MainMetadata','filesFields'])
            ->get()->each(function ($entry) use ($votingSession, $confDec, $voteCategories, $collection){
                //$votedCats = [];
                /**	@var Entry $entry */
                if($entry->mainMetadata != null && count($entry->mainMetadata) != 0){
                    $first = $entry->mainMetadata->first();
                    if($first) $entry->name = $first->value;
                    else $entry->name = Lang::get('contest.entryNoTitle');
                } else{
                    $entry->name = Lang::get('contest.entryNoTitle');
                }

                if($collection['show_prize'] == 1)
                    $entry->votes = $entry->getVotingSessionResults($votingSession, $voteCategories);

                $entry->main_metadata = null;

                /*foreach($entry->votes as $categs => $vote){
                    array_push($votedCats, $categs);
                }*/

                /*if(isset($confDec->shortListConfig) && count($confDec->shortListConfig) > 0){
                   $entry->categories_id = $entry->entryCategories()->whereIn('category_id', array_values($voteCategories))->whereIn('id', $votingShortLists)->lists('category_id');
                }*/

                $entry->categories_id = $entry->entryCategories()->whereIn('category_id', array_values($voteCategories))->lists('category_id');

                $catsNames = [];
                foreach($entry->categories_id as $key => $categoryId){
                    array_push($catsNames, $this->categoryFullPath($result, $categoryId,null));
                    $catsNames[$key]['id'] = $categoryId;
                    $entry->parent_id = $catsNames[$key]['parent_id'];
                }
                $entry->categoryName = $catsNames;
            });

        return $entries;
    }

    public function categoryFullPath(&$result, $id, $name){
        $category = Category::where('id', $id)
            ->select('id', 'name', 'parent_id', 'final')
            ->first();

        $category->final == 1 ? $newStr = $category->name : $newStr = $category->name.">>".$name;
        $category->final == 0 ? $newId = $id : $newId = null;

        $result = ['name' => $newStr, 'parent_id' => $newId];

        if(isset($category->parent_id)) {
            $this->categoryFullPath($result, $category->parent_id, $newStr);
        }

        return $result;
    }

    public function getCollectionEntryView($contest){
        $con = $this->getContest($contest);
        /*$user = Auth::user();
        $colaborator = $con->getUserInscription(Auth::user(), Inscription::COLABORATOR);
        $permits = $colaborator['permits'];
        $owner = $con->getUserInscription(Auth::user(), Inscription::OWNER);*/

        //check if collection is visible

        return View::make('contest.collection', ['contest' => $con]);
    }

    public function getCollectionKeyView($contest){
        $con = $this->getContest($contest);
        return View::make('contest.collection-key', ['contest' => $con]);
    }

    public function getCollectionEntry($contest, $code, $id){
        /** @var Contest $con */
        $con = $this->getContest($contest);

        /*$user = Auth::user();
        $superadmin = Auth::check() && Auth::user()->isSuperAdmin();
        $owner = $con->getUserInscription($user, Inscription::OWNER);
        if(!$superadmin && !$owner && !$con->getUserInscription($user, Inscription::INSCRIPTOR) && !$con->getUserInscription($user, Inscription::COLABORATOR)) {
            return Response::make(Lang::get('Inscription not found'), 404);
        }*/
        $entry = $con->collectionEntry($code, $id);

        $first = $entry->mainMetadata->first();
        if($first) $entry->name = $first->value;

        return json_encode($entry, JSON_NUMERIC_CHECK);
    }

    public function getCollectionMetadataFields($contest, $code){
        $con = $this->getContest($contest);
        $metadata = [];
        $collection = Collection::where('code',$code)->first();
        $collection->metadata_config = json_decode($collection->metadata_config);

        foreach($collection->metadata_config as $metadataField){
            array_push($metadata, explode(',', $metadataField)[0]);
        }

        $metadataFields = EntryMetadataField::with('EntryMetadataConfigTemplate')
            ->whereIn('id', $metadata)
            ->orderBy('order')
            ->get();
        return json_encode($metadataFields, JSON_NUMERIC_CHECK);
    }

    public function getCollectionKeys($contest, $collectionCode){
        $con = $this->getContest($contest, false);
        $collection = Collection::where('code','=',$collectionCode)->where('contest_id','=',$con->id)->firstOrFail();
        $keys = [];
        for($i = 0; $i < 10; $i++){
            $key = CollectionKey::createSimpleKey();
            CollectionKey::create(['collection_id'=>$collection->id, 'key'=>$key]);
            $keys[] = $key;
        }

        return Response::json(array('keys' => $keys), 200, [], JSON_NUMERIC_CHECK);
    }

    public function postCollectionInvites($contest, $collectionCode){
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest not found', 400);
        /** @var VotingSession $votingSession */
        $collection = Collection::where('code', '=', $collectionCode)->where('contest_id','=',$con->id)->firstOrFail();
        $emails = Input::get('emails');
        $emailsLines = explode("\n", str_replace(["\r",","],"\n", $emails));
        $goodEmails = [];
        $emailsNames = [];
        foreach ($emailsLines as $emailLine) {
            if($emailLine != ''){
                $emailLine = str_replace("\t"," ",$emailLine);
                $spacePos = strpos($emailLine, " ");
                if($spacePos !== false){
                    $email = substr($emailLine, 0, $spacePos);
                    $name = substr($emailLine, $spacePos + 1);
                }else{
                    $email = $emailLine;
                    $name = "";
                }
                $validator = Validator::make(
                    array(
                        'email' => $email
                    ),
                    array(
                        'email' => 'required|email'
                    )
                );
                if (!$validator->fails())
                {
                    if(!in_array($email, $goodEmails)) array_push($goodEmails, $email);
                    $emailsNames[$email] = $name;
                }
            }
        }

        if(count($goodEmails) == 0) {
            return Response::json(['status' => 200, 'errors' => Lang::get("contest.votingNoGoodEmails")]);
        }

        foreach ($goodEmails as $email) {
            $user = CollectionKey::where('collection_id', $collection->id)
                ->where('email', $email)
                ->first();

            if(!$user){
                CollectionKey::create(['collection_id'=>$collection->id, 'email'=>$email]);
            }
        }
        return Response::json(['status' => 200, 'msg'=> Lang::get("contest.votingJudgesCreated"), 'collection' => $collection]);
    }

    public function getCollectionUser($contest){
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest not found', 400);
        $code = Input::get('code');
        $key = Input::get('key');

        $collection = Collection::where('code', $code)
            ->where('contest_id', $con->id)
            ->first();

        $collectionKey = CollectionKey::where('collection_id', $collection['id'])
                        ->where('key', $key)
                        ->first();

        if(!isset($collectionKey)){
            return Response::json(['success'=>false, 'msg' => 'Codigo invalido']);
        }

        $email = $collectionKey['key']."@oxoawards.com";
        $password = substr(md5(rand(0, 1000000)), 0, 7);
        $user = User::where('email', $email)->first();
        if(!$user){
            $user = User::create(['first_name' => 'collection',
                'last_name' => 'viewer',
                'email' => $email,
                'password' => $password
                ]);
        }
        if($collectionKey['email'] == "" || $collectionKey['email'] == null){
            $collectionKey['email'] = $email;
            $collectionKey->save();
        }
        Auth::login($user);
        return Response::json(['success' => true]);
    }

    public function deleteCollection($contest, $code){
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest not found',  400);
        Collection::where('code', $code)->delete();
    }

    public function postResetPassword($contest){
        $con = $this->getContest($contest);
        if(!$con) return Response::make('Contest not found', 400);
        $userId = Input::all();
        $user = User::where('id', $userId)->first();
        $newPass = strtolower(str_random(6));
        $user->password = Hash::make($newPass);
        $user->save();
        return Response::json(['user' => $user, 'msg' => 'Password Reset to: '.$newPass]);
    }

    public function getMetadataAnalytics($contest){
        $con = $this->getContest($contest);
        /*** Metadata Fields that can be standarized: MULTIPLES, SELECT. ***/
        $metadataFields = EntryMetadataField::where('contest_id', '=', $con->id)
                            ->whereIn('type',[MetadataField::MULTIPLE, MetadataField::SELECT])
                            ->get();

        foreach($metadataFields as $key=>$field){
            $values = EntryMetadataValue::where('entry_metadata_field_id', $field->id)
                    ->select('value')
                    ->get();
            if($field->type == MetadataField::SELECT){
                $preValues = [];
                foreach($values as $value){
                    $index = array_search($value->value, json_decode($field->config)->options);
                    //if($index === 0) return Response::json(['value' => $value, 'index' => $index]);
                    if($index !== false){
                        if(!isset($preValues[$index])) $preValues[$index] = 0;
                        $preValues[$index] = $preValues[$index] + 1;
                    }else{
                        $index = array_search($value->value, json_decode($field->trans)->options->pt);
                        if($index !== false) {
                            //return Response::json(['value' => $value, 'index' => $index]);
                            if (!isset($preValues[$index])) $preValues[$index] = 0;
                            $preValues[$index] = $preValues[$index] + 1;
                        }else{
                            $index = array_search($value->value, json_decode($field->trans)->options->us);
                            if($index !== false) {
                                //return Response::json(['value' => $value, 'index' => $index]);
                                if (!isset($preValues[$index])) $preValues[$index] = 0;
                                $preValues[$index] = $preValues[$index] + 1;
                            }
                        }
                    }
                }
                $values = [];
                foreach($preValues as $index => $value){
                    array_push($values, ['value' => json_decode($field->config)->options[$index], 'total' => $value]);
                }
            }

            if($field->type == MetadataField::MULTIPLE) {
                $auxValues = [];
                foreach(json_decode($field->config)->options as $option){
                    array_push($auxValues, ['value' => $option, 'total' => 0]);
                }
                //return $values;
                foreach($values as $value){
                    foreach(json_decode($value) as $val){
                        //return Response::json([json_decode($val)[0]]);
                        foreach(json_decode($val) as $v){
                            $auxValues[$v]['total'] += 1;
                        }
                    }

                }
                $values = $auxValues;
            }
            $metadataFields[$key]->values = $values;
        }
        /*** Finish of metadata fields ***/
        $inscriptionType = InscriptionType::where('contest_id', $con->id)
            ->select('id', 'name')
            ->get()->toArray();
        foreach($inscriptionType as $key => $type){
            $categories = CategoryConfigType::where('inscription_type_id', $type['id'])
                ->select('categories.name', 'categories.id')
                ->join('categories', 'categories.id', '=', 'category_config_type.category_id')
                ->get()->toArray();
                foreach($categories as $key2 => $cat){
                    $entries = EntryCategory::where('category_id', $cat['id'])
                            ->select('entries.id', 'entries.status', 'entries.user_id', 'users.email')
                            ->join('entries', 'entries.id', '=', 'entry_categories.entry_id')
                            ->join('users', 'users.id', '=', 'entries.user_id')
                            ->whereNull('entries.deleted_at')
                            ->get();
                    $categories[$key2]['entries'] = [];

                    foreach($entries as $key3 => $entry){
                        $country = Inscription::where('user_id', $entry['user_id'])
                                    ->where('contest_id', $con->id)
                                    ->select('inscription_metadata_values.value')
                                    ->join('inscription_metadata_values', 'inscription_metadata_values.inscription_id', '=', 'inscriptions.id')
                                    ->where('inscription_metadata_values.inscription_metadata_field_id', 1246)
                                    ->first();
                        if($country != null){
                            switch(json_decode($country->value)[0]){
                                case '0':
                                    $country = "Argentina";
                                    break;
                                case '1':
                                    $country = "Brasil";
                                    break;
                                case '2':
                                    $country = "Mexico";
                                    break;
                            }
                        }
                        $entries[$key3]['country'] = $country;

                        array_push($categories[$key2]['entries'], $entry);
                    }
                    //array_push($categories[$key2], ['entries' => $entries]);
                }
            array_push($inscriptionType[$key], $categories);
        }

        return Response::json(['metadataFields' => $metadataFields, 'inscriptionTypes' => $inscriptionType]);
    }
}
