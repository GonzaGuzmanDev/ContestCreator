@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-trash-o"></i> @lang('account.deleteAccount')
@endsection

@section('modal-content')
    @lang('account.deleteAccount.confirm')
    <br/>
    <span class="text-danger">@lang('account.deleteAccount.warn')</span>
@endsection

@section('modal-actions')
    <button type="button" class="btn btn-danger" ng-click="delete()">Si</button>
    <button type="button" class="btn btn-default" ng-click="close()" focus-me="true">No</button>
@endsection
