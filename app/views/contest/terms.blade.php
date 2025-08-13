@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-trash-o"></i> {{$contest->name}} - @lang('contest.terms')
@endsection

@section('modal-content')
    {{$contest->getAsset(ContestAsset::TERMS)->content}}
@endsection

@section('modal-actions')
    <button type="button" class="btn btn-default" ng-click="close()" focus-me="true">Cerrar</button>
@endsection
