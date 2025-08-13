@include('includes.header')
<div class="main-block contents container">
    <h2><i class="fa fa-user"></i> @lang('account.title')</h2>
    <div class="row">
        <div class="col-sm-3 col-lg-3">
            @include('account.menu')
        </div>
        <div class="col-sm-9 col-lg-9">
            <form name="userForm" class="form-hover" ng-submit="savePass()">
                <h3><i class="fa fa-shield"></i> @lang('account.security')</h3>
                <h4><?=Lang::get('user.changePassword')?></h4>
                <div class="form-group" ng-class="{error: user.current_password.$invalid && !user.current_password.$pristine}">
                    <label for="current_password"><?=Lang::get('user.oldPassword')?></label>
                    <input type="password" class="form-control" id="current_password" placeholder="" name="current_password" ng-model="passChange.current_password" required focus-me="true">
                    <div ng-show="errors.current_password" class="help-inline text-danger form-control-static">@{{errors.current_password.toString()}}</div>
                </div>
                <div class="form-group" ng-class="{error: user.new_password.$invalid && !user.new_password.$pristine}">
                    <label for="new_password"><?=Lang::get('user.newPassword')?></label>
                    <input type="password" class="form-control" id="new_password" placeholder="" name="new_password" ng-model="passChange.new_password" required>
                </div>
                <div class="form-group" ng-class="{error: user.repeat_password.$invalid && !user.repeat_password.$pristine}">
                    <label for="repeat_password"><?=Lang::get('user.repeatPassword')?></label>
                    <input type="password" class="form-control" id="repeat_password" placeholder="" name="repeat_password" ng-model="passChange.repeat_password" required>
                    <div ng-show="user.new_password != user.repeat_password" class="help-inline text-danger form-control-static"><?=Lang::get('user.passwordMismatch')?></div>
                    <div ng-show="errors.new_password" class="help-inline text-danger form-control-static">@{{errors.new_password.toString()}}</div>
                </div>
                <div class="alert alert-@{{flashStatus}}" ng-show="flash">
                    <span ng-bind="flash"></span>
                </div>
                <hr/>
                <div class="form-group">
                    <button type="submit" ng-disabled="userForm.$invalid" class="btn btn-primary"><i class="fa fa-save"></i> <?=Lang::get('user.save')?></button>
                </div>
            </form>
        </div>
    </div>
    @include('includes.footer')
</div>
