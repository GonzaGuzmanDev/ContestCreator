<? $forTypes = isset($forTypes) ? $forTypes : false; ?>
<div class="alert alert-warning alert-xs {{ isset($wide) ? "col-sm-8 col-sm-offset-4" : "col-sm-8  col-sm-offset-2 col-md-6 col-lg-4" }}" ng-show="{{$forTypes}}">
    <div class=""><i class="fa fa-info-circle"></i> @lang('contest.deadlinesManagedByTypes')</div>
</div>
<div class="clearfix" ng-show="{{$forTypes}}"></div>
<div class="form-group" ng-class="{error: {{ $start }}.$invalid && !{{ $start }}.$pristine}">
    <label for="inputStartAt" class="{{ isset($wide) ? "col-sm-4" : "col-sm-2" }} control-label">@lang('contest.startAt')</label>
    <div class="{{ isset($wide) ? "col-sm-8" : "col-sm-8 col-md-6 col-lg-4" }}">
        <div ng-if="!showThis" class="form-control-static">
            @{{ {{{$start}}} || '-' }}
        </div>
        <div ng-if="showThis">
        @include('includes.datetimepicker', array('field'=>$start, 'placeholder' => Lang::get('contest.startAt'), 'disabled' => $forTypes))
        </div>
        <div ng-show="contestForm.inscription_start_at.$error.required && !contestForm.inscription_start_at.$pristine" class="help-inline text-danger form-control-static">@lang('contest.completeStartAt')</div>
        <div ng-show="errors.{{ $errstart }}" class="help-inline text-danger form-control-static">@{{errors.{{{ $errstart }}}.toString()}}</div>
    </div>
    <div class="{{ isset($wide) ? "col-sm-4" : "col-sm-4 col-md-6 col-lg-4" }} form-control-static">
        <span am-time-ago="{{$start}}"></span>
    </div>
    <div class="clearfix"></div>
</div>
<div class="form-group" ng-class="{error: {{ $deadline1 }}.$invalid && !{{ $deadline1 }}.$pristine}">
    <label for="inputDeadLine1At" class="{{ isset($wide) ? "col-sm-4" : "col-sm-2" }} control-label">@lang('contest.deadline1At')</label>
    <div class="{{ isset($wide) ? "col-sm-8" : "col-sm-8 col-md-6 col-lg-4" }}">
        <div ng-if="!showThis" class="form-control-static">
            @{{ {{{$deadline1}}} || '-' }}
        </div>
        <div ng-if="showThis">
        @include('includes.datetimepicker', array('field'=>$deadline1, 'placeholder' => Lang::get('contest.deadLine1At'), 'disabled' => $forTypes))
        </div>
        <div ng-show="contestForm.inscription_deadline1_at.$error.required && !contestForm.inscription_deadline1_at.$pristine" class="help-inline text-danger form-control-static">@lang('contest.completeDeadLine1At')</div>
        <div ng-show="errors.{{ $errdeadline1 }}" class="help-inline text-danger form-control-static">@{{errors.{{{ $errdeadline1 }}}.toString()}}</div>
    </div>
    <div class="{{ isset($wide) ? "col-sm-4" : "col-sm-4 col-md-6 col-lg-4" }} form-control-static">
        <span am-time-ago="{{$deadline1}}"></span>
    </div>
    <div class="clearfix"></div>
</div>
<div class="form-group" ng-class="{error: {{ $deadline2 }}.$invalid && !{{ $deadline2 }}.$pristine}">
    <label for="inputDeadLine2At" class="{{ isset($wide) ? "col-sm-4" : "col-sm-2" }} control-label">@lang('contest.deadline2At')</label>
    <div class="{{ isset($wide) ? "col-sm-8" : "col-sm-8 col-md-6 col-lg-4" }}">
        <div ng-if="!showThis" class="form-control-static">
            @{{ {{{$deadline2}}} || '-' }}
        </div>
        <div ng-if="showThis">
        @include('includes.datetimepicker', array('field'=>$deadline2, 'placeholder' => Lang::get('contest.deadLine2At'), 'disabled' => $forTypes))
        </div>
        <div ng-show="errors.{{ $errdeadline2 }}" class="help-inline text-danger form-control-static">@{{errors.{{{ $errdeadline2 }}}.toString()}}</div>
    </div>
    <div class="{{ isset($wide) ? "col-sm-4" : "col-sm-4 col-md-6 col-lg-4" }} form-control-static">
        <span am-time-ago="{{$deadline2}}"></span>
    </div>
    <div class="clearfix"></div>
</div>