@extends('admin.contests.form', array('active' => 'invitation'))
@section('form')
    <h4 class="well well-sm">
        @lang('contest.invitations')
        <i class="fa fa-question-circle text-info" popover="@lang('contest.explain.invitations')" popover-placement="right" popover-trigger="mouseenter" class="btn btn-default"></i>
    </h4>
    <div class="tab-pagination">
        <!--<a href="#{{{ $superadmin ? '/contests/edit/@{{ contest.code }}/voting' : '/admin/voting/' }}}" class="btn btn-success"><i class="fa fa-plus"></i> @lang('contest.newVotingSession')</a>-->
        <a href="#/contests/edit/@{{ contest.code }}/invite" class="btn btn-success"><i class="fa fa-plus"></i> @lang('contest.newInvitation')</a>

        <input type="text" ng-model="query" class="form-control inline" placeholder="@lang('general.search')">
        <uib-pagination boundary-links="true" total-items="pagination.total" items-per-page="pagination.perPage" max-size="7" rotate="false" ng-model="pagination.page" class="pagination-sm" ng-show="pagination.total/pagination.perPage > 1" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></uib-pagination>
        <span class="pagination-data" ng-show="dataLoaded">@{{((pagination.page-1) * pagination.perPage)+1}} @lang('general.to') @{{ pagination.shownMax }} @lang('general.of') @{{pagination.total}} @lang('general.results')</span>
        <div class="clearfix"></div>
    </div>
    <table class="table table-striped table-hover table-condensed">
        <thead>
            <tr>
                <th></th>
                <th><a data-ng-click="changeOrder('id')"># <i ng-show="pagination.orderBy == 'id'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                <th><a data-ng-click="changeOrder('name')">@lang('general.name') <i ng-show="pagination.orderBy == 'name'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                <th><a data-ng-click="changeOrder('subject')">@lang('contest.invitation.subject') <i ng-show="pagination.orderBy == 'vote_type'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                <th><a data-ng-click="changeOrder('content')">@lang('contest.invitation.content') <i ng-show="pagination.orderBy == 'start_at'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                <!--<th><a data-ng-click="changeOrder('finish_at')">@lang('contest.finishAt') <i ng-show="pagination.orderBy == 'finish_at'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>-->
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="invitation in invitations">
                <td></td>
                <td><a href="#{{{ $superadmin ? '/contests/edit/@{{contest.code}}/invite/@{{invitation.id}}' : '/admin/invite/@{{invitation.id}}' }}}">@{{invitation.id}}</a></td>
                <td>@{{invitation.name}}</td>
                <td>@{{invitation.subject}}</td>
                <td>@{{invitation.content}}</td>
                <td class="text-right">
                    <a href="#{{{ $superadmin ? '/contests/edit/@{{contest.code}}/invite/@{{invitation.id}}' : '/admin/invite/@{{invitation.id}}' }}}" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> @lang('general.edit')</a>
                    <button class="btn btn-danger btn-xs" ng-click="delete(invitation)"><i class="fa fa-trash"></i> @lang('general.delete')</button>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !contestForm.$dirty">
        <span ng-bind-html="flash"></span>
    </div>
@endsection