<?php

/**
 * VotingSession
 *
 * @property integer $id
 * @property integer $contest_id
 * @property string $name
 * @property string $code
 * @property string $config
 * @property int vote_type
 * @property int public
 * @property \Carbon\Carbon start_at
 * @property \Carbon\Carbon finish_at
 * @property \Carbon\Carbon finish_at2
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\VotingSession whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\VotingSession whereContestId($value)
 * @method static \Illuminate\Database\Query\Builder|\VotingSession whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\VotingSession whereConfig($value)
 * @method static \Illuminate\Database\Query\Builder|\VotingSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\VotingSession whereUpdatedAt($value)
 * @property \Illuminate\Database\Eloquent\Collection|\VotingUser[] $votingUsers
 * @property \Illuminate\Database\Eloquent\Collection|\VotingCategory[] $votingCategories
 * @property \Illuminate\Database\Eloquent\Collection|\VotingGroup[] $votingGroups
 * @property \Illuminate\Database\Eloquent\Collection|\VotingSessionKey[] $keys
 * @property \Illuminate\Database\Eloquent\Collection|\Category[] $categories
 * @property \Illuminate\Database\Eloquent\Collection|\Contest $contest
 * @property VotingUser $judge
 */
class VotingSession extends Eloquent {

	protected $fillable = ['name', 'contest_id', 'code', 'config', 'trans', 'vote_type', 'public', 'publicAnonymous', 'start_at', 'finish_at', 'finish_at2'];
	const VERITRON = 0;
	const AVERAGE = 1;
	const YESNO = 2;
	const STV = 3;
	const METAL = 4;

	protected $hidden = ['created_at','updated_at'];

	public function contest() {
		return $this->belongsTo('Contest');
	}

	public function votingUsers() {
		return $this->hasMany('VotingUser');
	}

	public function votingGroups() {
		return $this->hasMany('VotingGroup');
	}
	public function keys() {
		return $this->hasMany('VotingSessionKey');
	}

	public function votingCategories() {
		return $this->hasMany('VotingCategory');
	}
    public function categories(){
        return $this->belongsToMany('Category', 'voting_categories');
    }
    public function shortlist(){
        return $this->hasMany('VotingShortlist', 'voting_session_id', 'id');
    }

    public function parentShortlist(){
        $confDec = json_decode($this->config);
        if(isset($confDec->shortListConfig) && count($confDec->shortListConfig) > 0) {
            foreach ($confDec->shortListConfig as $votingSessionId) {
                /** @var VotingSession $parentVotingSession */
                $parentVotingSession = VotingSession::find($votingSessionId);
                if ($parentVotingSession) {
                    return $parentVotingSession->shortlist;
                }
            }
        }
        return false;
    }

	public function scopeJudges($query){
	    $query->with(['votingUsers'=>function($q){
			$q->with(['inscription'=>function($q){
				$q->select('id','inscription_type_id','role','email','invitename','user_id')->with(['user'=>function($q){
					$q->basic();
				}]);
			}, 'votingGroups'=>function($q){
				$q->select('voting_groups.id');
			}]);
		}])->with('votingGroups');
	}

	public function loadJudgesProgress(){
		$expectedVotes = $this->getExpectedVotesCount();
		$filterCategories = count($this->votingCategories);
        foreach($this->votingUsers as $votingUser){
            if($this->public == 1) $votingUser->progress = ['total' => 0, 'votes' => 0, 'abstains' => 0];
			else $votingUser->loadProgress($filterCategories, $this->vote_type, $expectedVotes, $this->config);
		}
	}

    /**
     * @param VotingUser $judge
     * @param bool $showAllEntries
     */
	public function loadJudgeProgress($judge, $showAllEntries = false){
		$expectedVotes = $this->getExpectedVotesCount();
		$filterCategories = count($this->votingCategories);
        if($this->public == 1) $judge->progress = ['total' => 0, 'votes' => 0, 'abstains' => 0];
		else $judge->loadProgress($filterCategories, $this->vote_type, $expectedVotes, $this->config, $showAllEntries);
	}

	/**
	 * @return mixed
     */
	public function getVoteConfig(){
		return json_decode($this->config, true);
	}

	public function getExpectedVotesCount(){
		switch($this->vote_type){
			case self::AVERAGE:
				$conf = $this->getVoteConfig();
				if(isset($conf['usecriteria']) && $conf['usecriteria']){
					return count($conf['criteria']);
				}
			break;
		}
		return 1;
	}

	static public function getAllVoteTypes(){
		return array(
			self::VERITRON => Lang::get('contest.veritron'),
			self::AVERAGE => Lang::get('contest.average'),
			self::YESNO => Lang::get('contest.yesno'),
			self::STV => Lang::get('contest.stv'),
			self::METAL => Lang::get('contest.metal'),
		);
	}

	static public function createCode(){
		do{
			$code = User::getRandomCode();
			$ret = VotingSession::where('code','=',$code)->get();
		}while(count($ret));
		return $code;
	}

	public function toArray()
	{
		$array = parent::toArray();
		if(isset($array['config'])) $array['config'] = json_decode($array['config'], true);
		if(isset($array['trans'])) $array['trans'] = json_decode($array['trans'], true);
		return $array;
	}

    /**
     * @return Entry[]
     */
    public function GetAllEntriesResults(){
        $voteCategories = [];
        $voteCatCounter = 0;
        if(count($this->votingCategories) > 0) {
            foreach ($this->votingCategories as $cat) {
                $voteCatCounter++;
                array_push($voteCategories, $cat->category_id);
            }
        }

        if(count($this->votingCategories) == 0) {
            $voteCategoriesQuery = Category::where('contest_id', $this->contest_id)
                ->where('final', '=', 1)
                ->select('id')
                ->get()
                ->toArray();

            foreach ($voteCategoriesQuery as $cat) {
                $voteCatCounter++;
                array_push($voteCategories, $cat['id']);
            }
        }

        $me = $this;
        $judgesEntriesCategories = VotingUserEntryCategory::whereHas('votingUser', function ($query) use ($me){
            $query->where('voting_session_id', '=', $me->id);
        })->groupBy('voting_user_id', 'entry_category_id')->lists('entry_category_id');

        $groupsEntriesCategories = VotingGroupEntryCategory::whereHas('votingGroup', function ($query) use ($me){
            $query->where('voting_session_id', '=', $me->id);
        })->lists('entry_category_id');

        $judgesFullEntries = VotingUser::where('voting_session_id', '=', $this->id)
            ->whereDoesntHave('votingUserEntryCategory')
            ->count();

        $query = Entry::where('contest_id', $this->contest_id)
            ->where('status', Entry::APPROVE);

        $votingShortLists = null;
        $confDec = json_decode($this->config);

        // ONLY SHORT-LIST RESULTS
        if(isset($confDec->shortListConfig) && count($confDec->shortListConfig) > 0){
            $votingShortLists = VotingShortlist::whereIn('voting_session_id', $confDec->shortListConfig)
                ->select('entry_category_id')
                ->get()
                ->toArray();

            $voteCategoriesShortLists = EntryCategory::whereIn('id', $votingShortLists)
                ->select('category_id')
                ->groupBy('category_id')
                ->get()
                ->toArray();

            if(sizeof($voteCategories) != $voteCatCounter)
                array_merge($voteCategories, $voteCategoriesShortLists);
            else $voteCategories = $voteCategoriesShortLists;

            $query->whereHas('entryCategories', function ($q) use ($votingShortLists) {
                $q->whereIn('id', $votingShortLists);
            });
        }

        //Si hay categorías seleccionadas y...
        //No hay ningún juez con entries asignados o hay al menos uno que no tenga asignado ninguno

        if(count($voteCategories) && (count($judgesEntriesCategories) == 0 || $judgesFullEntries > 0) && count($groupsEntriesCategories) === 0) {
            $query->whereHas('entryCategories', function ($q) use ($voteCategories) {
                $q->whereIn('category_id', $voteCategories);
            });
        }else {
            if (count($judgesEntriesCategories) > 0) {
                $query->whereHas('entryCategories', function ($q) use ($judgesEntriesCategories) {
                    $q->whereIn('id', $judgesEntriesCategories);
                });
            }
            if (count($groupsEntriesCategories) > 0) {
                if (count($judgesEntriesCategories) > 0) {
                    $query->orWhereHas('entryCategories', function ($q) use ($groupsEntriesCategories) {
                        $q->whereIn('id', $groupsEntriesCategories);
                    });
                }else {
                    $query->whereHas('entryCategories', function ($q) use ($groupsEntriesCategories) {
                        $q->whereIn('id', $groupsEntriesCategories);
                    });
                }
            }
        }
        /** @var Entry[] $entries */
        $entries = $query->with(['MainMetadata'])
            ->get()->each(function ($entry) use ($me, $confDec, $voteCategories, $votingShortLists, $groupsEntriesCategories){
            //$votedCats = [];
            /**	@var Entry $entry */
            if($entry->mainMetadata != null && count($entry->mainMetadata) != 0){
                $first = $entry->mainMetadata->first();
                if($first) $entry->name = $first->value;
                else $entry->name = Lang::get('contest.entryNoTitle');
            } else{
                $entry->name = Lang::get('contest.entryNoTitle');
            }

            $entry->votes = $entry->getVotingSessionResults($me, $voteCategories);
            $entry->main_metadata = null;

            /*foreach($entry->votes as $categs => $vote){
                array_push($votedCats, $categs);
            }*/

            if(isset($confDec->shortListConfig) && count($confDec->shortListConfig) > 0){
                $entry->categories_id = $entry->entryCategories()->whereIn('category_id', array_values($voteCategories))->whereIn('id', $votingShortLists)->lists('category_id');
            }
            elseif(count($groupsEntriesCategories) > 0){
                $entry->categories_id = $entry->entryCategories()->whereIn('id', array_values($groupsEntriesCategories))->lists('category_id');

                $categories = $entry->categories;
                foreach($categories as $key => $category){
                    $entryCateg = EntryCategory::where('entry_id', $entry->id)
                        ->where('category_id', $category->id)
                        ->first();
                    if(!in_array($entryCateg->id, $groupsEntriesCategories)){
                        $categories[$key] = null;
                        unset($category);
                        unset($categories[$key]);
                    }
                }
                $entry->categories = $categories;
            }
            else
                $entry->categories_id = $entry->entryCategories()->whereIn('category_id', array_values($voteCategories))->lists('category_id');
                /*$entry->categoryName = Category::whereIn('id', $entry->categories_id)
                    ->select('id', 'name')
                    ->get()
                    ->toArray();*/
        });

        return $entries;
    }

    public function getCollectionCategories(){
        $confDec = json_decode($this->config);
        // ONLY FROM PREVIOUS SHORT-LIST RESULTS
        if(isset($confDec->shortListConfig) && count($confDec->shortListConfig) > 0) {
            $votingSessionId = $confDec->shortListConfig;
        }else{
            $votingSessionId = [intval($this->id)];
        }

        $votingShortLists = VotingShortlist::whereIn('voting_session_id', $votingSessionId)
        ->select('entry_category_id')
        ->get()
        ->toArray();

        $voteCategoriesShortLists = EntryCategory::whereIn('id', $votingShortLists)
            ->select('category_id')
            ->groupBy('category_id')
            ->get()
            ->toArray();

        $collectionCategories = [];

        $collectionCategories = array_merge($collectionCategories, $voteCategoriesShortLists);

        do{
        $parentCategories = Category::whereIn('id', $voteCategoriesShortLists)
            ->select('parent_id')
            ->groupBy('parent_id')
            ->get()
            ->toArray();

        $collectionCategories = array_merge($collectionCategories, $parentCategories);

        $voteCategoriesShortLists = $parentCategories;
        }while(sizeof($parentCategories) > 0);

        return $collectionCategories;
    }
}