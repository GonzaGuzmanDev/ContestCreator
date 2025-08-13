@extends('admin.users.form', array('section' => 'users'))
@section('tabs')
@include('admin.users.form-tabs', array('active' => 'general'))
@endsection
@section('form')
<h4><?=Lang::get('user.personalData')?></h4>
<div class="form-group" ng-class="{error: user.email.$invalid && !user.email.$pristine}">
    <label for="inputEmail" class="col-sm-2 control-label"><?=Lang::get('user.email')?> <span class="text-danger" ng-show="userForm.email.required">*</span></label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <input type="email" class="form-control" id="inputEmail" placeholder="" name="email" ng-model="user.email" required focus-me="true">
        <div ng-show="userForm.email.$error.required && !userForm.email.$pristine" class="help-inline text-danger form-control-static"><?=Lang::get('user.completeEmail')?></div>
        <div ng-show="userForm.email.$error.email && !userForm.email.$pristine" class="help-inline text-danger form-control-static"><?=Lang::get('user.invalidEmail')?></div>
        <div ng-show="errors.email" class="help-inline text-danger form-control-static">@{{errors.email.toString()}}</div>
    </div>
</div>
<div class="form-group" ng-class="{error: user.first_name.$invalid && !user.first_name.$pristine}">
    <label for="inputName" class="col-sm-2 control-label"><?=Lang::get('user.firstName')?></label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <input type="text" class="form-control" id="inputName" placeholder="" name="first_name" ng-model="user.first_name" required>
        <div ng-show="userForm.first_name.$error.required && !userForm.first_name.$pristine" class="help-inline text-danger form-control-static"><?=Lang::get('user.completeFullName')?></div>
        <div ng-show="errors.first_name" class="help-inline text-danger form-control-static">@{{errors.first_name.toString()}}</div>
    </div>
</div>
<div class="form-group" ng-class="{error: user.last_name.$invalid && !user.last_name.$pristine}">
    <label for="inputName2" class="col-sm-2 control-label"><?=Lang::get('user.lastName')?></label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <input type="text" class="form-control" id="inputName2" placeholder="" name="last_name" ng-model="user.last_name" required>
        <div ng-show="userForm.last_name.$error.required && !userForm.last_name.$pristine" class="help-inline text-danger form-control-static"><?=Lang::get('user.completeFullName')?></div>
        <div ng-show="errors.last_name" class="help-inline text-danger form-control-static">@{{errors.last_name.toString()}}</div>
    </div>
</div>
<h4 ng-show="!user.id"><?=Lang::get('user.password')?></h4>
<h4 ng-show="user.id"><?=Lang::get('user.changePassword')?></h4>
<div class="form-group" ng-class="{error: user.new_password.$invalid && !user.new_password.$pristine}">
    <label for="new_password" class="col-sm-2 control-label"><?=Lang::get('user.newPassword')?></label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <input type="password" class="form-control" id="new_password" placeholder="" name="new_password" ng-model="user.new_password" ng-required="!user.id">
    </div>
</div>
<div class="form-group" ng-class="{error: user.repeat_password.$invalid && !user.repeat_password.$pristine}">
    <label for="repeat_password" class="col-sm-2 control-label"><?=Lang::get('user.repeatPassword')?></label>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <input type="password" class="form-control" id="repeat_password" placeholder="" name="repeat_password" ng-model="user.repeat_password" ng-required="!user.id">
        <div ng-show="user.new_password != user.repeat_password" class="help-inline text-danger form-control-static"><?=Lang::get('user.passwordMismatch')?></div>
        <div ng-show="errors.new_password" class="help-inline text-danger form-control-static">@{{errors.new_password.toString()}}</div>
    </div>
</div>
<h4><?=Lang::get('user.permissions')?></h4>
<div class="form-group" ng-class="{error: user.super.$invalid && !user.super.$pristine}">
    <div class="col-sm-2 control-label"></div>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <div class="checkbox">
            <label>
                <input type="checkbox" ng-model="user.super" ng-value="user.super" ng-true-value="1" ng-checked="user.super == 1" ng-false-value="0"> @lang('user.superadmin')
            </label>
        </div>
        <div ng-show="errors.super" class="help-inline text-danger form-control-static">@{{errors.super.toString()}}</div>
    </div>
</div>
<div class="form-group" ng-class="{error: user.super.$invalid && !user.super.$pristine}">
    <div class="col-sm-2 control-label"></div>
    <div class="col-sm-8 col-md-6 col-lg-4">
        <div class="checkbox">
            <label>
                <input type="checkbox" ng-model="user.active" ng-value="user.active" ng-true-value="0" ng-checked="user.active == 0" ng-false-value="1"> @lang('user.blockUser')
            </label>
        </div>
        <div ng-show="errors.super" class="help-inline text-danger form-control-static">@{{errors.super.toString()}}</div>
    </div>
</div>
@endsection