@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-cog"></i> @lang('contest.log')
@endsection
@section('modal-content')
    <div class="container col-md-12">
    <strong>@lang('contest.entryLogMsg')</strong>
    <textarea style="resize:none" type="text" rows="5" class="form-control col-md-8" placeholder="@lang('contest.describeMsg')" id="" required ng-model="msg"/>
    </div>
    <div class="clearfix"></div>
@endsection
@section('modal-actions')
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !modalForm.$dirty">
        <span ng-bind-html="flash"></span>
    </div>
    <button type="button" class="btn btn-default" ng-click="cancel()">@lang('general.cancel')</button>
    <button type="button" class="btn btn-default" ng-disabled="(status == {{ Entry::ERROR }} && !msg) || (tech_status == {{ ContestFile::TECH_ERROR }} && !msg)" ng-click="accept()">@lang('general.ok')</button>
@endsection