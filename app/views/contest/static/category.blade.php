@extends('contest.static.layout')

@section('css')
    <link rel="stylesheet" href="<?= $download ? ($groups ? "../":"")."../../" : asset("/") ?>css/bootstrap.min.css"/>
    <link rel="stylesheet" href="<?= $download ? ($groups ? "../":"")."../../" : asset("/") ?>css/font-awesome.min.css"/>
    <link rel="stylesheet" href="<?= $download ? ($groups ? "../":"")."../../" : asset("/") ?>css/bootstrap.slate.css"/>
    <link rel="stylesheet" href="<?= $download ? ($groups ? "../":"")."../../" : asset("/") ?>css/app.css"/>
    <link rel="stylesheet" href="<?= $download ? ($groups ? "../":"")."../../" : asset("/") ?>css/responsive.css"/>
@stop
@section('content')
    <div class="container">
        <a href="<?= $download ? ($groups ? "../":"")."../../".(isset($group) ? $group['name'] : "index").".html" : url($contest->code.'/voting/'.$votingSession->code."/static/".$groupIndex."/") ?>" class="btn btn-info"><i class="fa fa-arrow-circle-left"></i> Volver</a>
        <h1>
            {{ $contest->name }}
        </h1>
        <h3>{{isset($group) ? $group->name : ''}}</h3>
        <div class="well well-sm">
            <h4>
                <? $cCat = $category;
                $breadCrumbs = "";?>
                @while($parent = $cCat->parentCategory)
                    <? $breadCrumbs = ($parent->parent_id == null ? '<i class="fa fa-chevron-right"></i> ':'').$parent->name.' <i class="fa fa-angle-double-right"></i> '.$breadCrumbs; ?>
                    <? $cCat = $parent; ?>
                @endwhile
                {{$breadCrumbs}} {{$category->name}}
            </h4>
        </div>
        @if(count($entries) == 0)
            <div class="alert alert-info text-center">No hay entries en esta categor&iacute;a</div>
        @endif
        @foreach($entries as $entry)
            @if($entry->status == Entry::APPROVE)
            <div class="well well-sm entry">
                <div class="row">
                    <div class="entry-title col-xs-12">
                        <a href="<?= $download ? 'entries/'.$entry->id.'.html' : url($contest->code.'/voting/'.$votingSession->code."/static/".$groupIndex."/".$category->id."/".$entry->id) ?>">
                            <span class="entry-id text-info">#{{ $entry->id }}</span>
                            <span >{{ $entry->getName() }}</span>
                            <div class="thumbs">
                            @foreach($entry->filesFields as $fFields)
                                <?$md = $fFields->entry_metadata_field;
                                    $config = json_decode($md->config);?>
                                @if($config->important == 1)
                                    @foreach($fFields->files as $file)
                                        @if($file->type == Format::VIDEO || $file->type == Format::IMAGE || $file->type == Format::AUDIO)
                                            <img src="{{ $download ? ($groups ? "../":"")."../../media/thumbs/".$file->id."/th".($file->type == Format::VIDEO ? "-".str_pad(5, 4, "0", STR_PAD_LEFT) : "").".jpg" : $file->toArray()['thumb']}}" alt="">
                                        @endif
                                    @endforeach
                                @endif
                                @endforeach
                            </div>
                            <div class="clearfix"></div>
                        </a>
                    </div>
                </div>
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
