@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-trash"></i> @lang('contest.deleteInscription')
@endsection
@section('modal-content')
        <p>@lang('contest.sureInscription') [@{{inscription.id}}] @{{inscription.first_name}} @{{inscription.last_name}}?</p>
        <p>@lang('contest.inscriptionRole'): <strong>@{{allRoles[inscription.role]}}</strong></p>
@endsection
@section('modal-actions')
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !modalForm.$dirty">
        <span ng-bind-html="flash"></span>
    </div>
    <button type="button" class="btn btn-default" ng-click="close()">@lang('general.close')</button>
    <button type="button" class="btn btn-danger" ng-click="destroy()">@lang('general.delete')</button>
@endsection