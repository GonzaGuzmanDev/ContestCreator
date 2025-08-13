@extends('admin.contests.form', array('active' => 'inscriptions-list'))
@section('form')
    <h4 class="well well-sm">
        @lang('contest.inscriptions-list')
        {{--@if(!$superadmin)
            <i class="fa fa-question-circle text-info" popover="@lang('contest.explain.inscriptions-list')" popover-placement="right" popover-trigger="mouseenter" class="btn btn-default"></i>
        @endif--}}
    </h4>
    <div class="col-sm-9 col-lg-9">
        <div class="row">
            <div class="tab-pagination">
                <div>
                    <a href="#{{{ $superadmin ? '/contests/edit/@{{ contest.code }}/inscription' : '/admin/inscription/' }}}" class="btn btn-success"><i class="fa fa-plus"></i> @lang('contest.newInscription')</a>
                    <a class="btn btn-default" ng-href="<?=url('/')?>/@{{ contest.code }}/exportEntriesData" uib-tooltip="@lang('contest.download.entriesList')">
                        <i class="fa fa-download"></i>
                        <i class="fa fa-file-excel-o"></i>
                    </a>
                </div><br>
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
                    <input ng-model-options="{debounce: 500}" type="text" ng-model="pagination.query" class="form-control" placeholder="@lang('general.search')" focus-me="true">
                </div>
                <div class="btn-group">
                    <button type="button" class="btn" ng-class="{'btn-default':!pagination.owner, 'btn-success':pagination.owner}" ng-click="filterUsers({{Inscription::OWNER}})"> @lang('user.owner') </button>
                    <button type="button" class="btn" ng-class="{'btn-default':!pagination.inscriptor, 'btn-success':pagination.inscriptor}" ng-click="filterUsers({{Inscription::INSCRIPTOR}})"> @lang('contest.inscriptor') </button>

                    <div class="btn-group">
                        <button ng-if="inscriptionTypes.length > 0 && pagination.inscriptor == 1" class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ng-class="">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                            <li ng-repeat="inscriptionType in inscriptionTypes" ng-if="inscriptionType.role == {{Inscription::INSCRIPTOR}}">
                                <a ng-click="filterByType(inscriptionType.id)">
                                    <i class="fa fa-check" ng-if="filterType.indexOf(inscriptionType.id) != -1"></i>@{{ inscriptionType.name }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn" ng-class="{'btn-default':!pagination.judge, 'btn-success':pagination.judge}" ng-click="filterUsers({{Inscription::JUDGE}})"> @lang('user.judge') </button>

                        <button ng-if="inscriptionTypes.length > 0 && pagination.judge == 1" class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ng-class="">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                            <li ng-repeat="inscriptionType in inscriptionTypes" ng-if="inscriptionType.role == {{Inscription::JUDGE}}">
                                <a ng-click="filterByTypeJudge(inscriptionType.id)">
                                    <i class="fa fa-check" ng-if="filterJudgeType.indexOf(inscriptionType.id) != -1"></i>@{{ inscriptionType.name }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <button type="button" class="btn" ng-class="{'btn-default':!pagination.colaborator, 'btn-success':pagination.colaborator}" ng-click="filterUsers({{Inscription::COLABORATOR}})"> @lang('user.colaborator') </button>

                    <!--<button ng-if="inscriptionTypes.length > 0 && pagination.colaborator == 1" class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ng-class="">
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu3">
                        <li ng-repeat="inscriptionType in inscriptionTypes" ng-if="inscriptionType.role == {{Inscription::COLABORATOR}}"><a ng-click="filterByTypeCol(inscriptionType.id)"> @{{ inscriptionType.name }} </a></li>
                    </ul>-->
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <uib-pagination style="float:right;!important" boundary-links="true" total-items="pagination.total" items-per-page="pagination.perPage" max-size="7" rotate="false" ng-model="pagination.page" class="pagination-sm" ng-show="pagination.total/pagination.perPage > 1" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></uib-pagination><br>
        <br>
        <span style="float:right;!important" class="pagination-data" ng-show="dataLoaded">@{{((pagination.page-1) * pagination.perPage)+1}} @lang('general.to') @{{ pagination.shownMax }} @lang('general.of') @{{pagination.total}} @lang('general.results')</span>
    </div>
    <div class="clearfix"></div>
    <table class="table table-striped table-hover table-condensed">
        <thead>
            <tr>
                <th></th>
                <th>#</th>
                <th><a data-ng-click="changeOrder('first_name')">@lang('general.name') <i ng-show="pagination.orderBy == 'first_name'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                <th><a data-ng-click="changeOrder('last_name')">@lang('general.lastName') <i ng-show="pagination.orderBy == 'last_name'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                <th><a data-ng-click="changeOrder('email')">@lang('general.email') <i ng-show="pagination.orderBy == 'email'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                <th><a data-ng-click="changeOrder('role')">@lang('contest.inscriptionRole') <i ng-show="pagination.orderBy == 'role'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                <th><a data-ng-click="changeOrder('name')">@lang('contest.inscriptionTypeName') <i ng-show="pagination.orderBy == 'name'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                <th><a data-ng-click="changeOrder('entries_count')">@lang('contest.entries_count') <i ng-show="pagination.orderBy == 'entries_count'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                <th><a data-ng-click="changeOrder('deadline1_at')">@lang('contest.inscriptionDeadline1') <i ng-show="pagination.orderBy == 'deadline1_at'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                <th><a data-ng-click="changeOrder('deadline2_at')">@lang('contest.inscriptionDeadline2') <i ng-show="pagination.orderBy == 'deadline2_at'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="inscription in inscriptions">
                <td></td>
                <td><a href="#/admin/inscription/@{{inscription.id}}">@{{((pagination.page-1) * pagination.perPage)+$index+1}}</a></td>
                <td><a href="#/admin/inscription/@{{inscription.id}}">@{{inscription.first_name}}</a></td>
                <td><a href="#/admin/inscription/@{{inscription.id}}">@{{inscription.last_name}}</a></td>
                <td><a href="#/admin/inscription/@{{inscription.id}}">@{{inscription.email}}</a></td>
                <td><i class="fa" ng-class="{'fa-ticket': inscription.role == '{{Inscription::INSCRIPTOR}}', 'fa-user-circle-o': inscription.role == '{{Inscription::COLABORATOR}}', 'fa-user-circle': inscription.role == '{{Inscription::OWNER}}', 'fa-legal': inscription.role == '{{Inscription::JUDGE}}'}"></i> @{{allRoles[inscription.role]}}</td>
                <td>@{{inscription.name}}</td>
                <td>@{{inscription.entries_count}}</td>
                <td>@{{inscription.deadline1_at}}</td>
                <td>@{{inscription.deadline2_at}}</td>
                <td class="text-right">
                    @if($superadmin)
                        <a class="btn btn-info btn-xs" href="<?= url('/admin/loginAsInscription/')?>/@{{ inscription.id }}"><i class="fa fa-user"></i> @lang('general.loginAs')</a>
                    @endif
                    <a ng-hide="viewer" href="#{{{ $superadmin ? '/contests/edit/@{{contest.code}}/inscription/@{{inscription.id}}' : '/admin/inscription/@{{inscription.id}}' }}}" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> @lang('general.edit')</a>
                    <a ng-hide="viewer" class="btn btn-danger btn-xs" ng-click="delete(inscription)"><i class="fa fa-trash"></i> @lang('general.delete')</a>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !contestForm.$dirty">
        <span ng-bind-html="flash"></span>
    </div>
@endsection