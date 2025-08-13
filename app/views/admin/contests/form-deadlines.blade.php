@extends('admin.contests.form', array('active' => $active != null ? $active : 'deadlines'))
@section('form')
<div class="row">
    <span ng-if="{{ isset($contest->wizard_status) ? $contest->wizard_status >= Contest::WIZARD_DATES && $contest->wizard_status != Contest::WIZARD_FINISHED : 0}}">
    @include('includes.wizardProgress', array('active' => Contest::WIZARD_DATES))
        <br>
    <div class="clearfix"></div>
    <br>
    <h3 class="text-center"> @lang('contest.wizard.deadlinesFormTittle') </h3>
    <br>
    </span>
    <h4 class="well well-sm">
        @lang('contest.contestDates')
    </h4>
    <div class="clearfix"></div>
    <br>
    <div class="col-lg-6">
    <div class="form-group" ng-class="{error: contest.start_at.$invalid && !contest.start_at.$pristine}">
        <label for="inputStartAt" class="col-sm-2 control-label">@lang('contest.startAt')</label>
        <div class="col-sm-8 col-md-6 col-lg-4" ng-if="showThis">
            @include('includes.datetimepicker', array('field'=>'contest.start_at', 'placeholder' => Lang::get('contest.startAt')))
            <div ng-show="contestForm.start_at.$error.required && !contestForm.start_at.$pristine" class="help-inline text-danger form-control-static">@lang('contest.completeStartAt')</div>
            <div ng-show="errors.start_at" class="help-inline text-danger form-control-static">@{{errors.start_at.toString()}}</div>
        </div>
        <span class="col-sm-8 col-md-6 col-lg-4 form-control-static" ng-if="!showThis">
            @{{ contest.start_at ? contest.start_at : '-'}}
        </span>
        <div class="col-sm-4 form-control-static">
            <span am-time-ago="contest.start_at"></span>
        </div>
    </div>
    <div class="clearfix"></div>
    <br>
    <div class="form-group" ng-class="{error: contest.finish_at.$invalid && !contest.finish_at.$pristine}">
        <label for="inputDeadLine1At" class="col-sm-2 control-label">@lang('contest.finishAt')</label>
        <div class="col-sm-8 col-md-6 col-lg-4" ng-if="showThis">
            @include('includes.datetimepicker', array('field'=>'contest.finish_at', 'placeholder' => Lang::get('contest.finishAt')))
            <div ng-show="contestForm.finish_at.$error.required && !contestForm.finish_at.$pristine" class="help-inline text-danger form-control-static">@lang('contest.completeFinishAt')</div>
            <div ng-show="errors.finish_at" class="help-inline text-danger form-control-static">@{{errors.finish_at.toString()}}</div>
        </div>
        <span class="col-sm-8 col-md-6 col-lg-4 form-control-static" ng-if="!showThis">
            @{{ contest.finish_at ? contest.finish_at : '-'}}
        </span>
        <div class="col-sm-4 form-control-static">
            <span am-time-ago="contest.finish_at"></span>
        </div>
    </div>
    </div>
    <div class="clearfix"></div>
    <br>
    <div class="col-lg-6">
        <h4 class="well well-sm">@lang('contest.inscriptionDates')</h4>
        <div class="form-group" ng-class="{error: contest.inscription_public.$invalid && !contest.inscription_public.$pristine}">
            <label for="inputDeadLine2At" class="col-sm-2 control-label">@lang('contest.register_public')</label>
            <div class="col-sm-8">
                <div class="checkbox">
                    <label ng-if="showThis">
                        <input type="checkbox" name="" ng-model="contest.inscription_public" ng-checked="contest.inscription_public == 1" ng-true-value="1" ng-false-value="0" id=""/>
                    </label>
                    <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': contest.inscription_public == 1,'fa-square-o': contest.inscription_public != 1 }"></i>
                </div>
                <div ng-show="errors.inscription_public" class="help-inline text-danger form-control-static">@{{errors.inscription_public.toString()}}</div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group" ng-if="contest.inscription_public" ng-class="{error: contest.inscription_register_picture.$invalid && !contest.inscription_register_picture.$pristine}">
            <label for="inputDeadLine2At" class="col-sm-2 control-label">@lang('contest.register_picture')</label>
            <div class="col-sm-8">
                <div class="checkbox">
                    <label ng-if="showThis">
                        <input type="checkbox" name="" ng-model="contest.inscription_register_picture" ng-checked="contest.inscription_register_picture == 1" ng-true-value="1" ng-false-value="0" id=""/>
                    </label>
                    <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': contest.inscription_register_picture == 1,'fa-square-o': contest.inscription_register_picture != 1 }"></i>
                </div>
                <div ng-show="errors.inscription_register_picture" class="help-inline text-danger form-control-static">@{{errors.inscription_register_picture.toString()}}</div>
            </div>
            <div class="clearfix"></div>
        </div>
        @include('admin.contests.deadlines', array('start' => 'contest.inscription_start_at', 'deadline1' => 'contest.inscription_deadline1_at', 'deadline2' => 'contest.inscription_deadline2_at',  'errstart' => 'inscription_start_at', 'errdeadline1' => 'inscription_deadline1_at', 'errdeadline2' => 'inscription_deadline2_at', 'forTypes' => 'contest.inscriptorRegistration'))
        <h4 class="well well-sm">@lang('contest.votersDates')</h4>
        <div class="form-group" ng-class="{error: contest.voters_public.$invalid && !contest.voters_public.$pristine}">
            <label for="inputDeadLine2At" class="col-sm-2 control-label">@lang('contest.register_public')</label>
            <div class="col-sm-8">
                <div class="checkbox">
                    <label ng-if="showThis">
                        <input type="checkbox" name="" ng-model="contest.voters_public" ng-checked="contest.voters_public == 1" ng-true-value="1" ng-false-value="0" id=""/>
                    </label>
                    <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': contest.voters_public == 1,'fa-square-o': contest.voters_public != 1 }"></i>
                </div>
                <div ng-show="errors.voters_public" class="help-inline text-danger form-control-static">@{{errors.voters_public.toString()}}</div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group" ng-if="contest.voters_public" ng-class="{error: contest.voters_register_picture.$invalid && !contest.voters_register_picture.$pristine}">
            <label for="inputDeadLine2At" class="col-sm-2 control-label">@lang('contest.register_picture')</label>
            <div class="col-sm-8">
                <div class="checkbox">
                    <label ng-if="showThis">
                        <input type="checkbox" name="" ng-model="contest.voters_register_picture" ng-checked="contest.voters_register_picture == 1" ng-true-value="1" ng-false-value="0" id=""/>
                    </label>
                    <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': contest.voters_register_picture == 1,'fa-square-o': contest.voters_register_picture != 1 }"></i>
                </div>
                <div ng-show="errors.voters_register_picture" class="help-inline text-danger form-control-static">@{{errors.voters_register_picture.toString()}}</div>
            </div>
            <div class="clearfix"></div>
        </div>
        @include('admin.contests.deadlines', array('start' => 'contest.voters_start_at', 'deadline1' => 'contest.voters_deadline1_at', 'deadline2' => 'contest.voters_deadline2_at', 'errstart' => 'voters_start_at', 'errdeadline1' => 'voters_deadline1_at', 'errdeadline2' => 'voters_deadline2_at', 'forTypes' => 'contest.judgeRegistration'))
    </div>
    <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Calendar</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12 col-lg-12">
                        <div ui-calendar="uiConfig.calendar" ng-model="contestEvents"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
