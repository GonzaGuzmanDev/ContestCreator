@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-trash"></i> @lang('contest.VoteDeleteJudge')
@endsection
@section('modal-content')
        <p>@lang('contest.VoteConfirmDeleteJudge') "@{{ voting.name }}"?</p>
        <span user-card user-card-model="judge.inscription.user" ng-if="judge.inscription && judge.inscription.user" user-show-email="true"></span>
        <span ng-if="judge.inscription.invitename">@{{ judge.inscription.invitename }}</span>
        <span ng-if="judge.inscription.email">@{{ judge.inscription.email }}</span>
        <br>
        <br>
        <div class="alert alert-danger">
            <i class="fa fa-warning"></i> @lang('contest.VoteDeleteJudgeWarning')
        </div>
@endsection
@section('modal-actions')
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !modalForm.$dirty">
        <span ng-bind-html="flash"></span>
    </div>
    <button type="button" class="btn btn-default" ng-click="close()">@lang('general.close')</button>
    <button type="button" class="btn btn-danger" ng-click="destroy()">@lang('general.delete')</button>
@endsection