@extends('layouts.default')
@section('content')
    <div class="" ng-view></div>
@stop
@section('app-config')
    <script>
        OxoAwards.constant('billingStatus', {{$billingStatus}});
        OxoAwards.constant('bill', {{$bill->toJson(JSON_NUMERIC_CHECK)}});
        OxoAwards.constant('entry', {{$entry->toJson(JSON_NUMERIC_CHECK)}});
        OxoAwards.constant('contest', @if(isset($contest)) {{$contest->toJson(JSON_NUMERIC_CHECK)}} @else {{'null'}} @endif);
    </script>
@stop
@section('appcss')
    <link rel="stylesheet" href="<?php echo asset('css/contest.css'); ?>"/>
@stop
@section('appjs')
    <script src="<?php echo asset('js/app/billing-redirect.js'); ?>"></script>
@stop
@section('app-controllersjs')
    <script src="<?php echo asset('js/controllers/billing-redirect.js'); ?>"></script>
@stop