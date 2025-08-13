@extends('admin.contests.form', array('active' => 'pages'))
@section('form')
    <h4 class="well well-sm">
        @lang('contest.pages')
        {{--@if(!$superadmin)
            <i class="fa fa-question-circle text-info" popover="@lang('contest.explain.pages')" popover-placement="right" popover-trigger="mouseenter" class="btn btn-default"></i>
        @endif--}}
    </h4>
    <div class="tab-pagination">
        <a href="#{{{ $superadmin ? '/contests/edit/@{{ contest.code }}/page/' : '/admin/pages/page/' }}}" class="btn btn-success"><i class="fa fa-plus"></i> @lang('contest.newPage')</a>
        <input type="text" ng-model="query" class="form-control inline" placeholder="@lang('general.search')">
        <uib-pagination boundary-links="true" total-items="pagination.total" items-per-page="pagination.perPage" max-size="7" rotate="false" ng-model="pagination.page" class="pagination-sm" ng-show="pagination.total/pagination.perPage > 1" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></uib-pagination><br>
        <span class="pagination-data" ng-show="dataLoaded">@{{((pagination.page-1) * pagination.perPage)+1}} @lang('general.to') @{{ pagination.shownMax }} @lang('general.of') @{{pagination.total}} @lang('general.results')</span>
        <div class="clearfix"></div>
    </div>
    <table class="table table-striped table-hover table-condensed">
        <thead>
        <tr>
            <th></th>
            <th><a data-ng-click="changeOrder('id')"># <i ng-show="pagination.orderBy == 'id'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
            <th><a data-ng-click="changeOrder('name')">@lang('general.name') <i ng-show="pagination.orderBy == 'name'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
            <tr ng-repeat="page in pages | filter:query">
                <td><a href="#{{{ $superadmin ? '/contests/edit/@{{contest.code}}/page/@{{page.id}}' : '/admin/pages/page/@{{page.id}}' }}}">@{{page.id}}</a></td>
                <td><a href="#{{{ $superadmin ? '/contests/edit/@{{contest.code}}/page/@{{page.id}}' : '/admin/pages/page/@{{page.id}}' }}}">@{{page.name}}</a></td>
                <td class="text-right">
                    <a href="@{{page.url}}" class="btn btn-primary btn-xs" target="_blank"><i class="fa fa-link"></i> @lang('general.view')</a>
                    <a href="#{{{ $superadmin ? '/contests/edit/@{{contest.code}}/page/@{{page.id}}' : '/admin/pages/page/@{{page.id}}' }}}" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> @lang('general.edit')</a>
                    <a class="btn btn-danger btn-xs" ng-click="delete(page)"><i class="fa fa-trash"></i> @lang('general.delete')</a>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !contestForm.$dirty">
        <span ng-bind-html="flash"></span>
    </div>
@endsection