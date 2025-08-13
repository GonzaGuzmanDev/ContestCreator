<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="<?= asset("/") ?>css/bootstrap.min.css"/>
<link rel="stylesheet" href="<?= asset("/") ?>css/font-awesome.min.css"/>
<link rel="stylesheet" href="<?= asset("/") ?>css/app.css"/>
<link rel="stylesheet" href="<?= asset("/") ?>css/responsive.css"/>
<style>
    body{
        background-color: white;
    }

</style>
<div class="container printEntry">
    @if(isset($contest))
        {{ $contest->getAsset(ContestAsset::SMALL_BANNER_HTML)->content }}
    @endif
    <div class="col-xs-12">
        <h4 class="col-sm-12"> @lang('general.entryId') : {{ $entryId }} </h4>
        <h4 class="col-sm-12"> <b>{{ $category }}</b></h4>
    </div>
    <br><br>
    <br><br>
    <div class="entry-print col-xs-offset-1 col-sm-offset-1 col-sm-10 col-xs-10">
        @foreach($entry_metadata_values_with_fields as $field)
            <div class="col-xs-12">
                @if($field->type == MetadataField::TITLE || $field->type == MetadataField::DESCRIPTION)
                    <div>
                        @if(sizeof(json_decode($field->config)->options) > 0)
                        <{{json_decode($field->config)->options}}>
                            <b>{{$field->label}}</b>
                        </{{json_decode($field->config)->options}}>
                        @else
                            <b>{{$field->label}}</b>
                        @endif
                    </div>
                @endif
                @if($field->type == MetadataField::TEXTAREA || $field->type == MetadataField::RICHTEXT)
                    <div class="col-xs-12">
                        <h4><b>{{$field->label}}</b></h4>
                    </div>
                    <div class="col-xs-12">
                        <h5><i> {{$field->description}} </i></h5>
                    </div>
                    @if($field->type == MetadataField::TEXTAREA)
                        <div class="col-xs-12 text-left">
                            <div class="form-control-static-print">
                                <h4><?php echo trim($field->value) ?></h4>
                            </div>
                        </div>
                    @endif
                    @if($field->type == MetadataField::RICHTEXT)
                        <div class="col-xs-12 text-left">
                            <div class="form-control-static-print">
                                <h4> <?php echo trim($field->value) ?></h4>
                            </div>
                        </div>
                    @endif
                @endif
                @if($field->type == MetadataField::TEXT ||
                $field->type == MetadataField::EMAIL ||
                $field->type == MetadataField::DATE ||
                $field->type == MetadataField::NUMBER)
                    <div class="col-xs-5 text-left">
                        <h4><b>{{$field->label}}</b></h4>
                    </div>
                    <div class="col-xs-6 form-control-static-print">
                        <h4>{{$field->value}}</h4>
                    </div>
                @if($field->type == MetadataField::SELECT)
                    <div class="col-xs-12">
                        <h4><b>{{$field->label}}</b></h4>
                    </div>
                    <div class="col-xs-12">
                        <h5><i> {{$field->description}} </i></h5>
                    </div>
                    <div class="col-xs-12 text-left">
                        <div class="form-control-static-print">
                            <h4> <?php echo trim($field->value) ?></h4>
                        </div>
                    </div>
                @endif
                @endif
                @if($field->type == MetadataField::TAB)
                    <hr style="border-color: navajowhite;">
                @endif
                @if($field->type == MetadataField::MULTIPLE && is_array(json_decode($field->value)))
                    <div class="col-xs-12">
                        <h4><b>{{$field->label}}</b></h4>
                    </div>
                    <div class="col-xs-12">
                        <h5><i> {{$field->description}} </i></h5>
                    </div>
                    <div class="col-xs-12">
                        @foreach(json_decode($field->value) as $value)
                            @if(json_decode($field->config)->horizontal == 1)
                                <div class="checkbox col-sm-3">
                                    <h4>{{json_decode($field->config)->options[$value]}}</h4>
                                </div>
                            @endif
                            @if(json_decode($field->config)->horizontal == 0)
                                <div class="checkbox">
                                    <h4>{{json_decode($field->config)->options[$value]}}</h4>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
                @if($field->type == MetadataField::MULTIPLEWITHCOLUMNS)
                <div class="col-xs-12 text-left">
                        <h4><b>{{$field->label}}</b></h4>
                    </div>
                    <div class="col-xs-12">
                        <h5><i> {{$field->description}} </i></h5>
                    </div>
                    <div class="col-xs-12">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th> # </th>
                                @foreach(json_decode($field->config)->columns as $value)
                                    <th>
                                        <div class="text-center">{{ $value }}</div>
                                    </th>
                                @endforeach
                            </tr>
                            </thead>
                            <!-- Muestro los nombres de las opciones -->
                            <tbody class="text-center">
                            @foreach(json_decode($field->config)->labels as $row)
                                <tr>
                                    <td>
                                        {{ $row }}
                                    </td>
                                    @foreach(json_decode($field->config)->columns as $column)
                                        <td>
                                            @if(is_array($value = $contest->getValueMultipleWithColumns($field, $row, $column)))
                                                @foreach($value as $key => $val)
                                                    {{$val}}
                                                @endforeach
                                            @else
                                                {{$value}}
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                @if($field->type == MetadataField::FILE)
                    <div class="col-xs-12">
                        <h4><b>{{$field->label}}</b></h4>
                    </div>
                    <div class="col-xs-12">
                        <h5><i> {{$field->description}} </i></h5>
                    </div>
                    <div class="form-control-static col-xs-12">
                            @for($i = 0; $i < sizeof($field->files); $i++)
                                @foreach($field->files[$i]->contest_file_versions as $fileVersion)
                                    @if($fileVersion->source == 0 && $fileVersion->contest_file->type == Format::IMAGE)
                                    <span class="list-group row selected-files">
                                        <span class="file-print-pdf">
                                                <span class="thumbnail-pdf">
                                                    <span class="img-holder-print">
                                                        <img src="{{$fileVersion->image}}" alt="">
                                                    </span>
                                                </span>
                                            </span>
                                    </span>
                                    @endif
                                @endforeach
                            @endfor
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

