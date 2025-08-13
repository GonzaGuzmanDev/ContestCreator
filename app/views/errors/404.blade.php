@extends('layouts.default')

@section('content')
    <div class="error404">
        <a href="<?=URL::to('/');?>/#/home"><img src="<?=asset('img/logo-white.png')?>" alt="" class="logo"/></a>
        <br/>
        <br/>
        <img src="<?=asset('img/error.png')?>" alt=""/>
        <br/>
        <br/>
        <div class="text-info">{{$msg}}</div>
    </div>

    @include('includes.footer')
@stop

@section('appjs')
    <script src="<?php echo asset('js/app/error.js'); ?>"></script>
@stop
@section('app-config')
    <script>
        OxoAwards.constant('contest', null);
    </script>
@stop
@section('appcss')
    <link rel="stylesheet" href="<?php echo asset('css/errors.css'); ?>"/>
@stop