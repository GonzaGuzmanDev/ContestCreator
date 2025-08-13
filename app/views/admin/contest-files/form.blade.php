@include('admin.header')
<div class="main-block contents">
    <h3>
        <a href="#/home"><i class="fa fa-wrench"></i> @lang('general.admin')</a>
        /
        <a href="#/contest-files"><i class="fa fa-archive"></i> @lang('general.contestFiles')</a>
        /
        <span ng-show="contestFile.id"><i class="fa fa-edit"></i> @lang('contest-file.editContestFile') @{{contestFile.id}}: @{{contestFile.code}}</span>
        <span ng-show="!contestFile.id"><i class="fa fa-plus"></i> @lang('contest-file.createContestFile')</span>
    </h3>
    <div class="row">
        @include('admin.menu', array('section' => 'contest-files'))
        <div class="col-sm-9 col-lg-10">
            <form name="contestFileForm" class="form-horizontal form-hover" novalidate >
                <h4>@lang('contest-file.contestFileData')</h4>
                <div class="form-group" ng-class="{error: contestFile.name.$invalid && !contestFile.name.$pristine}">
                    <label for="inputName" class="col-sm-2 control-label">@lang('general.name')</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputName" placeholder="@lang('general.name')" name="name" ng-model="contestFile.name" required>
                        <div ng-show="contestFileForm.name.$error.required && !contestFileForm.name.$pristine" class="help-inline text-danger form-control-static">@lang('contest-file.completeName')</div>
                        <div ng-show="errors.name" class="help-inline text-danger form-control-static">@{{errors.name.toString()}}</div>
                    </div>
                </div>
                <div class="form-group" ng-class="{error: contestFile.contestId.$invalid && !contestFile.contestId.$pristine}">
                    <label for="inputLabel" class="col-sm-2 control-label">@lang('general.contest')</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputLabel" placeholder="@lang('general.contest')" name="label" ng-model="contestFile.contestId" required>
                        <div ng-show="contestFileForm.contestId.$error.required && !contestFileForm.contestId.$pristine" class="help-inline text-danger form-control-static">@lang('contest-file.completeContest')</div>
                        <div ng-show="errors.contestId" class="help-inline text-danger form-control-static">@{{errors.contestId.toString()}}</div>
                    </div>
                </div>
                <hr class="col-sm-10 col-sm-offset-2"/>
                <div class="clearfix"></div>
                <div class="form-group" ng-class="{error: contestFile.userId.$invalid && !contestFile.userId.$pristine}">
                    <label for="inputUserId" class="col-sm-2 control-label">@lang('general.user')</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputUserId" placeholder="@lang('general.user')" name="type" ng-model="contestFile.userId" required>
                        <div ng-show="contestFileForm.userId.$error.required && !contestFileForm.userId.$pristine" class="help-inline text-danger form-control-static">@lang('contest-file.completeUser')</div>
                        <div ng-show="errors.userId" class="help-inline text-danger form-control-static">@{{errors.userId.toString()}}</div>
                    </div>
                </div>
                <div class="form-group" ng-class="{error: contestFile.status.$invalid && !contestFile.status.$pristine}">
                    <label for="inputStatus" class="col-sm-2 control-label">@lang('general.status')</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputStatus" placeholder="@lang('general.status')" name="status" ng-model="contestFile.status" required>
                        <div ng-show="contestFileForm.status.$error.required && !contestFileForm.status.$pristine" class="help-inline text-danger form-control-static">@lang('contest-file.completeStatus')</div>
                        <div ng-show="errors.status" class="help-inline text-danger form-control-static">@{{errors.status.toString()}}</div>
                    </div>
                </div>
                <hr class="col-sm-10 col-sm-offset-2"/>
                <div class="clearfix"></div>
                <div class="form-group">
                    <div class="col-sm-10 col-sm-offset-2">
                        <a href="#/contest-files/" class="btn btn-default"><i class="fa fa-ban"></i> @lang('general.cancel')</a>
                        <button ng-click="save()" ng-disabled="contestFileForm.$invalid" class="btn btn-primary"><i class="fa fa-save"></i> @lang('general.save')</button>
                        <button ng-click="delete()" ng-show="contestFile.id" class="btn btn-danger"><i class="fa fa-trash"></i> @lang('general.delete')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>