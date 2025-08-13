@extends('admin.contests.form-deadlines', array('active' => 'general'))
@section('form')
<h4 class="well well-sm">
    @lang('contest.contestData')
</h4>
<div class="form-group" ng-class="{error: contest.name.$invalid && !contest.name.$pristine}">
    <label for="inputName" class="col-sm-2 control-label">@lang('general.name')</label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <input type="text" class="form-control col-md-3" id="inputName" placeholder="@lang('general.name')" name="name" ng-model="contest.name" required focus-me="true">
        <div ng-show="contestForm.name.$error.required && !contestForm.name.$pristine" class="help-inline text-danger form-control-static">@lang('contest.completeName')</div>
        <div ng-show="errors.name" class="help-inline text-danger form-control-static">@{{errors.name.toString()}}</div>
    </div>
</div>
<div class="form-group" ng-class="{error: contest.code.$invalid && !contest.code.$pristine}">
    <label for="inputCode" class="col-sm-2 control-label">@lang('general.code')</label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <input type="text" class="form-control" id="inputCode" placeholder="@lang('general.code')" name="code" ng-model="contest.code" required>
        <div ng-show="contestForm.code.$error.required && !contestForm.code.$pristine" class="help-inline text-danger form-control-static">@lang('contest.completeCode')</div>
        <div ng-show="errors.code" class="help-inline text-danger form-control-static">@{{errors.code.toString()}}</div>
    </div>
</div>
<div class="clearfix"></div>
<h4 class="well well-sm">
    @lang('contest.generalConfig')
</h4>
<div class="form-group">
    <label class="col-sm-2 control-label">
        @lang('general.type')
    </label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <div class="checkbox">
            <select ng-model="contest.type" class="form-control">
                <option value="{{Contest::TYPE_CONTEST}}">@lang('oxoTicket.contest')</option>
                <option value="{{Contest::TYPE_TICKET}}">@lang('oxoTicket.ticket')</option>
            </select>
            <br>
            <span class="text-muted">@lang('contest.typeExplain')</span>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        @lang('contest.oneEntryPerCategory')
    </label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <div class="checkbox">
            <label for="">
                <input type="checkbox" ng-model="contest.single_category" id="single_category"/>
                @lang('general.enable')
            </label>
            <br>
            <span class="text-muted">@lang('contest.oneEntryPerCategoryExplain')</span>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">
        @lang('contest.blockedFinishedEntry')
    </label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <div class="checkbox">
            <label>
                <input type="checkbox" ng-model="contest.block_finished_entry" id="block_finished_entry">
                @lang('general.enable')
            </label>
            <br>
            <span class="text-muted">@lang('contest.blockFinishedEntryExplain')</span>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">
        @lang('contest.adminResetPassword')
    </label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <div class="checkbox">
            <label>
                <input type="checkbox" ng-model="contest.admin_reset_password" id="admin_reset_password">
                @lang('general.enable')
            </label>
            <br>
            <span class="text-muted">@lang('contest.adminResetPassword')</span>
        </div>
    </div>
</div>


<div class="form-group">
    <label class="col-sm-2 control-label">
        @lang('contest.maxEntriesPerContest')
    </label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <input type="number" min="0" class="form-control"  ng-model="contest.max_entries" id=""/>
        <span class="text-muted">@lang('contest.maxEntriesPerContestExplain')</span>
        <div ng-show="errors.max_entries" class="help-inline text-danger form-control-static">@{{errors.max_entries.toString()}}</div>
    </div>
</div>
@if(Config::get('cloud.enabled'))
<div class="clearfix"></div>
<h4 class="well well-sm">
    @lang('contest.cloudConfig')
</h4>
<div class="form-group">
    <label class="col-sm-2 control-label">
        @lang('contest.cloudSourcesBucket')
    </label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <div class="checkbox">
            <? $bucketsList = Cloud::Instance()->GetBuckets(); ?>
            <select ng-model="contest.storage_sources_bucket" class="form-control">
                <option value=""></option>
                @foreach($bucketsList as $bucket)
                    <option value="{{$bucket->name}}">{{$bucket->name}}</option>
                @endforeach
            </select>
            <br>
            <span class="text-muted">@lang('contest.cloudSourcesBucketDesc')</span>
            <div ng-show="errors.storage_sources_bucket" class="help-inline text-danger form-control-static">@{{errors.storage_sources_bucket.toString()}}</div>
        </div>
    </div>
</div>
@endif
<div class="clearfix"></div>
<h4 class="well well-sm">
    @lang('contest.contestDates')
</h4>
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
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">
        @lang('contest.public')
    </label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <div class="checkbox">
            <label for="">
                <input type="checkbox" ng-model="contest.public" ng-checked="contest.public == 1" ng-true-value="1" ng-false-value="0" id=""/>
                @lang('general.enable')
            </label>
        </div>
    </div>
</div>
@parent
<div class="col-lg-12">
    <h4 class="well well-sm">@lang('contest.analytics')</h4>
    <div class="form-group">
        <label class="col-sm-2 control-label">
            @lang('contest.googleAnalytics')
        </label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            <input type="text" min="0" class="form-control"  ng-model="contest.google_analytics_id" id=""/>
            <span class="text-muted">@lang('contest.googleAnalyticsExplain')</span>
            <div ng-show="errors.google_analytics_id" class="help-inline text-danger form-control-static">@{{errors.google_analytics_id.toString()}}</div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">
            @lang('contest.facebookPixel')
        </label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            <input type="text" min="0" class="form-control"  ng-model="contest.facebook_pixel_id" id=""/>
            <span class="text-muted">@lang('contest.facebookPixelExplain')</span>
            <div ng-show="errors.facebook_pixel_id" class="help-inline text-danger form-control-static">@{{errors.facebook_pixel_id.toString()}}</div>
        </div>
    </div>
</div>
@endsection