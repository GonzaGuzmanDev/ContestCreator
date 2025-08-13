@extends('layouts.default')
@section('content')
    <div class="" ng-view></div>
@stop

@section('app-config')
    <script>
        OxoAwards.constant('inviteRegister', {{$allowRegister ? 'true':'false'}});
        OxoAwards.constant('contest', @if(isset($contest)) {{$contest->toJson(JSON_NUMERIC_CHECK)}} @else {{'null'}} @endif);
    </script>
@stop
@section('appcss')
    <link rel="stylesheet" href="<?php echo asset('css/contest.css'); ?>"/>
@stop
@section('appjs')
    <script src="<?php echo asset('js/app/invite.js'); ?>"></script>
@stop
@section('app-controllersjs')
    <script src="<?php echo asset('js/controllers/invite.js'); ?>"></script>
@stop