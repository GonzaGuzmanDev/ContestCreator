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
        <a href="<?= $download ? "../screening/index.html" : url($contest->code.'/voting/'.$votingSession->code."/static/"   ) ?>" class="btn btn-info"><i class="fa fa-arrow-circle-left"></i> Volver</a>
        <h1 style="margin-bottom: 20px;">
            {{ $contest->name }}
            @if(!$download)
                <a href="<?=url($contest->code.'/voting/'.$votingSession->code.'/static/download')?>" class="btn btn-success pull-right">
                    <i class="fa fa-download"></i> Download screening (.zip)
                </a>
            @endif
        </h1>
        <h3>{{isset($group) ? $group->name : ''}}</h3>
        @foreach($categories as $cat)
            @if($cat->final)
                <div class="well well-sm">
                    <h4>
                    <a href="{{ $download ? 'cats/'.(isset($groups) ? $groupIndex."/":"").$cat->id.'/index.html' : url($contest->code.'/voting/'.$votingSession->code.'/static/'.($groupIndex != null ? $groupIndex : "0")."/".$cat->id)}}">
                        {{$cat->name}}
                    </a>
                    </h4>
                </div>
            @endif
        @endforeach
        <br>
        <br>
        <br>
        <br>
        <br>
    </div>
@stop
