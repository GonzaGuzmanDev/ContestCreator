@extends('admin.contests.form', array('active' => 'collections'))
@section('form')
    <h4 class="well well-sm">
        @lang('contest.tab.collections')
        {{--@if(!$superadmin)
            <i class="fa fa-question-circle text-info" popover="@lang('contest.explain.collections')" popover-placement="right" popover-trigger="mouseenter" class="btn btn-default"></i>
        @endif--}}
    </h4>
    <div class="tab-pagination">
        <a href="#/admin/collection/" class="btn btn-success"><i class="fa fa-plus"></i> @lang('contest.newCollection')</a>
        <input type="text" ng-model="query" class="form-control inline" placeholder="@lang('general.search')">
        <uib-pagination boundary-links="true" total-items="pagination.total" items-per-page="pagination.perPage" max-size="7" rotate="false" ng-model="pagination.page" class="pagination-sm" ng-show="pagination.total/pagination.perPage > 1" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></uib-pagination><br>
        <span class="pagination-data" ng-show="dataLoaded">@{{((pagination.page-1) * pagination.perPage)+1}} @lang('general.to') @{{ pagination.shownMax }} @lang('general.of') @{{pagination.total}} @lang('general.results')</span>
        <div class="clearfix"></div>
    </div>
    <table class="table table-striped table-hover table-condensed">
        <thead>
        <tr>
            <th><a data-ng-click="changeOrder('id')"># <i ng-show="pagination.orderBy == 'id'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
            <th><a data-ng-click="changeOrder('name')">@lang('general.name') <i ng-show="pagination.orderBy == 'name'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
            <th><a data-ng-click="changeOrder('status')">@lang('general.status') <i ng-show="pagination.orderBy == 'name'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
            <th><a data-ng-click="changeOrder('status')">@lang('general.deadlines.start_at') <i ng-show="pagination.orderBy == 'name'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
            <th><a data-ng-click="changeOrder('status')">@lang('collection.end') <i ng-show="pagination.orderBy == 'name'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
            <tr ng-repeat="collection in collections | filter:query">
                <td><a href="#{{{ $superadmin ? '/contests/edit/@{{contest.code}}/collection/@{{collection.code}}' : '/admin/collection/@{{collection.code}}' }}}">@{{$index + 1}}</a></td>
                <td><a href="#{{{ $superadmin ? '/contests/edit/@{{contest.code}}/collection/@{{collection.code}}' : '/admin/collection/@{{collection.code}}' }}}">@{{collection.name}}</a></td>
                <td><a href="#{{{ $superadmin ? '/contests/edit/@{{contest.code}}/collection/@{{collection.code}}' : '/admin/collection/@{{collection.code}}' }}}">@{{collection.private == 1 ? "privado" : "publico" }}</a></td>
                <td><a href="#{{{ $superadmin ? '/contests/edit/@{{contest.code}}/collection/@{{collection.code}}' : '/admin/collection/@{{collection.code}}' }}}">@{{collection.start_at}}</a></td>
                <td><a href="#{{{ $superadmin ? '/contests/edit/@{{contest.code}}/collection/@{{collection.code}}' : '/admin/collection/@{{collection.code}}' }}}">@{{collection.finish_at}}</a></td>
                <td class="text-right">
                    <a href="#/collection/@{{collection.code}}" class="btn btn-primary btn-xs" target="_blank"><i class="fa fa-link"></i> @lang('general.view')</a>
                    <a href="#/admin/collection/@{{collection.code}}" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> @lang('general.edit')</a>
                    <a class="btn btn-danger btn-xs" ng-click="delete(collection)"><i class="fa fa-trash"></i> @lang('general.delete')</a>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !contestForm.$dirty">
        <span ng-bind-html="flash"></span>
    </div>
@endsection