@include('includes.header')
<div class="main-block contents container">
    <h2><i class="fa fa-user"></i> @lang('account.title')</h2>
    <div class="row">
        <div class="col-sm-3 col-lg-3">
            @include('account.menu')
        </div>
        <div class="col-sm-9 col-lg-9">
            <form name="userForm" class="form-hover" ng-submit="save()">
                <h3><i class="fa fa-info-circle"></i> @lang('account.data')</h3>
                <div class="form-group" ng-class="{error: user.email.$invalid && !user.email.$pristine}">
                    <label for="inputEmail"><?=Lang::get('user.email')?> <span class="text-danger" ng-show="userForm.email.required">*</span></label>
                    <input type="email" class="form-control" id="inputEmail" placeholder="" name="email" ng-model="user.email" required focus-me="true">
                    <div ng-show="userForm.email.$error.required && !userForm.email.$pristine" class="help-inline text-danger form-control-static"><?=Lang::get('user.completeEmail')?></div>
                    <div ng-show="userForm.email.$error.email && !userForm.email.$pristine" class="help-inline text-danger form-control-static"><?=Lang::get('user.invalidEmail')?></div>
                    <div ng-show="errors.email" class="help-inline text-danger form-control-static">@{{errors.email.toString()}}</div>
                    <div class="text-warning" ng-show="user.verified==0" style="margin-top: 5px;"><i class="fa fa-warning"></i> @lang('user.emailNotVerified')
                        <button class="btn-default" onclick="this.disabled=true" ng-click="sendVerifyEmail();">@lang('user.verifyEmail')</button>
                    </div>
                    <div class="text-success" ng-show="user.verified==1" style="margin-top: 5px;"><i class="fa fa-check"></i> @lang('user.emailVerified')</div>
                </div>
                <div class="form-group" ng-class="{error: user.first_name.$invalid && !user.first_name.$pristine}">
                    <label for="inputName"><?=Lang::get('user.firstName')?></label>
                    <input type="text" class="form-control" id="inputName" placeholder="" name="first_name" ng-model="user.first_name" required>
                    <div ng-show="userForm.first_name.$error.required && !userForm.first_name.$pristine" class="help-inline text-danger form-control-static"><?=Lang::get('user.completeFullName')?></div>
                    <div ng-show="errors.first_name" class="help-inline text-danger form-control-static">@{{errors.first_name.toString()}}</div>
                </div>
                <div class="form-group" ng-class="{error: user.last_name.$invalid && !user.last_name.$pristine}">
                    <label for="inputName2"><?=Lang::get('user.lastName')?></label>
                    <input type="text" class="form-control" id="inputName2" placeholder="" name="last_name" ng-model="user.last_name" required>
                    <div ng-show="userForm.last_name.$error.required && !userForm.last_name.$pristine" class="help-inline text-danger form-control-static"><?=Lang::get('user.completeFullName')?></div>
                    <div ng-show="errors.last_name" class="help-inline text-danger form-control-static">@{{errors.last_name.toString()}}</div>
                </div>
                <div class="alert alert-@{{flashStatus}}" ng-show="flash">
                    <span ng-bind="flash"></span>
                </div>
                <hr/>
                <div class="form-group">
                    <button type="submit" ng-disabled="userForm.$invalid || userForm.$pristine" class="btn btn-primary"><i class="fa fa-save"></i> <?=Lang::get('user.save')?></button>
                </div>
            </form>
        </div>
    </div>

    @include('includes.footer')
</div>
