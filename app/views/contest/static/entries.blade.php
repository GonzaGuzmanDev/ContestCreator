@extends('contest.static.layout')

@section('css')
    <link rel="stylesheet" href="<?= $download ? "" : asset("/") ?>css/bootstrap.min.css"/>
    <link rel="stylesheet" href="<?= $download ? "" : asset("/") ?>css/font-awesome.min.css"/>
    <link rel="stylesheet" href="<?= $download ? "" : asset("/") ?>css/bootstrap.slate.css"/>
    <link rel="stylesheet" href="<?= $download ? "" : asset("/") ?>css/app.css"/>
    <link rel="stylesheet" href="<?= $download ? "" : asset("/") ?>css/responsive.css"/>
@stop
@section('content')
    @foreach($entries as $entry)
        <div class="container">

            <div class="well well-lg entry">
                <div class="row">
                    <span class="entry-title col-lg-12">
                        <h3 class="entry-id text-info">#{{ $entry->id }} {{ $entry->getName() }}</h3>
                    </span>
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
                                    <video controls style="width: 100%;">
                                        @foreach($file->contest_file_versions as $fv)
                                            @if($fv->source == 0 && ($fv->format == null || $fv->format->position = 1))
                                                <? $url = $download ? ($groups ? "":"")."media/".$fv->id.".".$fv->extension : str_replace('newawards.oxobox.tv/awards-pica','oxoawards.com',$fv->toArray()['url']); ?>
                                                <source src="{{$url}}" type="video/mp4">
                                            @endif
                                        @endforeach
                                    </video>
                                @elseif($file->type == Format::AUDIO)
                                    <audio controls style="width: 100%;">
                                        @foreach($file->contest_file_versions as $fv)
                                            @if($fv->source == 0)
                                                <? $url = $download ? ($groups ? "":"")."media/".$fv->id.".".$fv->extension : str_replace('newawards.oxobox.tv/awards-pica','oxoawards.com',$fv->toArray()['url']); ?>
                                                <source src="{{$url}}" type="audio/mp3">
                                            @endif
                                        @endforeach
                                    </audio>
                                @elseif($file->type == Format::IMAGE)
                                    @foreach($file->contest_file_versions as $fv)
                                        @if($fv->source == 0)
                                            <? $url = $download ? ($groups ? "":"")."media/".$fv->id.".".$fv->extension : str_replace('newawards.oxobox.tv/awards-pica','oxoawards.com',$fv->toArray()['url']); ?>
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
                @elseif($val->entry_metadata_field->type != EntryMetadataField::FILE && false)
                    <div class="row">
                        <div class="col-sm-2">
                            <h4>{{$val->EntryMetadataField->label}}</h4>
                        </div>
                        <div class="col-sm-10">
                            <div class="form-control-static">
                                {{nl2br($val->value)}}
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
            <hr>
            <hr>
            <hr>
        </div>
    @endforeach
@stop
