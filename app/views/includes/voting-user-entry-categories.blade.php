<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" ng-click="cancel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title"> @lang('voting.editJudge') </h4>
    </div>
    <form role="form" name="modalForm" class="form-horizontal" data-ng-submit="@yield('modal-submit', '')">
        <div class="modal-body modal-fixed">
            <h4 class="well well-sm">
                <span> @lang('general.user') </span>
            </h4>
            <h4>
                <span user-card user-card-model="judge.inscription.user" ng-if="judge.inscription && judge.inscription.user" user-show-email="true"></span>
            </h4>
            <span ng-if="judge.inscription.email">@{{ pagination.newMail == null || pagination.newMail == '' ? judge.inscription.email : pagination.newMail }}</span>
            <button class="btn btn-info btn-xs pull-right" ng-click="changeMail()" ng-if="!judge.inscription.user"> @lang('general.edit') </button>
            <div ng-if="mailChange == true">
                <input ng-model-options="{debounce: 500}" type="email" class="form-control form-inline" data-ng-model="pagination.newMail" placeholder="@lang('general.user')">
                <!--<button class="btn btn-success" ng-click="acceptNewMail(judge.inscription, pagination.newMail)"> @lang('general.accept') </button>-->
            </div>
            <h4 class="well well-sm">
                <span> @lang('voting.selectedEntries') </span>
            </h4>
            <input ng-model-options="{debounce: 500}" type="text" class="form-control form-inline" data-ng-model="pagination.query" placeholder="@lang('voting.judgesfilter')">
            <label> @lang('voting.selected') : @{{ totalSelectedEntries }} </label>
            <br>
            <br>
            <div class="btn-group">
                <button class="btn btn-default btn-sm" ng-click="selectedCategories()"> @lang('voting.watch') </button>
                <button class="btn btn-default btn-sm" ng-click="expandAll()"> <i class="fa fa-angle-double-down"></i> </button>
                <button class="btn btn-default btn-sm" ng-click="collapseAll()"> <i class="fa fa-angle-double-up"></i> </button>
            </div>
            <label ng-if="countGroupEntries(judgeEntries) == countGroupEntries(sessionEntries)"> @lang('voting.allSelected') </label>
            <label ng-if="countGroupEntries(judgeEntries) == 0"> @lang('voting.noSelected') </label>
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

