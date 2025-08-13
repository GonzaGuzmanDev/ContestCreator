@extends('admin.contests.form', array('active' => 'inscriptions'))
@section('form')
    <script type="text/ng-template" id="editInscriptionTypeConfig.html">
        @include('admin.contests.config-inscriptionType')
    </script>
    <span ng-if="{{ $contest->wizard_status >= Contest::WIZARD_REGISTER_FORM && $contest->wizard_status != Contest::WIZARD_FINISHED}}">
    @include('includes.wizardProgress', array('active' => Contest::WIZARD_REGISTER_FORM))
    <br><div class="clearfix"></div><br>
    <h3 class="text-center"> @lang('contest.wizard.createdFollowInstructions') </h3>
    <br>
    <h4 class="text-center"> @lang('contest.wizard.hasInscriptions')
    <div class="btn-group">
        <button type="button" class="btn" ng-class="{'btn-default': wizardHasInscriptions == false, 'btn-success': wizardHasInscriptions == true}" ng-click="changeWizardHasInscriptions(true)">
            <span> Si</span>
        </button>
        <button type="button" class="btn" ng-class="{'btn-default': wizardHasInscriptions == true, 'btn-danger': wizardHasInscriptions == false}" ng-click="changeWizardHasInscriptions(false)">
            <span> No </span>
        </button>
    </div>
        <i class="fa fa-question-circle text-info" popover="@lang('contest.wizard.inscriptionsHelp')" popover-placement="right" popover-trigger="mouseenter"></i>
    </h4>
    <br><br>
    <h4 ng-if="wizardHasInscriptions == false" class="alert alert-danger alert-sm alert-box text-center">
            @lang('contest.wizard.noInscriptions')
    </h4>
    </span>
    <span ng-hide="{{isset($contest->wizard_status) ? ($contest->wizard_status != Contest::WIZARD_FINISHED ? 1 : 0) : 0}} && wizardHasInscriptions == false">
    <h4 class="well well-sm">
        @lang('contest.inscriptions')
        {{--@if(!$superadmin)
            <i class="fa fa-question-circle text-info" ng-if="template.name" popover="@lang('contest.explain.inscriptions')" popover-placement="right" popover-trigger="mouseenter" class="btn btn-default"></i>
        @endif--}}
        <div class="btn-group pull-right">
            <a href="" class="btn btn-sm btn-default btn-small" ng-repeat="(key,lang) in langs.All" ng-class="{'active':selectedLang == key}" ng-click="setLang(key)"><i class="flag-icon flag-icon-@{{key}}"></i> @{{lang}}</a>
        </div>
        <div class="clearfix"></div>
    </h4>
    <div class="roles-list">
        <!--<div class="btn-group">
            <div class="form-inline" ng-if="{{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}}">
                <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    @lang('contest.importUserForm')
                </button>
                <ul class="dropdown-menu scrollable-menu">
                    <li style="padding: 0 5px 5px;"><input type="text" ng-model="filterContest" ng-click="$event.stopPropagation();" class="form-control searchBox" style="width:100%;" placeholder="@lang('general.search')"/></li>
                    <li ng-repeat="contest in contestsIds | filter: filterContest"><a type="button" ng-click="import(contest.code)"> @{{ contest.name }} </a></li>
                </ul>
            </div>
        </div>-->
        <uib-tabset justified="true" type="pills">
            <uib-tab ng-repeat="(roleId, role) in roles" class="row" ng-hide="{{isset($contest->wizard_status) ? ($contest->wizard_status != Contest::WIZARD_FINISHED ? 1 : 0) : 0}}">
                <tab-heading>
                    <i class="fa" ng-class="{'fa-ticket': roleId == '{{Inscription::INSCRIPTOR}}', 'fa-user-circle-o': roleId == '{{Inscription::COLABORATOR}}', 'fa-legal': roleId == '{{Inscription::JUDGE}}'}"></i>
                    @{{role}}
                </tab-heading>

                <div class=" col-md-12 col-lg-12">
                    <h4>@lang('contest.inscriptionsTypes')
                    {{-- <i class="fa fa-question-circle text-info" popover="@lang('contest.explain.inscriptionsTypes')" popover-placement="right" popover-trigger="mouseenter" class="btn btn-default"></i>--}}
                    </h4>
                    <div class="row form-group no-hover" ng-show="(inscriptionTypes | filter:{role:roleId}).length">
                        <div class="col-md-4">
                            @lang('contest.inscriptionType.name')
                        </div>
                        <div class="col-md-2 form-control-static" ng-hide="{{isset($contest->wizard_status) ? ($contest->wizard_status != Contest::WIZARD_FINISHED ? 1 : 0) : 0}}">
                            @lang('general.deadlines.start_at')
                        </div>
                        <div class="col-md-2 form-control-static" ng-hide="{{isset($contest->wizard_status) ? ($contest->wizard_status != Contest::WIZARD_FINISHED ? 1 : 0) : 0}}">
                            @lang('general.deadlines.deadline1_at')
                        </div>
                        <div class="col-md-2 form-control-static" ng-hide="{{isset($contest->wizard_status) ? ($contest->wizard_status != Contest::WIZARD_FINISHED ? 1 : 0) : 0}}">
                            @lang('general.deadlines.deadline2_at')
                        </div>
                        <div class="col-md-2 form-control-static">
                            @lang('contest.inscriptionTypePublic')
                        </div>
                    </div>
                    <div class="row form-group" ng-repeat="(ind,type) in inscriptionTypes | filter:{role:roleId}">
                        <div ng-if="showThis">
                            <div class="col-md-4">
                                <input type="text" class="form-control" ng-model="type.name" required ng-if="selectedLang == langs.Default">
                                <input type="text" class="form-control input-trans" ng-model="type.trans[key].name" placeholder="@{{!type.trans[key].name ? type.name : ''}}" ng-if="selectedLang == key" ng-repeat="(key,lang) in langs.Editables">
                            </div>
                            <div class="col-md-2" ng-hide="{{isset($contest->wizard_status) ? ($contest->wizard_status != Contest::WIZARD_FINISHED ? 1 : 0) : 0}}">
                                @include('includes.datetimepicker', array('field'=>'type.start_at', 'placeholder' => Lang::get('contest.startAt')))
                                <span am-time-ago="type.start_at"></span>
                            </div>
                            <div class="col-md-2" ng-hide="{{isset($contest->wizard_status) ? ($contest->wizard_status != Contest::WIZARD_FINISHED ? 1 : 0) : 0}}">
                                @include('includes.datetimepicker', array('field'=>'type.deadline1_at', 'placeholder' => Lang::get('contest.deadLine1At')))
                                <span am-time-ago="type.deadline1_at"></span>
                            </div>
                            <div class="col-md-2" ng-hide="{{isset($contest->wizard_status) ? ($contest->wizard_status != Contest::WIZARD_FINISHED ? 1 : 0) : 0}}">
                                @include('includes.datetimepicker', array('field'=>'type.deadline2_at', 'placeholder' => Lang::get('contest.deadLine2At')))
                                <span am-time-ago="type.deadline2_at"></span>
                            </div>
                            <div class="col-md-1">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="" ng-model="type.public" ng-checked="type.public == 1" ng-true-value="1" ng-false-value="0" id=""/>
                                        @lang('contest.inscriptionTypePublic')
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-1 text-right">
                                <button type="button" ng-click="configInscriptionType(type)" class="btn btn-info" uib-tooltip="@lang('contest.inscriptionType.config')"><i class="fa fa-cog"></i></button>
                                <button type="button" ng-click="removeInscriptionType(type)" class="btn btn-default" uib-tooltip="@lang('contest.inscriptionType.delete')"><i class="fa fa-remove"></i></button>
                            </div>
                        </div>
                        <div ng-if="!showThis">
                            <div class="col-md-4 form-control-static"><trans ng-model="type" trans-prop="'name'"></trans></div>
                            <div class="col-md-2">@{{ type.start_at }}<br><span am-time-ago="type.start_at"></span></div>
                            <div class="col-md-2">@{{ type.deadline1_at }}<br><span am-time-ago="type.deadline1_at"></span></div>
                            <div class="col-md-2">@{{ type.deadline2_at }}<br><span am-time-ago="type.deadline2_at"></span></div>
                            <div class="col-md-2">@{{ type.public == 1 ? '@lang('general.yes')' : '@lang('general.no')'}}</div>
                        </div>
                        <div class="col-md-12 text-danger">
                            @{{ errors[type.errMsg] }}
                        </div>
                    </div>
                    <div class="row form-group no-hover" ng-show="showThis">
                        <div class="col-md-6 col-ld-12">
                            <a href="" ng-click="addInscriptionType(roleId)" class="btn btn-info"><i class="fa fa-plus"></i> @lang('contest.addInscriptionsType')</a>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>
                <div class="col-md-12 col-lg-12">
                    <h4>@lang('contest.inscriptionsMetadata')</h4>
                    <div>
                        <div class="row form-group no-hover" ng-show="metadatas[roleId].length">
                            <div class="col-md-6" ng-show="showThis">
                                <div class="col-md-7">
                                    @lang('metadata.label')
                                </div>
                                <div class="col-md-3">
                                    @lang('metadata.type')
                                </div>
                                <div class="col-md-2">
                                    @lang('metadata.config')
                                </div>
                            </div>
                            <div class="col-md-1 text-right">
                                    @lang('metadata.delete')
                                </div>
                            <div class="col-md-5 text-center">
                                @lang('metadata.preview')
                            </div>
                        </div>
                        <ul ui-sortable="{handle: '.handle',cursor: 'move'}" ng-model="metadatas[roleId]" class="clean-list" ng-class="{'list-stripped':showThis}">
                            <li ng-repeat="field in metadatas[roleId] track by $index">
                                <div class="row form-group">
                                    <div class="col-sm-7" ng-show="showThis">
                                        @include('metadata.edit', ['private'=>false, 'config'=>false, 'delete'=>true, 'draggable' => false, 'list'=>'metadatas[roleId]'])
                                    </div>
                                    <div ng-class="{'col-sm-5': showThis, 'col-sm-8': !showThis}">
                                        <div class="row form-group">
                                            @include('metadata.field', array('model'=>'void', 'filemodel'=>'void', 'mainField'=>'void','allValues'=>'void','disabled'=>true, 'forceRequired' => false))
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 text-danger">
                                    @{{ errors2[field.errMsg] }}
                                </div>
                            </li>
                            <div class="clearfix"></div>
                        </ul>
                        <div class="row form-group no-hover" ng-if="showThis">
                            <div class="col-md-6">
                                @include('metadata.add-field')
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </uib-tab>
        </uib-tabset>
    </div>
    <div class="clearfix"></div>
    </span>
@endsection