@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-cog"></i> @lang('contest.categories.config')
@endsection
@section('modal-content')
        <strong>@lang('contest.category'):
            <span ng-include="'categoryList.html'" onload="first=true;"></span>
        </strong>
        <div ng-repeat="(roleId, role) in roles" class="role-item" ng-show="(inscriptionTypes | filter:{role:roleId}).length">
            <h3 class="well well-sm">@{{ role }}</h3>
            <ul class="clean-list" role="menu">
                <li ng-repeat="type in inscriptionTypes | filter:{role:roleId} track by $index">
                    <label class="label-normal">
                        <i class="fa fa-check" ng-if="category.category_config_type == type.id"></i>
                        <!--<input type="checkbox" name=""  checklist-model="category.category_config_type" checklist-value="type.id" id=""/>-->
                        @{{ type.name }}
                    </label>
                </li>
            </ul>
        </div>
@endsection
@section('modal-actions')
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !modalForm.$dirty">
        <span ng-bind-html="flash"></span>
    </div>
    <button type="button" class="btn btn-default" ng-click="close()">@lang('general.ok')</button>
@endsection