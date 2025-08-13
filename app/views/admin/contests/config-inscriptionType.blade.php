@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-cog"></i> @lang('contest.inscriptionType.config')
@endsection
@section('modal-content')
    <p><strong>@lang('contest.inscriptionType'): @{{type.name}}</strong></p>
    <uib-tabset>
        <uib-tab heading="@lang('contest.inscriptionType.configcategories')" ng-if="categories.length > 0">
            <ul class="category-tree">
                <li ng-repeat="category in categories track by $index" ng-include="'categoryTree.html'" onload="selectable=true;modelList = type.category_config_type;"></li>
            </ul>
        </uib-tab>
        <uib-tab heading="@lang('contest.inscriptionType.configmetadata')">
            <ul class="clean-list" role="menu">
                <li role="presentation" class="">
                    <i class="fa fa-asterisk" uib-tooltip="@lang('contest.inscriptionType.required')" tooltip-placement="top"></i>
                    <i class="fa fa-eye" uib-tooltip="@lang('contest.inscriptionType.visible')" tooltip-placement="top"></i>
                </li>
                <li ng-repeat="field in metadatas[type.role] track by $index">
            <span class="dropdown-item" ng-class="{'text-muted':field.inscription_metadata_config_types[type.id].visible != 1}">
                <input type="checkbox" name="" ng-model="field.inscription_metadata_config_types[type.id].required" ng-checked="field.inscription_metadata_config_types[type.id].required == 1" ng-true-value="1" ng-false-value="0" ng-disabled="!field.inscription_metadata_config_types[type.id].visible" id=""/>
                <input type="checkbox" name="" ng-model="field.inscription_metadata_config_types[type.id].visible" ng-checked="field.inscription_metadata_config_types[type.id].visible == 1" ng-true-value="1" ng-false-value="0" id=""/>
                @{{ field.label }}
                <span class="text-danger" ng-show="field.inscription_metadata_config_types[type.id].required == 1 && field.inscription_metadata_config_types[type.id].visible == 1">*</span>
            </span>
                </li>
            </ul>
        </uib-tab>
        <uib-tab heading="@lang('contest.inscriptionType.configdeadlines')" ng-hide="{{isset($contest->wizard_status) ? ($contest->wizard_status != Contest::WIZARD_FINISHED ? 1 : 0) : 0}}">
            @include('admin.contests.deadlines', array('start' => 'type.start_at', 'deadline1' => 'type.deadline1_at', 'deadline2' => 'type.deadline2_at', 'errstart' => 'start_at', 'errdeadline1' => 'deadline1_at', 'errdeadline2' => 'deadline2_at', 'wide'=>true))
        </uib-tab>
    </uib-tabset>
@endsection
@section('modal-actions')
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !modalForm.$dirty">
        <span ng-bind-html="flash"></span>
    </div>
    <button type="button" class="btn btn-default" ng-click="close()">@lang('general.accept')</button>
@endsection