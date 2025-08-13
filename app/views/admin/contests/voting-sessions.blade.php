@extends('admin.contests.form', array('active' => 'voting-sessions'))
@section('form')
    <h4 class="well well-sm">
        @lang('contest.voting-session')
    </h4>
    <div class="row">
    <div class="tab-pagination col-sm-6">
        <a href="#{{{ $superadmin ? '/contests/edit/@{{ contest.code }}/voting' : '/admin/voting-session/' }}}" class="btn btn-success"><i class="fa fa-plus"></i> @lang('contest.newVotingSession')</a>
        <!--<a href="#/contests/edit/@{{ contest.code }}/voting" class="btn btn-success"><i class="fa fa-plus"></i> @lang('contest.newVotingSession')</a>-->
        <input ng-model-options="{debounce: 500}" type="text" ng-model="pagination.query" class="form-control inline" placeholder="@lang('general.search')" focus-me="true">
    </div>
    <div class="col-sm-6">
        <uib-pagination style="float:right;!important" boundary-links="true" total-items="pagination.total" items-per-page="pagination.perPage" max-size="7" rotate="false" ng-model="pagination.page" class="pagination-sm" ng-show="pagination.total/pagination.perPage > 1" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></uib-pagination><br>
        <span style="float:right;!important" class="pagination-data" ng-show="dataLoaded">@{{((pagination.page-1) * pagination.perPage)+1}} @lang('general.to') @{{ pagination.shownMax }} @lang('general.of') @{{pagination.total}} @lang('general.results')</span>
    </div>
    </div>
    <div class="clearfix"></div>
    <table class="table table-striped table-hover table-condensed">
        <thead>
            <tr>
                <th></th>
                <th><a data-ng-click="changeOrder('name')">@lang('general.name') <i ng-show="pagination.orderBy == 'name'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                <th><a data-ng-click="changeOrder('vote_type')">@lang('contest.voteType') <i ng-show="pagination.orderBy == 'vote_type'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                <th><a data-ng-click="changeOrder('start_at')">@lang('contest.startAt') <i ng-show="pagination.orderBy == 'start_at'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                <th><a data-ng-click="changeOrder('finish_at')">@lang('contest.finishAt') <i ng-show="pagination.orderBy == 'finish_at'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                <th><a data-ng-click="changeOrder()">@lang('voting.judgesprogress') </a></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="(key, voting) in votingSessions">
                <td></td>
                <td><a href="#{{{ $superadmin ? '/contests/edit/@{{contest.code}}/votingSession/@{{voting.code}}' : '/admin/voting-session/@{{voting.code}}' }}}">@{{voting.name}}</a></td>
                <td>@{{voteTypes[voting.vote_type]}}</td>
                <td>@{{voting.start_at}}</td>
                <td>@{{voting.finish_at}}</td>
                <td>
                    <uib-progressbar class="@{{ results[key] == 100 ? '' : 'progress-striped' }} active" value="totalProgress[key] != null ? totalProgress[key] : 0" type="@{{ results[key] == 100 ? 'success' : 'warning' }}"><i >@{{ results[key] }}%</i></uib-progressbar>
                </td>
                <td class="text-right">
                    <a href="#{{{ $superadmin ? '/contests/edit/@{{contest.code}}/voting/@{{voting.code}}' : '/admin/voting-session/@{{voting.code}}' }}}" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> @lang('general.edit')</a>
                    <a class="btn btn-danger btn-xs" ng-click="delete(voting)"><i class="fa fa-trash"></i> @lang('general.delete')</a>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="col-sm-12 text-center" ng-if="dataLoaded == false">
        <div class="spinner">
            <div class="rect1"></div>
            <div class="rect2"></div>
            <div class="rect3"></div>
            <div class="rect4"></div>
            <div class="rect5"></div>
        </div>
    </div>
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !contestForm.$dirty">
        <span ng-bind-html="flash"></span>
    </div>
@endsection