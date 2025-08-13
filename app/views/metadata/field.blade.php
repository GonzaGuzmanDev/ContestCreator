<div ng-switch="field.type" ng-controller="MetadataFieldController" ng-init="init({{ $mainField or 'formData[field.id]' }})">
    <div ng-switch-when="{{MetadataField::TAB}}" >
        <ul class="nav nav-tabs">
            <li><a href="">...</a></li>
            <li class="active"><a href=""><trans ng-model='field' trans-prop="'label'"></trans></a></li>
            <li><a href="">...</a></li>
        </ul>
        <div class="help-block" ng-if="!viewOptions.hideDescriptions && field.description != null"><trans ng-model='field' trans-prop="'description'"></trans></div>
    </div>
    <div ng-switch-when="{{MetadataField::TITLE}}" class="col-sm-7">
        <trans ng-model='field' trans-prop="'label'" ng-class="field.config.options"></trans>
        <div class="help-block" ng-if="!viewOptions.hideDescriptions && field.description != null"><trans ng-model='field' trans-prop="'description'"></trans></div>
    </div>
    <div ng-switch-when="{{MetadataField::DESCRIPTION}}" class="col-sm-offset-3 col-sm-7">
        <trans ng-model='field' trans-prop="'label'"></trans>
    </div>
    <div ng-switch-default>
        <div ng-if="field.type == {{MetadataField::MULTIPLEWITHCOLUMNS}} || field.type == {{MetadataField::TEXTAREA}} || field.type == {{MetadataField::RICHTEXT}}" class="col-sm-3 control-label">
            <label ng-if="field.type == {{MetadataField::TEXTAREA}} || field.type == {{MetadataField::RICHTEXT}}">
                <trans ng-model='field' trans-prop="'label'"></trans>
                <span ng-show="isFieldRequired(field) && (!showStatic || !{{$model or 'formData[field.id]'}} )" class="text-danger">*</span>
            </label>
            <label ng-if="field.type == {{MetadataField::MULTIPLEWITHCOLUMNS}}">
                <trans ng-model='field' trans-prop="'label'"></trans>
                <span ng-show="isFieldRequired(field)" class="text-danger">*</span>
            </label>
            <div ng-show="!showStatic" class="help-block" ng-if="!viewOptions.hideDescriptions && field.description != null">
                <trans ng-model='field' trans-prop="'description'"></trans>
            </div>
        </div>
        <label ng-if="field.type != {{MetadataField::MULTIPLEWITHCOLUMNS}} && field.type != {{MetadataField::TEXTAREA}} && field.type != {{MetadataField::RICHTEXT}}" for="input@{{$index}}" class="col-sm-3 control-label">
            <trans ng-model='field' trans-prop="'label'"></trans>
            <span ng-show="isFieldRequired(field) && (!showStatic || !{{$model or 'formData[field.id]'}} || !{{$model or 'formData[field.id]'}}.length )" class="text-danger">*</span>
            <div class="help-block" ng-if="field.type == {{MetadataField::FILE}}">
                @{{ getFileConfigString(field.config) }}
            </div>
        </label>


        <div class="col-sm-7" ng-if="field.type == {{ MetadataField::MULTIPLEWITHCOLUMNS }}">
            <table class="table table-striped table-bordered">
                <!-- Muestro los nombres de las columnas -->
                <thead>
                <tr>
                    <th> # </th>
                    <th ng-repeat="value in field.config.columns track by $index">
                        <div class="text-center"><trans-options ng-model='field' trans-prop="'columns'" trans-index="$index"></trans-options></div>
                    </th>
                </tr>
                </thead>
                <!-- Muestro los nombres de las opciones -->
                <tbody>
                <tr ng-repeat="label in field.config.labels">
                    <td>
                        <trans-options ng-model='field' trans-prop="'labels'" trans-index="$index"></trans-options>
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

                    <!-- Muestro los checkbox en la vista de edicion -->
                    <td ng-repeat="column in field.config.columns" ng-if="!showStatic" class="text-center">
                        <div ng-if="isText(label, field.config.text) == 0">
                            <input type="checkbox" checklist-model="columnsAndLabels[field.id][label]" checklist-value="column" {{$disabled ? "disabled='disabled'":""}}>
                        </div>
                        <div ng-if="isText(label, field.config.text) == 1">
                            <input type="text" class="form-control" ng-if="isText(label, field.config.text) == 1" ng-model="columnsAndLabels[field.id][label][column]" {{$disabled ? "disabled='disabled'":""}}>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div ng-switch="field.type" ng-show="!showStatic">
            <div class="col-sm-6 col-md-6 col-lg-6" ng-switch-when="{{MetadataField::TEXTAREA}}"
                 ng-init="countChar({{$model or 'formData[field.id]'}})">
                <span ng-if="field.config.characters"> Restan: @{{ field.config.characters - textAreaLen }} caracteres </span>
                <textarea maxlength="@{{ field.config.characters }}"
                          ng-keydown="countChar({{$model or 'formData[field.id]'}})"
                          {{$disabled ? "disabled='disabled'":""}}
                          cols="30" rows="10" class="form-control"
                          id="input@{{$index}}"
                          focus-me="$index == 0"
                          ng-model="{{ $model or 'formData[field.id]' }}"
                          ng-pattern="/^[^<>]*$/">
                </textarea>

            </div>
            <div class="col-sm-7" ng-switch-when="{{MetadataField::RICHTEXT}}">
                <div text-angular name="htmlcontent@{{ $index }}" ta-toolbar="[['p', 'pre', 'quote'],
                                        ['bold', 'italics', 'underline', 'ul', 'ol', 'redo', 'undo', 'clear'],
                                        ['justifyLeft','justifyCenter','justifyRight', 'justifyFull'],
                                        ['insertImage', 'insertLink']]"
                     ng-model="{{ $model or 'formData[field.id]' }}" {{$disabled ? "ta-disabled='true'":""}}>

                </div>
            </div>
        </div>

        <div class="col-sm-7" ng-show="showStatic && field.type != {{MetadataField::FILE}} && field.type != {{MetadataField::LINK}}">
            <div ng-if="field.type == {{MetadataField::RICHTEXT}}" class="form-control-static" ng-bind-html="{{ $model or 'formData[field.id]' }}"></div>

            <div ng-if="field.type != {{MetadataField::MULTIPLE}} &&
            field.type != {{MetadataField::MULTIPLEWITHCOLUMNS}} &&
            field.type != {{MetadataField::RICHTEXT}} && field.type != {{MetadataField::SELECT}}"
             class="form-control-static"
             ng-bind-html="{{ $model or 'formData[field.id]' }} || '@lang('metadata.emptyvalue')' | decode | nl2br:true">
            </div>

            <div ng-if="field.type === {{MetadataField::SELECT}}" class="form-control-static">
                @{{ field.model.value.length > 0 ? field.model.value : ""}}
            </div>

            <div ng-if="field.type == {{MetadataField::MULTIPLE}}">
                <span ng-if="isobject(field.value[0])">
                    <div class="checkbox" ng-class="{'col-sm-3': field.config.horizontal == 1}" ng-repeat="value in field.value[0]">
                        <trans-options ng-model='field' trans-prop="'options'" trans-index="value"></trans-options>
                    </div>
                </span>
                <span ng-if="!isobject(field.value[0])">
                    <div class="checkbox" ng-class="{'col-sm-3': field.config.horizontal == 1}" ng-repeat="value in {{$model}}">
                        <trans-options ng-model='field' trans-prop="'options'" trans-index="value"></trans-options>
                    </div>
                </span>
            </div>
        </div>

        <div ng-switch="field.type" class="col-sm-7">
            <div ng-switch-when="{{MetadataField::LINK}}">
                <div class="form-control-static" ng-if="field.config.type == 'button'">
                    <a class="btn btn-md btn-success" ng-href="@{{ field.config.link }}" style="max-width: 100%;" target="_blank" uib-tooltip="" tooltip-placement="top">
                        <trans-options ng-model='field' trans-prop="'buttonText'" trans-index="index"></trans-options></a>
                </div>
                <div class="form-control-static" ng-if="field.config.type == 'hyperText'">
                    <a ng-href="@{{ field.config.link }}" style="max-width: 100%; " target="_blank">
                        <trans-options ng-model='field' trans-prop="'hypText'" trans-index="index"></trans-options></a>
                </div>
                <div class="" ng-if="field.config.type == 'image'">
                    <img ng-src="@{{ field.config.link }}" style="max-width: 100%;"/>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6" ng-switch="field.type" ng-show="!showStatic">
            <input ng-switch-when="{{MetadataField::TEXT}}"
                   {{$disabled ? "disabled='disabled'":""}}
                   type="text"
                   class="form-control"
                   id="input@{{$index}}"
                   focus-me="$index == 0"
                   ng-model="{{$model or 'formData[field.id]'}}"
                   ng-pattern="/^[^<>]*$/">
            <input ng-switch-when="{{MetadataField::EMAIL}}" {{$disabled ? "disabled='disabled'":""}} type="email" class="form-control" id="input@{{$index}}" focus-me="$index == 0" ng-model="{{ $model or 'formData[field.id]' }}"/>
        <!--<input ng-switch-when="{{MetadataField::DATE}}" {{$disabled ? "disabled='disabled'":""}} type="date" class="form-control" id="input@{{$index}}" focus-me="$index == 0" ng-model="{{ $model or 'formData[field.id]' }}">-->
            <div class="row">
                <span class="col-sm-6" ng-switch-when="{{MetadataField::DATE}}">
                    <div class="dropdown">
                        <a class="dropdown-toggle" id="dropdown{{ $model or 'formData[field.id]' }}" role="button" data-toggle="dropdown">
                            <div class="input-group">
                                <input type="text" class="form-control" id="input@{{$index}}" {{$disabled ? "disabled='disabled'":""}}
                                    ng-model="{{ $model or 'formData[field.id]' }}" date-field>
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </a>
                        <span class="help-block" ng-if="field.config.minDate || field.config.maxDate">
                            <span ng-if="field.config.minDate">@lang('metadata.dateMin'): @{{ field.config.minDate | amUtc | amDateFormat:'YYYY/MM/DD' }}</span>
                            <span ng-if="field.config.maxDate">@lang('metadata.dateMax'): @{{ field.config.maxDate | amUtc | amDateFormat:'YYYY/MM/DD' }}</span>
                        </span>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"
                            min-date="field.config.minDate" max-date="field.config.maxDate" date-limiter="">
                            <datetimepicker {{$disabled ? "disabled='disabled'":""}} ng-model="{{ $model or 'formData[field.id]' }}"
                                            data-datetimepicker-config="{ startView: 'month', minView: 'day', minuteStep:10 {{ isset($limitLeft) ? ', limitLeft:'.$limitLeft:''}}{{ isset($limitRight) ? ', limitRight:'.$limitRight:''}}}"
                                            data-before-render="dateBeforeRender($view, $dates, $leftDate, $upDate, $rightDate)"
                            ></datetimepicker>
                        </ul>
                    </div>
                </span>
            </div>
            <select style="width: 30%;" ng-switch-when="{{MetadataField::SELECT}}"
            ng-if="selectedLang == 'es'"
            {{$disabled ? "disabled='disabled'":""}}
            class="form-control" id="input@{{$index}}"
            focus-me="$index == 0"
            ng-model="field.model.value"
            ng-options="option as option for option in field.config.options">
            </select>
            <select style="width: 30%;" ng-switch-when="{{MetadataField::SELECT}}"
                    ng-if="selectedLang == 'us'"
                    {{$disabled ? "disabled='disabled'":""}}
                    class="form-control" id="input@{{$index}}"
                    focus-me="$index == 0"
                    ng-model="field.model.value"
                    ng-options="option as option for option in field.trans.options.us">
            </select>
            <select style="width: 30%;" ng-switch-when="{{MetadataField::SELECT}}"
                    ng-if="selectedLang == 'pt'"
                    {{$disabled ? "disabled='disabled'":""}}
                    class="form-control" id="input@{{$index}}"
                    focus-me="$index == 0"
                    ng-model="field.model.value"
                    ng-options="option as option for option in field.trans.options.pt">
            </select>

            <div ng-switch-when="{{MetadataField::MULTIPLE}}">
                <span ng-if="isobject(formData[field.id][0])">
                    <div class="checkbox" ng-class="{'col-sm-3':field.config.horizontal == 1}" ng-repeat="option in field.config.options" checked="checked">
                        <label>
                            <input type="checkbox" {{$disabled ? "disabled='disabled'":""}} checklist-model="{{'formData[field.id][0]' }}" checklist-value="$index">
                            <trans-options ng-model='field' trans-prop="'options'" trans-index="$index"></trans-options>
                        </label>
                    </div>
                </span>
                <span ng-if="!isobject(formData[field.id][0])">
                    <div class="checkbox" ng-class="{'col-sm-3':field.config.horizontal == 1}" ng-repeat="option in field.config.options" checked="checked">
                        <label>
                            <input type="checkbox" {{$disabled ? "disabled='disabled'":""}} checklist-model="{{$model or 'formData[field.id]' }}" checklist-value="$index">
                            <trans-options ng-model='field' trans-prop="'options'" trans-index="$index"></trans-options>
                        </label>
                    </div>
                </span>
                <div class="clearfix"></div>
            </div>
            <div class="help-block" ng-if="!viewOptions.hideDescriptions && field.description != null && field.description != '' && field.type != {{MetadataField::TEXTAREA}} && field.type != {{MetadataField::RICHTEXT}} && field.type != {{MetadataField::FILE}} && field.type != {{MetadataField::MULTIPLEWITHCOLUMNS}}"><trans ng-model='field' trans-prop="'description'"></trans></div>
        </div>
        <!-- FILES -->
        <div class="col-sm-6 col-md-6 col-lg-6" ng-if="field.type == {{MetadataField::FILE}}">
            <div class="form-control-static">
                <div class="row">
                    @if(!$disabled)
                        <files-gallery ng-files-list="{{ $filemodel or 'formData[field.id]' }}" ng-files-field="field" ng-class="{'col-sm-6':!showStatic, 'col-sm-12':!showStatic}" ng-if="{{ $filemodel or 'formData[field.id]' }}.length">
                            <ul ui-sortable="{handle:'.handle',cursor:'move',placeholder: 'ui-state-highlight'}" ng-model="{{ $filemodel or 'formData[field.id]' }}" class="clean-list selected-files" ng-class="{'opened':!showStatic}" >
                                <li class="col-md-12 file" ng-class="{'imgDefault': showStatic, 'imgEdit': !showStatic}" ng-repeat="file in {{ $filemodel or 'formData[field.id]' }}">
                                    <span class="thumbnail" ng-class="{'error':file.status == <?=ContestFile::ERROR;?> || file.status == <?=ContestFile::UPLOAD_INTERRUPTED;?> || file.status == <?=ContestFile::CANCELED;?>}">
                                        <div class="imgh" ng-class="{'img-holder-static': showStatic, 'img-holder': !showStatic}" ng-click="openGallery({{ $filemodel or 'formData[field.id]' }}, $index)" ng-style="{'background-image':'url(\''+file.thumb+'\')'}">
                                            <i class="fa fa-7x @{{ getTypeIcon(file.type) }} @{{ getTypeTextStyle(file.type) }}" ng-if="(file.type == '{{Format::DOCUMENT}}' && file.thumbs != 1)|| file.type == '{{Format::OTHER}}'"></i>
                                            <span class="title" ng-class="{'error':file.status == <?=ContestFile::ERROR;?> || file.status == <?=ContestFile::UPLOAD_INTERRUPTED;?> || file.status == <?=ContestFile::CANCELED;?>}">
                                                <i class="fa @{{ getTypeIcon(file.type) }} @{{ getTypeTextStyle(file.type) }}"></i>
                                                <!--@{{ file.name }}-->
                                                <div class="my-progress" ng-if="file.status == <?=ContestFile::ENCODING;?> || file.status == <?=ContestFile::UPLOADING;?>" style="z-index:1;">
                                                    <uib-progressbar class="active" value="file.progress" type="info" ng-if="file.status == <?=ContestFile::UPLOADING;?>">
                                                        <div class="progress-bar-content">
                                                        @lang('general.filesStatus.uploading') @{{file.progress | number : 1}}%
                                                        </div>
                                                    </uib-progressbar>
                                                    <uib-progressbar class="progress-striped active" value="file.progress" type="warning" ng-if="file.status == <?=ContestFile::ENCODING;?>">
                                                        <div class="progress-bar-content">
                                                            @{{file.progress | number : 1}}%
                                                        </div>
                                                    </uib-progressbar>
                                                </div>
                                            </span>
                                            <div class="file-error" ng-if="file.status == <?=ContestFile::ERROR;?>">
                                                <span class="text-danger" ><i class="fa fa-warning"></i> @lang('general.filesStatus.error')</span>
                                                @lang('general.filesStatus.errorexplain')
                                            </div>
                                            <div class="file-error" ng-if="file.status == <?=ContestFile::UPLOAD_INTERRUPTED;?>">
                                                <span class="text-warning" ><i class="fa fa-unlink"></i> @lang('general.filesStatus.uploadinterrupted')</span>
                                                @lang('general.filesStatus.uploadinterruptedexplain')
                                            </div>
                                            <div class="file-error" ng-if="file.status == <?=ContestFile::CANCELED;?>">
                                                <span class="text-muted" ><i class="fa fa-ban"></i> @lang('general.filesStatus.canceled')</span>
                                                @lang('general.filesStatus.canceledexplain')
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-xs btn-warning btn-remove" ng-click="removeFile(file, {{ $filemodel or 'formData[field.id]' }})" ng-show="!showStatic" uib-tooltip="@lang('metadata.removefile')" tooltip-placement="top"><i class="fa fa-remove"></i></button>
                                        <span class="handle"><i class="fa fa-arrows" ng-show="!showStatic"></i></span>
                                        <div class="clearfix"></div>
                                    </span>
                                </li>
                            </ul>
                        </files-gallery>
                        <div class="col-sm-6" ng-show="!showStatic">
                            <files-panel ng-if="field.type == {{ MetadataField::FILE }}" user="entry.user"
                                         field="{{ $filemodel or 'formData[field.id]' }}"
                                         toggleable="true"
                                         show-selection="true" config="field.config"></files-panel>
                        </div>
                    @endif
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="help-block" ng-if="!viewOptions.hideDescriptions && field.description != null && field.description != null"><trans ng-model='field' trans-prop="'description'"></trans></div>
        </div>
        <div class="clearfix"></div>
        <div ng-show="errors[field.id]" class="col-sm-push-3 col-sm-9 help-inline text-danger form-control-static">@{{errors[field.id].toString()}}</div>
    </div>
</div>
