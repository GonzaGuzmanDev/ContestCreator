@extends('contest.static.layout')

@section('css')
    <link rel="stylesheet" href="<?= $download ? "" : asset("/") ?>css/bootstrap.min.css"/>
    <link rel="stylesheet" href="<?= $download ? "" : asset("/") ?>css/font-awesome.min.css"/>
    <link rel="stylesheet" href="<?= $download ? "" : asset("/") ?>css/bootstrap.slate.css"/>
    <link rel="stylesheet" href="<?= $download ? "" : asset("/") ?>css/app.css"/>
    <link rel="stylesheet" href="<?= $download ? "" : asset("/") ?>css/responsive.css"/>
@stop
@section('content')
    <div class="container">
        <h1 style="margin-bottom: 20px;">
            {{ $contest->name }}
            @if(!$download)
                <a href="<?=url($contest->code.'/voting/'.$votingSession->code.'/static/download')?>" class="btn btn-success pull-right">
                    <i class="fa fa-download"></i> Download screening (.zip)
                </a>
            @endif
        </h1>

        @foreach($groups as $index => $group)
            <div class="well text-center">
                <h2 style="margin: 0;">
                <a href="{{ $download ? $group['name'].'.html' : url($contest->code.'/voting/'.$votingSession->code.'/static/'.$index)}}">
                    {{$group['name']}}
                </a>
                </h2>
            </div>
        @endforeach
        <br>
        <br>
        <br>
        <br>
        <br>
    </div>
@stop
