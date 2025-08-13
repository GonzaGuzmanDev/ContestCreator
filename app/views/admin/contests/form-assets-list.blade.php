@extends('admin.contests.form', array('active' => 'assets'))
@section('form')
    <h4 class="well well-sm">
        @lang('contest.assets')
        {{--@if(!$superadmin)
            <i class="fa fa-question-circle text-info" popover="@lang('contest.explain.assets')" popover-placement="right" popover-trigger="mouseenter" class="btn btn-default"></i>
        @endif--}}
    </h4>
    <div class="tab-pagination">
        <div flow-init="{target: fileUploadUrl, testChunks:false}"
             flow-files-submitted="$flow.upload()"
             flow-file-success="$file.msg = $message; updateAllAssetsList();"
             flow-file-error="$file.msg = $message" class="pull-right">
            <div class="w2">
                <div ng-repeat="file in $flow.files" ng-class="{danger:file.error, success:file.isComplete()}">
                    <uib-progressbar ng-show="file.isUploading()" animate="false" value="file.progress() * 100" type="success" ng-class="{active:file.isUploading()}"><b>@{{file.progress() | percentage:1}}</b></uib-progressbar>
                    <div ng-show="file.error"><i class="fa fa-exclamation-circle text-danger"></i> Error @{{file.msg.flash}}</div>
                    <div ng-show="file.isComplete()"><i class="fa fa-check-circle text-success"></i></div>
                </div>
            </div>
            <div class="w2 text-right">
                <span flow-btn class="btn btn-success"><i class="fa fa-upload"></i> @lang('contest.newAsset')</span>
            </div>
        </div>
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
            <th><a data-ng-click="changeOrder('content_type')">@lang('contest.content_type') <i ng-show="pagination.orderBy == 'content_type'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
            <th>@lang('contest.link')</th>
            <th>@lang('contest.preview')</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
            <tr ng-repeat="asset in assets | filter:query">
                <td><a href="#{{{ $superadmin ? '/contests/edit/@{{contest.code}}/asset/@{{asset.id}}' : '/admin/assets/asset/@{{asset.id}}' }}}">@{{asset.id}}</a></td>
                <td><a href="#{{{ $superadmin ? '/contests/edit/@{{contest.code}}/asset/@{{asset.id}}' : '/admin/assets/asset/@{{asset.id}}' }}}">@{{asset.name}}</a></td>
                <td>@{{asset.content_type}}</td>
                <td><a ng-href="@{{asset.url}}" target="_blank"><i class="fa fa-link"></i> @{{asset.url}}</a></td>
                <td><div ng-if="asset.preview" class="asset-preview" ng-bind-html="asset.preview"></div></td>
                <td class="text-right">
                    <?/*<a href="#{{{ $superadmin ? '/contests/edit/@{{contest.code}}/asset/@{{asset.id}}' : '/admin/assets/asset/@{{asset.id}}' }}}" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> @lang('general.edit')</a>*/?>
                    <a class="btn btn-danger btn-xs" ng-click="delete(asset)"><i class="fa fa-trash"></i> @lang('general.delete')</a>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !contestForm.$dirty">
        <span ng-bind-html="flash"></span>
    </div>
@endsection