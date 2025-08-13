@extends('layouts.default')
@section('content')
    <div class="" ng-view></div>
@stop

@section('appjs')
    <script src="<?php echo asset('js/app/reset.js'); ?>"></script>
    <script>
    OxoAwards.constant('resetToken', "{{$token}}");
    </script>
@stop
@section('app-config')
    <script>
        OxoAwards.constant('contest', null);
    </script>
@stop
@section('app-controllersjs')
    <script src="<?php echo asset('js/controllers/reset.js'); ?>"></script>
@stop