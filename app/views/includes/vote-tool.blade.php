<script type="text/ng-template" id="voteTool.html">
<div ng-switch="voteSession.vote_type">
    <span ng-switch-when="{{VotingSession::AVERAGE}}">
        <span ng-if="!voteSession.config.usecriteria && !readOnly">
            <span ng-if="voteSession.config.step == 1">
                <div class="btn-group">
                    <button ng-hide="myVote == 'abstain'" type="button" class="btn"
                            ng-class="{'btn-primary': myVote[cat].vote == null,
                                'btn-info': myVote[cat].vote == voteSession.config.min + $index ,
                                'btn-success': myVote[cat].vote > voteSession.config.min + $index ,
                                'btn-default': myVote[cat].abstain || myVote[cat].vote < voteSession.config.min + $index }"
                            ng-click="BeforeVoteUpdate();myVote[cat].abstain = false; myVote[cat].vote = myVote[cat].vote == voteSession.config.min + $index  ? null : voteSession.config.min + $index; myVote[cat].cat = cat;VoteUpdated(myVote);"
                            ng-repeat="_ in getTotalVotes() track by $index">
                        @{{ voteSession.config.min + $index }}
                    </button>
                    <div ng-include src="'abstainButton.html'" include-replace ng-if="voteSession.config.abs && !readOnly"></div>
                </div>
            </span>
            <span ng-if="voteSession.config.step != 1">
                <select ng-disabled="myVote == 'abstain'" ng-model="myVote[cat].vote" ng-change="BeforeVoteUpdate();myVote[cat].abstain = false;VoteUpdated(myVote);" ng-if="!myVote[cat].abstain" class="form-control form-inline" ng-class="{'text-info':!myVote[cat].abstain && myVote[cat].vote != null}">
                    <option ng-repeat="_ in getTotalVotes() track by $index"
                            ng-selected="myVote[cat].vote[0] == voteSession.config.min + $index * voteSession.config.step"
                            ng-if="voteSession.config.min + $index * voteSession.config.step <= voteSession.config.max"
                            value="@{{ voteSession.config.min + $index * voteSession.config.step | number : (voteSession.config.step == 1 ? 0 : 1) }}">
                        @{{ voteSession.config.min + $index * voteSession.config.step | number : (voteSession.config.step == 1 ? 0 : 1) }}
                    </option>
                </select>
                <div ng-include src="'abstainButton.html'" include-replace ng-if="voteSession.config.abs && myVote[cat].vote == null && !readOnly"></div>
            </span>
        </span>
        <span ng-if="!voteSession.config.usecriteria && readOnly">
            <span class="label label-default average-result" ng-if="!hideResult">@{{ myVote[cat].vote || '@lang('contest.VoteNoVote')' }}</span>
        </span>
        <span ng-if="voteSession.config.usecriteria">
            <span ng-if="!myVote[cat].abstain">
                <span data-ng-repeat="criterio in voteSession.config.criteria" class="average-criteria">
                    @{{ criterio.name }}:
                    <select ng-disabled="myVote == 'abstain'" ng-model="myVote[cat].vote[$index]" ng-if="!readOnly && !myVote[cat].abstain" ng-change="BeforeVoteUpdate();myVote[cat].abstain = false;VoteUpdated(myVote);" class="form-control form-inline input-sm" ng-class="{'text-info':!myVote[cat].abstain && myVote[cat].vote[$index] != null}">
                        <option ng-repeat="_ in getTotalVotes() track by $index"
                                ng-if="voteSession.config.min + $index * voteSession.config.step <= voteSession.config.max"
                                >@{{ voteSession.config.min + $index * voteSession.config.step | number : (voteSession.config.step == 1 ? 0 : 1) }}</option>
                    </select>
                    <span class="badge badge-info" ng-if="readOnly">@{{ myVote[cat].vote[$index] || '-'}}</span>
                </span>
                <span class="label label-default average-result" ng-show="!myVote[cat].abstain && !hideResult" ng-class="{'label-info':!!getCriteriaResult()}">@{{ getCriteriaResult() || '@lang('contest.VoteNoVote')' }}</span>
            </span>
            <div ng-include src="'abstainButton.html'" ng-init="small = true" class="abstain-container" include-replace ng-if="voteSession.config.abs && myVote[cat].vote == null && !readOnly"></div>
        </span>
        <a href="" class="" ng-click="BeforeVoteUpdate();myVote[cat].vote = null; myVote[cat].extra = null; myVote[cat].abstain = false;VoteUpdated(myVote);" ng-if="!readOnly && (myVote[cat].vote != null || myVote[cat].extra != null || myVote[cat].abstain)">
            <i class="fa fa-close"></i> @lang('contest.VoteReset')
        </a>
    </span>

    <span ng-switch-when="{{VotingSession::METAL}}">
        <span ng-if="(isEmpty(myVote) || myVote[cat].vote == null) && !readOnly">
            <div class="btn-group">
                <button ng-if="controlPerCategory(votes, cat)" data-ng-repeat="votes in voteItems" ng-model="myVote[cat].vote" ng-click="BeforeVoteUpdate();myVote[cat].vote = votes;VoteUpdated(myVote);" class="btn" style="background-color:@{{ votes.color }};color:white;font-weight: bold;" type="button"> @{{ votes.name }} </button>
            </div>
        </span>
        <span ng-if="!isEmpty(myVote) && myVote[cat].vote != null">
            <div ng-model="myVote[cat].vote" class="label label-as-badge" ng-disabled="readOnly || myVote == 'abstain'" style="background-color:@{{ myVote[cat].vote.color }};color:white;font-weight: bold; font-size:1.2em;">
                @{{ myVote[cat].vote.name }}
            </div>
            <a href="" class="" ng-click="BeforeVoteUpdate();myVote[cat].vote = null;VoteUpdated(myVote);" ng-if="!readOnly">
                <i class="fa fa-close"></i> @lang('contest.VoteReset')
            </a>
        </span>
    </span>

    <span ng-switch-when="{{VotingSession::YESNO}}">
        <div class="btn-group">
            <button type="button" class="btn" ng-click="BeforeVoteUpdate();myVote[cat].vote == 1 ? myVote[cat].vote = null : myVote[cat].vote = 1;VoteUpdated(myVote);" ng-class="{'btn-success' : myVote[cat].vote == 1}">
                <strong>@lang('voting.yes')</strong>
            </button>
            <button type="button" class="btn" ng-click="BeforeVoteUpdate();myVote[cat].vote == 0 ? myVote[cat].vote = null : myVote[cat].vote = 0;VoteUpdated(myVote);" ng-class="{'btn-danger' : myVote[cat].vote == 0}">
                <strong>@lang('voting.no')</strong>
            </button>
        </div>
    </span>

    <span ng-switch-when="{{VotingSession::VERITRON}}">
        <span ng-if="!voteSession.config.usecriteria">
            <span ng-if="voteSession.config.step == 1">
                <div class="btn-group">
                    <button type="button" class="btn"
                            ng-class="{'btn-primary': myVote[cat].vote == null,
                                'btn-info': myVote[cat].vote != null && myVote[cat].vote == voteSession.config.min + $index ,
                                'btn-danger': myVote[cat].vote != null && myVote[cat].vote == voteSession.config.min + $index && $index == 0,
                                'btn-success': myVote[cat].vote > voteSession.config.min + $index ,
                                'btn-default': myVote[cat].abstain || myVote[cat].vote < voteSession.config.min + $index }"
                            ng-click="BeforeVoteUpdate();myVote[cat].abstain = false; myVote[cat].vote = myVote[cat].vote == voteSession.config.min + $index  ? null : voteSession.config.min + $index; myVote[cat].cat = cat; VoteUpdated(myVote);"
                            ng-repeat="_ in getTotalVotes() track by $index"
                            ng-disabled="readOnly || myVote == 'abstain'">
                        @if(isset($contest->id) && $contest->id == 340)
                            @{{ voteSession.config.min + $index == 0 ? 'No' :
                            voteSession.config.min + $index == 1 ? 'BRONCE' :
                            voteSession.config.min + $index == 2 ? 'PLATA' :
                            voteSession.config.min + $index == 3 ? 'ORO' : '' }}
                        @else
                            @{{ voteSession.config.min + $index == 0 ? 'No' : voteSession.config.min + $index }}
                        @endif
                    </button>
                    <div ng-include src="'abstainButton.html'" include-replace ng-if="voteSession.config.abs"></div>
                </div>
            </span>
            <span ng-if="voteSession.config.step != 1">
                <select ng-model="myVote[cat].vote" ng-disabled="readOnly || myVote == 'abstain'" ng-change="BeforeVoteUpdate();myVote[cat].abstain = false;VoteUpdated(myVote);" ng-if="!myVote[cat].abstain" class="form-control form-inline" ng-class="{'text-info':!myVote[cat].abstain && myVote[cat].vote != null}">
                    <option ng-repeat="_ in getTotalVotes() track by $index"
                            ng-if="voteSession.config.min + $index * voteSession.config.step <= voteSession.config.max"
                            value="@{{ voteSession.config.min + $index * voteSession.config.step | number : (voteSession.config.step == 1 ? 0 : 1) }}">
                        @{{ voteSession.config.min + $index * voteSession.config.step | number : (voteSession.config.step == 1 ? 0 : 1) }}
                    </option>
                </select>
                <div ng-include src="'abstainButton.html'" include-replace ng-if="voteSession.config.abs && myVote[cat].vote == null"></div>
            </span>
        </span>
        <span ng-if="!voteSession.config.usecriteria && readOnly">
            <span class="label label-default average-result" ng-if="!hideResult">@{{ myVote[cat].vote || '@lang('contest.VoteNoVote')' }}</span>
        </span>
        <span ng-if="voteSession.config.usecriteria">
            <span ng-if="!myVote[cat].abstain">
                <span data-ng-repeat="criterio in voteSession.config.criteria" class="average-criteria">
                    @{{ criterio.name }}:
                    <select ng-model="myVote[cat].vote[$index]" ng-if="!readOnly && !myVote[cat].abstain" ng-change="BeforeVoteUpdate();myVote[cat].abstain = false;VoteUpdated(myVote);" class="form-control form-inline input-sm" ng-class="{'text-info':!myVote[cat].abstain && myVote[cat].vote[$index] != null}">
                        <option ng-repeat="_ in getTotalVotes() track by $index"
                                ng-if="voteSession.config.min + $index * voteSession.config.step <= voteSession.config.max"
                                >@{{ voteSession.config.min + $index * voteSession.config.step | number : (voteSession.config.step == 1 ? 0 : 1) }}</option>
                    </select>
                    <span class="badge badge-info" ng-if="readOnly">@{{ myVote[cat].vote[$index] || '-'}}</span>
                </span>
                <span class="label label-default average-result" ng-show="!myVote[cat].abstain && !hideResult" ng-class="{'label-info':!!getCriteriaResult()}">@{{ getCriteriaResult() || '@lang('contest.VoteNoVote')' }}</span>
            </span>
            <div ng-include src="'abstainButton.html'" ng-init="small = true" class="abstain-container" include-replace ng-if="voteSession.config.abs && myVote[cat].vote == null && !readOnly"></div>
        </span>
        <a href="" class="" ng-click="BeforeVoteUpdate();myVote[cat].vote = null; myVote[cat].extra = null; myVote[cat].abstain = false;VoteUpdated(myVote);" ng-if="!readOnly && (myVote[cat].vote != null || myVote[cat].extra != null || myVote[cat].abstain)">
            <i class="fa fa-close"></i> @lang('contest.VoteReset')
        </a>
    </span>
</div>

<div class="row">
    <div ng-class="{'col-sm-12':readOnly}">
        <span data-ng-repeat="extra in voteSession.config.extra">
            <span ng-switch="extra.type">
                <span ng-switch-when="{{Vote::EXTRA_CHECKBOX}}">
                    <div class="col-sm-12" ng-if="!readOnly">
                        <label>
                            <input ng-disabled="myVote == 'abstain'" type="checkbox" ng-model="myVote[cat].extra[$index]" ng-change="BeforeVoteUpdate();myVote[cat].abstain = false;VoteUpdated(myVote);" id="">
                            @{{ extra.name }}
                        </label>
                    </div>
                    <span ng-if="readOnly && myVote[cat].extra[$index]" class="extra-vote text-success">
                        <i class="fa fa-check" ng-if="!results"></i>
                        <span class="badge badge-info" ng-if="results">@{{ myVote[cat].extra[$index] || '-'}}</span>
                        @{{ extra.name }}
                    </span>
                </span>
                <span ng-switch-when="{{Vote::EXTRA_TEXTAREA}}">
                    <div class="col-sm-12" ng-if="!readOnly">
                        @{{ extra.name }}
                        <textarea class="form-control" style="width: 100%;"
                                  ng-model="myVote[cat].extra[$index]"
                                  ng-change="BeforeVoteUpdate();VoteUpdated(myVote);"
                                  ng-model-options="{ updateOn: 'blur' }"
                                  ng-keyup="cancel($event)"
                                  ></textarea>
                    </div>
                    <span ng-if="readOnly && myVote[cat].extra[$index] != '' && myVote[cat].extra[$index] != null" class="extra-vote text-success">
                        <i class="fa fa-comment" ng-if="!results"></i>
                        <span class="badge badge-info" ng-if="results">@{{ myVote[cat].extra[$index] || '-'}}</span>
                        @{{ extra.name }}
                    </span>
                </span>
            </span>
        </span>
    </div>
</div>
<span class="text-warning" ng-if="voteResult.error"><i class="fa fa-warning"></i> @lang('contest.VoteCouldNotBeSaved')</span>
</script>
<script type="text/ng-template" id="voteWeightedTool.html">
    <div ng-if="voteSession.vote_type == {{ VotingSession::METAL }}" class="entry-status vote-result-tool alert" style="border-radius: @{{borderRadius(keys)}};background-color:@{{ myVote[cat].vote.color }};">
        <div class="label label-as-badge" ng-if="myVote[cat].vote[0] && !inscription" style="background-color: @{{ myVote[cat].vote[0].color }};font-size:1.2em;"> @{{ ShowVoteResult(myVote[cat].vote) }} </div>
        <div ng-if="myVote[cat].vote.name && inscription" style="color:white;"> <i class="fa fa-2x fa-check-circle"></i> </div>
        <div ng-if="!myVote[cat].vote.name && inscription">  - </div>
    </div>
    <div ng-if="voteSession.vote_type == {{ VotingSession::AVERAGE }}" class="entry-status vote-result-tool alert" style="border-radius: @{{borderRadius(keys)}}"
         ng-class="{'alert-default': !myVote[cat].abstain && !getCriteriaResult(), 'alert-info': !!getCriteriaResult(), 'alert-warning': myVote[cat].abstain}">
        <div>@{{ getCriteriaResult() || (myVote[cat].abstain ? '@lang('contest.VoteAbstainedAbrv')' : '@lang('contest.VoteNoVote')') }}</div>
    </div>
    <div ng-if="voteSession.vote_type == {{ VotingSession::VERITRON }}" class="entry-status vote-result-tool alert" style="border-radius: @{{borderRadius(keys)}}"
         ng-class="{'alert-danger': getCriteriaResult() == 'No', 'alert-default': !myVote[cat].abstain && !getCriteriaResult(), 'alert-info': getCriteriaResult() !== 0, 'alert-warning': myVote[cat].abstain}">
        <div ng-if="inscription !== 3">@{{ getCriteriaResult() || (myVote[cat].abstain ? '@lang('contest.VoteAbstainedAbrv')' : '@lang('contest.VoteNoVote')') }}</div>

    </div>
</script>
<script type="text/ng-template" id="abstainButton.html">
    <button type="button" class="btn btn-primary"
            ng-class="{'btn-primary': myVote[cat].vote == null && !myVote[cat].abstain,
                        'btn-default': myVote[cat].vote != null,
                        'btn-info': myVote[cat].abstain,
                        'btn-sm': !!small}"
            ng-click="BeforeVoteUpdate();myVote[cat].abstain = !myVote[cat].abstain; myVote[cat].vote = null; VoteUpdated(myVote);"
            ng-disabled="readOnly || myVote == 'abstain'">
        <i class="fa fa-check" ng-if="myVote[cat].abstain"></i>
        @{{ myVote[cat].abstain ? '@lang('contest.VoteAbstained')' : '@lang('contest.VoteAbstain')'}}
    </button>
</script>