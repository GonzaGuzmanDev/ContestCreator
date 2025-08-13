<div class="row">
    <div class="col-md-6">
        <div class="input-group ">
            <span class="input-group-addon" ng-if="{{!$draggable}}">@{{ $index+1 }}</span>
            <span class="input-group-btn" ng-if="{{$draggable}}">
                <span class="btn btn-default" uib-tooltip="@lang('metadata.sort')" ng-class="{'handle':{{$draggable ? 1 : 0}}}">
                    @{{ $index+1 }}
                    <i class="fa fa-arrows-v"></i>
                </span>
            </span>
            <input type="text" class="form-control" ng-model="field.label" required ng-if="selectedLang == langs.Default">
            <input type="text" class="form-control input-trans" ng-model="field.trans[key].label" placeholder="@{{!field.trans[key].label ? field.label : ''}}" ng-if="selectedLang == key" ng-repeat="(key,lang) in langs.Editables">
        </div>

        <div ng-if="field.type != {{MetadataField::DESCRIPTION}}">
            <textarea type="text" class="form-control" ng-model="field.description" ng-if="selectedLang == langs.Default"
                    placeholder="@lang('metadata.description')" style="max-width: 100%;"></textarea>
            <textarea type="text" class="form-control input-trans" ng-model="field.trans[key].description" style="max-width: 100%;"
                      placeholder="@{{!field.trans[key].description ? field.description : ''}}" ng-if="selectedLang == key" ng-repeat="(key,lang) in langs.Editables"></textarea>
            <div ng-if="field.type == {{ MetadataField::MULTIPLE }}">
                <!--<button type="button" class="btn btn-default pull-right" ng-click="field.showConfig = !field.showConfig" ng-show="!field.showConfig"><i class="fa fa-cog"></i></button>-->
                <!--<div ng-show="field.showConfig">-->
                <label class="checkbox-inline">
                    <input type="checkbox" ng-model="field.config.horizontal" ng-checked="field.config.horizontal == 1" ng-true-value="1" ng-false-value="0"> @lang('metadata.horizontal')
                </label>
                <!--</div>-->
            </div>
            <span ng-if="field.type == {{MetadataField::FILE}}">
                <button type="button" class="btn btn-default pull-right" ng-click="field.showConfig = !field.showConfig" ng-show="!field.showConfig"><i class="fa fa-cog"></i></button>
                <div ng-show="field.showConfig">
                    <strong>@lang('metadata.mediatype')</strong>
                    <br/>
                    @foreach(Format::getAllTypesData() as $id => $label)
                        <label class="checkbox-inline">
                            <input type="checkbox" checklist-model="field.config.types" checklist-value="{{$id}}"> {{$label}}
                        </label>
                    @endforeach
                    <br/><br/>
                    <strong>@lang('metadata.ammount') <i class="fa fa-question-circle" uib-tooltip="@lang('metadata.ammount-tt')"></i></strong>
                    <br/>
                    @lang('metadata.ammountmin') <input type="number" min="0" max="20" value="0" ng-model="field.config.min" class="form-control form-inline input-sm"/>
                    @lang('metadata.ammountmax') <input type="number" min="0" max="20" value="0" ng-model="field.config.max" class="form-control form-inline input-sm"/>
                </div>
            </span>
        </div>

        <div class="btn-group form-control-static pull-right" role="group" ng-if="{{!$draggable}}">
            <button type="button" class="btn btn-sm btn-default" ng-disabled="$index == 0" ng-click="moveFieldUp({{$list}},$index)"><i class="fa fa-arrow-up"></i></button>
            <button type="button" class="btn btn-sm btn-default" ng-disabled="$index == {{$list}}.length - 1" ng-click="moveFieldDown({{$list}},$index)"><i class="fa fa-arrow-down"></i></button>
        </div>
    </div>
    <div class="col-md-3">
        <select ng-model="field.type" class="form-control">
            <option></option>
            @foreach(MetadataField::getAllTypesData() as $typeId => $typeLabel)
                <option value="{{$typeId}}">{{$typeLabel}}</option>
            @endforeach
        </select>
        <div ng-if="field.type == {{MetadataField::LINK}}">
            <i>@lang('metadata.link')</i>
            <input type="text" class="form-control" ng-model="field.config.link" required>
            <div class="btn-group" required>
                @lang('metadata.options')
                <br>
                <label class="btn-sm btn-primary" ng-model="field.config.type" uib-btn-radio="'button'"> @lang('metadata.button') </label>
                <input type="text" class="form-control" ng-model="field.config.buttonText[index]" ng-if="selectedLang == langs.Default" placeholder="@lang('metadata.text')">
                <input type="text" class="form-control input-trans" ng-model="field.trans.buttonText[key][find]" ng-if="selectedLang == key" placeholder="@lang('metadata.text')" ng-repeat="(key,lang) in langs.Editables">
                <label class="btn-sm btn-primary" ng-model="field.config.type" uib-btn-radio="'hyperText'"> @lang('metadata.hyperText') </label>
                <input type="text" class="form-control" ng-model="field.config.hypText[index]" ng-if="selectedLang == langs.Default" placeholder="@lang('metadata.text')">
                <input type="text" class="form-control input-trans" ng-model="field.trans.hypText[key][find]" ng-if="selectedLang == key" placeholder="@lang('metadata.text')" ng-repeat="(key,lang) in langs.Editables">
                <label class="btn-sm btn-primary" ng-model="field.config.type" uib-btn-radio="'image'"> @lang('metadata.image') </label><br>
            </div>
        </div>
        <div ng-if="field.type == {{MetadataField::SELECT}} || field.type == {{MetadataField::MULTIPLE}}">
            <i>@lang('metadata.options')</i>
            <div class="input-group input-group-sm" ng-repeat="option in field.config.options track by $index" ng-init="find = $index">
                <input type="text" class="form-control" ng-model="field.config.options[$index]" required ng-if="selectedLang == langs.Default">
                <input type="text" class="form-control input-trans" ng-model="field.trans.options[key][find]" placeholder="@{{!field.trans.options[key][find] ? field.config.options[find] : ''}}" ng-if="selectedLang == key" ng-repeat="(key,lang) in langs.Editables">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button" ng-click="field.config.options.splice($index,1)"><i class="fa fa-remove"></i></button>
                </span>
            </div>
            <div class="clearfix"></div>
            <a href="" ng-click="addFieldOption(field)" class="btn btn-info btn-sm"><i class="fa fa-plus"></i></a>
        </div>
        <div ng-if="field.type == {{MetadataField::TITLE}}">
            <div class="btn-group">
                @lang('metadata.size') <br>
                <label class="btn-sm btn-primary" ng-model="field.config.options" uib-btn-radio="'h1'">@lang('metadata.bigger')</label><br>
                <label class="btn-sm btn-primary" ng-model="field.config.options" uib-btn-radio="'h2'">@lang('metadata.big')</label><br>
                <label class="btn-sm btn-primary" ng-model="field.config.options" uib-btn-radio="'h3'">@lang('metadata.medium')</label><br>
                <label class="btn-sm btn-primary" ng-model="field.config.options" uib-btn-radio="'h4'">@lang('metadata.normal')</label><br>
            </div>
        </div>
        <div ng-if="field.type == {{MetadataField::TEXTAREA}}">
            @lang('metadata.characters') <input type="number" class="form-control" ng-model="field.config.characters" value="-1">
        </div>
        <div ng-if="field.type == {{MetadataField::MULTIPLEWITHCOLUMNS}}">
            <i>@lang('metadata.columns')</i>
            <div class="input-group input-group-sm" ng-repeat="column in field.config.columns track by $index" ng-init="find = $index">
                <input type="text" class="form-control" ng-model="field.config.columns[$index]" required ng-if="selectedLang == langs.Default">
                <input type="text" class="form-control input-trans" ng-model="field.trans.columns[key][find]" placeholder="@{{!field.trans.columns[key][find] ? field.config.columns[find] : ''}}" ng-if="selectedLang == key" ng-repeat="(key,lang) in langs.Editables">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button" ng-click="field.config.columns.splice($index,1)"><i class="fa fa-remove"></i></button>
                </span>
            </div>
            <div class="clearfix"></div>
            <a ng-if="field.config.columns.length < 8 || !field.config.columns" href="" ng-click="addFieldColumn(field)" class="btn btn-info btn-sm"><i class="fa fa-plus"></i></a>
            <br>

            <i>@lang('metadata.labels')</i>
            <div ng-repeat="label in field.config.labels track by $index" ng-init="find = $index">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" ng-model="field.config.labels[$index]" required ng-if="selectedLang == langs.Default">
                    <input type="text" class="form-control input-trans" ng-model="field.trans.labels[key][find]" placeholder="@{{!field.trans.labels[key][find] ? field.config.labels[find] : ''}}" ng-if="selectedLang == key" ng-repeat="(key,lang) in langs.Editables">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button" ng-click="field.config.labels.splice($index,1)"><i class="fa fa-remove"></i></button>
                    </span>
                </div>
                <input type="checkbox" ng-model="field.config.text[field.config.labels[$index]]" ng-checked="field.config.text[field.config.labels[$index]] == 1" ng-true-value="1" ng-false-value="0"> @lang('metadata.text')
            </div>
            <div class="clearfix"></div>
            <a href="" ng-click="addFieldLabel(field)" class="btn btn-info btn-sm"><i class="fa fa-plus"></i></a>
        </div>
        <div ng-if="field.type == {{MetadataField::DATE}}">
            <i>@lang('metadata.dateMin')</i>
            <div class="dropdown">
                <a class="dropdown-toggle" id="dropdown@{{$index}}" role="button" data-toggle="dropdown">
                    <div class="input-group">
                        <input type="text" class="form-control" id="input@{{$index}}" ng-model="field.config.minDate" date-field>
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                    <datetimepicker ng-model="field.config.minDate"></datetimepicker>
                </ul>
            </div>
            <i>@lang('metadata.dateMax')</i>
            <div class="dropdown">
                <a class="dropdown-toggle" id="dropdown@{{$index}}" role="button" data-toggle="dropdown">
                    <div class="input-group">
                        <input type="text" class="form-control" id="input@{{$index}}" ng-model="field.config.maxDate" date-field>
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                    <datetimepicker ng-model="field.config.maxDate"></datetimepicker>
                </ul>
            </div>
        </div>

    </div>
    <div class="col-md-3">
        <div class="pull-right">
            @if($delete)
            <button type="button" ng-click="removeMetadataField(field)" class="btn btn-sm btn-default" uib-tooltip="@lang('metadata.remove')"><i class="fa fa-remove"></i></button>
            @endif
        </div>
        @if($private)
            <label class="label-stick" uib-tooltip="@lang('metadata.privateexp')" tooltip-placement="bottom">
                <input type="checkbox" ng-model="field.private" ng-checked="field.private == 1" ng-true-value="1" ng-false-value="0"/>
                @lang('metadata.private')
            </label>
            <br>
        @endif
        <label class="label-stick" ng-if="field.type != {{MetadataField::TITLE}} && field.type != {{MetadataField::DESCRIPTION}} && field.type != {{MetadataField::LINK}}  && field.type != {{MetadataField::TAB}}">
            <input type="checkbox" ng-model="field.required" ng-checked="field.required == 1" ng-true-value="1" ng-false-value="0"/>
            @lang('metadata.required')
        </label>
        <!--<label class="label-stick" ng-if="field.type != {{MetadataField::FILE}} && field.type != {{MetadataField::TITLE}} && field.type != {{MetadataField::DESCRIPTION}} && field.type != {{MetadataField::LINK}}  && field.type != {{MetadataField::TAB}}">-->
        <label class="label-stick" ng-if="field.type != {{MetadataField::DESCRIPTION}} && field.type != {{MetadataField::LINK}}  && field.type != {{MetadataField::TAB}}">
            <input type="checkbox" ng-model="field.config.exportable" ng-checked="field.config.exportable == 1" ng-true-value="1" ng-false-value="0"/>
            <i class="fa fa-file-excel-o"></i>
            @lang('metadata.exportable')
        </label>
        <label class="label-stick" ng-if="field.type != {{MetadataField::TAB}}">
            <input type="checkbox" ng-model="field.config.important" ng-checked="field.config.important == 1" ng-true-value="1" ng-false-value="0"/>
            <i class="fa fa-exclamation-circle"></i>
            @lang('metadata.important')
        </label>
        @if($config)
        <div class="clearfix"></div>
        <div ng-if="EntryMetadataTemplates.length">
            <i class="fa fa-eye" uib-tooltip="@lang('contest.inscriptionType.visible')" tooltip-placement="top"></i>
            <i class="fa fa-asterisk" uib-tooltip="@lang('contest.inscriptionType.required')" tooltip-placement="top" ng-if="field.type != {{MetadataField::TITLE}} && field.type != {{MetadataField::DESCRIPTION}} && field.type != {{MetadataField::LINK}}  && field.type != {{MetadataField::TAB}}"></i>
            <div class="" ng-repeat="(ind,template) in EntryMetadataTemplates" ng-init="c = getFieldTemplateConfig(field,template);">
                <input type="checkbox" name="" ng-model="c.visible" ng-true-value="1" ng-false-value="0" id=""/>
                <input type="checkbox" name="" ng-model="c.required" ng-true-value="1" ng-false-value="0" ng-disabled="!c.visible" id="" ng-if="field.type != {{MetadataField::TITLE}} && field.type != {{MetadataField::DESCRIPTION}}  && field.type != {{MetadataField::LINK}}  && field.type != {{MetadataField::TAB}}"/>
                <span ng-bind-html="template.name || '@lang('metadata.templatenoname')'"></span>
            </div>
        </div>
        @endif
    </div>
    <div class="clearfix"></div>
</div>