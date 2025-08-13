<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" ng-click="cancel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title"> @lang('voting.editGroup') </h4>
    </div>
    <form role="form" name="modalForm" class="form-horizontal" data-ng-submit="@yield('modal-submit', '')">
        <div class="modal-body modal-fixed">
            <span>@{{ pagination.newGroupName == '' || pagination.newGroupName == null ? group.name : pagination.newGroupName }}</span>
            <button class="btn btn-info btn-xs" ng-click="changeGroupName()"> @lang('general.edit') </button>
            <div ng-if="groupNameChange == true">
                <br>
                <input ng-model-options="{debounce: 500}" type="text" class="form-control form-inline" data-ng-model="pagination.newGroupName" placeholder="@lang('general.edit')">
            </div>
            <h4 class="well well-sm">
                <span> @lang('voting.selectedEntries') </span>
            </h4>
            <input ng-model-options="{debounce: 500}" type="text" class="form-control form-inline" data-ng-model="pagination.query" placeholder="@lang('voting.judgesfilter')">
            <label> @lang('voting.selected') : @{{ countGroupEntries(groupEntries) }} </label>
            <br>
            <br>
            <div class="btn-group">
                <button class="btn btn-default btn-sm" ng-click="selectedCategories()">
                <span ng-if="!showSelected">@lang('voting.watch')</span>
                <span ng-if="showSelected">@lang('voting.watchAll')</span>
                </button>
                <button class="btn btn-default btn-sm" ng-click="expandAll()"> <i class="fa fa-angle-double-down"></i> </button>
                <button class="btn btn-default btn-sm" ng-click="collapseAll()"> <i class="fa fa-angle-double-up"></i> </button>
            </div>
            <label ng-if="countGroupEntries(groupEntries) == countGroupEntries(sessionEntries)"> @lang('voting.allSelected') </label>
            <label ng-if="countGroupEntries(groupEntries) == 0"> @lang('voting.noSelected') </label>
            <br>
            <br>
            <div class="col-sm-12 entries-categories-tree">
                <ul ng-model="categories" class="category-list">
                    <li ng-repeat="category in categories track by $index"
                    ng-include="'categoryGroup.html'"></li>
                </ul>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="modal-footer">
            <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash">
                <span ng-bind-html="flash"></span>
            </div>
            <button type="button" class="btn btn-default" ng-disabled="sending" ng-click="cancel()">@lang('general.cancel')</button>
            <button type="button" class="btn btn-default" ng-disabled="modalForm.$invalid || sending" ng-click="accept()">@lang('general.accept')</button>
        </div>
    </form>
</div>

