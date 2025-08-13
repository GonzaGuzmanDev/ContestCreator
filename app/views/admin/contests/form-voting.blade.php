@extends('admin.contests.form', array('active' => 'voting-sessions'))
@section('form')

    @include('includes.categoryList')
    @include('includes.categoryDropdown')
    <script type="text/ng-template" id="categoryGroup.html">
        <div ng-if="category.final != 1">
            <div ng-repeat="category in category.children_categories track by $index" ng-include="'categoryGroup.html'"></div>
        </div>
        <div ng-if="category.final == 1 && !filterSelected(category.id)">
            <div class="well well-sm cat-header" ng-hide="category.filteredEntries.length == 0">
                <h4>
                    <button class="btn btn-default btn-sm" ng-click="selectCategory(category, category.id)"><i class="fa fa-check"></i> </button>
                    <span ng-click="toggleCat(category)">
                    <a href="">
                    <i class="fa" ng-class="{'fa-chevron-right': !category.open, 'fa-chevron-down': category.open}"></i></a>
                    <span ng-include="'categoryList.html'" onload="first=true; editable=false; results = false;"></span>
                    <!--<div class="label label-as-badge cat-entries-badge" ng-class="{'label-primary': !category.entriesCount, 'label-info': !category.final && category.entriesCount, 'label-success': category.final && category.entriesCount}">
                        @{{ category.entriesCount || '-' }}
                    </div>-->
                    </span>
                    <div class="clearfix"></div>
                </h4>
            </div>
            <div class="clearfix"></div>
            <div ng-show="category.open && category.filteredEntries.length > 0">
                <ul ng-model="category.children_categories" class="category-list"><li ng-repeat="category in category.children_categories track by $index" ng-include="'categoryGroup.html'"></li></ul>
                <div>
                    <div class="text-center cat-loading" ng-show="category.loading  && category.filteredEntries.length > 0"><i class="fa fa-circle-o-notch fa-spin fa-2x"></i></div>
                    <div class="entries" ng-if="category.final == 1 && category.open">
                        <div class="alert alert-transparent" ng-if="category.filteredEntries.length == 0">
                            @lang('contest.noEntriesInCategory')
                        </div>
                        <span ng-repeat="eRow in category.entriesRows">
                            <span ng-repeat="entry in eRow track by $index">
                                <div class="entry" ng-include="'entry.html'" ng-class="{'col-xs-12': listView == 'list', 'col-md-3 col-sm-4 col-xs-12':listView == 'thumbs'}" onload="hideCats = true; showOnlyCat = category.id;"></div>
                                <div class="clearfix visible-md visible-lg" ng-show="($index + 1) % 4 == 0"></div>
                                <div class="clearfix visible-sm visible-xs" ng-show="($index + 1) % 3 == 0"></div>
                        </span>
                    </span>
                        <span in-view="$inview && inViewLoadMoreCatEntries(category)" in-view-options="{offset: 1}" ng-if="!category.lastEntryShown">
                    </span>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="text/ng-template" id="entry.html">
        <div class="panel panel-default" ng-click="addEntriesToBulk(entry, category.id);">
            <div class="panel-heading" ng-hide="showSelected == 1 && !isSelected(entry, category.id) || category.filteredEntries.length == 0">
                <div class="row">
                    <div class="entry-status alert">
                        <i class="fa fa-fw" ng-class="{'fa-square-o': !isSelected(entry, category.id), 'fa-check-square-o': isSelected(entry, category.id)}"></i>
                    </div>
                    <div class="entry-title col-xs-12" ng-class="{'col-sm-7':listView == 'list','col-sm-10':listView == 'thumbs'}">
                        <span entry-card entry="entry" class=""></span>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="text/ng-template" id="category.html">
        <div ng-if="category.final != 1">
            <div ng-repeat="category in category.children_categories track by $index" ng-include="'category.html'"></div>
        </div>
        <div ng-if="category.final == 1">
            <div class="well well-sm cat-header" ng-click="toggleCat(category)" ng-hide="category.filteredEntries.length == 0">
                <h4>
                    <a href="">
                        <i class="fa" ng-class="{'fa-chevron-right': !category.open, 'fa-chevron-down': category.open}"></i></a>
                    <i class="fa text-muted" ng-if="category.final == 1" ng-class="{'fa-circle-o': !category.entries || category.entries.length==0, 'fa-circle text-warning': category.entries.length>0}"></i>
                    <span ng-include="'categoryList.html'" onload="first=true; editable=false; results = false;"></span>
                    <div class="pull-right">
                        <div class="badge">@{{category.filteredEntries.length}}</div>
                    </div>
                    <div class="clearfix"></div>
                </h4>
            </div>
            <div class="clearfix"></div>
            <div ng-show="category.open">
                <div class="entries" ng-if="category.final == 1 && category.open" ng-hide="category.filteredEntries.length == 0">
                    <div class="alert alert-transparent" ng-if="category.filteredEntries.length == 0">
                        @lang('contest.noEntriesInCategory')
                    </div>
                    <span ng-repeat="eRow in category.entriesRows">
                        <span ng-repeat="entry in eRow">
                            <div class="entry" ng-include="'entryResult.html'" onload="hideCats = true; showOnlyCat = category.id;" ng-class="{'col-xs-12': listView == 'list', 'col-md-3 col-sm-4 col-xs-12':listView == 'thumbs'}"></div>
                            <div class="clearfix visible-md visible-lg" ng-show="($index + 1) % 4 == 0"></div>
                            <div class="clearfix visible-sm visible-xs" ng-show="($index + 1) % 3 == 0"></div>
                        </span>
                    </span>
                    <span in-view="$inview && inViewLoadMoreCatEntries(category)" in-view-options="{offset: -100}" ng-if="!category.lastEntryShown">
                        <div class="col-sm-12 text-center">
                            <div class="spinner">
                                <div class="rect1"></div>
                                <div class="rect2"></div>
                                <div class="rect3"></div>
                                <div class="rect4"></div>
                                <div class="rect5"></div>
                            </div>
                        </div>
                    </span>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </script>

    <script type="text/ng-template" id="categoryList2.html">
        <span ng-include="'categoryList.html'" onload="category = getCategory(cat); first = false;"></span>
        <span><a href="" ng-click="removeCategory(cat)" uib-tooltip="@lang("contest.removeFromCategory")" ng-show="showThis" tooltip-placement="bottom"><i class="fa fa-remove"></i></a></span>
    </script>

    <script type="text/ng-template" id="voting-group.html">
        <div class="well well-sm cat-header" ng-click="toggleGroup(group)">
            <div class="row">
                <div class="col-xs-8">
                    <h4>
                        <a href=""><i class="fa" ng-class="{'fa-chevron-right': !group.open, 'fa-chevron-down': group.open}"></i></a>
                        @{{ group.name }}
                        <div class="badge">@lang('voting.totaljudges'): @{{ filteredGroupJudges[0] ? filteredGroupJudges[group.id ? group.id : 0].length : 0}}</div>
                        <div class="badge" ng-if="group.name != 'Ungrouped'">@lang('contest.entries'): @{{ group.countEntries }}</div>
                    </h4>
                </div>
                <div class="col-xs-2">
                </div>
                <div class="col-xs-1">
                    <uib-progressbar class="@{{ groupsTotals[group.id ? group.id : 0] == 100 ? '' : 'progress-striped' }} progress-total active" value="groupsTotals.length == 0 ? 0 : groupsTotals[group.id ? group.id : 0]" type="@{{ groupsTotals[group.id ? group.id : 0] == 100 ? 'success' : 'warning' }}"><i>@{{ groupsTotals.length == 0 ? 0 : groupsTotals[group.id ? group.id : 0] }}%</i></uib-progressbar>
                </div>
                <div class="col-xs-1" ng-if="group.name != 'Ungrouped'">
                    <button type="button" class="btn btn-xs btn-info" ng-disabled="!showThis" ng-click="editVotingGroup(group);$event.stopPropagation();" disabled="disabled"> @lang('general.edit') </button>
                    <button type="button" class="btn btn-danger btn-xs" ng-disabled="!showThis" data-ng-click="deleteGroup(group);$event.stopPropagation();"><i class="fa fa-trash-o"></i></button>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div ng-show="group.open">
            <div ng-include="'judges-list.html'"></div>
        </div>
    </script>

    <script type="text/ng-template" id="judge.html">
        <td> <input ng-disabled="!showThis" checklist-model="bulks" checklist-value="judge" ng-change="addBulkJudge(judge)" type="checkbox"></td>
        <td>@{{ $index + 1 }}</td>
        <td>
            <span user-card user-card-model="judge.inscription.user" ng-if="judge.inscription && judge.inscription.user" user-show-email="true"></span>
            <span ng-if="judge.inscription.invitename">@{{ judge.inscription.invitename }}</span>
            <span ng-if="judge.inscription.email">@{{ judge.inscription.email }}</span>
        </td>
        <td>
            <judge-progress judge="judge"></judge-progress>
        </td>
        <td>@{{ judge.progress.abstains }}</td>
        <td ng-switch="judge.status">
            <span ng-switch-when="{{VotingUser::PENDING_NOTIFICATION}}" class="text-default">
                <i class="fa fa-clock-o"></i> @lang('voting.pendingNotification')
            </span>
            <span ng-switch-when="{{VotingUser::NOTIFIED}}" class="text-warning">
                <i class="fa fa-envelope"></i> @lang('voting.notified')
            </span>
            <span ng-switch-when="{{VotingUser::VISITED_PAGE}}" class="text-info">
                <i class="fa fa-envelope-o"></i> @lang('voting.visitedPage')
            </span>
            <span ng-switch-when="{{VotingUser::REJECTED}}" class="text-danger">
                <i class="fa fa-thumbs-down"></i> @lang('voting.rejectedInvitation')
            </span>
            <span ng-switch-when="{{VotingUser::ACCEPTED}}" class="text-success">
                <i class="fa fa-check"></i> @lang('voting.accepted')
            </span>
            <span ng-switch-when="{{VotingUser::RESEND}}" class="text-warning">
                <i class="fa fa-mail-reply-all"></i> @lang('voting.resend')
            </span>
            <span ng-switch-when="{{VotingUser::IN_LOBBY}}" class="text-warning">
                <i class="fa fa-clock-o"></i> @lang('voting.inLobby')
            </span>
            <span ng-if="judge.status != {{VotingUser::ACCEPTED}} && judge.status != {{VotingUser::IN_LOBBY}}">
                <br>
                <button type="button" copy-to-clipboard="<?=url("/".$contest->code."/invite")?>/@{{ judge.invitation_key }}" class="btn btn-xs btn-default">
                    @lang('voting.copy_link')
                </button>
            </span>
        </td>
        <td><span am-time-ago="judge.last_seen_at"></span></td>
        <td ng-if="voting.config.oxoMeeting" class="text-right">
            <span>
                <a class="btn btn-sm btn-danger" ng-click="userInLobby(judge)" ng-if="judge.status == {{VotingUser::IN_LOBBY}}"> <i class="fa fa-close"/> LOBBY </a>
            </span>
        </td>
        <td class="text-right">
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-info" ng-disabled="!showThis" ng-click="editJudgeVotingEntries(judge.inscription.user, judge)">@lang('general.edit')</button>
                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ng-disabled="!showThis">
                    <i class="fa fa-caret-down"></i>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu multi-level dropdown-menu-right">
                    <li><a href="" data-ng-click="sendInvitations(judge)"><i class="fa fa-send"></i> @lang('voting.sendInvitation')</a></li>
                    <li role="separator" class="divider"></li>
                    <li class="dropdown-submenu pull-left"><a href="#"><i class="fa fa-users"></i> @lang('voting.changeGroup')</a>
                        <ul class="dropdown-menu multi-level dropdown-menu-right">
                            <li ng-repeat="group in voting.voting_groups">
                                <a href="" data-ng-click="toggleJudgeGroup(judge, group.id)">
                                    <i class="fa @{{ judge.voting_groups.indexOf(group.id) == -1 ? 'fa-square-o' : 'fa-check-square-o'}}"></i>
                                    @{{ group.name }}
                                </a>
                            </li>
                            <li role="separator" class="divider"></li>
                            <li>
                                <a href="" data-ng-click="toggleJudgeGroup(judge, null)">
                                    <i class="fa fa-"></i>
                                    @lang('voting.noGroup')
                                </a>
                            </li>
                        </ul>
                    </li>
                    <div class="clearfix"></div>
                    <li role="separator" class="divider"></li>
                    <li class="text-danger"><a href="" data-ng-click="deleteJudge(judge)"><i class="fa fa-trash-o"></i> @lang('voting.judgesdelete')</a></li>
                    <li class="text-danger"><a href="" data-ng-click="autoAbstainsModal(voting, judge)"><i class="fa fa-edit"></i> @lang('voting.abstains')</a></li>
                </ul>
            </div>
        </td>
    </script>

    <script type="text/ng-template" id="judges-list.html">
        @{{ deleteJudgeMsg }}
        <div>
            <div class="dropdown btn-group">
                <button ng-disabled="!showThis" class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    @lang('general.bulkActions')
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <li><a href="" data-ng-click="sendInvitations(bulks)"><i class="fa fa-send"></i> @lang('voting.sendInvitation')</a></li>
                    <li class="dropdown-submenu"><a href="#"><i class="fa fa-users"></i> @lang('voting.changeGroup')</a>
                        <ul class="dropdown-menu multi-level dropdown-menu-right">
                            <li ng-repeat="group in voting.voting_groups">
                                <a href="" data-ng-click="toggleBulkJudgeGroup(bulks, group.id)">
                                    <i class="fa @{{ bulks[0].voting_groups.indexOf(group.id) == -1 ? 'fa-square-o' : 'fa-check-square-o'}}"></i>
                                    @{{ group.name }}
                                </a>
                            </li>
                            <li role="separator" class="divider"></li>
                            <li>
                                <a href="" data-ng-click="toggleBulkJudgeGroup(bulks, null)">
                                    <i class="fa fa-"></i>
                                    @lang('voting.noGroup')
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li><a ng-click="bulkDeleteJudge(bulks)">@lang('general.delete')</a></li>
                </ul>
            </div>
            <span>
                <button type="button" class="btn btn-sm btn-info" ng-disabled="!showThis" ng-click="showAddJudges = !showAddJudges;">
                    <i class="fa fa-fw" ng-class="{'fa-plus':!showAddJudges, 'fa-caret-up':showAddJudges}"></i>
                    @{{ showAddJudges ? '@lang("general.close")' : '@lang("contest.VoteAddJudges")' }}
                </button>
            </span>
            <div ng-show="showAddJudges && showThis">
                <h5>@lang('contest.VoteAddJudges')</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><span class="badge">1.</span> @lang('contest.VoteAddJudgesInviteEmails')</p>
                        <textarea id="" ng-model="addJudges.groups[group.id].newEmails" style="width: 100%; max-width: 100%; min-width: 100%;" rows="10" class="form-control"></textarea>
                        <span class="help-block">@lang('contest.VoteAddJudgesInviteEmailsFormat')</span>
                        <div class="text-right">
                            <br>
                            <i class="fa fa-spin fa-spinner" data-ng-show="addJudges.groups[group.id].sending"></i>
                            @{{ addJudges.groups[group.id].msg }}
                            <span class="text-danger">@{{ addJudges.groups[group.id].errors }}</span>
                            <button type="button" class="btn btn-info" ng-disabled="addJudges.groups[group.id].newEmails[group.id] == ''" data-ng-click="inviteEmails(group.id)"><i class="fa fa-plus"></i> @lang('contest.VoteInviteEmails')</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p><span class="badge">2.</span> @lang('contest.VoteAddJudgesInviteKeys')</p>
                        <button type="button" class="btn btn-info" data-ng-click="requestKeys(group.id, false)"><i class="fa fa-ticket"></i> @lang('contest.VoteInviteKeys')</button>
                        <button type="button" class="btn btn-info" data-ng-click="requestKeys(group.id, true)"><i class="fa fa-ticket"></i> @lang('contest.VoteInviteSimpleKeys')</button>
                        <i class="fa fa-spin fa-spinner" data-ng-show="addJudges.groups[group.id].requesting"></i>
                        <br>
                        <br>
                        <div class="alert alert-success alert-sm" ng-if="addJudges.groups[group.id].invitationKeys.length">
                            <i class="fa fa-info-circle"></i>
                            @lang('contest.VoteAddJudgesKeysUrl')
                            <br>
                            <a href="<?=url("/".$contest->code."/invite-key/")?>"><?=url("/".$contest->code."/invite-key/")?></a>
                        </div>
                        <div class="row">
                            <div data-ng-repeat="keysList in addJudges.groups[group.id].invitationKeys" class="col-sm-2">
                                <span data-ng-repeat="key in keysList">
                                    @{{ key }}<br>
                                </span>
                            </div>
                        </div>
                        <br>
                        <br>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div ng-if="voting.voting_groups.length === 0">
            <pagination
                    ng-model="judgesPagination.currentPage"
                    total-items="filteredJudges.length"
                    max-size="judgesPagination.maxSize"
                    boundary-links="true">
            </pagination>
        </div>
        <table class="table table-condensed table-hover judges-table">
            <thead>
            <tr>
                <th><input ng-disabled="!showThis" checklist-model="groupBulks" checklist-value="filteredGroupJudges[group.name == 'Ungrouped' ? 0 : group.id]" ng-change="addGroupBulkJudge(filteredGroupJudges[group.name == 'Ungrouped' ? 0 : group.id])" type="checkbox"></th>
                <th></th>
                <th><a data-ng-click="changeJudgesOrder('judge')">@lang('voting.judgesoremails') <i ng-show="judgesPagination.sortBy == 'judge'" ng-class="{'fa-chevron-down': !judgesPagination.sortInverted,'fa-chevron-up': judgesPagination.sortInverted}" class="fa"></i></a></th>
                <th>
                    <a data-ng-click="changeJudgesOrder('progress.progress')">@lang('voting.judgesprogress') <i ng-show="judgesPagination.sortBy == 'progress.progress'" ng-class="{'fa-chevron-down': !judgesPagination.sortInverted,'fa-chevron-up': judgesPagination.sortInverted}" class="fa"></i></a>
                </th>
                <th><a data-ng-click="changeJudgesOrder('progress.abstains')">@lang('voting.judgesabstentions') <i ng-show="judgesPagination.sortBy == 'progress.abstains'" ng-class="{'fa-chevron-down': !judgesPagination.sortInverted,'fa-chevron-up': judgesPagination.sortInverted}" class="fa"></i></a></th>
                <th><a data-ng-click="changeJudgesOrder('status')">@lang('voting.judgesstatus') <i ng-show="judgesPagination.sortBy == 'status'" ng-class="{'fa-chevron-down': !judgesPagination.sortInverted,'fa-chevron-up': judgesPagination.sortInverted}" class="fa"></i></a></th>
                <th><a data-ng-click="changeJudgesOrder('last_seen_at')">@lang('voting.judgeslastseen') <i ng-show="judgesPagination.sortBy == 'last_seen_at'" ng-class="{'fa-chevron-down': !judgesPagination.sortInverted,'fa-chevron-up': judgesPagination.sortInverted}" class="fa"></i></a></th>
                <th ng-if="voting.config.oxoMeeting"></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr ng-repeat="judge in filteredGroupJudges[group.id ? group.id : 0]" ng-include="'judge.html'"></tr>
            </tbody>
            <tfoot>
            <tr>
                <th></th>
                <th>@{{ filteredGroupJudges[group.id ? group.id : 0].length}}/@{{ voting.voting_users.length }} @lang('voting.totaljudges')</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            </tfoot>
        </table>
        <div ng-if="voting.voting_groups.length === 0">
            <pagination
                    ng-model="judgesPagination.currentPage"
                    total-items="filteredJudges.length"
                    max-size="judgesPagination.maxSize"
                    boundary-links="true">
            </pagination>
        </div>
        <br> <div class="clearfix"></div>
    </script>

    <h4 class="well well-sm">
        <a href="#/admin/voting-sessions" class="" role="button"><i class="fa fa-arrow-left"></i> @lang('contest.voting-session')</a>
        /
        <span ng-show="!voting.code">@lang('contest.creatingVotingSession')</span>
        <span ng-show="voting.code">@lang('contest.editingVotingSession') @{{ voting.name }}</span>
    </h4>
    <uib-tabset active="active">
        <uib-tab index="0" ng-if="colaborator == null">
            <uib-tab-heading>
                <i class="fa fa-sliders"></i> @lang('contest.VoteConfig')
            </uib-tab-heading>
            <div class="form-group" ng-class="{error: errors.name}">
                <label for="inputName" class="col-sm-2 control-label">@lang('general.name')</label>
                <div class="col-sm-8 col-md-6 col-lg-4">
                    <input type="text" class="form-control col-md-3" placeholder="@lang('general.name')" id="inputName" required ng-model="voting.name" ng-if="showThis">
                    <div class="form-control-static" ng-if="!showThis">@{{ voting.name }}</div>
                </div>
                <div ng-show="errors.name" class="help-inline text-danger form-control-static">@{{errors.name.toString()}}</div>
            </div>
            <div class="form-group" ng-class="{error: voting.public.$invalid && !voting.public.$pristine}">
                <label for="votingPublic" class="col-sm-2 control-label">@lang('contest.votingPublic')</label>
                <div class="col-sm-8">
                    <div class="checkbox">
                        <label ng-if="showThis">
                            <input type="checkbox" name="" ng-model="voting.public" id=""/>
                        </label>
                        <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': voting.public == 1,'fa-square-o': voting.public != 1 }"></i>
                    </div>
                    <div ng-show="errors.public" class="help-inline text-danger form-control-static">@{{errors.public.toString()}}</div>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="form-group" ng-class="{error: voting.publicAnonymous.$invalid && !voting.publicAnonymous.$pristine}">
                <label for="votingPublicAnonymous" class="col-sm-2 control-label">@lang('contest.votingPublicAnonymous')</label>
                <div class="col-sm-8">
                    <div class="checkbox">
                        <label ng-if="showThis">
                            <input type="checkbox" name="" ng-model="voting.publicAnonymous" id=""/>
                        </label>
                        <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': voting.publicAnonymous == 1,'fa-square-o': voting.publicAnonymous != 1 }"></i>
                    </div>
                    <div ng-show="errors.publicAnonymous" class="help-inline text-danger form-control-static">@{{errors.publicAnonymous.toString()}}</div>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="form-group" ng-class="{error: voting.config.percentageByJudge.$invalid && !voting.config.percentageByJudge.$pristine}">
                <label for="percentageByJudge" class="col-sm-2 control-label">@lang('voting.percentageByJudge')</label>
                <div class="col-sm-1 col-lg-1 col-md-1">
                    <div class="checkbox">
                        <label ng-if="showThis">
                            <input type="checkbox" name="" ng-model="voting.config.percentageByJudge" id="" ng-true-value="1" ng-false-value="0"/>
                        </label>
                        <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': voting.config.percentageByJudge == 1,'fa-square-o': voting.config.percentageByJudge != 1 }"></i>
                        <span ng-if="!showThis">@{{ voting.config.percentage }} <span ng-if="voting.config.percentage"> % </span></span>
                    </div>
                </div>
                <div class="col-sm-3 col-lg-1 col-md-2">
                    <div class="input-group" ng-if="voting.config.percentageByJudge && showThis">
                        <input type="number" min="0" class="form-control form-inline" placeholder="0" required ng-model="voting.config.percentage">
                        <span class="input-group-addon">%</span>
                    </div>
                </div>
                    <div ng-show="errors.percentageByJudge" class="help-inline text-danger form-control-static">@{{errors.public.toString()}}</div>
                <div class="clearfix"></div>
            </div>

            <div class="form-group" ng-class="{error: voting.autoAbstain.$invalid && !voting.autoAbstain.$pristine}">
                <label for="autoAbstain" class="col-sm-2 control-label">@lang('voting.autoAbstain')</label>
                <div class="col-sm-1">
                    <div class="checkbox">
                        <label ng-if="showThis">
                            <input type="checkbox" name="" ng-model="voting.autoAbstain" ng-true-value="1" ng-false-value="0" id=""/>
                        </label>
                        <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': voting.autoAbstain == 1,'fa-square-o': voting.autoAbstain != 1 }"></i>
                        <div ng-show="errors.autoAbstain" class="help-inline text-danger form-control-static">@{{errors.autoAbstain.toString()}}</div>
                    </div>
                </div>
                <div class="input-group col-sm-2" ng-if="voting.autoAbstain && showThis">
                    <button type="button" class="btn btn-default form-control form-inline" ng-click="abstainsModal()"> @lang('voting.austAbstainSelect')</button>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="form-group" ng-class="{error: voting.config.showAbstains.$invalid && !voting.config.showAbstains.$pristine}">
                <label for="showAbstains" ng-if="voting.autoAbstain" class="col-sm-2 control-label">@lang('voting.showAbstains')</label>
                <div class="col-sm-8">
                    <div class="checkbox">
                        <label ng-if="voting.autoAbstain && showThis">
                            <input type="checkbox" name="" ng-model="voting.config.showAbstains" ng-true-value="1" ng-false-value="0" id=""/>
                        </label>
                        <i class="fa" ng-if="!showThis && voting.autoAbstain" ng-class="{'fa-check-square-o': voting.config.showAbstains == 1,'fa-square-o': voting.config.showAbstains != 1 }"></i>
                    </div>
                    <div ng-show="errors.config.showAbstains" class="help-inline text-danger form-control-static">@{{errors.config.showAbstains.toString()}}</div>
                </div>
                <div class="clearfix"></div>
            </div>

            <h4 class="well well-sm">@lang('contest.dates')</h4>
            @include('admin.contests.voting-deadlines', array('start' => 'voting.start_at', 'deadline1' => 'voting.finish_at', 'deadline2' => 'voting.finish_at2'))

            <div class="clearfix"></div>

            <h4 class="well well-sm">@lang('contest.VotingType')</h4>

            <div class="form-group">
                <label class="col-sm-2 control-label">@lang('contest.showVotingToolEntriesList')</label>
                <div class="col-sm-8">
                    <div class="checkbox">
                        <label ng-if="showThis">
                            <input type="checkbox" name="" ng-model="voting.config.showVotingTool" id="" ng-true-value="1" ng-false-value="0"/>
                        </label>
                        <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': voting.config.showVotingTool == 1,'fa-square-o': voting.config.showVotingTool != 1 }"></i>
                    </div>
                </div>
            </div>

            <div class="form-group" ng-class="{error: voting.config.oxoMeeting.$invalid && !voting.config.oxoMeeting.$pristine}">
                <label for="oxoMeeting" class="col-sm-2 control-label">@lang('voting.oxoMeeting')</label>
                <div class="row">
                    <div class="col-sm-8">
                        <span class="checkbox">
                        <label ng-if="showThis">
                            <input type="checkbox" name="" ng-model="voting.config.oxoMeeting" id="" ng-true-value="1" ng-false-value="0"/>
                        </label>
                        <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': voting.config.oxoMeeting == 1,'fa-square-o': voting.config.oxoMeeting != 1 }"></i>
                            <span ng-if="voting.config.oxoMeeting === 1">
                                <span class="input-group" ng-if="showThis">
                                    @lang('voting.oxoMeetServer')
                                    <input type="text" class="form-control form-inline" ng-model="voting.config.oxoMeetServer">
                                </span>
                                <span ng-if="!showThis">
                                    <b >Server: </b> @{{ voting.config.oxoMeetServer ? voting.config.oxoMeetServer : "default" }}
                                </span>
                                <b>| Sala:</b> @{{ voting.config.oxoMeetingLink }}
                                <b>| Password:</b> @{{ voting.config.oxoMeetingPassword }}
                                <span ng-if="voting.config.oxoMeetingLink"></span>
                            </span>
                        </span>
                    </div>
                    <div class="col-sm-offset-2 col-sm-8">
                        <span class="input-group" ng-if="voting.config.oxoMeeting && showThis">
                            @lang('voting.oxoMeetModerators')
                            <input type="text" class="form-control form-inline" ng-model="voting.config.oxoMeetModerators">
                        </span>
                    </div>
                    <div class="col-sm-offset-2 col-sm-8">
                        <span ng-if="voting.config.oxoMeeting && !showThis" class="form-control-static">
                            <b> * Moderadores: </b> @{{ voting.config.oxoMeetModerators }}
                        </span>
                    </div>
                </div>

                <div ng-show="errors.oxoMeeting" class="help-inline text-danger form-control-static">@{{errors.public.toString()}}</div>
                <div class="clearfix"></div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">@lang('contest.showDefaultEntriesList')</label>
                <div class="col-sm-8">
                    <div class="checkbox">
                        <label ng-if="showThis">
                            <input type="checkbox" name="" ng-model="voting.config.showDefaultEntries" id="" ng-true-value="1" ng-false-value="0"/>
                        </label>
                        <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': voting.config.showDefaultEntries == 1,'fa-square-o': voting.config.showDefaultEntries != 1 }"></i>
                    </div>
                </div>
            </div>
            <div class="form-group"  ng-class="{error: errors.name}">
                <label for="inputType" class="col-sm-2 control-label">@lang('contest.VotingType')</label>
                <div class="col-sm-8 col-md-6 col-lg-4">
                    <select ng-model="voting.vote_type" id="inputType" class="form-control form-inline" ng-if="showThis">
                        <option ng-repeat="voteType in voteTypes" value="@{{voteType.id}}" ng-selected="voteType.id == voting.vote_type" required>@{{voteType.label}}</option>
                    </select>
                    <div class="form-control-static" ng-if="!showThis">
                        <span ng-repeat="voteType in voteTypes" ng-if="voteType.id == voting.vote_type">@{{voteType.label}}</span>
                    </div>
                </div>
                <div ng-show="errors.vote_type" class="help-inline text-danger form-control-static">@{{errors.vote_type.toString()}}</div>
            </div>

            <div ng-switch="voting.vote_type">
                <div ng-switch-when="{{VotingSession::AVERAGE}}" ng-init="!voting.config ? voting.config = {} : {}">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('contest.VoteFromTo')</label>
                        <div class="col-sm-8 col-md-6 col-lg-4">
                            <span  ng-if="showThis">
                            @lang('contest.VoteFrom')
                                <input type="number" min="0" max="@{{ voting.config.max - 1 }}" data-ng-model="voting.config.min" class="form-control form-inline">
                                @lang('contest.VoteTo')
                                <input type="number" min="@{{ voting.config.min + 1 }}" max="100" data-ng-model="voting.config.max" class="form-control form-inline">
                                @lang('contest.VoteSteps')
                                <select ng-model="voting.config.step" class="form-control form-inline">
                                    <option value="1">1</option>
                                    <option value="0.5">0.5</option>
                                    <option value="0.1">0.1</option>
                                </select>
                                @{{ voting.config.step == 1 ? "@lang('contest.VoteStepsPoint')" : "@lang('contest.VoteStepsPoints')" }}
                            </span>
                            <div class="form-control-static" ng-if="!showThis">
                                @lang('contest.VoteFrom') @{{voting.config.min}}
                                @lang('contest.VoteTo') @{{voting.config.max}}
                                @lang('contest.VoteSteps') @{{voting.config.step}}
                                @{{ voting.config.step == 1 ? "@lang('contest.VoteStepsPoint')" : "@lang('contest.VoteStepsPoints')" }}
                            </div>
                        </div>
                        <div ng-show="errors.vote_type" class="help-inline text-danger form-control-static">@{{errors.vote_type.toString()}}</div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('contest.VoteAllowAbstain')</label>
                        <div class="col-sm-8">
                            <div class="checkbox">
                                <label ng-if="showThis">
                                    <input type="checkbox" name="" ng-model="voting.config.abs" id=""/>
                                </label>
                                <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': voting.config.abs == 1,'fa-square-o': voting.config.abs != 1 }"></i>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('contest.VoteMinVotes')</label>
                        <div class="col-sm-8">
                            <div class="checkbox">
                                <span ng-if="showThis">
                                    <input type="number" min="0" data-ng-model="voting.config.minvotes" class="form-control form-inline">
                                    <select ng-model="voting.config.minvotesunit" class="form-control form-inline">
                                        <option value="{{Vote::MIN_VOTES_PERC}}">@lang('contest.VoteMinVotesPerc')</option>
                                        <option value="{{Vote::MIN_VOTES_JUDGES}}">@lang('contest.VoteMinVotesJudges')</option>
                                    </select>
                                </span>
                                <span ng-if="!showThis">
                                    @{{ voting.config.minvotes }}
                                    @{{ {'{{{Vote::MIN_VOTES_PERC}}}' : '@lang('contest.VoteMinVotesPerc')','{{{Vote::MIN_VOTES_JUDGES}}}' : '@lang('contest.VoteMinVotesJudges')'} | echoswitch : voting.config.minvotesunit }}
                                </span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('contest.VoteCriteria')</label>
                        <div class="col-sm-8">
                            <div class="checkbox">
                                <label ng-if="showThis">
                                    <input type="checkbox" name="" ng-model="voting.config.usecriteria" id=""/>
                                </label>
                                <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': voting.config.usecriteria == 1,'fa-square-o': voting.config.usecriteria != 1 }"></i>
                            </div>
                            <div ng-if="voting.config.usecriteria">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-control-static">
                                            <strong>
                                                <div class="row">
                                                    <div class="col-sm-7">
                                                        @lang('contest.VoteCriteriaName')
                                                    </div>
                                                    <div class="col-sm-4">
                                                        @lang('contest.VoteCriteriaWeight')
                                                    </div>
                                                </div>
                                            </strong>
                                        </div>
                                        <div class="row" data-ng-repeat="criterio in voting.config.criteria">
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control col-md-3" placeholder="@lang('contest.VoteCriteriaName')" required ng-model="criterio.name" ng-if="showThis">
                                                <div class="form-control-static" ng-if="!showThis">@{{ criterio.name }}</div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="input-group" ng-if="showThis">
                                                    <input type="text" class="form-control" placeholder="@lang('contest.VoteCriteriaWeight')" ng-model="criterio.weight" ng-if="showThis">
                                                    <span class="input-group-addon">%</span>
                                                </div>
                                                <div class="form-control-static" ng-if="!showThis">@{{ criterio.weight }} %</div>
                                            </div>
                                            <div class="col-sm-1">
                                                <button type="button" data-ng-click="removeCriteria(criterio)" ng-if="showThis" class="btn btn-primary"><i class="fa fa-close"></i></button>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <button type="button" data-ng-click="addCriteria()" ng-if="showThis" class="btn btn-info"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('contest.VoteSelector')</label>
                        <div class="col-sm-8">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-control-static">
                                        <strong>
                                            <div class="row">
                                                <div class="col-sm-7">
                                                    @lang('contest.VoteSelectorName')
                                                </div>
                                                <div class="col-sm-4">
                                                    @lang('contest.VoteSelectorType')
                                                </div>
                                            </div>
                                        </strong>
                                    </div>
                                    <div class="row" data-ng-repeat="selector in voting.config.extra">
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control col-md-3" placeholder="@lang('contest.VoteSelectorName')" required ng-model="selector.name" ng-if="showThis">
                                            <div class="form-control-static" ng-if="!showThis">@{{ selector.name }}</div>
                                        </div>
                                        <div class="col-sm-4">
                                            <select ng-if="showThis" ng-model="selector.type" class="form-control form-inline" >
                                                <option value="{{Vote::EXTRA_CHECKBOX}}">@lang('contest.VoteSelectorCheckbox')</option>
                                                <option value="{{Vote::EXTRA_TEXTAREA}}">@lang('contest.VoteSelectorTextarea')</option>
                                            </select>
                                            <div class="form-control-static" ng-if="!showThis">
                                                @{{ {'{{{Vote::EXTRA_CHECKBOX}}}' : '@lang('contest.VoteSelectorCheckbox')','{{{Vote::EXTRA_TEXTAREA}}}' : '@lang('contest.VoteSelectorTextarea')'} | echoswitch : selector.type }}
                                            </div>
                                        </div>
                                        <div class="col-sm-1">
                                            <button type="button" data-ng-click="removeSelector(selector)" ng-if="showThis" class="btn btn-primary"><i class="fa fa-close"></i></button>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <button type="button" data-ng-click="addSelector()" ng-if="showThis" class="btn btn-info"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('contest.VotingPreview')</label>
                        <div class="col-sm-8">
                            <div class="form-control-static">
                                <vote-tool vote-session="voting" my-vote="testVote"></vote-tool>
                            </div>
                        </div>
                    </div>
                </div>

                <div ng-switch-when="{{VotingSession::METAL}}" ng-init="!voting.config ? voting.config = {} : {}">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('contest.showResultsAdmins')</label>
                        <div class="col-sm-8">
                            <div class="checkbox">
                                <label ng-if="showThis">
                                    <input type="checkbox" name="" ng-model="voting.config.showResultsAdmins" id=""/>
                                </label>
                                <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': voting.config.showResultsAdmins == 1,'fa-square-o': voting.config.showResultsAdmins != 1 }"></i>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('contest.guidedVoteSession')</label>
                        <div class="col-sm-8">
                            <div class="checkbox">
                                <label ng-if="showThis">
                                    <input type="checkbox" name="" ng-model="voting.config.guidedVote" id=""/>
                                </label>
                                <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': voting.config.guidedVote == 1,'fa-square-o': voting.config.guidedVote != 1 }"></i>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('contest.awards')</label>
                        <div class="col-sm-8">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-control-static">
                                        <strong>
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    @lang('contest.VoteSelectorName')
                                                </div>
                                                <div class="col-sm-2">
                                                    @lang('contest.score')
                                                </div>
                                                <div class="col-sm-2">
                                                    @lang('contest.color')
                                                </div>
                                                <div class="col-sm-2">
                                                    @lang('voting.countPerCategory')
                                                </div>
                                            </div>
                                        </strong>
                                    </div>
                                    <div class="row" data-ng-repeat="selector in voting.config.extra">
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control col-md-3" placeholder="@lang('contest.VoteSelectorName')" required ng-model="selector.name" ng-if="showThis">
                                            <div class="form-control-static" ng-if="!showThis">@{{ selector.name }}</div>
                                        </div>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control col-md-3" placeholder="@lang('contest.score')" required ng-model="selector.score" ng-if="showThis">
                                            <div class="form-control-static" ng-if="!showThis">@{{ selector.score }}</div>
                                        </div>
                                        <div class="col-sm-2">
                                            <input type="color" value="#ff0000" style="height:37px" ng-model="selector.color" ng-if="showThis">
                                            <input type="color" value="#ff0000" style="height:37px" ng-model="selector.color" ng-if="!showThis" disabled>
                                            <div class="form-control-static" ng-if="!showThis">
                                                @{{ {'{{{Vote::EXTRA_CHECKBOX}}}' : '@lang('contest.VoteSelectorCheckbox')','{{{Vote::EXTRA_TEXTAREA}}}' : '@lang('contest.VoteSelectorTextarea')'} | echoswitch : selector.type }}
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control col-md-3" placeholder="0" required ng-model="selector.countPerCategory" ng-if="showThis">
                                            <div class="form-control-static" ng-if="!showThis">@{{ selector.countPerCategory }}</div>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="button" data-ng-click="removeSelector(selector)" ng-if="showThis" class="btn btn-primary"><i class="fa fa-close"></i></button>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <button type="button" data-ng-click="addSelector()" ng-if="showThis" class="btn btn-info"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('contest.VotingPreview')</label>
                        <div class="col-sm-8">
                            <div class="form-control-static">
                                <vote-tool vote-session="voting" my-vote="testVote"></vote-tool>
                            </div>
                        </div>
                    </div>
                </div> <!-- Fin de METALES -->

                <div ng-switch-when="{{VotingSession::VERITRON}}" ng-init="!voting.config ? voting.config = {} : {}">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('contest.VoteFromTo')</label>
                        <div class="col-sm-8 col-md-6 col-lg-4">
                            <span  ng-if="showThis">
                            @lang('contest.VoteFrom')
                                <input type="number" min="0" max="@{{ voting.config.max - 1 }}" data-ng-model="voting.config.min" class="form-control form-inline">
                                @lang('contest.VoteTo')
                                <input type="number" min="@{{ voting.config.min + 1 }}" max="10" data-ng-model="voting.config.max" class="form-control form-inline">
                                @lang('contest.VoteSteps')
                                <select ng-model="voting.config.step" class="form-control form-inline">
                                    <option value="1">1</option>
                                    <option value="0.5">0.5</option>
                                    <option value="0.1">0.1</option>
                                </select>
                                @{{ voting.config.step == 1 ? "@lang('contest.VoteStepsPoint')" : "@lang('contest.VoteStepsPoints')" }}
                            </span>
                            <div class="form-control-static" ng-if="!showThis">
                                @lang('contest.VoteFrom') @{{voting.config.min}}
                                @lang('contest.VoteTo') @{{voting.config.max}}
                                @lang('contest.VoteSteps') @{{voting.config.step}}
                                @{{ voting.config.step == 1 ? "@lang('contest.VoteStepsPoint')" : "@lang('contest.VoteStepsPoints')" }}
                            </div>
                        </div>
                        <div ng-show="errors.vote_type" class="help-inline text-danger form-control-static">@{{errors.vote_type.toString()}}</div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('contest.VoteAllowAbstain')</label>
                        <div class="col-sm-8">
                            <div class="checkbox">
                                <label ng-if="showThis">
                                    <input type="checkbox" name="" ng-model="voting.config.abs" id=""/>
                                </label>
                                <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': voting.config.abs == 1,'fa-square-o': voting.config.abs != 1 }"></i>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('contest.VoteAllowComments')</label>
                        <div class="col-sm-8">
                            <div class="checkbox">
                                <label ng-if="showThis">
                                    <input type="checkbox" name="" ng-model="voting.config.comments" id=""/>
                                </label>
                                <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': voting.config.comments == 1,'fa-square-o': voting.config.comments != 1 }"></i>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('contest.VoteMinVotes')</label>
                        <div class="col-sm-8">
                            <div class="checkbox">
                                <span ng-if="showThis">
                                    <input type="number" min="0" data-ng-model="voting.config.minvotes" class="form-control form-inline">
                                    <select ng-model="voting.config.minvotesunit" class="form-control form-inline">
                                        <option value="{{Vote::MIN_VOTES_PERC}}">@lang('contest.VoteMinVotesPerc')</option>
                                        <option value="{{Vote::MIN_VOTES_JUDGES}}">@lang('contest.VoteMinVotesJudges')</option>
                                    </select>
                                </span>
                                <span ng-if="!showThis">
                                    @{{ voting.config.minvotes }}
                                    @{{ {'{{{Vote::MIN_VOTES_PERC}}}' : '@lang('contest.VoteMinVotesPerc')','{{{Vote::MIN_VOTES_JUDGES}}}' : '@lang('contest.VoteMinVotesJudges')'} | echoswitch : voting.config.minvotesunit }}
                                </span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('contest.VoteSelector')</label>
                        <div class="col-sm-8">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-control-static">
                                        <strong>
                                            <div class="row">
                                                <div class="col-sm-7">
                                                    @lang('contest.VoteSelectorName')
                                                </div>
                                                <div class="col-sm-4">
                                                    @lang('contest.VoteSelectorType')
                                                </div>
                                            </div>
                                        </strong>
                                    </div>
                                    <div class="row" data-ng-repeat="selector in voting.config.extra">
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control col-md-3" placeholder="@lang('contest.VoteSelectorName')" required ng-model="selector.name" ng-if="showThis">
                                            <div class="form-control-static" ng-if="!showThis">@{{ selector.name }}</div>
                                        </div>
                                        <div class="col-sm-4">
                                            <select ng-if="showThis" ng-model="selector.type" class="form-control form-inline" >
                                                <option value="{{Vote::EXTRA_CHECKBOX}}">@lang('contest.VoteSelectorCheckbox')</option>
                                                <option value="{{Vote::EXTRA_TEXTAREA}}">@lang('contest.VoteSelectorTextarea')</option>
                                            </select>
                                            <div class="form-control-static" ng-if="!showThis">
                                                @{{ {'{{{Vote::EXTRA_CHECKBOX}}}' : '@lang('contest.VoteSelectorCheckbox')','{{{Vote::EXTRA_TEXTAREA}}}' : '@lang('contest.VoteSelectorTextarea')'} | echoswitch : selector.type }}
                                            </div>
                                        </div>
                                        <div class="col-sm-1">
                                            <button type="button" data-ng-click="removeSelector(selector)" ng-if="showThis" class="btn btn-primary"><i class="fa fa-close"></i></button>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <button type="button" data-ng-click="addSelector()" ng-if="showThis" class="btn btn-info"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('contest.VotingPreview')</label>
                        <div class="col-sm-8">
                            <div class="form-control-static">
                                <vote-tool vote-session="voting" my-vote="testVote"></vote-tool>
                            </div>
                        </div>
                    </div>
                </div>
                <div ng-switch-when="{{VotingSession::YESNO}}" ng-init="!voting.config ? voting.config = {} : {}">
                    <div class="form-group">
                        <div class="col-sm-2 control-label">
                            <label>@lang('voting.yesPerCategory')</label>
                        </div>
                        <div class="col-sm-2">
                            <div ng-if="showThis">
                                <input type="number" class="form-control" ng-model="voting.config.yesPerCategory" min="0">
                            </div>
                            <div ng-if="!showThis">
                                @{{ voting.config.yesPerCategory }}
                            </div>
                            <span ng-if="showThis">@lang('voting.ceroForAll')</span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('contest.VotingPreview')</label>
                        <div class="col-sm-8">
                            <div class="form-control-static">
                                <vote-tool vote-session="voting" my-vote="testVote"></vote-tool>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>
            <button type="button" class="btn btn-md btn-default" ng-init="useCategories = true" ng-click="useCategories = true; useShortList = false;"> <h4>@lang('contest.categories')</h4></button>
            <button type="button" class="btn btn-md btn-default" ng-init="useShortList = false" ng-click="useShortList = true; useCategories = false;"> <h4>@lang('voting.inShortlist')</h4></button>
            <span ng-if="useCategories">
                <h4 class="well well-sm">@lang('contest.entryCategories')</h4>
                <div class="form-group">
                    <label class="col-sm-2 control-label">@lang('contest.entryCategories')</label>
                    <div class="col-sm-8">
                        <div class="form-control-static">
                            <ul class="category-tree">
                                <li ng-if="showThis" ng-repeat="category in categories track by $index" ng-include="'categoryTreeVoteConfig.html'" onload="selectable=true;modelList = voteCategories;"></li>
                                <li ng-if="!showThis" ng-repeat="category in categories track by $index" ng-include="'selectedCategoryTree.html'" onload="selectable=false;modelList = voteCategories;"></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </span>
            <span ng-if="useShortList">
                <h4 class="well well-sm">@lang('voting.inShortlist')</h4>
                <div class="form-group">
                    <label class="col-sm-2 control-label"> @lang('voting.selected')</label>
                    <div class="col-sm-8">
                        <div class="form-control-static">
                            <div ng-repeat="voteSessionShortlist in listOfShortLists">
                                <input ng-if="showThis" type="checkbox" name="" checklist-model="modelListShortList" checklist-value="voteSessionShortlist.id" ng-change="fromShortList(voteSessionShortlist.id)" id=""/>
                                <span ng-if="showThis"> @{{ voteSessionShortlist.name }} </span>
                                <span ng-if="!showThis && modelListShortList.indexOf(voteSessionShortlist.id) != -1"> @{{ voteSessionShortlist.name }} </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"> @lang('voting.editShortlist')</label>
                    <div class="col-sm-8">
                        <div class="form-control-static">
                            <input ng-if="showThis" type="checkbox" name="" ng-model="voting.config.editShortlist"  id=""/>
                            <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': voting.config.editShortlist == 1,'fa-square-o': voting.config.editShortlist != 1 }"></i>
                        </div>
                    </div>
                </div>
            </span>
        </uib-tab>
        <uib-tab index="1" ng-if="!!voting.code">
            <uib-tab-heading>
                <i class="fa fa-gavel"></i> @lang('contest.VoteJudges')
            </uib-tab-heading>
                    <div class="form-control-static">
                        <div class="alert alert-warning" ng-if="voting.voting_users.length == 0">
                            <i class="fa fa-info-circle"></i>
                            @lang('contest.VoteNoJudges')
                        </div>
                        <div class="row">
                            <div class="col-lg-8 col-md-8 text-left">
                                <div class="filter-buttons">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default" ng-click="toggleFilterBy()">
                                            <span>@{{ countJudges() }}</span>
                                            <div class="filter-label">@lang('contest.total') </div>
                                        </button>
                                        <button type="button" class="btn" ng-class="{'btn-default': statusFilters.indexOf({{ VotingUser::PENDING_NOTIFICATION }}) == -1, 'btn-primary':statusFilters.indexOf({{ VotingUser::PENDING_NOTIFICATION }}) != -1}" ng-click="toggleFilterBy({{ VotingUser::PENDING_NOTIFICATION }})">
                                            <span ng-class="{'label label-primary label-as-badge':statusFilters.indexOf({{ VotingUser::PENDING_NOTIFICATION }}) == -1,'badge':statusFilters.indexOf({{ VotingUser::PENDING_NOTIFICATION }}) != -1}">@{{ countJudges({{{VotingUser::PENDING_NOTIFICATION}}}) }}</span>
                                            <div class="filter-label">@lang('voting.pendingNotificationFilter')</div>
                                        </button>
                                        <button type="button" class="btn" ng-class="{'btn-default': statusFilters.indexOf({{ VotingUser::NOTIFIED }}) == -1, 'btn-warning':statusFilters.indexOf({{ VotingUser::NOTIFIED }}) != -1}" ng-click="toggleFilterBy({{ VotingUser::NOTIFIED }})">
                                            <span ng-class="{'label label-warning label-as-badge':statusFilters.indexOf({{ VotingUser::NOTIFIED }}) == -1,'badge':statusFilters.indexOf({{ VotingUser::NOTIFIED }}) != -1}">@{{ countJudges({{{ VotingUser::NOTIFIED }}}) }}</span>
                                            <div class="filter-label">@lang('voting.notifiedFilter')</div>
                                        </button>
                                        <button type="button" class="btn" ng-class="{'btn-default': statusFilters.indexOf({{ VotingUser::RESEND }}) == -1, 'btn-warning':statusFilters.indexOf({{ VotingUser::RESEND }}) != -1}" ng-click="toggleFilterBy({{ VotingUser::RESEND }})">
                                            <span ng-class="{'label label-warning label-as-badge':statusFilters.indexOf({{ VotingUser::RESEND }}) == -1,'badge':statusFilters.indexOf({{ VotingUser::RESEND }}) != -1}">@{{ countJudges({{{ VotingUser::RESEND }}}) }}</span>
                                            <div class="filter-label">@lang('voting.resendFilter')</div>
                                        </button>
                                        <button type="button" class="btn" ng-class="{'btn-default': statusFilters.indexOf({{ VotingUser::VISITED_PAGE }}) == -1, 'btn-info':statusFilters.indexOf({{ VotingUser::VISITED_PAGE }}) != -1}" ng-click="toggleFilterBy({{ VotingUser::VISITED_PAGE }})">
                                            <span ng-class="{'label label-info label-as-badge':statusFilters.indexOf({{ VotingUser::VISITED_PAGE }}) == -1,'badge':statusFilters.indexOf({{ VotingUser::VISITED_PAGE }}) != -1}">@{{ countJudges({{{ VotingUser::VISITED_PAGE }}}) }}</span>
                                            <div class="filter-label">@lang('voting.visitedPageFilter')</div>
                                        </button>
                                        <button type="button" class="btn" ng-class="{'btn-default': statusFilters.indexOf({{ VotingUser::ACCEPTED }}) == -1, 'btn-success':statusFilters.indexOf({{ VotingUser::ACCEPTED }}) != -1}" ng-click="toggleFilterBy({{ VotingUser::ACCEPTED }})">
                                            <span ng-class="{'label label-success label-as-badge':statusFilters.indexOf({{ VotingUser::ACCEPTED }}) == -1,'badge':statusFilters.indexOf({{ VotingUser::ACCEPTED }}) != -1}">@{{ countJudges({{{ VotingUser::ACCEPTED }}}) }}</span>
                                            <div class="filter-label">@lang('voting.accepted')</div>
                                        </button>
                                        <button type="button" class="btn" ng-class="{'btn-default': statusFilters.indexOf({{ VotingUser::REJECTED }}) == -1, 'btn-danger':statusFilters.indexOf({{ VotingUser::REJECTED }}) != -1}" ng-click="toggleFilterBy({{ VotingUser::REJECTED }})">
                                            <span ng-class="{'label label-danger label-as-badge':statusFilters.indexOf({{ VotingUser::REJECTED }}) == -1,'badge':statusFilters.indexOf({{ VotingUser::REJECTED }}) != -1}">@{{ countJudges({{{ VotingUser::REJECTED }}}) }}</span>
                                            <div class="filter-label">@lang('voting.rejectedInvitationFilter')</div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 text-right">
                                <uib-progressbar class="@{{ totalProgress == 100 ? '' : 'progress-striped' }} progress-total active" value="totalProgress" type="@{{ totalProgress == 100 ? 'success' : 'warning' }}"><i>@{{ totalProgress }}%</i></uib-progressbar>
                                <i class="fa fa-spin fa-spinner" data-ng-show="addingGroup"></i>
                                <div class="filter-buttons">
                                <div class="btn-group">
                                    <a type="button" class="btn btn-sm btn-primary col-lg-4 col-md-4" ng-href="<?=url('/')?>/@{{ contest.code }}/exportJudges/@{{ voting.code }}"><!--ng-click="exportJudges()"-->
                                        <i class="fa fa-download"></i> @lang('voting.exportJudges')
                                    </a>
                                    <button type="button" class="btn btn-sm btn-success col-lg-4 col-md-4" data-ng-click="addGroup()" ng-disabled="!showThis || addingGroup">
                                        <i class="fa fa-users"></i> @lang('voting.addGroup')
                                    </button>
                                    <button type="button" class="btn btn-sm btn-info col-lg-4 col-md-4" data-ng-click="sendInvitations()" ng-disabled="(voting.voting_users|filter:{status:{{VotingUser::PENDING_NOTIFICATION}}}).length == 0">
                                        <i class="fa fa-send"></i> @lang('voting.sendInvitations')
                                    </button>
                                </div>
                                </div>
                                <label><input type="checkbox" ng-model="addJudges.autoUpdateJudges" />
                                    @lang('contest.VoteAutoUpdateJudges')
                                </label>
                                <button type="button" class="btn btn-md btn-default" ng-click="updateJudges()">
                                    <i class="fa fa-refresh"></i>

                                </button>
                            </div>
                        </div>
                        <input type="text" class="form-control form-inline" data-ng-model="judgesPagination.query" placeholder="@lang('voting.judgesfilter')">
                        <div class="btn-group" ng-if="voting.voting_groups.length">
                            <button type="button" class="btn btn-default" ng-click="expandAllGroups(voting.voting_groups)" tooltip-placement="bottom" uib-tooltip="@lang('contest.expandAll')"><i class="fa fa-angle-double-down"></i></button>
                            <button type="button" class="btn btn-default" ng-click="closeAllGroups(voting.voting_groups)" tooltip-placement="bottom" uib-tooltip="@lang('contest.collapseAll')"><i class="fa fa-angle-double-up"></i></button>
                        </div>
                        <a class="btn btn-right btn-default" ng-href="<?=url('/')?>/@{{ contest.code }}/voting/@{{ voting.code }}/static" target="_blank" >
                            <i class="fa fa-external-link"></i> @lang('voting.viewStatic')
                        </a>
                        <div class="clearfix"></div>
                        <br>

                        <div class="judges-groups" ng-if="voting.voting_groups.length">
                            <div>
                                <ul class="category-list">
                                    <!--<li ng-repeat="jgroup in voting.voting_groups" ng-include="'voting-group.html'" ng-if="!(judgesPagination.query.length > 0 && filteredGroupJudges[jgroup.id ? jgroup.id : 0].length == 0 || !filteredGroupJudges[jgroup.id ? jgroup.id : 0])" onload="group = jgroup;"></li>
                                    <li ng-include="'voting-group.html'" onload="group = ungroupedJudges;" ng-if="!(judgesPagination.query.length > 0 && filteredGroupJudges[ungroupedJudges.id ? ungroupedJudges.id : 0].length == 0 || !filteredGroupJudges[ungroupedJudges.id ? ungroupedJudges.id : 0])"></li>-->
                                    <li ng-repeat="jgroup in voting.voting_groups" ng-include="'voting-group.html'" onload="group = jgroup;"></li>
                                    <li ng-include="'voting-group.html'" onload="group = ungroupedJudges;" ></li>
                                </ul>
                            </div>
                        </div>
                        <div ng-if="!voting.voting_groups || voting.voting_groups.length == 0">
                            <div ng-include="'judges-list.html'" onload="group = ungroupedJudges;"></div>
                        </div>

                    </div>
        </uib-tab>
        <uib-tab index="1" ng-if="!!voting.code">
            <uib-tab-heading>
                <i class="fa fa-trophy"></i> @lang('contest.VoteResults')
            </uib-tab-heading>
            <div ng-if="loadingResults">
                <div class="col-sm-12 text-center">
                    <div class="spinner">
                        <div class="rect1"></div>
                        <div class="rect2"></div>
                        <div class="rect3"></div>
                        <div class="rect4"></div>
                        <div class="rect5"></div>
                    </div>
                </div>
            </div>
            <div ng-if="!loadingResults">
            <div class="row entries-header">
                <div class="col-sm-12 col-lg-12" style="padding-top: 10px;!important">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 text-left">
                            <div class="filter-buttons">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default" ng-click="toggleEntryFilterBy()">
                                        <span>@{{ countEntries() }}</span>
                                        <div class="filter-label">@lang('contest.total') </div>
                                    </button>
                                    <button type="button" class="btn" ng-class="{'btn-default': votingEntriesFilters.indexOf({{ Entry::NO_VOTED }}) == -1, 'btn-primary':votingEntriesFilters.indexOf({{ Entry::NO_VOTED }}) != -1}" ng-click="toggleEntryFilterBy({{ Entry::NO_VOTED }})">
                                        <span ng-class="{'label label-primary label-as-badge':votingEntriesFilters.indexOf({{ Entry::NO_VOTED }}) == -1,'badge':votingEntriesFilters.indexOf({{ Entry::NO_VOTED }}) != -1}">@{{ countEntries({{{ Entry::NO_VOTED }}}) }}</span>
                                        <div class="filter-label">@lang('voting.notVoted')</div>
                                    </button>
                                    <button type="button" class="btn" ng-class="{'btn-default': votingEntriesFilters.indexOf({{ Entry::VOTED }}) == -1, 'btn-info':votingEntriesFilters.indexOf({{ Entry::VOTED }}) != -1}" ng-click="toggleEntryFilterBy({{ Entry::VOTED }})">
                                        <span ng-class="{'label label-info label-as-badge':votingEntriesFilters.indexOf({{ Entry::VOTED }}) == -1,'badge':votingEntriesFilters.indexOf({{ Entry::VOTED }}) != -1}">@{{ countEntries({{{ Entry::VOTED }}}) }}</span>
                                        <div class="filter-label">@lang('voting.voted')</div>
                                    </button>
                                    <button ng-if="voting.vote_type == {{ VotingSession::AVERAGE }} || voting.vote_type == {{ VotingSession::VERITRON }}" type="button" class="btn" ng-class="{'btn-default': votingEntriesFilters.indexOf({{ Entry::ABSTAIN }}) == -1, 'btn-warning':votingEntriesFilters.indexOf({{ Entry::ABSTAIN }}) != -1}" ng-click="toggleEntryFilterBy({{ Entry::ABSTAIN }})">
                                        <span ng-class="{'label label-warning label-as-badge':votingEntriesFilters.indexOf({{ Entry::ABSTAIN }}) == -1,'badge':votingEntriesFilters.indexOf({{ Entry::ABSTAIN }}) != -1}">@{{ countEntries({{{ Entry::ABSTAIN }}}) }}</span>
                                        <div class="filter-label">@lang('voting.judgesabstentions')</div>
                                    </button>
                                </div>
                                <span class="btn-group">
                                    <button ng-if="voting.vote_type == {{ VotingSession::METAL }}" type="button" ng-repeat="metal in voting.config.extra" class="btn btn-default" style="background:@{{ dinamicEntriesFilters.indexOf(metal.name) != -1 ? metal.color : red}};font-weight:bold;" ng-click="dinamicEntriesFilter(metal.name)">
                                        <span class="badge">@{{ countEntries(metal.name, true) }}</span>
                                        <div class="filter-label">@{{ metal.name }}</div>
                                    </button>
                                </span>
                                <span class="btn-group" ng-if="voting.vote_type == {{ VotingSession::YESNO }}">
                                    <button type="button" class="btn" ng-click="yesNoFilters(true)" ng-class="{'btn-default':yesNoEntriesFilters.indexOf(true) == -1, 'btn-success': yesNoEntriesFilters.indexOf(true) != -1}">
                                        <span class="label label-success label-as-badge">@{{ countYesNoEntries(true) }}</span>
                                        <div class="filter-label"> @lang('voting.yes') </div>
                                    </button>
                                    <button type="button" class="btn" ng-click="yesNoFilters(false)" ng-class="{'btn-default':yesNoEntriesFilters.indexOf(false) == -1, 'btn-danger': yesNoEntriesFilters.indexOf(false) != -1}">
                                        <span class="label label-danger label-as-badge">@{{ countYesNoEntries(false) }}</span>
                                        <div class="filter-label"> @lang('voting.no') </div>
                                    </button>
                                    <button type="button" class="btn" ng-click="yesNoFilters(2)" ng-class="{'btn-default':yesNoEntriesFilters.indexOf(2) == -1, 'btn-info': yesNoEntriesFilters.indexOf(2) != -1}">
                                        <span class="label label-info label-as-badge">@{{ countYesNoEntries(2) }}</span>
                                        <div class="filter-label"> @lang('voting.tie') </div>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <input type="text" class="form-control form-inline" data-ng-model="pagination.query" ng-model-options="{debounce: 500}" placeholder="@lang('voting.resultsfilter')">
                            <div class="btn btn-default" ng-click="openRankingConfig()">
                            <i class="fa fa-download"></i> @lang('voting.ranking')
                            </div>
                            <div type="button" class="btn btn-primary" ng-click="selectAllShortlist()" ng-show="showThis"> Shortlist </div>
                        </div>
                        <div class="col-sm-6 text-right">

                            <label><input type="checkbox" ng-model="autoUpdateResults" />
                                @lang('contest.VoteAutoUpdateResults')</label>
                            <button type="button" class="btn btn-sm btn-default" ng-click="updateResults()">
                                <i class="fa fa-refresh"></i>
                            </button>
                            <!--<a class="btn btn-default" ng-href="<?=url('/')?>/@{{ contest.code }}/exportResults/@{{ voting.code }}" >-->
                            <a class="btn btn-default" ng-click="openExportModal()" uib-tooltip="@lang('contest.download.entriesList')" tooltip-placement="bottom">
                                <i class="fa fa-download"></i> <i class="fa fa-file-excel-o"></i>
                            </a>
                            <div class="btn-group" role="group">
                                <!--<button type="button" class="btn btn-default" ng-click="setClearView()" tooltip-placement="bottom">
                                    @{{ clearView ? '@lang('voting.detailedView')' : '@lang('voting.clearView')' }}
                                </button>-->
                                <button type="button" class="btn btn-default" ng-click="toggleListGrouped()" uib-tooltip="@lang('general.toggleGroupedView')" tooltip-placement="bottom">
                                    <i class="fa" ng-class="{'fa-bars': showGrouped, 'fa-folder': !showGrouped}"></i>
                                </button>
                                <button type="button" ng-if="showGrouped && !entryPerUser" class="btn btn-default" ng-click="expandAll()" tooltip-placement="bottom" uib-tooltip="@lang('contest.expandAll')"><i class="fa fa-angle-double-down"></i></button>
                                <button type="button" ng-if="showGrouped && !entryPerUser" class="btn btn-default" ng-click="collapseAll()" tooltip-placement="bottom" uib-tooltip="@lang('contest.collapseAll')"><i class="fa fa-angle-double-up"></i></button>
                            </div>
                            <button type="button" class="btn btn-default" ng-click="toggleListView()" uib-tooltip="@lang('general.toggleView')" tooltip-placement="bottom">
                                <i class="fa" ng-class="{'fa-th': listView == 'list', 'fa-align-justify': listView == 'thumbs'}"></i>
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false" uib-tooltip="@lang('general.sortBy')" tooltip-placement="left"><i class="fa" ng-class="{'fa-sort-alpha-desc':pagination.sortInverted,'fa-sort-alpha-asc':!pagination.sortInverted} "></i></button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li ng-class="{'active': pagination.sortBy == 'name'}">
                                        <a href="" ng-click="setSortBy('name')">
                                            <i class="fa" ng-if="pagination.sortBy == 'name'" ng-class="{'fa-sort-amount-asc':pagination.sortInverted, 'fa-sort-amount-desc':!pagination.sortInverted}"></i>
                                            @lang('general.sortBy.name')
                                        </a>
                                    </li>
                                    <li ng-class="{'active': pagination.sortBy == 'id'}">
                                        <a href="" ng-click="setSortBy('id')">
                                            <i class="fa" ng-if="pagination.sortBy == 'id'" ng-class="{'fa-sort-amount-asc':pagination.sortInverted, 'fa-sort-amount-desc':!pagination.sortInverted}"></i>
                                            @lang('general.sortBy.id')
                                        </a>
                                    </li>
                                    <li ng-class="{'active': pagination.sortBy == resultCustomSort}">
                                        <a href="" ng-click="setSortBy(resultCustomSort)">
                                            <i class="fa" ng-if="pagination.sortBy == resultCustomSort" ng-class="{'fa-sort-amount-asc':pagination.sortInverted, 'fa-sort-amount-desc':!pagination.sortInverted}"></i>
                                            @lang('general.sortBy.result')
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <script type="text/ng-template" id="entryResult.html">
                <div class="panel panel-default entry-result">
                    <div class="panel-heading">
                        <div class="row">
                            <div ng-class="{'col-sm-5':listView == 'list','col-sm-10':listView == 'thumbs'}" style="font-size:20px;">
                                <div class="col-xs-12">
                                    <a href="#/entry/@{{ entry.id }}">
                                        <span entry-card entry="entry" class=""></span>
                                    </a>
                                </div>
                                <div class="cats-list text-primary col-xs-8" ng-class="{'text-muted':listView == 'thumbs'}">
                                    <div ng-repeat="catid in entry.categories_id" ng-include="'categoryListVoting.html'" onload="voteSession = voting; category = catMan.GetCategory(catid); first=true; results=true;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </script>
            <div class="row">
            <div class="voting">
            <div class="results entries" ng-if="!showGrouped"  ng-class="listView">
                <span ng-repeat="eRow in entriesRows">
                    <span ng-repeat="entry in eRow">
                        <div class="entry" ng-include="'entryResult.html'" ng-class="{'col-xs-12': listView == 'list', 'col-md-3 col-sm-4 col-xs-12':listView == 'thumbs'}"></div>
                        <div class="clearfix visible-md visible-lg" ng-show="($index + 1) % 4 == 0"></div>
                        <div class="clearfix visible-sm visible-xs" ng-show="($index + 1) % 3 == 0"></div>
                    </span>
                </span>

                <span in-view="$inview && inViewLoadMoreEntries()" in-view-options="{offset: -100}" ng-if="!lastEntryShown">
                    <div class="col-sm-12 text-center">
                        <div class="spinner">
                            <div class="rect1"></div>
                            <div class="rect2"></div>
                            <div class="rect3"></div>
                            <div class="rect4"></div>
                            <div class="rect5"></div>
                        </div>
                    </div>
                </span>
            </div>
            <div class="col-sm-12 entries-categories-tree" ng-if="showGrouped">
                <ul ng-model="categories" class="category-list"><li ng-repeat="category in categories track by $index" ng-include="'category.html'"></li></ul>
            </div>
            </div>
            </div>
            </div>
        </uib-tab>
    </uib-tabset>
@endsection