@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-trash"></i> @lang('contest-file.deleteContestFile')
@endsection
@section('modal-content')
        <p>@lang('contest-file.sure') [@{{contestFile.id}}] @{{contestFile.name}} (@{{contestFile.label}})?</p>
@endsection
@section('modal-actions')
    <button type="button" class="btn btn-default" ng-click="close()">@lang('general.close')</button>
    <button type="button" class="btn btn-danger" ng-click="destroy()">@lang('general.delete')</button>
@endsection