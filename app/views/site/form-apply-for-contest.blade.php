@include('includes.header')
<br><br>
<div class="main-block panned col-md-offset-3 col-md-6 col-sm-6">
    <h3 class="title"> @lang('contest.wizard.createContestTitle')</h3>
    <h4> @lang('contest.wizard.createContestExplain', ['hasContests' => $hasContests]) </h4>
<br>
<div class="clearfix"></div>
<br>
<h4 class="well well-sm">
    @lang('contest.wizard.name')
</h4>
<div class="clearfix"></div>
<br>
<div class="form-group" ng-class="{error: contest.name.$invalid && !contest.name.$pristine}">
    <label for="inputName" class="col-sm-2 control-label">@lang('general.name')</label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <input ng-model-options="{debounce: 500}" type="text" class="form-control col-md-3" id="inputName" placeholder="@lang('general.name')" name="name" ng-model="contest.name" required focus-me="true">
        <span ng-if="nameAvailable == true" class="text-success"> <i class="fa fa-fw fa-check"></i>@lang('contest.available') </span>
        <span ng-if="nameAvailable == false" class="text-danger"> <i class="fa fa-fw fa-close"></i>@lang('contest.notAvailable')</span>
        <div ng-show="contestForm.name.$error.required && !contestForm.name.$pristine" class="help-inline text-danger form-control-static">@lang('contest.completeName')</div>
        <div ng-show="errors.name" class="help-inline text-danger form-control-static">@{{errors.name.toString()}}</div>
    </div>
</div>
<div class="clearfix"></div>
<br>
<div class="form-group" ng-class="{error: contest.code.$invalid && !contest.code.$pristine}">
    <label for="inputCode" class="col-sm-2 control-label">@lang('general.code')</label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <input ng-model-options="{debounce: 500}" type="text" class="form-control" id="inputCode" placeholder="@lang('general.code')" name="code" ng-model="contest.code" required>
        <i class="fa fa-question-circle text-info" popover="@lang('contest.wizard.codeHelp')" popover-placement="right" popover-trigger="mouseenter"></i>
        <span ng-if="codeAvailable == true" class="text-success"> <i class="fa fa-fw fa-check"></i> @lang('contest.available') </span>
        <span ng-if="codeAvailable == false" class="text-danger"> <i class="fa fa-fw fa-close"></i> @lang('contest.notAvailable')</span>
        <div ng-show="contestForm.code.$error.required && !contestForm.code.$pristine" class="help-inline text-danger form-control-static">@lang('contest.completeCode')</div>
        <div ng-show="errors.code" class="help-inline text-danger form-control-static">@{{errors.code.toString()}}</div>
    </div>
</div>
<div class="clearfix"></div>
<br>
<!--<h4 class="well well-sm">
    @lang('contest.contestDates')
</h4>
<div class="clearfix"></div>
<br>
<div class="form-group" ng-class="{error: contest.start_at.$invalid && !contest.start_at.$pristine}">
    <label for="inputStartAt" class="col-sm-2 control-label">@lang('contest.startAt')</label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        @include('includes.datetimepicker', array('field'=>'contest.start_at', 'placeholder' => Lang::get('contest.startAt')))
        <div ng-show="contestForm.start_at.$error.required && !contestForm.start_at.$pristine" class="help-inline text-danger form-control-static">@lang('contest.completeStartAt')</div>
        <div ng-show="errors.start_at" class="help-inline text-danger form-control-static">@{{errors.start_at.toString()}}</div>
    </div>
    <div class="col-sm-4 form-control-static">
        <span am-time-ago="contest.start_at"></span>
    </div>
</div>
<div class="clearfix"></div>
<br>
<div class="form-group" ng-class="{error: contest.finish_at.$invalid && !contest.finish_at.$pristine}">
    <label for="inputDeadLine1At" class="col-sm-2 control-label">@lang('contest.finishAt')</label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        @include('includes.datetimepicker', array('field'=>'contest.finish_at', 'placeholder' => Lang::get('contest.finishAt')))
        <div ng-show="contestForm.finish_at.$error.required && !contestForm.finish_at.$pristine" class="help-inline text-danger form-control-static">@lang('contest.completeFinishAt')</div>
        <div ng-show="errors.finish_at" class="help-inline text-danger form-control-static">@{{errors.finish_at.toString()}}</div>
    </div>
    <div class="col-sm-4 form-control-static">
        <span am-time-ago="contest.finish_at"></span>
    </div>
</div>-->
<div class="clearfix"></div>
<br>
<span> @lang('contest.wizard.disclaimer') </span>
<button type="button" class="btn btn-info btn-md pull-right" ng-click="saveAndNext()"> @lang('contest.wizard.next') <i class="fa fa-arrow-circle-right"></i> </button>
<button type="button" class="btn btn-info btn-md pull-right" ng-click="saveAndNext(true)" ng-hide="{{$hasContests}} == 0"> @lang('contest.wizard.saveAndImport') <i class="fa fa-copy fa-fw"></i> </button>
@include('includes.footer')
</div>