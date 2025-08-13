@include('admin.header')
<div class="main-block contents">
    <h3>
        <a href="#/home"><i class="fa fa-wrench"></i> @lang('general.admin')</a>
        /
        <i class="fa fa-sitemap"></i> @lang('general.formats')
    </h3>
    <div class="row">
        @include('admin.menu', array('section' => 'formats'))
        <div class="col-sm-9 col-lg-10">
            <form class="form-inline" role="form">
                <a href="#/formats/new" class="btn btn-success"><i class="fa fa-plus"></i> @lang('format.newFormat')</a>
                <div class="form-group">
                    <input type="text" ng-model="query" class="form-control inline" placeholder="@lang('general.search')">
                </div>
            </form>
            <uib-pagination boundary-links="true" total-items="pagination.total" items-per-page="pagination.perPage" max-size="7" rotate="false" ng-model="pagination.page" class="pagination-sm" ng-show="formats.length > 0" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></uib-pagination>
            <span class="pagination-data" ng-show="dataLoaded">@{{((pagination.page-1) * pagination.perPage)+1}} @lang('general.to') @{{Math.min(pagination.page * pagination.perPage, pagination.total)}} @lang('general.of') @{{pagination.total}} @lang('general.results')</span>
            <div class="clearfix"></div>
            <table class="table table-striped table-hover table-condensed">
                <thead>
                    <tr>
                        <th><a data-ng-click="changeOrder('name')">@lang('general.name') <i ng-show="pagination.orderBy == 'name'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                        <th><a data-ng-click="changeOrder('label')">@lang('general.label') <i ng-show="pagination.orderBy == 'label'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                        <th><a data-ng-click="changeOrder('type')">@lang('general.type') <i ng-show="pagination.orderBy == 'type'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                        <th><a data-ng-click="changeOrder('position')">@lang('general.position') <i ng-show="pagination.orderBy == 'position'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                        <th><a data-ng-click="changeOrder('active')">@lang('general.active') <i ng-show="pagination.orderBy == 'active'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                        <th><a data-ng-click="changeOrder('extension')">@lang('general.extension') <i ng-show="pagination.orderBy == 'extension'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="format in formats">
                        <td><a href="#/formats/edit/@{{format.id}}">@{{format.name}}</a></td>
                        <td>@{{format.label}}</td>
                        <td ng-show="format.type == {{Format::VIDEO}}">@lang('general.video')</td>
                        <td ng-show="format.type == {{Format::IMAGE}}">@lang('general.image')</td>
                        <td ng-show="format.type == {{Format::AUDIO}}">@lang('general.audio')</td>
                        <td ng-show="format.type == {{Format::DOCUMENT}}">@lang('general.document')</td>
                        <td>@{{format.position}}</td>
                        <td>@{{format.active == '1' ? '@lang('general.yes')' : '@lang('general.no')'}}</td>
                        <td>@{{format.extension}}</td>
                        <td class="text-right">
                            <a href="#/formats/edit/@{{format.id}}" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> @lang('general.edit')</a>
                            <button class="btn btn-danger btn-xs" ng-click="delete(format)"><i class="fa fa-trash"></i> @lang('general.delete')</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>