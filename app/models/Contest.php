<?php
use Carbon\Carbon;
use Endroid\QrCode\QrCode;

/**
 * Contest
 *
 * @property integer $id
 * @property string $code
 * @property string $name
 * @property integer $user_id
 * @property integer $status
 * @property string $template
 * @property string $limits
 * @property string $sizes
 * @property string $billing
 * @property string $custom_style
 * @property integer $style
 * @property integer $type
 * @property bool $single_category
 * @property bool $block_finished_entry
 * @property bool $admin_reset_password
 * @property integer $max_entries
 * @property bool $public
 * @property string $facebook_pixel_id
 * @property string $google_analytics_id
 * @property string $wizard_config
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $start_at
 * @property \Carbon\Carbon $finish_at
 * @property boolean $inscription_public
 * @property boolean $inscription_register_picture
 * @property \Carbon\Carbon $inscription_start_at
 * @property \Carbon\Carbon $inscription_deadline1_at
 * @property \Carbon\Carbon $inscription_deadline2_at
 * @property boolean $voters_public
 * @property boolean $voters_register_picture
 * @property \Carbon\Carbon $voters_start_at
 * @property \Carbon\Carbon $voters_deadline1_at
 * @property \Carbon\Carbon $voters_deadline2_at
 * @property string $storage_sources_bucket
 * @method static \Illuminate\Database\Query\Builder|\Contest whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Contest whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\Contest whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Contest whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Contest whereTemplate($value)
 * @method static \Illuminate\Database\Query\Builder|\Contest whereLimits($value)
 * @method static \Illuminate\Database\Query\Builder|\Contest whereSizes($value)
 * @method static \Illuminate\Database\Query\Builder|\Contest whereBilling($value)
 * @method static \Illuminate\Database\Query\Builder|\Contest whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Contest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Contest whereStartAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Contest whereDeadline1At($value)
 * @method static \Illuminate\Database\Query\Builder|\Contest whereDeadline2At($value)
 * @method static \Illuminate\Database\Query\Builder|\Contest whereFinishAt($value)
 * @property-read \User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\Entry[] $entries
 * @property-read \Illuminate\Database\Eloquent\Collection|\Category[] $categories
 * @property-read \Illuminate\Database\Eloquent\Collection|\EntryMetadataField[] $EntryMetadataFields
 * @property-read \Illuminate\Database\Eloquent\Collection|\EntryMetadataTemplate[] $EntryMetadataTemplates
 * @property-read \Illuminate\Database\Eloquent\Collection|\InscriptionMetadataField[] $InscriptionMetadataFields
 * @property-read \Illuminate\Database\Eloquent\Collection|\InscriptionType[] $inscriptionTypes
 * @property-read \Illuminate\Database\Eloquent\Collection|\Inscription[] $inscriptions
 * @property-read \Illuminate\Database\Eloquent\Collection|\VotingSession[] $votingSessions
 * @property-read \Illuminate\Database\Eloquent\Collection|\ContestFile[] $contestFiles
 * @property-read \Illuminate\Database\Eloquent\Collection|\Category')->with('childrenCategories')->where('parent_id[] $childrenCategories 
 * @property-read \Illuminate\Database\Eloquent\Collection|\ContestAsset[] $contestAssets
 * @property-read \Illuminate\Database\Eloquent\Collection|\Discount[] $discounts
 */
class Contest extends Eloquent {

    /** Status of inscriptions in admin contest list */
	const STATUS_INSCRIPTIONS_UNKNOWN = -1;
	const STATUS_INSCRIPTIONS_NEXT = 0;
	const STATUS_INSCRIPTIONS_OPEN = 1;
	const STATUS_INSCRIPTIONS_CLOSED = 2;

	/** Contest status */
    const STATUS_WIZARD = 0;
    const STATUS_COMPLETE = 1;
    const STATUS_READY = 2;
    const STATUS_PUBLIC = 3;
    const STATUS_CLOSED = 4;
    const STATUS_BANNED = 5;

	const ADMIN = 'admin';
	const EDIT = 'edit';
	const BILLING = 'billing';
	const TECH = 'tech';
	const SIFTER = 'sifter';
	const DESIGN = 'design';
	const VIEWER = 'viewer';
	const VOTING = 'voting';

	const WIZARD_CREATE_CONTEST = 0;
    const WIZARD_REGISTER_FORM = 1;
    const WIZARD_ENTRY_FORM = 2;
    const WIZARD_CATEGORIES = 3;
    const WIZARD_PAYMENT_FORM = 4;
    const WIZARD_STYLE = 5;
    const WIZARD_DATES = 6;
	const WIZARD_FINISHED = 7;

	const TYPE_CONTEST = 0;
	const TYPE_TICKET = 1;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'contests';

	/**
	 * @var array
	 */
	protected $hidden = ['user_id','template','limits','sizes',
		'created_at','updated_at',
		'InscriptionMetadataValues'];

	/**
	 *
	 * @var array
	 */
	protected $fillable = ['code', 'name', 'user_id', 'template', 'limits', 'sizes', 'billing', 'single_category', 'max_entries', 'start_at', 'finish_at', 'public', 'inscription_public', 'inscription_register_picture', 'inscription_start_at', 'inscription_deadline1_at', 'inscription_deadline2_at', 'voters_public', 'voters_register_picture', 'voters_start_at', 'voters_deadline1_at', 'voters_deadline2_at', 'billing','storage_sources_bucket','google_analytics_id','facebook_pixel_id', 'status', 'wizard_config', 'type', 'block_finished_entry', 'admin_reset_password'];


	public function user() {
		return $this->belongsTo('User');
	}

	public function entries() {
		return $this->hasMany('Entry')->with('mainMetadata')->orderBy('id');
	}

	public function categories() {
		return $this->hasMany('Category')->with('CategoryConfigType')->orderBy('order');
	}
	public function categoriesByName() {
		return $this->hasMany('Category')->with('CategoryConfigType')->orderBy('name');
	}

	public function childrenCategories() {
		return $this->hasMany('Category')->with('CategoryConfigType')->with('childrenCategories')->where('parent_id', null)->orderBy('order');
	}
	public function discounts() {
		return $this->hasMany('Discount')->orderBy('min_entries', 'asc');
	}

	public function childrenCategoriesWithInscriptionType($value){
		$categories = $this->hasMany('Category')->with(['CategoryConfigType'])
			->whereHas('CategoryConfigType', function($q) use($value) {
				$q->where('inscription_type_id', '=', $value);
			})
			->orderBy('order')
			->get();

		return $categories;
	}

	public function childrenCategoriesWithVotingSession($value){
		$categories = $this->hasMany('Category')->with(['CategoryConfigType'])
			->whereHas('VotingCategories', function($q) use($value) {
				$q->where('voting_session_id', '=', $value->id);
			})
			->orderBy('order')
			->get();

		return $categories;
	}

	public function collectionChildrenCategories($value){
		$categories = $this->hasMany('Category')->with(['childrenCategories' => function($query) use ($value){
		    $query->whereIn('id', $value);
            }])
            ->whereIn('categories.id', $value)
            ->where('parent_id', null)
            ->orderBy('order')
			->get();

		return $categories;
	}

	public function EntryMetadataFields() {
		return $this->hasMany('EntryMetadataField')->with('EntryMetadataConfigTemplate')->orderBy('order');
	}

	public function EntryMetadataTemplates() {
		return $this->hasMany('EntryMetadataTemplate')->with('EntryMetadataConfigTemplates','Categories');
	}

	public function InscriptionMetadataFields() {
		return $this->hasMany('InscriptionMetadataField')->with('InscriptionMetadataConfigTypes')->orderBy('order');
	}

	public function InscriptionMetadataValues() {
		return $this->hasManyThrough('InscriptionMetadataValue', 'inscription')->orderBy('inscription_metadata_field_id');
	}

	public function inscriptionTypes() {
		return $this->hasMany('InscriptionType')->with('CategoryConfigType')->with('InscriptionMetadataConfigType');
	}

	public function inscriptions() {
		return $this->hasMany('Inscription');
	}

	public function votingSessions() {
		return $this->hasMany('VotingSession');
	}

	public function contestFiles() {
		return $this->hasMany('ContestFile');
	}

    public function contestAssets() {
        return $this->hasMany('ContestAsset');
    }
	public function scopeBasic($query){
		$query->select(['id', 'code', 'name', 'start_at', 'finish_at']);
	}
	public function scopeOpened($query){
		$query
			->whereDate('start_at', '<=', Carbon::today()->toDateString())
			->whereDate('finish_at', '>=', Carbon::today()->toDateString())
			->orWhereNotIn('status', [Contest::STATUS_CLOSED,Contest::STATUS_WIZARD])
            ->orderBy('start_at', 'desc')
		;
	}

	const THEMELIGHT = 0;
	const THEMEDARK = 1;

	public static function GetThemes()
    {
        return [
             self::THEMELIGHT => Lang::get("contest.styles.light"),
             self::THEMEDARK => Lang::get("contest.styles.dark")
        ];
    }

	public function getAllRolesData(){
		$roles = Inscription::getAllRoles();
		$data = [];
		foreach($roles as $role => $label){
			$data[$role] = array(
				'id'	=> $role,
				'label'	=> $label,
				'types'	=> InscriptionType::where('contest_id', $this->id)->where('role', $role)->get()
			);
		}
		return $data;
	}

    /**
     * Devuelve una URL con la imagen almacenada en base64
     * @param $type
     * @return ContestAsset
     */
    public function getAsset($type) {
        $contest = ContestAsset::where('contest_id', '=', $this->id)->where('type', '=', $type)->first();

        if (!$contest) {
            $contest = new ContestAsset;
        }else{
            /*** HARDCODE PARA IAB ***/
            $user = Auth::user();
            if($user && !Auth::user()->isSuperAdmin()){
                $inscription = $this->getUserInscription($user);
                if($inscription)
                    $contest->inscription = $inscription->inscription_type_id;
                else $contest->inscription = 0;
            }else{
                $contest->inscription = 0;
            }
            /*** FIN DEL HARDCODE ***/
        }
        return $contest;
    }

	/**
	 * @param User $user
	 * @param int $role
	 * @return Inscription
	 */

	public function getUserInscription($user, $role = null){
		if($role != null){ $inscriptionQuery = Inscription::where('role', '=', $role)
                            ->where('contest_id', '=', $this->id)
                            ->where('user_id', '=', $user->id);
        }
		else{ $inscriptionQuery = Inscription::where('contest_id', '=', $this->id)
            ->where('user_id', '=', $user->id);}
		$inscription = $inscriptionQuery->with('InscriptionType')->first();
		return $inscription;
	}

    public function getTickets($entries){
        foreach($entries as $entry){
            $entry->name = "Fecha de operación: ".$entry->created_at;
            $entryTickets = [];
            foreach($entry->entry_categories as $entryCategory){
                array_push($entryTickets, $entryCategory['id']);
            }

            $tickets = Ticket::select( DB::raw('count(tickets.entry_category_id) as tickets_count, tickets.code, tickets.entry_category_id, billing_entries_categories.price, billing_entries_categories.category_id') )
                ->join('billing_entries_categories', 'billing_entries_categories.id', '=', 'tickets.billing_entry_category_id')
                ->whereIn('tickets.entry_category_id', $entryTickets)
                ->groupBy('tickets.entry_category_id')
                ->get();

            $entry->tickets = $tickets;

            $ticketsQR = Ticket::select( 'tickets.code', 'billing_entries_categories.category_id')
                ->join('billing_entries_categories', 'billing_entries_categories.id', '=', 'tickets.billing_entry_category_id')
                ->whereIn('tickets.entry_category_id', $entryTickets)
                ->get();

            $qrCodes = [];
            foreach($ticketsQR as $QRcode){
                $qrCode = new QrCode();
                $qrCode->setText(url('/').'/'.$this->code.'/t/'.$QRcode->code)->setSize(100)->setPadding(10)->setErrorCorrection('low')
                    ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0));
                $imageUri = $qrCode->getDataUri();
                array_push($qrCodes, ['code' => $QRcode->code, 'QR' => $imageUri, 'category' => $QRcode->category_id]);
            }
            $entry->qr = $qrCodes;

            $entry->categories_id = $entry->entryCategories()->lists('category_id');
            $entry->mainMetadata = null;
            unset($entry->mainMetadata);
            $entry = null;
            unset($entry);
        }
        return $entries;
    }

	/**
	 * @param User $user
	 * @param null $categoryId
	 * @return Entry[]
	 */
	/*public function getUserEntries($user,$categoryId = NULL, $filteredCategories = NULL){
		$query = Entry::where('contest_id', $this->id)->where('user_id', $user->id)->orderBy('id', 'desc');
		if($categoryId != NULL){
			$query->whereHas('entryCategories', function($q) use ($categoryId)
			{
				$q->where('category_id', '=', $categoryId);
			});
		}

		$entries = $query->with(['MainMetadata','EntryCategories','EntryLog','FilesFields', 'importantFields'])->with(['Billings' => function ($q) {
			$q->with('billingEntryCategories')->short();
		}])->get()->each(function ($entry) {
			if($entry->mainMetadata != null && count($entry->mainMetadata) != 0){
                $first = $entry->mainMetadata->first();
                if($first) $entry->name = $first->value;
                else $entry->name = Lang::get('contest.entryNoTitle');
			} else{
				$entry->name = Lang::get('contest.entryNoTitle');
			}
			$entry->main_metadata = null;
			$entry->categories_id = $entry->entryCategories()->lists('category_id');

            $entry->incompleteFields = $entry->Validate();
		});

        if($this->type == Contest::TYPE_TICKET){
            $entries = $this->getTickets($entries);
        }

		return $entries;
	}*/


	public function hasCategories($contestId){
		$query = Category::where('contest_id','=', $contestId)->select('id')->get();
		if(count($query) > 0) return true;
		else return false;
	}

	/**
	 * @param null $categoryId
	 * @return Entry[]
	 */
	public function getAllEntries($params){

	    $filters         = isset($params['filters']) ? $params['filters'] :  null;
        $categoryId      = isset($params['categoryId']) ? $params['categoryId'] :  null;
        $user_id         = isset($params['user_id']) ? $params['user_id'] :  null;
        $entriesPerRow   = isset($params['entriesPerRow']) ? $params['entriesPerRow'] :  null;
        $lastEntryLoaded = isset($params['lastEntryLoaded']) ? $params['lastEntryLoaded'] :  null;
        $loggedUserByAdmin = isset($params['loggedUserByAdmin']) ? $params['loggedUserByAdmin'] :  null;
        $totalCatEntries = 0;
        $statusEntryCategory = null;
        $billingEntryCategory = null;
        $entriesIds = null;
        $metadataFields = null;

        $query = Entry::where('entries.contest_id', $this->id)
        ->whereHas('entryCategories', function(){});

        if($user_id){
            $query->where('entries.user_id', $user_id);
        }

        $query->where(
            function($q) use ($filters){
                if($filters){
                    if ($filters['query'] != '') {
                        $myArray = preg_split("/[\s,]+/", $filters['query'] );
                        foreach($myArray as $value){
                            if(is_numeric($value)){
                                $q->orWhere('entries.id', 'LIKE', '%' . $value . '%');
                            }else{
                                $q->whereHas('EntryMetadataValuesWithFields2', function($sq) use ($filters){
                                    $valueTrimmed = trim($filters['query']);
                                    $sq->where('entry_metadata_values.value', 'LIKE',  '%' .$valueTrimmed. '%');
                                });
                            }
                        }
                    }
                    if(!empty($filters['filterMetadata'])){
                        foreach($filters['filterMetadata'] as $filterMetadata) {
                            $valueTrimmed = trim($filterMetadata['value']);
                            $q->whereHas('EntryMetadataValuesWithFields2', function ($sq) use ($valueTrimmed, $filterMetadata) {
                                $sq->where('entry_metadata_values.value', 'LIKE', '%' .$valueTrimmed. '%');
                                $sq->where('entry_metadata_values.entry_metadata_field_id', $filterMetadata['id']);
                            });
                        }
                    }
                    if (count($filters['statusFilters']) > 0) {
                        $q->whereIn('entries.status', $filters['statusFilters']);
                    }
                }
            });
        if (count($filters['billingFilters']) > 0) {
            if(in_array(Billing::UNPAID, $filters['billingFilters'])){
                /* $$ REVIEW */
                $query->whereDoesntHave('billingEntryCategories')
                /*->orWhereHas('billingEntryCategories', function($q){
                    $q->whereNotNull('billing_entries_categories.deleted_at');
                })*/
                ->orWhereHas('billings', function ($q) use ($filters, $user_id) {
                    $q->WhereIn('billings.status', $filters['billingFilters']);
                    $q->where('billings.contest_id', $this->id);
                    if($user_id)
                        $q->where('billings.user_id', $user_id);
                });
            }
            else {
                $query->whereHas('billings', function ($q) use ($filters) {
                    $q->whereIn('status', $filters['billingFilters']);
                });
            }
        };

        /*$query3 = clone $query;
        $user = Auth::user();
        $totalMsgs = $query3->join('entry_log', 'entries.id', '=', 'entry_log.entry_id')
            ->where('read_by', 'not like', '%"'.$user->id.'"%')
            ->where('entry_log.status', 5)->count();*/

		if($categoryId!=null){
		    $query->whereHas('entryCategories', function($q) use ($categoryId)
			{
				$q->where('category_id', '=', $categoryId);
			});

            $totalCatEntries = $query->count();
		}

		if($filters['messageFilters']){
		    $query->whereHas('entryLog', function(){
            });
        }

		if($filters['checkFilters']){
		    $query->where('entries.check', true);
        }

		$votingSession = VotingSession::where('contest_id', $this->id)
								->where('vote_type', VotingSession::METAL)
								->first();

        $voteCategories = [];
		if($votingSession){
            if(count($votingSession->votingCategories) > 0) {
                foreach ($votingSession->votingCategories as $cat) {
                    array_push($voteCategories, $cat->category_id);
                }
            }
		}

        $totalEntries = $query->count();

		if(!$entriesPerRow) $entriesPerRow = $totalEntries;
		if(!$lastEntryLoaded) $lastEntryLoaded = 0;

        $query2 = clone $query;
        if($lastEntryLoaded<1){
            $entriesIds = $query2->select('entries.status', 'entries.id')
                ->with(['EntryCategories'])
                ->orderBy('entries.id','desc')
                ->get();

            //$hasTemplate = EntryMetadataTemplate::where('contest_id', $this->id)->first();
            if(EntryMetadataTemplate::where('contest_id', $this->id)->first()){
            $metadataFields = EntryMetadataField::where('entry_metadata_fields.contest_id', $this->id)
                ->join('entry_metadata_config_template','entry_metadata_config_template.entry_metadata_field_id', '=', 'entry_metadata_fields.id')
                ->join('entry_metadata_templates','entry_metadata_config_template.template_id', '=', 'entry_metadata_templates.id')
                ->whereNotIn('entry_metadata_fields.type',[EntryMetadataField::TITLE,EntryMetadataField::DESCRIPTION,EntryMetadataField::FILE,EntryMetadataField::TAB,EntryMetadataField::LINK])
                ->selectRaw('GROUP_CONCAT(entry_metadata_templates.name) as templates, entry_metadata_fields.id,entry_metadata_fields.label')
                ->groupBy('entry_metadata_fields.id')
                ->get()
                ->toArray();
            }else{
                $metadataFields = EntryMetadataField::where('entry_metadata_fields.contest_id', $this->id)
                    ->whereNotIn('entry_metadata_fields.type',[EntryMetadataField::TITLE,EntryMetadataField::DESCRIPTION,EntryMetadataField::FILE,EntryMetadataField::TAB,EntryMetadataField::LINK])
                    ->select('entry_metadata_fields.id','entry_metadata_fields.label')
                    ->get()
                    ->toArray();
            }
            foreach($metadataFields as $key => $field){
                $metadataFields[$key]['label'] = $field['label'].":";
            }
        }
        $entries = $query->with(['EntryCategories','User','EntryLog','filesFields','importantFields'])
                    ->with(['Billings' => function ($q) use ($votingSession) {
		            $q->with('billingEntryCategories')->short();
		            }])->skip($lastEntryLoaded)
                    ->take($entriesPerRow)
                    ->orderBy('entries.id','desc')
                    ->get();

        if($this->type == Contest::TYPE_CONTEST){
            if(count($filters['statusFilters']) > 0 || count($filters['billingFilters']) > 0 || !empty($filters['filterMetadata'])
                || $filters['query'] != '' || $filters['messageFilters'] || $loggedUserByAdmin){
                $finalTotal = 0;
                $payedEntries = 0;
                $updateStatusEntryCategory = false;
                $updateStatusEntryBilling = false;

                $query4 = clone $query;
                $query5 = clone $query;

                $statusEntryCategoryQuery = $query4->select('status', DB::raw('count(status) as total'))
                        ->join('entry_categories', 'entries.id', '=', 'entry_categories.entry_id')
                        ->groupBy('status')
                        ->get();


                $billingEntryCategoryQuery = $query5->select('billings.status', DB::raw('count(billings.status) as total'))
                        ->join('billing_entries_categories', 'billing_entries_categories.entry_id', '=', 'entries.id')
                        ->join('billings', 'billing_entries_categories.billing_id', '=', 'billings.id')
                        ->whereNull('billings.deleted_at')
                        ->whereNull('entries.deleted_at')
                        ->whereNull('billing_entries_categories.deleted_at')
                        ->groupBy('billings.status')
                        ->get();

                if(count($filters['statusFilters']) > 0 || count($filters['billingFilters']) > 0 || !empty($filters['filterMetadata'])
                || $filters['query'] != '' || $filters['messageFilters'] || $loggedUserByAdmin != null) {
                    $updateStatusEntryCategory = true;
                    $updateStatusEntryBilling = true;
                }

                foreach($statusEntryCategoryQuery as $totalEntCat) {
                    if($updateStatusEntryCategory)
                        $statusEntryCategory[$totalEntCat->status] = $totalEntCat->total;
                    $finalTotal += $totalEntCat->total;
                }

                foreach($billingEntryCategoryQuery as $totalbillQuery) {
                    if($updateStatusEntryBilling)
                        $billingEntryCategory[$totalbillQuery->status] = $totalbillQuery->total;
                    $payedEntries = $payedEntries + $totalbillQuery->total;
                }

                if($updateStatusEntryBilling) $billingEntryCategory[Billing::UNPAID] = $finalTotal - $payedEntries;
            }


            foreach($entries as $entry){
                /** @var Entry $entry */
                if(isset($entry->mainMetadata) || $entry->mainMetadata!=null && count($entry->mainMetadata) != 0){
                    $first = $entry->mainMetadata->first();
                    if($first) $entry->name = $first->value;
                    else $entry->name = Lang::get('contest.entryNoTitle');
                }else{
                    $entry->name = Lang::get('contest.entryNoTitle');
                }
                foreach($entry['files_fields'] as $field){
                foreach($field['files'] as $file){
                    if($file['status'] == ContestFile::ERROR
                    || $file['status'] == ContestFile::UPLOAD_INTERRUPTED
                    || $file['status'] == ContestFile::CANCELED){
                        $entry->errorInFiles = true;
                    }
                    }
                }
                /*** METALERO ***/
                if($votingSession) {
                    $entry->votes = $entry->getJudgeVotes(null, $votingSession, $voteCategories); /*** con null en vez de $voteCategories devuelve todo ****/
                    $entry->voteSession = $votingSession;
                }
                /**************/
                $entry->categories_id = $categoryId ? (array)$categoryId : $entry->entryCategories()->lists('category_id');

                if($entry->status == Entry::INCOMPLETE) $entry->incompleteFields = $entry->Validate();
            }
		}

        if($this->type == Contest::TYPE_TICKET){
            $entries = $this->getTickets($entries);
        }
        if(!isset($finalTotal)) $finalTotal = null;
        $data = array(
            'entries' => $entries,
            'totalEntries' => $totalEntries,
            'entriesIds' => $entriesIds,
            'totalEntryCategory' => $finalTotal,
            'totalCatEntries' => $totalCatEntries,
            'statusEntryCategory' => $statusEntryCategory,
            'billingEntryCategory' => $billingEntryCategory,
            'metadataFields' => $metadataFields,
            'user_id' => $user_id
        );

		return $data;
	}

	public function getEntriesPerUser(){
		$users = DB::table('users')->select('users.id', 'users.first_name', 'users.last_name', 'users.email', 'inscriptions.inscription_type_id')
			->join('inscriptions', 'inscriptions.user_id', '=', 'users.id')
			->where('inscriptions.contest_id', $this->id)
			->where('inscriptions.role', '!=', Inscription::JUDGE)
			->groupBy('users.id')
			->get();

		return $users;
	}

    /**
     * @param string $voteSession
     * @param Inscription $inscription
     * @param bool $showAllEntries
     * @return Entry[] Se usa para tomar los entries cuando se los muestra al juez,
     * Se usa para tomar los entries cuando se los muestra al juez,
     * para los entries por juez en sesion de votacion
     * y para los entries por grupo en sesion de votacion
     */
	public function getJudgeEntries($voteSession, $inscription, $showAllEntries = false){
		$query = Entry::where('contest_id', $this->id)->where('status', Entry::APPROVE);

        /** @var VotingSession $votingSession */
		$votingSession = VotingSession::where('code', $voteSession)->firstOrFail();

        /** @var VotingUser $votingUser */
		$votingUser = VotingUser::where('voting_session_id', $votingSession->id)->where('inscription_id', $inscription->id)->firstOrFail();

		/*** Categorías seleccionadas en la sesión de votación **/
		$voteCategories = VotingCategory::where('voting_session_id', '=', $votingSession->id)->lists('category_id');
		if(count($voteCategories) > 0) {
			$query->whereHas('entryCategories', function($q) use ($voteCategories)
			{
				$q->whereIn('category_id',$voteCategories);
			});
		}

		$totalEntries = 0;

		//return $votingSession;
		$votingShortLists = null;
		$confDec = json_decode($votingSession->config);
		if(isset($confDec->shortListConfig) && count($confDec->shortListConfig) > 0){
            if(isset($confDec->editShortlist) && !!$confDec->editShortlist && $showAllEntries) {
                foreach ($confDec->shortListConfig as $votingSessionId){
                    $parentVotingSession = VotingSession::find($votingSessionId);
                    if($parentVotingSession){
                        $votingSessionEntries = $parentVotingSession->GetAllEntriesResults();
                        $eIds = [];
                        foreach ($votingSessionEntries as $e){
                            $eIds[] = $e->id;
                        }
                        $query->whereIn('id', $eIds);
                    }
                }
            }else {
                $votingShortLists = VotingShortlist::whereIn('voting_session_id', $confDec->shortListConfig)->select('entry_category_id')->get()->toArray();
                //return $votingShortLists;
                $voteCategories2 = EntryCategory::whereIn('id', $votingShortLists)->select('category_id')->get()->toArray();
                //return Response::json(array('voteccat' => sizeof($voteCategories),'votecounter' =>$voteCatCounter));
                if (count($voteCategories) > 0)
                    array_merge($voteCategories, $voteCategories2);
                else $voteCategories = $voteCategories2;
                $query->whereHas('entryCategories', function ($q) use ($votingShortLists) {
                    $q->whereIn('id', $votingShortLists);
                });
            }
		}
		/*** Grupos en sesiones de  votacion ****/
		$votingUserGroup = VotingUserVotingGroup::where('voting_user_id', $votingUser->id)->lists('voting_group_id');
		$groups = VotingGroup::whereIn('id', $votingUserGroup)->get();
		$votingEntriesCategories = [];
		if($votingUserGroup){
			$votingEntriesCategories = VotingGroupEntryCategory::whereIn('voting_group_id', $votingUserGroup)->lists('entry_category_id');
		}
		if(count($votingUser->votingUserEntryCategory)){
			$votingEntriesCategories = array_merge($votingUser->votingUserEntryCategory->lists('entry_category_id'), $votingEntriesCategories);
		}
		if(count($votingEntriesCategories)) {
			$query->whereHas('entryCategories',
				function ($q) use ($votingEntriesCategories, $voteCategories) {
					$q->whereIn('id', $votingEntriesCategories);
					//if (count($voteCategories)) $q->whereIn('category_id', $voteCategories);
				});
		}
        $query->whereNull('deleted_at');
        $query->where('entries.status', '=', Entry::APPROVE);

        $totalSelectedEntries = $query->count();

		$entries = $query->with(['MainMetadata','filesFieldsEntries', 'importantFields'])
            ->orderBy('entries.id','desc')
            ->get()
            ->each(function ($entry) use($votingUser,$showAllEntries,$confDec,$groups,$voteCategories,$votingEntriesCategories,$votingSession,$votingShortLists) {
			/**	@var Entry $entry */
			if($entry->mainMetadata != null && count($entry->mainMetadata) != 0){
                $first = $entry->mainMetadata->first();
                if($first) $entry->name = $first->value;
                else $entry->name = Lang::get('contest.entryNoTitle');
			} else{
				$entry->name = Lang::get('contest.entryNoTitle');
			}

			$entry->categories = $entry->entry_categories;

			$entry->groups = $entry->getJudgeGroups($groups, $entry->entry_categories);
			$entry->votes = $entry->getJudgeVotes($votingUser, null, $voteCategories);
			$entry->main_metadata = null;

			if((!$showAllEntries || (isset($confDec->editShortlist) && !$confDec->editShortlist)) && isset($confDec->shortListConfig) && count($confDec->shortListConfig) > 0){
				$entry->categories_id = $entry->entryCategories()->whereIn('category_id', array_values($voteCategories))->whereIn('id', $votingShortLists)->lists('category_id');
			}
			else{
				$cIds = $entry->entryCategories();
				if(count($votingEntriesCategories)) {
					$cIds->whereIn('id', $votingEntriesCategories);
				}
				if(count($voteCategories)) {
					$cIds->whereIn('category_id', $voteCategories);
				}
				$entry->categories_id = $cIds->lists('category_id');
			}
		});

        $data = array(
            'entries' => $entries,
            'totalEntries' => $totalEntries,
            'totalSelectedEntries' => $totalSelectedEntries
        );

		return $data;
	}

	/**
	 * @param User $user
	 * @param integer $id
	 * @return entry
	 */
	public function getUserEntry($user, $id){
		$query = Entry::where('contest_id', $this->id)->where('user_id', $user->id)->where('id', $id);
		$query->with(['MainMetadata','EntryCategories', 'EntryMetadataValues']);
		//TODO: Validar los permisos de billing de la inscripci�n
		$query->with(['Billings' => function ($q) {
			$q->with('billingEntryCategories')->short();
		}]);
		$entry = $query->firstOrFail();
		if(isset($entry->mainMetadata) || $entry->mainMetadata!=null && count($entry->mainMetadata) != 0){
            $first = $entry->mainMetadata->first();
            if($first) $entry->name = $first->value;
            else $entry->name = Lang::get('contest.entryNoTitle');
		}
		$entry->main_metadata = null;
		$entry->categories_id = $entry->entryCategories()->lists('category_id');
		foreach($entry['entry_metadata_values'] as $key => $metadata_value){
			$type = EntryMetadataField::where('id', $metadata_value['entry_metadata_field_id'])->select('type')->first();
			if($type['type'] == EntryMetadataField::MULTIPLEWITHCOLUMNS || $type['type'] == EntryMetadataField::MULTIPLE)
				$entry['entry_metadata_values'][$key]['value'] = json_decode($metadata_value['value']);
		}
		return $entry;
	}

	/**
	 * @param integer $id
	 * @return Entry
	 */
	public function getEntry($id){
		$entry = Entry::where('contest_id', $this->id)->where('id', $id)->with(['EntryMetadataValues','EntryCategories','User','entryLog'])
			->with(['Billings' => function ($q) {
				$q->with('billingEntryCategories')->short();
			}])->firstOrFail();
		$entry->categories_id = $entry->entryCategories()->lists('category_id');
        if($this->type == Contest::TYPE_TICKET){
            $entry->selectedTickets = [];
        }
		foreach($entry['entry_metadata_values'] as $key => $metadata_value){
			$type = EntryMetadataField::where('id', $metadata_value['entry_metadata_field_id'])->select('type', 'label')->first();
			if($type['type'] == EntryMetadataField::MULTIPLEWITHCOLUMNS || $type['type'] == EntryMetadataField::MULTIPLE)
				$entry['entry_metadata_values'][$key]['value'] = json_decode($metadata_value['value']);
			$entry['entry_metadata_values'][$key]['fieldName'] = $type['label'];
			$entry['entry_metadata_values'][$key]['type'] = $type['type'];
		}
		return $entry;
	}

	/**
	 * @param $id
	 * @return Entry
	 */
    public function getJudgeEntry($id, $voteCategories, $votingSession, $inscription){
        $query = Entry::where('contest_id', $this->id)->where('id', $id)
            ->where('status','=',Entry::APPROVE)
            ->with(['EntryMetadataValues']);//'entryLog']) TODO: agregar VotingSessionLog
        $entry = $query->firstOrFail();

        /** @var VotingUser $votingUser */
        $votingUser = VotingUser::where('voting_session_id', $votingSession->id)->where('inscription_id', $inscription->id)->firstOrFail();

        $votingUserGroup = VotingUserVotingGroup::where('voting_user_id', $votingUser->id)->lists('voting_group_id');
        $groups = VotingGroup::whereIn('id', $votingUserGroup)->get();
        $votingEntriesCategories = [];
        if($votingUserGroup){
            $votingEntriesCategories = VotingGroupEntryCategory::whereIn('voting_group_id', $votingUserGroup)->lists('entry_category_id');
        }
        if(count($votingUser->votingUserEntryCategory)){
            $votingEntriesCategories = array_merge($votingUser->votingUserEntryCategory->lists('entry_category_id'), $votingEntriesCategories);
        }
        if(count($votingEntriesCategories)) {
            $query->whereHas('entryCategories',
                function ($q) use ($votingEntriesCategories, $voteCategories) {
                    $q->whereIn('id', $votingEntriesCategories);
                    //if (count($voteCategories)) $q->whereIn('category_id', $voteCategories);
                });
        }


        $entry->categories_id = $entry->entryCategories()->whereIn('category_id', array_values($voteCategories))->lists('category_id');
        foreach($entry['entry_metadata_values'] as $key => $metadata_value){
            $mdFiled = EntryMetadataField::where('id', $metadata_value['entry_metadata_field_id'])->select('type', 'private')->first();
            if($mdFiled['private'] == 1){
                $metadata_value['value'] = [];
                continue;
            }
            if($mdFiled['type'] == EntryMetadataField::MULTIPLEWITHCOLUMNS || $mdFiled['type'] == EntryMetadataField::MULTIPLE) {
                $entry['entry_metadata_values'][$key]['value'] = json_decode($metadata_value['value']);
            }
        }

        $cIds = $entry->entryCategories();
        if(count($votingEntriesCategories)) {
            $cIds->whereIn('id', $votingEntriesCategories);
        }
        if(count($voteCategories)) {
            $cIds->whereIn('category_id', $voteCategories);
        }
        $entry->categories_id = $cIds->lists('category_id');

        return $entry;
    }

	public function getUserFiles($user, $pagination, $tech = NULL, $downloadAll = false){
		$pagination = array_merge(['perPage'=>8, 'page'=>1,'sortBy'=>'name', 'sortInverted'=>false], $pagination);
		if($tech){
			$pagination = array_merge(['perPage'=>10, 'page'=>$pagination['page'],'sortBy'=>'name', 'sortInverted'=>false], $pagination);
			$q = ContestFile::where('contest_id', '=', $this->id);
		}
		else $q = ContestFile::where('user_id', '=', $user->id)->where('contest_id', '=', $this->id);

		if(isset($pagination['selectedTypes']) && count($pagination['selectedTypes'])) $q->whereIn('type', $pagination['selectedTypes']);
		if(isset($pagination['statusFilters']) && count($pagination['statusFilters'])) $q->whereIn('tech_status', $pagination['statusFilters']);
		$q->orderBy($pagination['sortBy'], !!$pagination['sortInverted']?'desc':'asc');

		$filesMetadataValueIndex = EntryMetadataField::where('contest_id', $this->id)
			->where('type', MetadataField::FILE)
			->groupBy('label')
			->select('label')
			->get();

		$q->with(['User','ContestFileVersions','EntryMetadataValues' => function ($query) use ($pagination){
			$query->join('entry_metadata_fields', 'entry_metadata_fields.id','=','entry_metadata_field_id')
			->join('entries', 'entries.id','=','entry_id')
			->join('entry_categories', 'entry_categories.entry_id','=','entries.id')
			->join('categories', 'categories.id','=','entry_categories.category_id')
			->where('entries.contest_id', $this->id)
			->where('entries.deleted_at', null)
			->select('entry_metadata_fields.label','entries.id as id', 'entries.status', 'categories.name as categ_name');
		}]);

		if($pagination['encodeErrorFiles'] == true){
			$q->whereHas('ContestFileVersions', function($item) use ($pagination){
				$item->where('status', 3);
			});
			//$q->WhereIn('status', [ContestFile::ERROR, ContestFile::UPLOAD_INTERRUPTED]);
		}

		if(isset($pagination['metadataFields']) && $pagination['metadataFields'] != '' && !empty($pagination['metadataFields'])){
			$q->whereHas('EntryMetadataValues', function($item) use ($pagination){
				$item->join('entry_metadata_fields', 'entry_metadata_fields.id','=','entry_metadata_field_id')
				->whereIn('entry_metadata_fields.label', $pagination['metadataFields']);
			});
		}

		if((isset($pagination['query']) && $pagination['query'] != '') || $pagination['inEntries'] == true){
			$pos = strpos($pagination['query'], ',');
			if((isset($pagination['query']) && $pagination['query'] != '') && ($pos || ctype_digit($pagination['query']))){
				$searchArray = explode(',', $pagination['query']);
			}
			else $searchArray = null;

			if(!isset($searchArray)) $q->where('name', 'like', '%'.$pagination['query'].'%');
			else{
				//if($pagination['inEntries'] == false) $q->orWhere('name', 'like', '%'.$pagination['query'].'%');
				if($pagination['inEntries'] == true) $q->where('name', 'like', '%'.$pagination['query'].'%');
			}

			$q->whereHas('EntryMetadataValues', function($item) use ($searchArray){
				if(isset($searchArray)) $item->whereIn('entry_id', $searchArray);
			});
		}

		$total = $q->count();
		/** @var ContestFile[] $files */
		if(!$downloadAll) $files = $q->skip(($pagination['page']-1) * $pagination['perPage'])->take($pagination['perPage'])->get();
		else $files = $q->with(['ContestFileVersions'])->get();

		if($pagination['page'] > ceil($total/$pagination['perPage'])){
			$pagination['page'] = 1;
		}
		foreach ($files as $file){
		    $file->checkVersionsStatus();
        }
		$deleted = [];
		return array(
			'files'=> $files,
			'total' => $total,
			'page' => $pagination['page'],
			'deleted' => $deleted,
			'search' => $pagination['query'],
			'filesMetadataValueIndex' => $filesMetadataValueIndex->toArray(),
			'pagination' => $pagination,
			'id' => $this->id
		);
	}

	public function getInscriptionStatus($role = Inscription::INSCRIPTOR){
		if($this->isRegistrationOpen($role)){
			return self::STATUS_INSCRIPTIONS_OPEN;
		}elseif($this->isRegistrationNext($role)){
			return self::STATUS_INSCRIPTIONS_NEXT;
		}elseif($this->isRegistrationOver($role)){
			return self::STATUS_INSCRIPTIONS_CLOSED;
		}
		return self::STATUS_INSCRIPTIONS_UNKNOWN;
	}

	public function getInscriptionOpenDate($role = Inscription::INSCRIPTOR){
        $timen = time();
        $time = date("Y-m-d H:i:s");
        if(($role == Inscription::INSCRIPTOR && $this->inscription_public) ||
			($role == Inscription::JUDGE && $this->voters_public)){
            /** @var InscriptionType $iType */
            $iType = InscriptionType::where('contest_id', $this->id)
                ->where('role', $role)
                ->where('start_at','>',$time)
				->orderBy('start_at', 'asc')
                ->first();
            if($iType != null){
                return $iType->start_at;
            }
            //Chequeamos los inscription types que tienen las fechas nulas
            //Si hay, nos fijamos las fechas del contest
            $count1 = InscriptionType::where('contest_id', $this->id)
                ->where('role', $role)
                ->where('public', 1)->count();
            $count2 = InscriptionType::where('contest_id', $this->id)
                ->where('role', $role)
                ->where('start_at',null)
                ->count();
            if($count1 == 0 || $count2 > 0){
                return $role == Inscription::INSCRIPTOR ? $this->inscription_start_at : $this->voters_start_at;
            }
        }
		return false;
	}

	public function isContestClosed(){
		/*$time = date("Y-m-d H:i:s");
		if($this->finish_at > $time)
			return 1;
		else return 0;*/
		if($this->status == Contest::STATUS_PUBLIC){
            return 1;
        }else return 0;
	}

	public function getWhichDeadline($role = Inscription::INSCRIPTOR){
		$timen = time();
		$time = date("Y-m-d H:i:s");
		if(($role == Inscription::INSCRIPTOR && $this->inscription_public) ||
			($role == Inscription::JUDGE && $this->voters_public)){
			/** @var InscriptionType $iType* */
            if(Auth::user()) $userInscription = $this->getUserInscription(Auth::user(), $role);
			$iType = InscriptionType::where('contest_id', $this->id)
				->where('role', $role)
				->where('start_at','<',$time)
				->where(function($query) use ($time)
				{
					$query->where('deadline1_at', '>', $time)
						->orWhere('deadline2_at', '>', $time);
				})
                ->where('id', isset($userInscription->inscription_type['id']) ? $userInscription->inscription_type['id'] : NULL)
                ->orderBy('deadline1_at', 'asc')
				->first();
			if($iType != null){
                if(strtotime($iType->deadline1_at) > $timen && !isset($iType->deadline2_at)) {
                    return; //"(".Lang::get("contest.deadLine1At").")";
                }
				if(strtotime($iType->deadline1_at) > $timen && isset($iType->deadline2_at)) {
					return "(".Lang::get("contest.deadLine1At").")";
				}
				if(strtotime($iType->deadline2_at) > $timen) {
					return "(".Lang::get("contest.deadline2At").")";
				}
			}
			//Chequeamos los inscription types que tienen las fechas nulas
			//Si hay, nos fijamos las fechas del contest
			$count1 = InscriptionType::where('contest_id', $this->id)
				->where('role', $role)
				->where('public', 1)->count();
			$count2 = InscriptionType::where('contest_id', $this->id)
				->where('role', $role)
				->where('start_at',null)
				->where('deadline1_at',null)->count();
			if($count1 == 0 || $count2 > 0){
				if($role == Inscription::INSCRIPTOR) {
					if (strtotime($this->inscription_deadline1_at) > $timen && isset($this->inscription_deadline2_at)) {
						return "(".Lang::get("contest.deadLine1At").")";
					}
					if (strtotime($this->inscription_deadline2_at) > $timen) {
						if($this->id == 83) return "(¡Última oportunidad!)";
						return "(".Lang::get("contest.deadline2At").")";
					}
				}elseif($role == Inscription::JUDGE) {
					if (strtotime($this->voters_deadline1_at) > $timen) {
						return "(".Lang::get("contest.deadLine1At").")";
					}
					if (strtotime($this->voters_deadline2_at) > $timen) {
						return "(".Lang::get("contest.deadline2At").")";
					}
				}
			}
		}
		return false;
	}
	public function getInscriptionNextDeadlineDate($role = Inscription::INSCRIPTOR){
		$timen = time();
		$time = date("Y-m-d H:i:s");
		$user = Auth::user();
        $superadmin = $owner = $colaborator = null;
        if($user){
            $superadmin = Auth::check() && Auth::user()->isSuperAdmin();
            $owner = $this->getUserInscription(Auth::user(), Inscription::OWNER);
            $colaborator = $this->getUserInscription(Auth::user(), Inscription::COLABORATOR);
        }
        if($superadmin || $owner || $colaborator) {
            if($role == Inscription::INSCRIPTOR) {
                if (strtotime($this->inscription_deadline1_at) > $timen) {
                    return $this->inscription_deadline1_at;
                }
                if (strtotime($this->inscription_deadline2_at) > $timen) {
                    return $this->inscription_deadline2_at;
                }
            }
        }
        elseif(($role == Inscription::INSCRIPTOR && $this->inscription_public) ||
			($role == Inscription::JUDGE && $this->voters_public)){
			/** @var InscriptionType $iType */
			if($user) $userInscription = $this->getUserInscription($user, $role);
			$query = InscriptionType::where('contest_id', $this->id)
				->where('role', $role)
				->where('start_at','<',$time)
				->where(function($query) use ($time)
				{
					$query->where('deadline1_at', '>', $time)
						->orWhere('deadline2_at', '>', $time);
				});
                if(isset($userInscription->inscription_type['id'])){
                    $query->where('id', $userInscription->inscription_type['id']);
                }
                $iType = $query->orderBy('deadline1_at', 'asc')->first();

			if($iType != null){
                if(strtotime($iType->deadline1_at) > $timen) {
                    return $iType->deadline1_at;
                }
                if(strtotime($iType->deadline2_at) > $timen) {
                    return $iType->deadline2_at;
                }
			}
			//Chequeamos los inscription types que tienen las fechas nulas
			//Si hay, nos fijamos las fechas del contest
            $count1 = InscriptionType::where('contest_id', $this->id)
                ->where('role', $role)
                ->where('public', 1)->count();
            $count2 = InscriptionType::where('contest_id', $this->id)
				->where('role', $role)
				->where('start_at',null)
				->where('deadline1_at',null)->count();
            if($count1 == 0 || $count2 > 0){
				if($role == Inscription::INSCRIPTOR) {
					if (strtotime($this->inscription_deadline1_at) > $timen) {
						return $this->inscription_deadline1_at;
					}
					if (strtotime($this->inscription_deadline2_at) > $timen) {
						return $this->inscription_deadline2_at;
					}
				}elseif($role == Inscription::JUDGE) {
					if (strtotime($this->voters_deadline1_at) > $timen) {
						return $this->voters_deadline1_at;
					}
					if (strtotime($this->voters_deadline2_at) > $timen) {
						return $this->voters_deadline2_at;
					}
				}
			}
		}
		return false;
	}

	public function isRegistrationOpen($role){
		if(($role == Inscription::INSCRIPTOR && !$this->inscription_public) ||
			($role == Inscription::JUDGE && !$this->voters_public)) return false;
		$timen = time();
		$time = date("Y-m-d H:i:s");
		$count = InscriptionType::where('contest_id', $this->id)
			->where('role', $role)
			->where('public',1)
			->where('start_at','<',$time)
			->where(function($query) use ($time)
			{
				$query->where('deadline1_at', '>', $time)
					->orWhere('deadline2_at', '>', $time);
			})->count();
		if($count > 0){
			return true;
		}
		//Chequeamos los inscription types que tienen las fechas nulas
		//Si hay, nos fijamos las fechas del contest
		$start_at = strtotime($role == Inscription::INSCRIPTOR ? $this->inscription_start_at : $this->voters_start_at);
		$deadline1 = strtotime($role == Inscription::INSCRIPTOR ? $this->inscription_deadline1_at : $this->voters_deadline1_at);
		$deadline2 = strtotime($role == Inscription::INSCRIPTOR ? $this->inscription_deadline2_at : $this->voters_deadline2_at);
		if($start_at < $timen && ($deadline1 > $timen || $deadline2 > $timen)){
			$count1 = InscriptionType::where('contest_id', $this->id)
				->where('role', $role)
				->where('public', 1)->count();
			$count2 = InscriptionType::where('contest_id', $this->id)
				->where('role', $role)
				->where('public', 1)
				->where('start_at', null)
				->where('deadline1_at', null)->count();
			if($count1 == 0 || $count2 > 0){
				return true;
			}
		}
		return false;
	}

	public function isRegistrationNext($role = Inscription::INSCRIPTOR){
		if(($role == Inscription::INSCRIPTOR && !$this->inscription_public) ||
			($role == Inscription::JUDGE && !$this->voters_public)) return false;
        $timen = time();
        $time = date("Y-m-d H:i:s");
        $count = InscriptionType::where('contest_id', $this->id)
            ->where('role', $role)
            ->where('public', 1)
            ->where('start_at', '>', $time)
            ->count();
        if($count > 0){
            return true;
        }
        //Chequeamos los inscription types que tienen las fechas nulas
        //Si hay, nos fijamos las fechas del contest

		$start_at = strtotime($role == Inscription::INSCRIPTOR ? $this->inscription_start_at : $this->voters_start_at);
		if($start_at > $timen){
			$count1 = InscriptionType::where('contest_id', $this->id)
				->where('role', $role)
				->where('public', 1)->count();
			$count2 = InscriptionType::where('contest_id', $this->id)
				->where('role', $role)
				->where('public', 1)
				->where('start_at', null)
				->where('deadline1_at', null)->count();
			if($count1 == 0 || $count2 > 0){
                return true;
            }
        }
        return false;
	}

	public function isRegistrationOver($role = Inscription::INSCRIPTOR){
		if(($role == Inscription::INSCRIPTOR && !$this->inscription_public) ||
			($role == Inscription::JUDGE && !$this->voters_public)) return true;
        $timen = time();
        $time = date("Y-m-d H:i:s");
        $count = InscriptionType::where('contest_id', $this->id)
            ->where('role', $role)
            ->where('public', 1)
            ->where(function($query) use ($time)
            {
                $query->where('deadline1_at', '>', $time)
                    ->orWhere('deadline2_at', '>', $time)
                    ->orWhere('start_at', '>', $time);
            })->count();
        if($count > 0){
            return false;
        }
        //Chequeamos los inscription types que tienen las fechas nulas
        //Si hay, nos fijamos las fechas del contest

		$start_at = strtotime($role == Inscription::INSCRIPTOR ? $this->inscription_start_at : $this->voters_start_at);
		$deadline1 = strtotime($role == Inscription::INSCRIPTOR ? $this->inscription_deadline1_at : $this->voters_deadline1_at);
		$deadline2 = strtotime($role == Inscription::INSCRIPTOR ? $this->inscription_deadline2_at : $this->voters_deadline2_at);
		if($start_at > $timen || $deadline1 > $timen || $deadline2 > $timen){
			$count1 = InscriptionType::where('contest_id', $this->id)
				->where('role', $role)
				->where('public', 1)->count();
			$count2 = InscriptionType::where('contest_id', $this->id)
				->where('role', $role)
				->where('public', 1)
				->where('start_at', null)
				->where('deadline1_at', null)->count();
			if($count1 == 0 || $count2 > 0){
                return false;
            }
        }
		return true;
	}

	public function isRegistrationOnlyFromTypes($role = Inscription::INSCRIPTOR){
		if(($role == Inscription::INSCRIPTOR && !$this->inscription_public) ||
			($role == Inscription::JUDGE && !$this->voters_public)) return false;
		$count1 = InscriptionType::where('contest_id', $this->id)
			->where('role', $role)
			->where('public', 1)->count();
		$count2 = InscriptionType::where('contest_id', $this->id)
			->where('role', $role)
			->where('public', 1)
			->where('start_at', null)
			->where('deadline1_at', null)->count();
		if($count1 > 0 && $count2 == 0){
			return true;
		}
		return false;
	}

	public function getBillingData(){
		$arr = $this->toArray();
		if(isset($arr['billing']) && gettype($arr['billing']) == "string") $arr['billing'] = json_decode($arr['billing'], true);
		return $arr['billing'];
	}

	public function toArray(){
		$arr = parent::toArray();
		$arr['inscriptorStatus'] = $this->getInscriptionStatus(Inscription::INSCRIPTOR);
		$arr['judgeStatus'] = $this->getInscriptionStatus(Inscription::JUDGE);
		if(Auth::check() && Auth::user()->isSuperAdmin()) {
			$arr['inscriptorRegistration'] = $this->isRegistrationOnlyFromTypes(Inscription::INSCRIPTOR);
			$arr['judgeRegistration'] = $this->isRegistrationOnlyFromTypes(Inscription::JUDGE);
		}
		if(isset($arr['billing']) && gettype($arr['billing']) == "string") $arr['billing'] = json_decode($arr['billing'], true);

		$arr['wizard_config'] = json_decode($this->wizard_config);
		$arr['type'] = $this->type;
        $arr['banners'][ContestAsset::BIG_BANNER_HTML] = $this->getAsset(ContestAsset::BIG_BANNER_HTML)->toArray();
        $arr['banners'][ContestAsset::SMALL_BANNER_HTML] = $this->getAsset(ContestAsset::SMALL_BANNER_HTML)->toArray();
		return $arr;
	}

    public function isAdmin(){
        return Auth::check() && ($this->isOwner() || Auth::user()->isSuperAdmin());
    }

    public function isOwner(){
        return $this->getUserInscription(Auth::user(), Inscription::OWNER) != null;
    }

	public function isColaborator($permitsList, $perm = null){
        if($permitsList === true) return true;
		$permits = json_decode($permitsList, true);
		if($permits) {
            foreach ($permits as $option => $value) {
                if ($perm == null) {
                    if (!!$value) {
                        if ($option == Contest::ADMIN) return ('');
                        if ($option == Contest::DESIGN) return ('style');
                        if ($option == Contest::BILLING) return ('billing');
                        if ($option == Contest::VOTING) return ('voting');
                    }
                } else {
                    if ($option == $perm) {
                        if (!!$value) {
                            return 1;
                        }
                        return 0;
                    }
                }
            };
        }
		return 0;
	}

	public function isInscriptor(){
        return $this->getUserInscription(Auth::user(), Inscription::INSCRIPTOR) != null;
    }

	public function isJudge(){
        return $this->getUserInscription(Auth::user(), Inscription::JUDGE) != null;
    }

    public function checkUserPermission($perm){
        if(!Auth::check()) return false;
        if($this->isAdmin()) return true;
        $data = $this->getUserInscription(Auth::user(), Inscription::COLABORATOR);
        if($data) {
            $permits = $data['permits'];
            return $this->isColaborator($permits, $perm);
        }
        return false;
    }

    public function getInscriptionsEvents(){
        if($this->inscription_start_at == null) return false;
        $ev = [];
        array_push($ev, [
            "title" => $this->name." Inscripción".($this->inscription_deadline2_at != null ? " - Deadline 1" : ""),
            "start" => $this->inscription_start_at,
            "end" => $this->inscription_deadline1_at,
        ]);
        if($this->inscription_deadline2_at != null){
            array_push($ev, [
                "title" => $this->name.' Inscripción - Deadline 2',
                "start" => $this->inscription_deadline1_at,
                "end" => $this->inscription_deadline2_at,
            ]);
        }
        return $ev;
    }
    public function getVotingEvents(){
        if($this->voters_start_at == null) return false;
        $ev = [];
        return $ev;
        array_push($ev, [
            "title" => $this->name." Votaciones".($this->voters_deadline2_at != null ? " - Deadline 1" : ""),
            "start" => $this->voters_start_at,
            "end" => $this->voters_deadline1_at,
        ]);
        if($this->voters_deadline2_at != null){
            array_push($ev, [
                "title" => $this->name." Votaciones - Deadline 2",
                "start" => $this->voters_deadline1_at,
                "end" => $this->voters_deadline2_at,
            ]);
        }
        return $ev;
    }
    public function getVotingSessionsEvents(){
        $ev = [];
        foreach ($this->votingSessions as $votingSession) {
            array_push($ev, [
                "title" => $this->name.": Votación ".$votingSession->name,
                "start" => $votingSession->start_at,
                "end" => $votingSession->finish_at,
            ]);
        }
        return $ev;
    }
    public function getEvents(){
        $ev = [];
        $inscriptions = [
            "events" => [],
            "color" => '#428bca',
            "textColor" => 'white',
        ];
        $voting = [
            "events" => [],
            "color" => '#fdb913',
            "textColor" => 'black',
        ];
        $votingSessions = [
            "events" => [],
            "color" => '#ff3e3e',
            "textColor" => 'white',
        ];
        $ciev = $this->getInscriptionsEvents();
        if($ciev) $inscriptions['events'] = array_merge($inscriptions['events'], $ciev);
        $cvev = $this->getVotingEvents();
        if($cvev) $voting['events'] = array_merge($voting['events'], $cvev);
        $cvsev = $this->getVotingSessionsEvents();
        if($cvsev) $votingSessions['events'] = array_merge($votingSessions['events'], $cvsev);
        array_push($ev, $inscriptions);
        array_push($ev, $voting);
        array_push($ev, $votingSessions);
        return $ev;
    }

    public function reachedMaxEntries($user)
    {
        if ($this->max_entries == 0) return 0;
        $superadmin = Auth::check() && Auth::user()->isSuperAdmin();
        $owner = $this->getUserInscription($user, Inscription::OWNER);
        $colaborator = $this->getUserInscription($user, Inscription::COLABORATOR);
        if($superadmin || $owner || $colaborator) {
            return 0;
        }
        $inscriptor = $this->getUserInscription($user, Inscription::INSCRIPTOR);
        if(!$inscriptor) {
            return Response::make(Lang::get('Inscription not found'), 404);
        }

        $params = [
            'user_id' => $user->id
        ];

        $data = $this->getAllEntries($params);
        return $data['totalEntries'] >= $this->max_entries ? 1 : 0;
    }

    public function getValueMultipleWithColumns($field, $row, $column){
        $entry_metadata_value = EntryMetadataValue::where('entry_id', $field->entry_id)
            ->where('entry_metadata_field_id', $field->entry_metadata_field_id)
            ->get();

        if($entry_metadata_value){
            foreach($entry_metadata_value as $emv) {
                $value = json_decode($emv['value']);
                if(is_object($value) || is_array($value)) {
                    if(strcmp($value->label, $row) === 0){
                        foreach ($value->value as $key => $val){
                            if(is_int($key)){
                                if(strcmp($val, $column) === 0){
                                    return "<i class='fa fa-check'>";
                                }
                            }
                            elseif(strcmp($key, $column) === 0){
                                return $val;
                            };
                        }
                    }
                }
            }
        };
    }

    /**
     * @param integer $id
     * @return Entry
     */
    public function collectionEntry($code, $id){
        $metadata = [];
        $collection = Collection::where('code',$code)->first();
        $collection->metadata_config = json_decode($collection->metadata_config);

        foreach($collection->metadata_config as $metadataField){
            array_push($metadata, explode(',', $metadataField)[0]);
        }

        $entry = Entry::where('contest_id', $this->id)->where('id', $id)
            ->with(['EntryMetadataValues' => function ($q) use ($metadata) {
                $q->whereIn('entry_metadata_field_id', $metadata);
            }])->firstOrFail();

        //$entry->categories_id = $entry->entryCategories()->lists('category_id');

       /* foreach($entry['entry_metadata_values'] as $key => $metadata_value){
            $type = EntryMetadataField::where('id', $metadata_value['entry_metadata_field_id'])->select('type', 'label')->first();
            if($type['type'] == EntryMetadataField::MULTIPLEWITHCOLUMNS || $type['type'] == EntryMetadataField::MULTIPLE)
                $entry['entry_metadata_values'][$key]['value'] = json_decode($metadata_value['value']);
            $entry['entry_metadata_values'][$key]['fieldName'] = $type['label'];
            $entry['entry_metadata_values'][$key]['type'] = $type['type'];
        }*/

        return $entry;
    }
}