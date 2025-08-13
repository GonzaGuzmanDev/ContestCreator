@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-cog"></i> @lang('contest.inscriptionType.config')
@endsection
@section('modal-content')
    <p><strong>@lang('contest.metadataTemplate'): @{{template.name}}</strong></p>
    <uib-tabset>
        <uib-tab heading="@lang('contest.metadataTemplate.configmetadata')">
            <ul class="clean-list" role="menu">
                <li role="presentation" class="">
                    <i class="fa fa-asterisk" uib-tooltip="@lang('contest.inscriptionType.required')" tooltip-placement="top"></i>
                    <i class="fa fa-eye" uib-tooltip="@lang('contest.inscriptionType.visible')" tooltip-placement="top"></i>
                </li>
                <li ng-repeat="field in EntryMetadataField track by $index">
                    <span class="dropdown-item" ng-class="{'text-muted':c.visible != 1}" ng-init="c = getFieldTemplateConfig(field,template);">
                        <input type="checkbox" name="" ng-model="c.required" ng-true-value="1" ng-false-value="0" ng-disabled="!c.visible" id=""/>
                        <input type="checkbox" name="" ng-model="c.visible" ng-true-value="1" ng-false-value="0" id=""/>
                        @{{ field.label }}
                        <span class="text-danger" ng-show="c.required == 1 && c.visible == 1">*</span>
                    </span>
                </li>
            </ul>
        </uib-tab>
        <uib-tab heading="@lang('contest.metadataTemplate.configcategories')">
            <uib-alert type="warning" ng-if="!template.categories_ids || template.categories_ids.length == 0" class="alert-sm"><i class="fa fa-fw fa-info-circle"></i> @lang('metadata.nocateogiresselected')</uib-alert>
            <ul class="category-tree">
                <li ng-repeat="category in categories track by $index" ng-include="'categoryTree.html'" onload="selectable=true; modelList = template.categories_ids;"></li>
            </ul>
        </uib-tab>
    </uib-tabset>
@endsection
@section('modal-actions')
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !modalForm.$dirty">
        <span ng-bind-html="flash"></span>
    </div>
    <button type="button" class="btn btn-default" ng-click="close()">@lang('general.ok')</button>
@endsection