@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-trash"></i> @lang('collection.deleteCollection')
@endsection
@section('modal-content')
        <p>@lang('collection.deleteCollectionOk') "@{{collection.name}}"?</p>
@endsection
@section('modal-actions')
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !modalForm.$dirty">
        <span ng-bind-html="flash"></span>
    </div>
    <button type="button" class="btn btn-default" ng-click="close()">@lang('general.close')</button>
    <button type="button" class="btn btn-danger" ng-click="destroy()">@lang('general.delete')</button>
@endsection