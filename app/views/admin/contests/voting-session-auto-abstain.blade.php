@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-check"></i> @lang('voting.autoAbstain')
@endsection
@section('modal-content')
    <div class="checkbox" ng-repeat="field in fields">
        <input type="checkbox" checklist-model="selected" checklist-value="field.id"> @{{ field.label }}
    </div>
@endsection
@section('modal-actions')
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !modalForm.$dirty">
        <span ng-bind-html="flash"></span>
    </div>
    <button type="button" class="btn btn-default" ng-click="close()">@lang('general.close')</button>
    <button type="button" class="btn btn-success" ng-click="send()">@lang('general.accept')</button>
@endsection