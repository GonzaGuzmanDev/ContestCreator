@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-lock"></i> @lang('login.passwordForget')
@endsection

@section('modal-submit')
submit()
@endsection

@section('modal-content')
@lang('reminders.form.description')
<div class="form-group">
    <br/>
    <label for="inputEmail">@lang('login.email')</label>
    <input type="email" id="inputEmail" class="form-control" placeholder="" ng-model="rememberForm.email" required focus-me="true">
</div>

<div class="alert alert-@{{flashStatus}}" ng-show="flash">
    <span ng-bind="flash"></span>
</div>
@endsection

@section('modal-actions')
        <button class="btn btn-success" type="submit"><?=Lang::get('general.accept')?></button>
        <button class="btn btn-default" type="button" ng-click="cancel()"><?=Lang::get('general.cancel')?></button>
@endsection