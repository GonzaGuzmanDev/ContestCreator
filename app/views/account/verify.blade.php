@extends('layouts.main')
@section('content')
    <div class="container" ng-controller="initController" ng-init="verified = {{ Auth::check() ? 'true' : 'false' }}">
    @if(isset($error))
    <div class="alert alert-danger"><i class="fa fa-remove"></i> {{$error}}</div>
    @elseif(isset($flash))
    <div class="alert alert-success"><i class="fa fa-check"></i> {{$flash}}</div>
    @endif
    </div>
@stop

@section('app-config')
    <script>
        OxoAwards.constant('contest', null);
    </script>
@stop
@section('appjs')
    <script src="<?php echo asset('js/app/verify.js'); ?>"></script>
@stop
@section('app-controllersjs')
    <script src="<?php echo asset('js/controllers/verify.js'); ?>"></script>
@stop