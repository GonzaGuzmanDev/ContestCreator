<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * Entry
 *
 * @property integer $id
 * @property integer $contest_id
 * @property integer $user_id
 * @property integer $status
 * @property string $error
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Entry whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Entry whereContestId($value)
 * @method static \Illuminate\Database\Query\Builder|\Entry whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Entry whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\Entry whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Entry whereUpdatedAt($value)
 * @property-read Contest $contest
 * @property-read User $user
 * @property \Illuminate\Database\Eloquent\Collection|\Billing[] $billings
 * @property-read \Illuminate\Database\Eloquent\Collection|\EntryCategory[] $entryCategories
 * @property \Illuminate\Database\Eloquent\Collection|\EntryMetadataValue[] $mainMetadata
 * @property-read \Illuminate\Database\Eloquent\Collection|\EntryMetadataValue[] $EntryMetadataValues
 * @property-read \Illuminate\Database\Eloquent\Collection|\EntryMetadataValue[] $FilesEntryMetadataValues
 * @property-read \Illuminate\Database\Eloquent\Collection|\entryLog[] $entryLog
 */
class Entry extends Eloquent {

	use SoftDeletingTrait;

	const INCOMPLETE = 0;
	const COMPLETE = 1;
	const FINALIZE = 2;
	const APPROVE = 3;
	const ERROR = 4;
	const ENTRY_MESSAGE = 5;

	const ABSTAIN = 0;
	const VOTED = 1;
	const NO_VOTED = 2;

	protected $fillable = [];

	protected $hidden = ['mainMetadata', 'user'];

	/*public function categories() {
		return $this->hasMany('Category');
	}*/

	public function entryCategories() {
		return $this->hasMany('EntryCategory');
	}

	public function entryCategoriesIds()
	{
		return $this->each(function ($entry) {
			$entry->categories_id = $entry->entryCategories()->lists('category_id');
		});
	}

	public function setIdAttribute($value)
	{
		$this->attributes['id'] = intval($value);
	}

	public function contest() {
		return $this->belongsTo('Contest');
	}

	public function user() {
		return $this->belongsTo('User');
	}

	public function entryLog() {
		return $this->hasMany('EntryLog')->where('status', Entry::ENTRY_MESSAGE);
	}

	public function categories() {
		return $this->belongsToMany('Category', 'entry_categories');
	}

	public function EntryMetadataValues() {
		return $this->hasMany('EntryMetadataValue')->with('Files');
	}

	public function PublicEntryMetadataValues() {
		return $this->hasMany('EntryMetadataValue')
            ->select(['entry_metadata_values.*'])
            ->join('entry_metadata_fields as CM', 'CM.id', '=', 'entry_metadata_values.entry_metadata_field_id')
            ->orderBy('CM.order', 'asc');
		/*->whereHas('EntryMetadataField', function($q) {
            $q->where('private', 0);
            $q->orderBy('order','desc');
        });*/
	}

	public function EntryMetadataValuesWithFields() {
		return $this->hasMany('EntryMetadataValue')
            ->select(['entry_metadata_values.*', 'CM.label', 'CM.config', 'CM.type', 'CM.order', 'CM.description'])
            ->join('entry_metadata_fields as CM', 'CM.id', '=', 'entry_metadata_values.entry_metadata_field_id')
            ->orderBy('CM.order','ASC')
            ->with('Files');
	}

    public function EntryMetadataValuesWithFieldsPDF() {
        return $this->hasMany('EntryMetadataValue')
            ->select(['entry_metadata_values.*', 'CM.label', 'CM.config', 'CM.type', 'CM.order', 'CM.description'])
            ->join('entry_metadata_fields as CM', 'CM.id', '=', 'entry_metadata_values.entry_metadata_field_id')
            ->where('CM.private', 0)
            ->orderBy('CM.order','ASC')
            ->with('Files');
    }

    public function EntryMetadataValuesWithFields2() {
        return $this->hasMany('EntryMetadataValue')
            ->select(['entry_metadata_values.*', 'CM.label', 'CM.config', 'CM.type'])
            ->join('entry_metadata_fields as CM', 'CM.id', '=', 'entry_metadata_values.entry_metadata_field_id');
    }


    public function getExportables() {
        return $this->hasMany('EntryMetadataValue')
            ->select(['entry_metadata_values.value', 'CM.label', 'CM.id'])
            ->join('entry_metadata_fields as CM', 'CM.id', '=', 'entry_metadata_values.entry_metadata_field_id');
    }

	public function mainMetadata(){
		return $this->hasMany('EntryMetadataValue')
			->select(['entry_metadata_values.value'])
			->join('entry_metadata_fields as CM', 'CM.id', '=', 'entry_metadata_values.entry_metadata_field_id')
			->whereIn('CM.type', MetadataField::getEditablesTypes())
			->orderBy('CM.order', 'asc');
	}

	public function filesFields(){
		return $this->hasMany('EntryMetadataValue')//->with('Files')
            ->whereHas('EntryMetadataField', function ($query) {
                $query->where('type', '=', MetadataField::FILE);
            })->with('Files')
            //->with('SimpleFiles')
            ;
	}

    public function importantFields(){
        return $this->hasMany('EntryMetadataValue')->select(['value', 'entry_id', 'CM.label','entry_metadata_field_id'])
        ->join('entry_metadata_fields as CM', 'CM.id', '=', 'entry_metadata_values.entry_metadata_field_id')
        ->whereHas('EntryMetadataField', function ($query) {
            $query->where('config', 'like', '%"important":1%');
        })->orderBy('CM.id');
    }

    public function filesFieldsEntries(){
		return $this->hasMany('EntryMetadataValue')//->with('Files')
            ->whereHas('EntryMetadataField', function ($query) {
                $query->where('private', 0);
                $query->where('type', '=', MetadataField::FILE);
            })->with('Files')
            //->with('SimpleFiles')
            ;
	}

    public function billingEntryCategories(){
        return $this->hasMany('BillingEntryCategory');
    }

	public function billings() {
		//return $this->hasMany('Billing');
		return $this->belongsToMany('Billing', 'billing_entries_categories')->short();
	}

	public function scopeBasic($query){
		$query->with(['MainMetadata','EntryCategories']);
		//TODO: Validar los permisos de billing de la inscripci�n
		$query->with(['Billings' => function ($q) {
			$q->with('billingEntryCategories')->short();
		}]);
	}
	public function scopeNameLoaded($query){
		$query->with('MainMetadata');
	}

	public function getName(){
	    $this->main_metadata;
        $arr = $this->toArray();
        return $arr['name'];
    }

	public function getJudgeGroups($groups, $entry_categories){
		$entryGroups = [];
		foreach($groups as $group){
			foreach($entry_categories as $entryCat){
				$exist = VotingGroupEntryCategory::where('voting_group_id', $group->id)
					->where('entry_category_id', $entryCat->id)->count();
				if($exist){
					array_push($entryGroups, $group);
				}
			}
		}
		return $entryGroups;
	}

    /**
     * @param VotingUser $judge
     * @param VotingSession $voteSession
     * @param int[] $voteCategories
     * @return array
     */
	public function getJudgeVotes($judge, $voteSession, $voteCategories){
		$votesData = [];
		$votingSession = isset($judge) ? $judge->votingSession : $voteSession;
		$conf = $votingSession->getVoteConfig();
        foreach($this->entry_categories as $entryCategories){
		    if(!isset($conf['shortListConfig']) && count($voteCategories) && !in_array($entryCategories->category_id, $voteCategories)) continue;
            if($judge){
				$votes = Vote::where('voting_session_id', $judge->voting_session_id)
					->where('voting_user_id', $judge->id)
					->where('entry_category_id', $entryCategories->id)
					//->where('type', Vote::TYPE_SCORE)
					->first();
			}else{
				$votes = Vote::where('voting_session_id', $votingSession->id)
					->where('entry_category_id', $entryCategories->id)
					->first();
			}
			if($votes){ /** TODO switch $votingSession->vote_type **/
				if($votingSession->vote_type == VotingSession::METAL){
					$config = json_decode($votingSession->config);
					foreach($config->extra as $item){
						if($item->id == $votes->vote){
							$data = (object)['name' => $item->name, 'score' => $item->score, 'color' => isset($item->color) ? $item->color : "#000000"];
							break;
						}
					}
					$votesData[$entryCategories->category_id]['vote'] = isset($data) ? $data : null;
                    continue;
					//return $votesData;
				}
				if($votingSession->vote_type == VotingSession::YESNO){
					$votesData[$entryCategories->category_id]['vote'] = isset($votes['vote_float']) ? json_decode($votes['vote_float']) : null;
                    continue;
					//return $votesData;
				}
				$data = (object)['vote' => null, 'abstain' => false, 'extra'=> null];
				if($votes['abstain'] == true){
					$data->abstain = true;
				}else{
					$criteriaVote = Vote::where('voting_session_id', $judge->voting_session_id)
						->where('voting_user_id', $judge->id)
						->where('entry_category_id', $entryCategories->id) //->whereIn('entry_category_id', $catIds) $$
						->where('type', Vote::TYPE_SCORE)
						->lists('vote_float', 'criteria');
					if($criteriaVote) {
						if(sizeof($criteriaVote) > 1) $data->vote = (object)$criteriaVote;
						if(sizeof($criteriaVote) == 1) $data->vote = $criteriaVote;
					}
				}
                if(isset($conf['extra']) && count($conf['extra'])) {
                    $extraVotes = Vote::where('voting_session_id', $judge->voting_session_id)
                        ->where('voting_user_id', $judge->id)
                        ->where('entry_category_id', $entryCategories->id)//->whereIn('entry_category_id', $catIds) $$
                        ->where('type', Vote::TYPE_EXTRA)
                        ->select(['criteria', 'vote', 'vote_float'])
                        ->get();
                    if ($extraVotes) {
                        $extras = [];
                        foreach ($extraVotes as $extraVote) {
                            switch ($conf['extra'][$extraVote['criteria']]['type']) {
                                case Vote::EXTRA_CHECKBOX:
                                    $extras[$extraVote['criteria']] = $extraVote['vote_float'] == 1;
                                    break;
                                case Vote::EXTRA_TEXTAREA:
                                    $extras[$extraVote['criteria']] = $extraVote['vote'];
                                    break;
                            }
                        }
                        $data->extra = (object)$extras;
                    }
                }
				$votesData[$entryCategories->category_id] = $data;
			}else{
				$votesData[$entryCategories->category_id] = (object)[];
			}
		}
		return $votesData;
	}

	/**
	 * @param VotingSession $votingSession
	 * @return array|\Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function getVotingSessionResults($votingSession, $voteCategories){
        $conf = $votingSession->getVoteConfig();
        $o = new stdClass();
        $o->catId = null;
        $results = [];
        foreach($this->entryCategories as $entryCategories) {
            if (!isset($conf['shortListConfig']) && count($voteCategories) && !in_array($entryCategories->category_id, $voteCategories)) continue;
            $o->catId = $entryCategories->id;
            switch ($votingSession->vote_type) {
                case VotingSession::AVERAGE:
                    /** @var $query Builder */
                    $judgesBuilder = VotingUser::where('voting_session_id', $votingSession->id)
                        ->where(function ($query) use ($o) {
                            $query->where(function ($query) {
                                $query->doesntHave('votingUserEntryCategory')->doesntHave('votingGroups');
                            })
                                ->orWhereHas('votingUserEntryCategory', function ($query) use ($o) {
                                    $query->where('entry_category_id', $o->catId);
                                })
                                ->orWhereHas('votingGroups', function ($query) use ($o) {
                                    $query->whereHas('votingGroupEntryCategory', function ($query) use ($o) {
                                        $query->where('entry_category_id', $o->catId);
                                    });
                                });
                        });
                    $judgesCountBuilder = clone $judgesBuilder;
                    $judges = $judgesCountBuilder->select(DB::raw('COUNT(*) as total'));
                    $judgesListBuilder = clone $judgesBuilder;
                    $judgesList = $judgesListBuilder->lists('id');

                    $votesBuilder = Vote::where('voting_session_id', $votingSession->id)
                        ->where('entry_category_id', $o->catId)
                        ->where('type', Vote::TYPE_SCORE)
                        ->whereIn('voting_user_id', $judgesList);
                    $votes = clone $votesBuilder;
                    $votes->where('abstain', 0)
                        ->select(DB::raw('(SUM(vote_float)/COUNT(*)) as result'))
                        ->addSelect('criteria');
                    $criterios = 1;
                    if (isset($conf['usecriteria']) && $conf['usecriteria']) {
                        //$votes->groupBy('entry_category_id','criteria');
                        $votes->groupBy('criteria');
                        $criterios = count($conf['criteria']);
                        //}else{
                        //$votes->groupBy('entry_category_id');
                    }
                    //$list = $votes->lists('SUM(vote_float)/COUNT(*)', 'criteria');
                    $total = clone $votesBuilder;
                    $total->where('abstain', 0)
                        ->select(DB::raw('COUNT(*) / ' . $criterios . ' as totalVotes'));
                    $abstains = clone $votesBuilder;
                    $abstains->where('abstain', 1)
                        ->select(DB::raw('COUNT(*) as totalVotes'));
                    //->groupBy('abstain');
                    $extras = Vote::where('voting_session_id', $votingSession->id)
                        ->where('entry_category_id', $o->catId)
                        ->where('type', Vote::TYPE_EXTRA)
                        ->select(DB::raw('COUNT(*) as total'))
                        ->addSelect('criteria')
                        ->groupBy('criteria');

                    $final = 0;
                    $critVotes = $votes->lists('result', 'criteria');
                    if ($criterios == 1) {
                        //TODO Chequear el índice cuando no se usan criterios
                        if (isset($critVotes[0]))
                            $final = $critVotes[0];
                    } else {
                        for ($i = 0; $i < $criterios; $i++) {
                            if (!isset($critVotes[$i])) break;
                            $weight = isset($conf['criteria'][$i]['weight']) ? $conf['criteria'][$i]['weight'] : null;
                            $weight = $weight != null && $weight != '' ? floatval($weight) / 100 : 1 / $criterios;
                            $final += $weight * $critVotes[$i];
                        }
                    }
                    // Devuelvo solo los que tienen jueces para votar
                    if($judges->first('total')['total'] > 0){
                    $results[$entryCategories->category_id] = [
                        'final' => $final,
                        'vote' => (object)$critVotes,
                        'total' => $total->first('totalVotes')['totalVotes'],
                        'abstains' => $abstains->first('totalVotes')['totalVotes'],
                        'extra' => (object)$extras->lists('total', 'criteria'),
                        'judges' => $judges->first('total')['total'],
                        'abstain' => false,
                    ];
                    }
                    break;
                case VotingSession::VERITRON:
                    /** @var $query Builder */
                    $judgesBuilder = VotingUser::where('voting_session_id', $votingSession->id)
                        ->where(function ($query) use ($o, $votingSession) {
                            $query->where(function ($query) {
                                $query->doesntHave('votingUserEntryCategory')->doesntHave('votingGroups');
                            })
                                ->orWhereHas('votingUserEntryCategory', function ($query) use ($o) {
                                    $query->where('entry_category_id', $o->catId);
                                })
                                ->orWhereHas('votingGroups', function ($query) use ($o, $votingSession) {
                                    $query->where('voting_session_id', $votingSession->id)
                                        ->whereHas('votingGroupEntryCategory', function ($query) use ($o) {
                                            $query->where('entry_category_id', $o->catId);
                                        });
                                });
                        });
                    $judgesCountBuilder = clone $judgesBuilder;
                    $judges = $judgesCountBuilder->select(DB::raw('COUNT(*) as total'));
                    $judgesListBuilder = clone $judgesBuilder;
                    $judgesList = $judgesListBuilder->lists('id');

                    $votesBuilder = Vote::where('voting_session_id', $votingSession->id)
                        ->where('entry_category_id', $o->catId)
                        ->where('type', Vote::TYPE_SCORE)
                        ->whereIn('voting_user_id', $judgesList);
                    $votes = clone $votesBuilder;
                    $votes->where('abstain', 0)->where('vote_float', '!=', 0)
                        ->select(DB::raw('(SUM(vote_float)/COUNT(*)) as result'))
                        ->addSelect('criteria');

                    $noCount = clone $votesBuilder;
                    $noCount->where('vote_float', 0)
                        ->select(DB::raw('COUNT(*) as totalVotes'));

                    $yesCount = clone $votesBuilder;
                    $yesCount->where('abstain', 0)->where('vote_float', '!=', 0)
                        ->select(DB::raw('COUNT(*) as totalVotes'));

                    $criterios = 1;
                    if (isset($conf['usecriteria']) && $conf['usecriteria']) {
                        //$votes->groupBy('entry_category_id','criteria');
                        $votes->groupBy('criteria');
                        $criterios = count($conf['criteria']);
                    }
                    $total = clone $votesBuilder;
                    $total->where('abstain', 0)
                        ->select(DB::raw('COUNT(*) / ' . $criterios . ' as totalVotes'));
                    $abstains = clone $votesBuilder;
                    $abstains->where('abstain', 1)
                        ->select(DB::raw('COUNT(*) as totalVotes'));
                    //->groupBy('abstain');
                    $extras = Vote::where('voting_session_id', $votingSession->id)
                        ->where('entry_category_id', $o->catId)
                        ->where('type', Vote::TYPE_EXTRA)
                        ->select(DB::raw('COUNT(*) as total'))
                        ->addSelect('criteria')
                        ->groupBy('criteria');

                    $final = 0;
                    $critVotes = $votes->lists('result', 'criteria');
                    if ($criterios == 1) {
                        //TODO Chequear el índice cuando no se usan criterios
                        if (isset($critVotes[0])) {
                            $critVotes[0] = round($critVotes[0], 2);
                            $final = $critVotes[0];
                        }
                    }
                    $no = $noCount->first('totalVotes')['totalVotes'];
                    $yes = $yesCount->first('totalVotes')['totalVotes'];
                    $totalYes = $yes+$no;
                    $results[$entryCategories->category_id] = [
                        'final' => $final,
                        'vote' => (object)$critVotes,
                        'total' => $total->first('totalVotes')['totalVotes'],
                        'abstains' => $abstains->first('totalVotes')['totalVotes'],
                        'extra' => (object)$extras->lists('total', 'criteria'),
                        'judges' => $judges->first('total')['total'],
                        'abstain' => false,
                        'noCount' => $no,
                        'yesPerc' => $totalYes == 0 ? 0 : round(($yes/$totalYes)*100, 2),
                    ];
                    break;
                case VotingSession::YESNO:
                    /** @var $query Builder */
                    if($votingSession->id == 602){
                        $judgesBuilder = VotingUser::where('voting_session_id', $votingSession->id);
                    }
                    else{
                        $judgesBuilder = VotingUser::where('voting_session_id', $votingSession->id)
                            ->where(function ($query) use ($o, $votingSession) {
                            $query->where(function ($query) {
                                $query->doesntHave('votingUserEntryCategory')->doesntHave('votingGroups');
                            })
                                ->orWhereHas('votingUserEntryCategory', function ($query) use ($o) {
                                    $query->where('entry_category_id', $o->catId);
                                })
                                ->orWhereHas('votingGroups', function ($query) use ($o, $votingSession) {
                                    $query->where('voting_session_id', $votingSession->id)
                                        ->whereHas('votingGroupEntryCategory', function ($query) use ($o) {
                                            $query->where('entry_category_id', $o->catId);
                                        });
                                });
                        });
                    }
                    $judgesCountBuilder = clone $judgesBuilder;
                    $judges = $judgesCountBuilder->select(DB::raw('COUNT(*) as total'));

                    $judgesListBuilder = clone $judgesBuilder;
                    $judgesList = $judgesListBuilder->lists('id');

                    $votesBuilder = Vote::where('voting_session_id', $votingSession->id)
                        ->where('entry_category_id', $o->catId)
                        ->where('type', Vote::TYPE_YESNO)
                        ->whereIn('voting_user_id', $judgesList)
                        ->whereNotNull('vote_float')
                        ->select('vote_float')
                        ->lists('vote_float');

                    $yesCount = 0;
                    $noCount = 0;
                    foreach ($votesBuilder as $votes) {
                        $vote = json_decode($votes);
                        if ($vote == 0) $noCount++;
                        if ($vote == 1) $yesCount++;
                    }


                    $total = Vote::where('voting_session_id', $votingSession->id)
                        ->whereNotNull('vote_float')
                        ->where('entry_category_id', $o->catId)
                        ->where('type', Vote::TYPE_YESNO)
                        ->whereIn('voting_user_id', $judgesList)
                        ->select(DB::raw('COUNT(*) as totalVotes'));

                    $results[$entryCategories->category_id] = [
                        'vote' => (object)$votesBuilder,
                        'totalYes' => $yesCount,
                        'totalNo' => $noCount,
                        'total' => $total->first('totalVotes')['totalVotes'],
                        'judges' => $judges->first('total')['total'],
                    ];
                    break;
                case VotingSession::METAL:
                    /** @var $query Builder */
                    $judgesBuilder = VotingUser::where('voting_session_id', $votingSession->id)
                        ->where(function ($query) use ($o, $votingSession) {
                            $query->where(function ($query) {
                                $query->doesntHave('votingUserEntryCategory')->doesntHave('votingGroups');
                            })
                                ->orWhereHas('votingUserEntryCategory', function ($query) use ($o) {
                                    $query->where('entry_category_id', $o->catId);
                                })
                                ->orWhereHas('votingGroups', function ($query) use ($o, $votingSession) {
                                    $query->where('voting_session_id', $votingSession->id)
                                        ->whereHas('votingGroupEntryCategory', function ($query) use ($o) {
                                            $query->where('entry_category_id', $o->catId);
                                        });
                                });
                        });
                    $judgesCountBuilder = clone $judgesBuilder;
                    $judges = $judgesCountBuilder->select(DB::raw('COUNT(*) as total'));
                    $judgesListBuilder = clone $judgesBuilder;
                    $judgesList = $judgesListBuilder->lists('id');

                    $votesBuilder = Vote::where('voting_session_id', $votingSession->id)
                        ->where('entry_category_id', $o->catId)
                        ->where('type', Vote::TYPE_METAL)
                        ->whereIn('voting_user_id', $judgesList);

                    $votes = Vote::where('voting_session_id', $votingSession->id)
                        ->where('entry_category_id', $o->catId)
                        ->where('type', Vote::TYPE_METAL)
                        ->whereIn('voting_user_id', $judgesList)
                        ->select('vote')
                        ->lists('vote');

                    $config = json_decode($votingSession->config);
                    $data = [];
                    $score = 0;
                    foreach ($config->extra as $item) {
                        foreach ($votes as $vote) {
                            if ($item->id == $vote) {
                                array_push($data, (object)['name' => $item->name, 'score' => $item->score, 'color' => isset($item->color) ? $item->color : "#000000", 'id' => $item->id]);
                                $score = $score + $item->score;
                            }
                        }
                    }
                    $votesData = isset($data) ? $data : null;


                    $total = clone $votesBuilder;
                    $total->where('abstain', 0)
                        ->select(DB::raw('COUNT(*) as totalVotes'));

                    $results[$entryCategories->category_id] = [
                        'vote' => (object)$votesData,
                        'score' => $score,
                        'total' => $total->first('totalVotes')['totalVotes'],
                        'judges' => $judges->first('total')['total'],
                        'abstain' => false,
                    ];
                    break;
            }
        }
		return $results;
	}

	/**
	 * @param Category $category
	 * @return bool
	 */
	public function mustPayCategory($category){
		$c = BillingEntryCategory::where('entry_id',$this->id)->where('category_id',$category->id)->has('Billing')->count();
		return $c == 0;
	}

	public function IsPaid(){
		return $this->getTotalPrice() == 0;
	}

	public function getTotalPrice(){
		$total = 0;
		for($i = 0; $i < count($this->categories); $i++){
			if(!$this->mustPayCategory($this->categories[$i])) continue;
			$total += $this->categories[$i]->getPrice();
		}
		return $total;
	}

	static public function getJSStatus(){
		return array(
			"INCOMPLETE" => self::INCOMPLETE,
			"COMPLETE" => self::COMPLETE,
			"FINALIZE" => self::FINALIZE,
			"APPROVE" => self::APPROVE,
			"ERROR" => self::ERROR,
			"ENTRY_MESSAGE" => self::ENTRY_MESSAGE,
		);
	}

	static public function getStatusName($status){
		switch($status){
			case self::INCOMPLETE: return Lang::get('contest.entry.incomplete');break;
			case self::COMPLETE: return Lang::get('contest.entry.complete');break;
			case self::FINALIZE: return Lang::get('contest.entry.finalized');break;
			case self::APPROVE: return Lang::get('contest.entry.approved');break;
			case self::ERROR: return Lang::get('contest.entry.error');break;
		}
	}

	/**
	 * @return bool|\Illuminate\Support\MessageBag
     */
	public function Validate(){
		$templatesIds = [];
		$defaultTemplate = false;
		foreach($this->categories as $cat) {
			if(!in_array($cat->template_id, $templatesIds)) array_push($templatesIds, $cat->template_id);
			if($cat->template_id == null) $defaultTemplate = true;
		}

		$rules = [];
		$metadataValues = [];
		$niceNames = [];
		$messages = [];

		/** @var EntryMetadataField[] $metadataFields */
		$metadataFields = EntryMetadataField::with('EntryMetadataConfigTemplate')
			->where('contest_id', '=', $this->contest->id)->get();

		foreach ($metadataFields as $metadata) {
			$required = $defaultTemplate && $metadata->required;
			$visible = $defaultTemplate;
			//print_r($metadata->EntryMetadataConfigTemplate);
			foreach($metadata->EntryMetadataConfigTemplate as $mdConfig){
				if(!in_array($mdConfig->template_id, $templatesIds)) continue;
				if($mdConfig->getAttribute('visible') && !$visible) $visible = true;
				if($mdConfig->required) $required = true;
			}
			if(!$visible) continue;
			$mdRules = [];
			if ($required) {
				$mdRules[] = "required";
			}
			switch ($metadata->type) {
				case MetadataField::DATE:
					//$mdRules[] = "date";
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

			$metadataValue = EntryMetadataValue::where('entry_id', $this->id)->where('entry_metadata_field_id', $metadata->id)->first();
			$rules[$metadata->id] = implode('|', $mdRules);
			if($metadata->type == MetadataField::FILE){
				//if(isset($metadataValue)) {echo (count($metadataValue->files)); }else{ echo 0; }
				$metadataValues[$metadata->id] = isset($metadataValue) ? count($metadataValue->files) : 0;
			}else{
				$metadataValues[$metadata->id] = isset($metadataValue) ? $metadataValue->value : null;
			}
			$niceNames[$metadata->id] = $metadata->label;
		}

		if(count($rules) == 0) return false;
		$validator = Validator::make($metadataValues, $rules, $messages);
		$validator->setAttributeNames($niceNames);
		if($validator->fails()) return $validator->messages();
		return false;
	}

	public function toArray(){
		$array = parent::toArray();
		if(!isset($array['name'])) {
			if (isset($array['main_metadata']) && count($array['main_metadata']) != 0) {
                try {
                    $array['name'] = $array['main_metadata'][0]->value;
                }catch (Exception $e){
                    $array['name'] = $array['main_metadata'][0]['value'];
                }
			} else {
				$array['name'] = Lang::get('contest.entryNoTitle');
			}
		}
		unset($array['main_metadata']);
		return $array;
	}

    public function getCollectionMetadataFields($fields){
        $metadata = $this->hasMany('EntryMetadataValue')->select(['value', 'entry_id', 'CM.label','entry_metadata_field_id'])
            ->join('entry_metadata_fields as CM', 'CM.id', '=', 'entry_metadata_values.entry_metadata_field_id')->get();
            /*->whereHas('EntryMetadataField', function ($query, $fields) {
                $query->where('id', $fields);
            })->orderBy('CM.id')->get();*/

        return Response::json(["metadata" => $metadata]);
    }
}