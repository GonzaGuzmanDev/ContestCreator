@extends('admin.contests.form', array('active' => 'entries'))
@section('form')
    <script type="text/ng-template" id="editMetadataTemplateConfig.html">
        @include('admin.contests.config-entryMetadataTemplate')
    </script>
    <span ng-if="{{ $contest->wizard_status >= Contest::WIZARD_ENTRY_FORM && $contest->wizard_status != Contest::WIZARD_FINISHED}}">
    @include('includes.wizardProgress', array('active' => Contest::WIZARD_ENTRY_FORM))
        <br><div class="clearfix"></div><br>
    <h3 class="text-center"> @lang('contest.wizard.entriesFormTitle') </h3>
    <br>
    <h4 class="text-center"> @lang('contest.wizard.hasEntries')
    <div class="btn-group">
        <button type="button" class="btn" ng-class="{'btn-default': wizardHasEntries == false, 'btn-success': wizardHasEntries == true}" ng-click="changeWizardHasEntries(true)">
            <span> Si</span>
        </button>
        <button type="button" class="btn" ng-class="{'btn-default': wizardHasEntries == true, 'btn-danger': wizardHasEntries == false}" ng-click="changeWizardHasEntries(false)">
            <span> No </span>
        </button>
    </div>
        <i class="fa fa-question-circle text-info" popover="@lang('contest.wizard.inscriptionsHelp')" popover-placement="right" popover-trigger="mouseenter"></i>

    </h4>
        <br><br>
        <h4 ng-if="wizardHasEntries == false" class="alert alert-danger alert-sm alert-box text-center">
                @lang('contest.wizard.noEntries')
        </h4>
    </span>

    <span ng-hide="wizardHasEntries == false && {{isset($contest->wizard_status) ? ($contest->wizard_status != Contest::WIZARD_FINISHED ? 1 : 0) : 0}}">
    <h4 class="well well-sm">
        @lang('contest.entriesMetadata')
        {{--@if(!$superadmin)
        <i class="fa fa-question-circle text-info" popover="@lang('contest.explain.entries')" popover-placement="right" popover-trigger="mouseenter" class="btn btn-default"></i>
        @endif--}}
        <div class="btn-group pull-right">
            <a href="" class="btn btn-sm btn-default btn-small" ng-repeat="(key,lang) in langs.All" ng-class="{'active':selectedLang == key}" ng-click="setLang(key)"><i class="flag-icon flag-icon-@{{key}}"></i> @{{lang}}</a>
        </div>
        <div class="clearfix"></div>
    </h4>

    <!--<div class="btn-group">
        <div class="form-inline" ng-if="{{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}}">
            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                @lang('contest.importEntryForms')
            </button>
            <ul class="dropdown-menu scrollable-menu">
                <li style="padding: 0 5px 5px;"><input type="text" ng-model="filterContest" ng-click="$event.stopPropagation();" class="form-control searchBox" style="width:100%;" placeholder="@lang('general.search')"/></li>
                <li ng-repeat="contest in contestsIds | filter: filterContest"><a type="button" ng-click="importForms(contest.code)"> @{{ contest.name }} </a></li>
            </ul>
        </div>
    </div>-->
    <div class=" col-md-12 col-lg-12">
        <h4 style="margin-top: 0;">@lang('contest.metadataTemplates')
            {{--<i class="fa fa-question-circle text-info" popover="@lang('contest.explain.metadataTemplates')" popover-placement="right" popover-trigger="mouseenter" class="btn btn-default"></i>--}}
        </h4>
        <div class="row form-group no-hover" ng-show="EntryMetadataTemplates.length">
            <div class="col-md-4">
                @lang('contest.inscriptionType.name')
            </div>
        </div>
        <div class="row form-group" ng-repeat="(ind,template) in EntryMetadataTemplates">
            <div ng-if="showThis">
                <div class="col-md-4">
                    <input type="text" class="form-control" ng-model="template.name" required ng-if="selectedLang == langs.Default">
                    <input type="text" class="form-control input-trans" ng-model="template.trans[key].name" placeholder="@{{!template.trans[key].name ? template.name : ''}}" ng-if="selectedLang == key" ng-repeat="(key,lang) in langs.Editables">
                </div>
                <div class="col-md-1 text-right">
                    <button type="button" ng-click="configMetadataTemplate(template)" class="btn btn-info" uib-tooltip="@lang('contest.inscriptionType.config')"><i class="fa fa-cog"></i></button>
                    <button type="button" ng-click="removeMetadataTemplate(template)" class="btn btn-default" uib-tooltip="@lang('contest.inscriptionType.delete')"><i class="fa fa-remove"></i></button>
                </div>
                <div class="col-md-4">
                    <div ng-if="!template.categories_ids || template.categories_ids.length == 0" class="text-warning form-control-static"><i class="fa fa-fw fa-info-circle"></i> @lang('metadata.nocateogiresselected')</div>
                </div>
            </div>
            <div ng-if="!showThis">
                <div class="col-md-4 form-control-static"><trans ng-model="template" trans-prop="'name'"></trans></div>
            </div>
            <div class="col-md-12 text-danger">
                @{{ errors[template.errMsg] }}
            </div>
        </div>
        <div class="row form-group no-hover" ng-if="showThis">
            <div class="col-md-6 col-ld-12">
                <a href="" ng-click="addMetadataTemplate()" class="btn btn-info"><i class="fa fa-plus"></i> @lang('contest.addMetadataStandard')</a>
            </div>
        </div>
        <div class="clearfix"></div>
        <h4>@lang('contest.entriesForm')</h4>
        <div class="row form-group" ng-show="EntryMetadataField.length">
            <div class="col-sm-6" ng-if="showThis">
                <div class="col-md-6">
                    @lang('metadata.label')
                </div>
                <div class="col-md-3">
                    @lang('metadata.type')
                </div>
                <div class="col-md-3">
                    @lang('metadata.config')
                </div>
            </div>
            <div ng-class="{'col-sm-5': showThis, 'col-sm-8': !showThis}">
                @lang('metadata.preview')
                <div class="btn-group pull-right" uib-dropdown ng-if="EntryMetadataTemplates.length > 0">
                    <button id="single-button" type="button" class="btn btn-sm btn-primary" uib-dropdown-toggle ng-class="{'btn-warning' : !!previewtemplate}">
                        <span ng-bind-html="previewtemplate ? (previewtemplate.name || '@lang('metadata.templatenoname')') : '@lang('metadata.selecttemplate')'"></span> <span class="caret"></span>
                    </button>
                    <ul uib-dropdown-menu aria-labelledby="single-button">
                        <li ng-class="{'active' : !previewtemplate}"><a href="" ng-click="setPreviewTemplate(null)"><em>@lang('metadata.noselectedtemplate')</em></a></li>
                        <li ng-repeat="(ind,template) in EntryMetadataTemplates" ng-class="{'active' : previewtemplate == template}"><a href="" ng-click="setPreviewTemplate(template)"><span ng-bind-html="template.name || '@lang('metadata.templatenoname')'"></span></a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-1 text-right" ng-if="showThis">
                @lang('metadata.delete')
            </div>
            <div class="clearfix"></div>
        </div>
        <ul ui-sortable="{handle: '.handle',cursor: 'move'}" ng-model="EntryMetadataField" class="clean-list" ng-class="{'list-stripped':showThis}">
            <li ng-repeat="field in EntryMetadataField track by $index" ng-class="{'hidden-field': isFieldHidden(field)}">
                <div class="row form-group">
                    <div class="col-sm-6" ng-if="showThis">
                        @include('metadata.edit', ['private'=>true, 'config'=>true, 'delete'=>false, 'draggable' => true, 'list'=>'EntryMetadataField'])
                    </div>
                    <div ng-class="{'col-sm-5': showThis, 'col-sm-8': !showThis}">
                        <div class="row form-group" ng-class="{'hidden-field': isFieldHidden(field)}">
                            @include('metadata.field', array('model'=>'void', 'filemodel'=>'void', 'mainField'=>'void','allValues'=>'void','disabled'=>true, 'forceRequired' => false))
                        </div>
                    </div>
                    <div class="col-sm-1 text-right" ng-if="showThis">
                        <button type="button" ng-click="removeMetadataField(field)" class="btn btn-sm btn-default" uib-tooltip="@lang('metadata.remove')"><i class="fa fa-remove"></i></button>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12 text-danger">
                        @{{ errors2[field.errMsg] }}
                    </div>
                </div>
            </li>
        </ul>
        <div class="row form-group" ng-if="showThis">
            <div class="col-md-6">
                @include('metadata.add-field')
            </div>
        </div>
    </div>
    </span>
@endsection