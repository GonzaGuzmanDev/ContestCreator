@include('includes.header')
<div class="main-block panned with-footer">
    <div class="container" ng-controller="resetController">
        <div class="row">
            <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                <h2><?=Lang::get('reminders.title')?></h2>
                <form role="form" method="POST" ng-submit="submit()" ng-hide="passReseted">
                    <div class="form-group" ng-class="{'has-error': !!errors.email}">
                        <label for="inputEmail"><?=Lang::get('login.email')?></label>
                        <input type="email" ng-model="reminder.email" id="inputEmail" class="form-control" placeholder="" required focus-me="true">
                        <span class="help-block" ng-show="errors.email">@{{errors.email.join()}}</span>
                    </div>
                    <div class="form-group" ng-class="{'has-error': !!errors.password}">
                        <label for="inputPassword"><?=Lang::get('login.password')?></label>
                        <input type="password" ng-model="reminder.password" id="inputPassword" class="form-control" placeholder="" required>
                        <span class="help-block" ng-show="errors.password">@{{errors.password.join()}}</span>
                    </div>
                    <div class="form-group" ng-class="{'has-error': !!errors.password_confirmation}">
                        <label for="inputPassword2"><?=Lang::get('reminders.password_confirmation')?></label>
                        <input type="password" ng-model="reminder.password_confirmation" id="inputPassword2" class="form-control" placeholder="" required>
                        <span class="help-block" ng-show="errors.password_confirmation">@{{errors.password_confirmation.join()}}</span>
                    </div>
                    <div class="alert alert-@{{flashStatus}}" ng-show="flash">
                        <span ng-bind="flash"></span>
                    </div>
                    <div class="alert alert-danger" ng-show="error">
                        <span ng-bind="error"></span>
                    </div>
                    <button type="submit" class="btn btn-md btn-info btn-block" style="font-size: 18px">
                        <?=Lang::get('reminders.title')?>
                    </button>
                </form>
                <div class="alert alert-@{{flashStatus}}" ng-show="flash && passReseted">
                    <span ng-bind-html="flash"></span>
                </div>
            </div>
        </div>
    </div>
</div>