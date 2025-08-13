@include('admin.header')
<div class="main-block contents">
    <h3>
        <a href="#/home"><i class="fa fa-wrench"></i> @lang('general.admin')</a>
        /
        <i class="fa fa-archive"></i> @lang('general.contestFiles')
    </h3>
    <div class="row">
        @include('admin.menu', array('section' => 'contest-files'))
        <div class="col-sm-9 col-lg-10">
            <form class="form-inline" role="form">
                <a href="#/contest-files/new" class="btn btn-success"><i class="fa fa-plus"></i> @lang('contest-file.newContestFile')</a>
                <div class="form-group">
                    <input type="text" ng-model="query" class="form-control inline" placeholder="@lang('general.search')">
                </div>
            </form>
            <uib-pagination boundary-links="true" total-items="pagination.total" items-per-page="pagination.perPage" max-size="7" rotate="false" ng-model="pagination.page" class="pagination-sm" ng-show="contestFiles.length > 0" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></uib-pagination>
            <span class="pagination-data" ng-show="dataLoaded">@{{((pagination.page-1) * pagination.perPage)+1}} @lang('general.to') @{{Math.min(pagination.page * pagination.perPage, pagination.total)}} @lang('general.of') @{{pagination.total}} @lang('general.results')</span>
            <div class="clearfix"></div>
            <table class="table table-striped table-hover table-condensed">
                <thead>
                    <tr>
                        <th><a data-ng-click="changeOrder('name')">@lang('general.name') <i ng-show="pagination.orderBy == 'name'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                        <th><a data-ng-click="changeOrder('status')">@lang('general.status') <i ng-show="pagination.orderBy == 'status'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="contestFile in contestFiles">
                        <td><a href="#/contest-files/edit/@{{contestFile.id}}">@{{contestFile.name}}</a></td>
                        <td>@{{contestFile.statuslabel}}</td>
                        <td class="text-right">
                            <a href="#/contest-files/edit/@{{contestFile.id}}" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> @lang('general.edit')</a>
                            <button class="btn btn-danger btn-xs" ng-click="delete(contestFile)"><i class="fa fa-trash"></i> @lang('general.delete')</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>