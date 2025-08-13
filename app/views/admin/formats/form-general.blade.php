@extends('admin.formats.form', array('section' => 'formats'))
@section('tabs')
@include('admin.formats.form-tabs', array('active' => 'general'))
@endsection
@section('form')
<h4>@lang('format.formatData')</h4>
<div class="form-group" ng-class="{error: format.name.$invalid && !format.name.$pristine}">
    <label for="inputName" class="col-sm-2 control-label">@lang('general.name')</label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <input type="text" class="form-control" id="inputName" placeholder="@lang('general.name')" name="name" ng-model="format.name" required>
        <div ng-show="formatForm.name.$error.required && !formatForm.name.$pristine" class="help-inline text-danger form-control-static">@lang('format.completeName')</div>
        <div ng-show="errors.name" class="help-inline text-danger form-control-static">@{{errors.name.toString()}}</div>
    </div>
</div>
<div class="form-group" ng-class="{error: format.label.$invalid && !format.label.$pristine}">
    <label for="inputLabel" class="col-sm-2 control-label">@lang('general.label')</label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <input type="text" class="form-control" id="inputLabel" placeholder="@lang('general.label')" name="label" ng-model="format.label" required>
        <div ng-show="formatForm.label.$error.required && !formatForm.label.$pristine" class="help-inline text-danger form-control-static">@lang('format.completeCode')</div>
        <div ng-show="errors.label" class="help-inline text-danger form-control-static">@{{errors.label.toString()}}</div>
    </div>
</div>
<div class="form-group" ng-class="{error: format.position.$invalid && !format.position.$pristine}">
    <label for="inputPosition" class="col-sm-2 control-label">@lang('general.position')</label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <input type="number" class="form-control" id="inputPosition" min="0" max="100" placeholder="@lang('general.position')" name="position" ng-model="format.position" required>
        <div ng-show="formatForm.position.$error.required && !formatForm.position.$pristine" class="help-inline text-danger form-control-static">@lang('format.completeCode')</div>
        <div ng-show="errors.position" class="help-inline text-danger form-control-static">@{{errors.position.toString()}}</div>
    </div>
</div>
<div class="form-group" ng-class="{error: format.position.$invalid && !format.position.$pristine}">
    <label for="inputPosition" class="col-sm-2 control-label">@lang('general.active')</label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <div class="checkbox">
            <label>
                <input type="checkbox" ng-model="format.active" ng-true-value="1" ng-false-value="0" class="ng-valid"> @lang('general.active')
            </label>
        </div>
    </div>
</div>
<h4>@lang('format.formatEncoder')</h4>
<div class="clearfix"></div>
<div class="form-group" ng-class="{error: format.type.$invalid && !format.type.$pristine}">
    <label for="inputType" class="col-sm-2 control-label">@lang('general.type')</label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <select ng-model="format.type" id="inputType" class="form-control">
            @foreach(Format::getAllTypesData() as $typeId => $typeStr)
                <option value="{{$typeId}}">{{$typeStr}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group" ng-class="{error: format.extension.$invalid && !format.extension.$pristine}">
    <label for="inputExtension" class="col-sm-2 control-label">@lang('general.extension')</label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <input type="text" class="form-control" id="inputExtension" placeholder="@lang('general.extension')" name="extension" ng-model="format.extension" required>
        <div ng-show="formatForm.extension.$error.required && !formatForm.extension.$pristine" class="help-inline text-danger form-control-static">@lang('format.completeExtension')</div>
        <div ng-show="errors.extension" class="help-inline text-danger form-control-static">@{{errors.extension.toString()}}</div>
    </div>
</div>
<div class="form-group" ng-class="{error: format.command.$invalid && !format.command.$pristine}">
    <label for="inputCommand" class="col-sm-2 control-label">@lang('general.command')</label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <textarea class="form-control" rows="3" id="inputCommand" placeholder="@lang('general.command')" name="command" ng-model="format.command" required>@{{ format.command }}</textarea>
        <div ng-show="formatForm.command.$error.required && !formatForm.command.$pristine" class="help-inline text-danger form-control-static">@lang('format.completeCommand')</div>
        <div ng-show="errors.command" class="help-inline text-danger form-control-static">@{{errors.command.toString()}}</div>
    </div>
</div>
@endsection