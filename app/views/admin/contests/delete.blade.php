@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-trash"></i> @lang('contest.deleteContest')
@endsection
@section('modal-content')
        <p>@lang('contest.sure') [@{{contest.id}}] @{{contest.name}} (@{{contest.code}})?</p>

        <div class="form-group" ng-class="{'has-error': !!errors.captcha}">
            <div class="well well-sm captcha-well text-center">
                <img ng-src="@{{captchaUrl}}" alt="Captcha image" class="captcha-img"/>
                <input type="text" id="inputCaptcha" class="form-control captcha-input input-sm" placeholder="<?=Lang::get('register.captcha')?>" ng-model="captcha" required>
                <div class="clearfix"></div>
                <span class="help-block" ng-show="errors.captcha">@{{errors.captcha.join()}}</span>
            </div>
        </div>
@endsection
@section('modal-actions')
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !modalForm.$dirty">
        <span ng-bind-html="flash"></span>
    </div>
    <button type="button" class="btn btn-default" ng-click="close()">@lang('general.close')</button>
    <button type="button" class="btn btn-danger" ng-click="destroy()">@lang('general.delete')</button>
@endsection