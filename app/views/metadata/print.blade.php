<div ng-switch="field.type" ng-controller="MetadataFieldController" ng-init="init({{ $mainField or 'formData[field.id]' }})" style="color: black;">
    <div ng-switch-when="{{MetadataField::TAB}}" >
        <h3 class="text-center"><u><trans ng-model='field' trans-prop="'label'"></trans></u></h3>
        <div class="help-block" ng-if="field.description != null"><trans ng-model='field' trans-prop="'description'"></trans></div>
    </div>
    <div ng-switch-when="{{MetadataField::TITLE}}" class="col-xs-12">
        <trans ng-model='field' trans-prop="'label'" ng-class="field.config.options"></trans>
        <div class="help-block" ng-if="field.description != null"><trans ng-model='field' trans-prop="'description'"></trans></div>
    </div>
    <div ng-switch-when="{{MetadataField::DESCRIPTION}}" class="col-xs-12"><trans ng-model='field' trans-prop="'label'"></trans></div>
    <div ng-switch-when="{{MetadataField::FILE}}" class="col-xs-12">
        <label class="col-xs-5 text-left" ng-show="{{ $filemodel or 'formData[field.id]' }}.length">
            <trans ng-model='field' trans-prop="'label'"></trans>
            <div class="help-block">@{{ getFileConfigString(field.config) }}</div>
        </label>
        <div class="col-xs-12">
            <div class="form-control-static">
                <div class="row">
                    @if(!$disabled)
                        <files-gallery ng-files-list="{{ $filemodel or 'formData[field.id]' }}" ng-files-field="field" ng-if="{{ $filemodel or 'formData[field.id]' }}.length">
                            <ul ng-model="{{ $filemodel or 'formData[field.id]' }}" class="list-group row selected-files">
                                <li class="file-print" ng-repeat="file in {{ $filemodel or 'formData[field.id]' }}">
                                <span class="thumbnail">
                                    <div class="img-holder-print">
                                        <img ng-src="@{{ fileversion.url }}" ng-if="fileversion.source == 0" ng-repeat="fileversion in file.contest_file_versions" alt=""/>
                                    </div>
                                    <span class="title">
                                        <i class="fa @{{ getTypeIcon(file.type) }} @{{ getTypeTextStyle(file.type) }}"></i>
                                        @{{ file.name }}
                                    </span>
                                    <div class="clearfix"></div>
                                </span>
                                </li>
                            </ul>
                        </files-gallery>
                    @endif
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="help-block" ng-if="field.description != null && field.description != null"><trans ng-model='field' trans-prop="'description'"></trans></div>
        </div>

        <div ng-show="errors[field.id]" class="help-inline text-danger form-control-static">@{{errors[field.id].toString()}}</div>
    </div>
    <div ng-switch-default>
        <div ng-if="field.type == {{MetadataField::MULTIPLEWITHCOLUMNS}} || field.type == {{MetadataField::TEXTAREA}} || field.type == {{MetadataField::RICHTEXT}}" class="col-xs-12" ng-class="{'col-h4': field.type == {{MetadataField::MULTIPLEWITHCOLUMNS}} }">
            <hr style="border-color: navajowhite;">
            <b><trans ng-model='field' trans-prop="'label'"></trans></b>
            <div class="help-block" ng-if="field.description != null"><trans ng-model='field' trans-prop="'description'"></trans></div>
        </div>
        <label ng-if="field.type != {{MetadataField::MULTIPLEWITHCOLUMNS}} && field.type != {{MetadataField::TEXTAREA}} && field.type != {{MetadataField::RICHTEXT}} && field.type != {{MetadataField::LINK}}" for="input@{{$index}}" class="col-xs-5 text-left">
            <trans ng-model='field' trans-prop="'label'"></trans>
        </label>
        <div class="col-xs-12 text-left" ng-show="showStatic && field.type == {{MetadataField::TEXTAREA}}">
            <div class="form-control-static-print" ng-bind-html="{{ $model or 'formData[field.id]' }} || '@lang('metadata.emptyvalue')' | decode | nl2br:true"></div>
        </div>
        <div class="col-xs-12 text-left" ng-show="showStatic && field.type == {{MetadataField::RICHTEXT}}">
            <div class="form-control-static-print" ng-bind-html="{{ $model or 'formData[field.id]' }} || '@lang('metadata.emptyvalue')'"></div>
        </div>
        <div class="col-xs-7 text-left" ng-show="showStatic">
            <div ng-if="field.type != {{MetadataField::MULTIPLE}} && field.type != {{MetadataField::MULTIPLEWITHCOLUMNS}} && field.type != {{MetadataField::TEXTAREA}} && field.type != {{MetadataField::RICHTEXT}}" class="form-control-static-print" ng-bind-html="{{ $model or 'formData[field.id]' }} || '@lang('metadata.emptyvalue')' | decode | nl2br:true"></div>

            <div ng-if="field.type == {{MetadataField::MULTIPLE}}">
                <div class="checkbox" ng-class="{'col-sm-3': field.config.horizontal == 1}" ng-repeat="value in {{ $model or 'formData[field.id]' }}">
                    <trans-options ng-model='field' trans-prop="'options'" trans-index="value"></trans-options>
                </div>
            </div>
        </div>

        <div class="col-sm-12" ng-if="field.type == {{ MetadataField::MULTIPLEWITHCOLUMNS }} && checkIfValues({{$allValues}},null)">
            <table class="table table-striped table-bordered">
                <!-- Muestro los nombres de las columnas -->
                <thead>
                <tr>
                    <th> # </th>
                    <th ng-repeat="value in field.config.columns">
                        <div class="text-center">@{{ value }}</div>
                    </th>
                </tr>
                </thead>
                <!-- Muestro los nombres de las opciones -->
                <tbody>
                <tr ng-repeat="label in field.config.labels" ng-if="checkIfValues({{$allValues}}, label)">
                    <td>
                        @{{ label }}
                    </td>
                    <!-- Muestro los valores de las opciones en la vista estatica-->
                    <td ng-repeat="column in field.config.columns" ng-if="showStatic">
                        <div ng-repeat="value in {{$allValues}}">
                            <div ng-if="value.value.label == label" class="text-center">
                                <div ng-if="isText(label, field.config.text) == 0">
                                    <span ng-if="showData(column, value.value.value) == 1"> <i class="fa fa-check"></i> </span>
                                    <span ng-if="showData(column, value.value.value) != 1"> </span>
                                </div>
                                <div ng-if="isText(label, field.config.text) == 1">
                                    @{{ columnsAndLabels[field.id][label][column] }}
                                </div>
                            </div>
                        </div>
                    </td>

                </tr>
                </tbody>
            </table>
        </div>


        <div ng-show="errors[field.id]" class="help-inline text-danger form-control-static">@{{errors[field.id].toString()}}</div>
    </div>
</div>