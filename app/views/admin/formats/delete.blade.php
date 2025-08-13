@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-trash"></i> @lang('format.deleteFormat')
@endsection
@section('modal-content')
        <p>@lang('format.sure') [@{{format.id}}] @{{format.name}} (@{{format.label}})?</p>
@endsection
@section('modal-actions')
    <button type="button" class="btn btn-default" ng-click="close()">@lang('general.close')</button>
    <button type="button" class="btn btn-danger" ng-click="destroy()">@lang('general.delete')</button>
@endsection