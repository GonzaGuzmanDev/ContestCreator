<? /** @var $entry Entry  */ ?>
@extends('contest.static.layout')

@section('css')
    <link rel="stylesheet" href="<?= $download ? ($groups ? "../":"")."../../../" : asset("/") ?>css/bootstrap.min.css"/>
    <link rel="stylesheet" href="<?= $download ? ($groups ? "../":"")."../../../" : asset("/") ?>css/font-awesome.min.css"/>
    <link rel="stylesheet" href="<?= $download ? ($groups ? "../":"")."../../../" : asset("/") ?>css/bootstrap.slate.css"/>
    <link rel="stylesheet" href="<?= $download ? ($groups ? "../":"")."../../../" : asset("/") ?>css/app.css"/>
    <link rel="stylesheet" href="<?= $download ? ($groups ? "../":"")."../../../" : asset("/") ?>css/responsive.css"/>
@stop
@section('content')
    <div class="container">
        <a href="<?= $download ? "../index.html" : url($contest->code.'/voting/'.$votingSession->code."/static/".$groupIndex."/".$category->id   ) ?>" class="btn btn-info"><i class="fa fa-arrow-circle-left"></i> Volver</a>
        <h1>
            {{ $contest->name }}
        </h1>
        <h3>{{isset($group) ? $group->name : ''}}</h3>
        <div class="well well-sm">
            <a href="<?= $download ? "../index.html" : url($contest->code.'/voting/'.$votingSession->code."/static/".$groupIndex."/".$category->id) ?>">
            <h4>
                <? $cCat = $category;
                $breadCrumbs = "";?>
                @while($parent = $cCat->parentCategory)
                    <? $breadCrumbs = ($parent->parent_id == null ? '<i class="fa fa-chevron-right"></i> ':'').$parent->name.' <i class="fa fa-angle-double-right"></i> '.$breadCrumbs; ?>
                    <? $cCat = $parent; ?>
                @endwhile
                {{$breadCrumbs}} {{$category->name}}
            </h4>
            </a>
        </div>

        <div class="well well-sm entry">
            <div class="row">
                <div class="entry-title col-xs-12">
                    <span class="entry-id text-info">#{{ $entry->id }}</span>
                    <span >{{ $entry->getName() }}</span>
                </div>
            </div>
        </div>
        <? $values = $entry->PublicEntryMetadataValues;//->sortBy('order');
        ?>
        @foreach($values as $val)
            <?$md = $val->EntryMetadataField;
            $config = json_encode($md->config);?>
            <?
            if (strpos($config, 'important\":') !== false) {
                $pos = strpos($config, 'important');
            }?>
            @if($val->entry_metadata_field->type == EntryMetadataField::FILE && substr($config, $pos+12, 1) == 1)
            <h4>{{$val->EntryMetadataField->label}}</h4>
            <br>
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2 text-center">
                        @foreach($val->files as $file)
                            @if($file->type == Format::VIDEO)
                                <video controls="true" style="width: 100%;">
                                @foreach($file->contest_file_versions as $fv)
                                    @if($fv->source == 0 && ($fv->format == null || $fv->format->position = 1))
                                        <? $url = $download ? ($groups ? "../":"")."../../../media/".$fv->id.".".$fv->extension : str_replace('newawards.oxobox.tv/awards-pica','oxoawards.com',$fv->toArray()['url']); ?>
                                        <source src="{{$url}}" type="video/mp4">
                                    @endif
                                @endforeach
                                </video>
                            @elseif($file->type == Format::AUDIO)
                                <audio controls="true" style="width: 100%;">
                                    @foreach($file->contest_file_versions as $fv)
                                        @if($fv->source == 0)
                                            <? $url = $download ? ($groups ? "../":"")."../../../media/".$fv->id.".".$fv->extension : str_replace('newawards.oxobox.tv/awards-pica','oxoawards.com',$fv->toArray()['url']); ?>
                                            <source src="{{$url}}" type="audio/mp3">
                                        @endif
                                    @endforeach
                                </audio>
                            @elseif($file->type == Format::IMAGE)
                                @foreach($file->contest_file_versions as $fv)
                                    @if($fv->source == 0)
                                        <? $url = $download ? ($groups ? "../":"")."../../../media/".$fv->id.".".$fv->extension : str_replace('newawards.oxobox.tv/awards-pica','oxoawards.com',$fv->toArray()['url']); ?>
                                        <a href="{{$url}}" target="_blank">
                                            <img src="{{$url}}" alt="" style="max-width: 100%" class="entry-img">
                                        </a>
                                    @endif
                                @endforeach
                            @else
                                <? /*@foreach($file->contest_file_versions as $fv)
                                    @if($fv->source == 1)
                                        <? $url = $download ? "../../../media/".$fv->id.".".$fv->extension : str_replace('newawards.oxobox.tv/awards-pica','oxoawards.com',$fv->toArray()['url']); ?>
                                        <a href="{{$url}}" target="_blank" class="btn btn-block btn-success">
                                            <i class="fa fa-download"></i> Descargar archivo {{$fv->extension}}
                                        </a>
                                    @endif
                                @endforeach*/ ?>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif
                @if($val->entry_metadata_field->type != EntryMetadataField::FILE && substr($config, $pos+12, 1) == 1)
                <div class="row">
                    <div class="col-sm-2">
                        <h4>{{$val->EntryMetadataField->label}}</h4>
                    </div>
                    <div class="col-sm-10">
                        <div class="form-control-static">
                            <h5>{{nl2br($val->value)}}</h5>
                        </div>
                    </div>
                </div>
                @endif
        @endforeach

        <br>
        <br>
        <br>
    </div>
@stop
