@extends('contest.static.layout')

@section('css')
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="<?= asset("/") ?>css/font-awesome.min.css"/>
    <link rel="stylesheet" href="<?= asset("/") ?>css/bootstrap.slate.css"/>
    <link rel="stylesheet" href="<?= asset("/") ?>css/app.css"/>
    <link rel="stylesheet" href="<?= asset("/") ?>css/responsive.css"/>
@stop

@section('content')
    @foreach($entries as $entry)
            @foreach($entry as $key => $value)
                @if($value != "-")
                    {{$key}} : {{$value}}
                @endif
            @endforeach
            ------------------------------------------------------------------
    @endforeach
@stop
