<?php
use Illuminate\Database\Query\Builder;

/**
 * VotingUser
 *
 * @property integer $id
 * @property integer $inscription_id
 * @property integer $voting_session_id
 * @property bool $status
 * @property string $invitation_key
 * @property Inscription $inscription
 * @property VotingSession $votingSession
 * @property \Carbon\Carbon $last_seen_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\VotingGroup[] $votingGroups
 * @property-read \Illuminate\Database\Eloquent\Collection|\VotingUserEntryCategory[] $votingUserEntryCategory
 * @method static \Illuminate\Database\Query\Builder|\VotingUser whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\VotingUser whereInscriptionId($value)
 * @method static \Illuminate\Database\Query\Builder|\VotingUser whereVotingSessionId($value)
 */
class VotingUser extends Eloquent {

	const PENDING_NOTIFICATION = 0;
	const NOTIFIED = 1;
	const VISITED_PAGE = 2;
	const REJECTED = 3;
	const ACCEPTED = 4;
	const RESEND = 5;
	const IN_LOBBY = 6;

	protected $fillable = ['voting_session_id','inscription_id','status'];
	protected $hidden = ['voting_session_id','voting_group_id','created_at','updated_at'];//,'invitation_key'

	public function inscription()
	{
		return $this->belongsTo('Inscription');
	}

	public function votingSession()
	{
		return $this->belongsTo('VotingSession');
	}

	public function votingGroups()
	{
		return $this->belongsToMany('VotingGroup', 'voting_user_voting_groups','voting_user_id','voting_group_id');
	}

	public function votes() {
		return $this->hasMany('Vote');
	}

	public function votingUserEntryCategory() {
		return $this->hasMany('VotingUserEntryCategory', 'voting_user_id', 'id');
	}

	public function toArray()
	{
		$array = parent::toArray();
		if(isset($array['voting_groups'])) $array['voting_groups'] = $this->votingGroups->lists('id');
		return $array;
	}

	public function getEmail(){
		if($this->inscription->email != null && $this->inscription->email != ''){
			return $this->inscription->email;
		}else{
			return $this->inscription->user->email;
		}
	}

    /**
     * @param bool $filterSessionCategories
     * @param $voteType
     * @param int $expectedVotes
     * @param null $config
     * @param bool $showAllEntries
     */
	public function loadProgress($filterSessionCategories, $voteType, $expectedVotes = 1, $config = null, $showAllEntries = false){
		if($expectedVotes == 0) return;
		$t = $this->getTable();
		/** @var $query Builder */
		 //TODO Reemplazar con este query

		$PDO = DB::connection('mysql')->getPdo();

		$filterUserEntryCategories = count($this->votingUserEntryCategory);
		$filterUserGroupsEntryCategories = count($this->votingGroups);
		$queryVoteType = $lastJoin = null;
		switch($voteType){
			case VotingSession::METAL: $queryVoteType = Vote::TYPE_METAL;
			   	$select = "SELECT count( DISTINCT eId ) AS total,
							(count(DISTINCT votes.id) / ". $expectedVotes .") AS votes,
							(count(DISTINCT votes.id) / ". $expectedVotes .") / count(DISTINCT eId) AS progress ";
				$lastJoin = "LEFT JOIN votes ON ec.eId = votes.entry_category_id
								AND votes.voting_user_id = ".$this->id."
								AND votes.type = ".$queryVoteType;
				break;
			case VotingSession::YESNO: $queryVoteType = Vote::TYPE_YESNO;
				$select = "SELECT count( DISTINCT eId ) AS total,
							(count(DISTINCT votes.id) / ". $expectedVotes .") AS votes,
							(count(DISTINCT votes.id) / ". $expectedVotes .") / count(DISTINCT eId) AS progress ";
				$lastJoin = "LEFT JOIN votes ON ec.eId = votes.entry_category_id
								AND votes.voting_user_id = ".$this->id."
								AND votes.type = ".$queryVoteType;
				break;
			case VotingSession::AVERAGE:
			case VotingSession::VERITRON: $queryVoteType = Vote::TYPE_SCORE; $queryExtraType = Vote::TYPE_EXTRA;
					$select = "SELECT count(DISTINCT eId) AS total,
				  			FLOOR((count(DISTINCT votes.id) / ". $expectedVotes ." + (count(DISTINCT abstains.id)))) AS votes,
				  			count(DISTINCT abstains.id) AS abstains,
				  			count(DISTINCT extras.id) AS extras";

				  	$lastJoin = "LEFT JOIN votes ON ec.eId = votes.entry_category_id
								AND ".$this->id." = votes.voting_user_id
								AND votes.abstain = 0
								AND votes.type = ". $queryVoteType."
								LEFT JOIN votes as abstains ON ec.eId = abstains.entry_category_id
								AND ". $this->id ." = abstains.voting_user_id
								AND abstains.abstain = 1
								LEFT JOIN votes as extras ON ec.eId = extras.entry_category_id
								AND extras.abstain = 0
								AND extras.type = ".$queryExtraType;
				break;
		}


		$shortList = $joinsQuery = $entryCatJoin = $entryCatJoinQuery2 = $entryCatUnionQuery2 = $joinEntries = null;
		$catJoin = false;

        $confDec = json_decode($config);
		if(isset($confDec->shortListConfig) && count($confDec->shortListConfig) > 0){
			if(isset($confDec->editShortlist) && !!$confDec->editShortlist && $showAllEntries){
                foreach ($confDec->shortListConfig as $votingSessionId){
                    $parentVotingSession = VotingSession::find($votingSessionId);
                    if($parentVotingSession){
                        $votingSessionEntries = $parentVotingSession->GetAllEntriesResults();
                        $eIds = [];
                        foreach ($votingSessionEntries as $e){
                            $eIds[] = $e->id;
                        }
                        $shortList = " AND entries.id IN (" . implode(',', $eIds) . ")";
                    }
                }
            }else {
                $votingShortLists = VotingShortlist::whereIn('voting_session_id', json_decode($config)->shortListConfig)->get()->lists('entry_category_id');
                if(count($votingShortLists)) {
                    $shortList = " AND entry_categories.id IN (" . implode(',', $votingShortLists) . ")";
                }
            }
		}

		if($filterSessionCategories || $filterUserEntryCategories || $filterUserGroupsEntryCategories){
			if ($filterSessionCategories) {
				//Filtramos los entries por categor�as seleccionadas en el voting session
				$joinsQuery = " INNER JOIN voting_categories ON voting_sessions.id = voting_categories.voting_session_id ";
				$entryCatJoin = $entryCatJoinQuery2 = " INNER JOIN entry_categories ON voting_categories.category_id = entry_categories.category_id ";

			}
			if ($filterUserEntryCategories) {
				//Filtramos por los asignados directamente a este jurado
				$joinsQuery = " INNER JOIN voting_user_entry_categories ON ".$t.".id = voting_user_entry_categories.voting_user_id ".$joinsQuery;
				$entryCatJoin = $entryCatJoinQuery2 = " INNER JOIN entry_categories ON voting_user_entry_categories.entry_category_id = entry_categories.id ";
				$catJoin = true;
			}
			if ($filterUserGroupsEntryCategories){
				//Filtramos por los asignados a los grupos de este jurado
				$joinsQuery = " INNER JOIN voting_user_voting_groups ON ".$t.".id = voting_user_voting_groups.voting_user_id
				INNER JOIN voting_groups ON voting_groups.id = voting_user_voting_groups.voting_group_id
				INNER JOIN voting_group_entry_categories ON voting_groups.id = voting_group_entry_categories.voting_group_id ".$joinsQuery;
				if ($catJoin == false){
					$entryCatJoin = $entryCatJoinQuery2 = " INNER JOIN entry_categories ON voting_group_entry_categories.entry_category_id = entry_categories.id ";
				}
				else{
					$entryCatJoinQuery2 = null;
					$entryCatUnionQuery2 = " INNER JOIN entry_categories ON voting_group_entry_categories.entry_category_id = entry_categories.id ";
				}
			}
			$joinEntries = "INNER JOIN entries ON entry_categories.entry_id = entries.id";
		}
		else{
			$joinsQuery = " INNER JOIN entries on voting_sessions.contest_id = entries.contest_id INNER JOIN entry_categories ON entries.id = entry_categories.entry_id ";
			//$joinEntries = "  ";
		}

		$query = $PDO->prepare($select." FROM
								(SELECT * FROM
									(SELECT
										entry_categories.id as eId
									FROM
										voting_users
									INNER JOIN voting_sessions ON voting_users.voting_session_id = voting_sessions.id
									".$joinsQuery."
									".$entryCatJoin."
									".$joinEntries."
									WHERE
										voting_users.id = ".$this->id."
										AND entries.deleted_at IS null
										AND entries.status = ". Entry::APPROVE.
										$shortList."
									) a
									UNION
									(SELECT
										entry_categories.id as eId
									FROM
										voting_users
									INNER JOIN voting_sessions ON voting_users.voting_session_id = voting_sessions.id
									".$joinsQuery."
									".$entryCatJoinQuery2."
									".$entryCatUnionQuery2."
									".$joinEntries."
									WHERE
										voting_users.id = ".$this->id."
										AND entries.deleted_at IS null
										AND entries.status = ". Entry::APPROVE
										.$shortList."
								)) as ec
								".$lastJoin);

		//$this->progress = $query;
		$query->execute();
		$progressAux = $query->fetchAll((\PDO::FETCH_ASSOC));
		header('Content-Type: application/json');
		$this->progress = $progressAux[0];
		if(!$this->progress) $this->progress = ['total' => 0, 'votes' => 0, 'abstains' => 0];
	}
}