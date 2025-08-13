<script type="text/ng-template" id="categoryListVoting.html">
    <span ng-if="(showOnlyCat == null || showOnlyCat == category.id)">
        <span ng-if="!hideCats">
            <span ng-if="category.parent_id">
                <span ng-include="'categoryListVoting.html'" onload="category = getCategory(category.parent_id); first = false;"></span>
                <i class="fa fa-angle-double-right"></i>
            </span>
            <span ng-if="{{$contest->type}} == {{Contest::TYPE_CONTEST}}">
                <span ng-if="!category.parent_id"><i class="fa fa-caret-right"></i></span>
                <trans ng-model='category' trans-prop="'name'"> </trans>
                <span ng-if="category.final && inscription.role != {{Inscription::JUDGE}}">
                    <span ng-if="entry.categories_id.length > 1 && contest.id == 127" class="label label-default label-fileversion label-as-badge">
                        @{{ contest.billing.mainCurrency }} @{{ category.price ? category.price : contest.billing.mainPrice }}
                    </span>
                    <br>
                    <trans ng-model='category' trans-prop="'description'"></trans>
                </span>
            </span>
            <span ng-if="{{$contest->type}} == {{Contest::TYPE_TICKET}}">
                <h4>
                    <span ng-if="!category.parent_id"><i class="fa fa-caret-right"></i></span>
                    <trans ng-model='category' trans-prop="'name'"> </trans> - <trans ng-model='category' trans-prop="'description'"></trans>
                    <br>
                </h4>
                <ul class="list-group">
                    <span ng-repeat="ticket in entry.tickets" ng-if="ticket.category_id == category.id">
                        <li class="list-group-item">
                            @lang('oxoTicket.operationType') Compra -
                            @lang('oxoTicket.unitPrice'): @{{ contest.billing.mainCurrency }} @{{ ticket.price / ticket.tickets_count}} -
                            @lang('oxoTicket.quantity'): @{{ ticket.tickets_count }}
                        </li>
                        <li class="list-group-item"> @lang('oxoTicket.totalPrice'): <span>@{{ contest.billing.mainCurrency }} @{{ ticket.price}} </span></li>
                    </span>
                </ul>
                <span class="col-sm-12">
                    <span ng-repeat="qr in entry.qr" ng-if="qr.category == category.id">
                        <span class="col-sm-6 text-center">
                            <h4>@lang('oxoTicket.paymentCode') <br>@{{ qr.code }}</h4>
                            <img ng-src="@{{ qr.QR }}"/>
                        </span>
                    </span>
                </span>
                <div class="clearfix"></div>
                <br>
                <hr>
            </span>
            <span class="pull-right" ng-if="first && !!checkEntriesSession">
                <button ng-click="addEntryCategory(category, entry)" class="button btn btn-info btn-xs" ng-if="checkEntriesSession[entry.id] == null || checkEntriesSession[entry.id].categories_id.indexOf(catid) == -1"><i class="fa fa-plus"></i> </button>
                <button ng-click="removeEntryCategory(category, entry)" class="button btn btn-danger btn-xs" ng-if="checkEntriesSession[entry.id] != null && checkEntriesSession[entry.id].categories_id.indexOf(catid) != -1"><i class="fa fa-trash"></i> </button>
            </span>
            <span class="pull-right" ng-if="first && deleteEntriesSession && (!entry.groups || entry.groups.length == 0)">
                <button ng-click="removeEntryCategory(category, entry)" class="button btn btn-danger btn-xs"><i class="fa fa-trash"></i> </button>
            </span>
            <span class="pull-right" ng-if="first && deleteEntriesSession && entry.groups && entry.groups.length > 0">
                <i class="fa fa-users"></i>
                <span ng-repeat="groups in entry.groups" class="label label-primary label-as-badge">
                    @{{ groups.name }}
                </span>
            </span>
            @if(null != Auth::user())
            <span ng-if="first && editable && (inscription.role != {{ Inscription::JUDGE}} || {{Auth::user()->isSuperAdmin()}})" ><a href="" ng-click="removeCategory(category, entry.id)" uib-tooltip="@lang("contest.removeFromCategory")" tooltip-placement="bottom"><i class="fa fa-remove"></i></a></span>
            @endif
        </span>
        <div ng-if="winners && first"> <span ng-include="'judgeVoteTool.html'"></span> </div>
        <div class="form-control-static" ng-if="(inscription.role == {{ Inscription::JUDGE }} && first) && voteSession.config.showVotingTool == 1">
            <span ng-include="'judgeVoteTool.html'"></span>
        </div>
        <span ng-repeat="entCat in entry.entry_categories">
            <div ng-if="!!results && first && entCat.category_id == catid">
                <span ng-if="!!results && first">
                    <span ng-include="'resultsVoteTool.html'"></span>
                </span>

                <div class="label label-as-badge" ng-show="!showThis && voting.shortlist.indexOf(entCat.id) != -1" ng-class="{'label-success':voting.shortlist.indexOf(entCat.id) != -1,'label-primary':!voting.shortlist.indexOf(entCat.id) != -1}">
                    <i class="fa fa-check"></i> @lang('voting.inShortlist')
                </div>
                <button type="button" class="btn btn-sm" ng-show="showThis" ng-class="{'btn-success':voting.shortlist.indexOf(entCat.id) != -1,'btn-primary':!voting.shortlist.indexOf(entCat.id) != -1}" data-ng-click="toggleFromShortlist(entCat.id)">
                    @lang('voting.inShortlist')
                </button>
            </div>
            <span ng-if="!!changeShortlist && first && entCat.category_id == catid">
                <button type="button" class="btn btn-sm" ng-class="{'btn-success':voting.shortlist.indexOf(entCat.id) != -1,'btn-primary':!voting.shortlist.indexOf(entCat.id) != -1}" data-ng-click="toggleFromShortlist(entCat.id)">
                    @lang('voting.inShortlist')
                </button>
            </span>
        </span>
    </span>
</script>


<script type="text/ng-template" id="judgeVoteTool.html">
    <span ng-if="voteSession.vote_type != {{ VotingSession::METAL }}"> <!-- && voteSession.vote_type != {{ VotingSession::YESNO }}"> -->
        <vote-tool ng-if="!entries" vote-session="voteSession" my-entry="entry" my-vote="entry.votes" cat="category.id"></vote-tool>
        <vote-tool ng-if="entries && clearView == false" read-only="false" hide-result="true" vote-session="voteSession" my-entry="entry" my-vote="entry.votes" cat="category.id"></vote-tool>
    </span>
    <span ng-if="voteSession.vote_type == {{ VotingSession::METAL }} && !winners"> <!-- || voteSession.vote_type == {{ VotingSession::YESNO }}">-->
        <span ng-if="voteSession.config.oxoMeeting">
            <span ng-if="voteSession.config.guidedVote == 1 && meetModerator == true">
                <vote-tool vote-session="voteSession" read-only="false" my-entry="entry" my-vote="entry.votes" cat="category.id" votes-per-cat="category.entriesRows"></vote-tool>
            </span>
            <span ng-if="voteSession.config.guidedVote != 1">
                <vote-tool vote-session="voteSession" read-only="false" my-entry="entry" my-vote="entry.votes" cat="category.id" votes-per-cat="category.entriesRows"></vote-tool>
            </span>
        </span>
        <span ng-if="!voteSession.config.oxoMeeting">
            <vote-tool vote-session="voteSession" read-only="false" my-entry="entry" my-vote="entry.votes" cat="category.id" votes-per-cat="category.entriesRows"></vote-tool>
        </span>
    </span>
    <span ng-if="winners">
        <vote-tool vote-session="winnersSession" read-only="true" my-entry="entry" my-vote="entry.votes" cat="category.id"></vote-tool>
    </span>
</script>
<script type="text/ng-template" id="resultsVoteTool.html">
    <a href="" vote-weighted-tool vote-session="voteSession" my-vote="entry.votes" cat="category.id" keys="{'key': key, 'length': entry.categories_id.length}"></a>
    <div class="average-criteria">
        <div ng-if="entry.votes[category.id].totalYes || entry.votes[category.id].totalNo" class="extra-vote-view" style="color:white;">
            <span class="entry-status vote-result-tool label-default"> %@lang('voting.yes') @{{ (entry.votes[category.id].totalYes / (entry.votes[category.id].totalYes + entry.votes[category.id].totalNo)) * 100 | number : 2}}%</span>
            <span class="entry-status vote-result-tool label-success"> @lang('voting.yes') @{{ entry.votes[category.id].totalYes }}</span>
            <span class="entry-status vote-result-tool label-danger"> @lang('voting.no') @{{ entry.votes[category.id].totalNo }}</span>
        </div>
        <div class="clearfix"></div>
        <div>
            <uib-progressbar class="@{{ entry.votes[category.id].total == entry.votes[category.id].judges ? '' : 'progress-striped' }} progress entry-vote-progress active"
                         max="entry.votes[category.id].judges" value="entry.votes[category.id].total ? entry.votes[category.id].total : 0"
                         type="@{{ entry.votes[category.id].judges == entry.votes[category.id].total ? 'success' : (((voting.config.minvotesunit == {{{ Vote::MIN_VOTES_JUDGES }}} && entry.votes[category.id].total < voting.config.minvotes) || (voting.config.minvotesunit == {{{ Vote::MIN_VOTES_PERC }}} && ((entry.votes[category.id].total / entry.votes[category.id].judges) * 100) < voting.config.minvotes )) ? 'warning':'info')}}">
            @{{ entry.votes[category.id].total ? entry.votes[category.id].total : 0}} @lang('voting.totalVotes') / @{{ entry.votes[category.id].judges }} @lang('voting.resultTotalJudges')
            </uib-progressbar>
        </div>
        <span ng-if="entry.votes[category.id].yesPerc>0" class="extra-vote-view">
            <span class="label label-as-badge" ng-class="{'label-danger': entry.votes[category.id].yesPerc <= 50, 'label-success': entry.votes[category.id].yesPerc}">
                @lang('voting.yesPerc')
                @{{ entry.votes[category.id].yesPerc }}
            </span>
        </span>
        <span ng-if="entry.votes[category.id].noCount>0" class="extra-vote-view">
            <span class="badge badge-info"> @lang('voting.no'): @{{ entry.votes[category.id].noCount }}</span>
        </span>
        <span ng-if="entry.votes[category.id].abstains>0" class="extra-vote-view">
            <span class="badge badge-info">@{{ entry.votes[category.id].abstains }} @lang('voting.abstains')</span>
        </span>
    </div>
</script>

<script type="text/ng-template" id="categoryListVotingPublic.html">
    <span>
        <span ng-if="!hideCats">
            <span ng-if="category.parent_id">
                <span ng-include="'categoryListVotingPublic.html'" onload="category = getCategory(category.parent_id); first = false;"></span>
                <i class="fa fa-angle-double-right"></i>
            </span>
        </span>
        <span ng-if="{{$contest->type}} == {{Contest::TYPE_CONTEST}}">
                <!--<span ng-if="!category.parent_id"><i class="fa fa-caret-right"></i></span>
                <trans ng-model='category' trans-prop="'name'"> </trans>-->
            </span>

        <div class="form-control-static" ng-if="voteSession.config.showVotingTool == 1">
            <span ng-include="'judgeVoteToolPublic.html'"></span>
        </div>
        <span ng-repeat="entCat in entry.entry_categories">
            <div ng-if="!!results && first && entCat.category_id == catid">
                <span ng-if="!!results && first">
                    <span ng-include="'resultsVoteTool.html'"></span>
                </span>
            </div>
        </span>
    </span>
</script>
<script type="text/ng-template" id="judgeVoteToolPublic.html">
    <span ng-if="voteSession.vote_type != {{ VotingSession::METAL }}">
        <vote-tool ng-if="!entries" vote-session="voteSession" my-entry="entry" my-vote="entry.votes" cat="category.id"></vote-tool>
        <vote-tool ng-if="entries" read-only="false" hide-result="true" vote-session="voteSession" my-entry="entry" my-vote="entry.votes" cat="category.id"></vote-tool>
    </span>
    <span ng-if="voteSession.vote_type == {{ VotingSession::METAL }} && !winners">
        <vote-tool vote-session="voteSession" read-only="false" my-entry="entry" my-vote="entry.votes" cat="category.id" votes-per-cat="category.entriesRows"></vote-tool>
    </span>
</script>
