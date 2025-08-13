@extends('layouts.default')

@section('content')
<div class="" ng-view></div>
@stop

@section('appcss')
    <link rel="stylesheet" href="<?php echo asset('css/site.css'); ?>"/>
@stop
@section('appjs')
<script src="<?php echo asset('js/app/site.js'); ?>"></script>
@stop
@section('app-run')
@stop
@section('app-config')
    <script>
        OxoAwards.constant('contest', null);
    </script>
@stop
@section('app-controllersjs')
<script src="<?php echo asset('js/controllers/site.js'); ?>"></script>
@stop