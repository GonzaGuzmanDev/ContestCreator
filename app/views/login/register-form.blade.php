<h2><?=Lang::get('register.register')?></h2>
@if(Config::get('registration.enabled'))
    <form role="form" method="post" ng-submit="register()">
        <div class="form-group" ng-class="{'has-error': !!errors.first_name}">
            <label for="inputName" class="control-label"><?=Lang::get('register.firstName')?></label>
            <input type="text" id="inputName" class="form-control" placeholder="" ng-model="registration.first_name" required focus-me="true">
            <span class="help-block" ng-show="errors.first_name">@{{errors.first_name.join()}}</span>
        </div>
        <div class="form-group" ng-class="{'has-error': !!errors.last_name}">
            <label for="inputName2" class="control-label"><?=Lang::get('register.lastName')?></label>
            <input type="text" id="inputName2" class="form-control" placeholder="" ng-model="registration.last_name" required>
            <span class="help-block" ng-show="errors.last_name">@{{errors.last_name.join()}}</span>
        </div>
        <div class="form-group" ng-class="{'has-error': !!errors.email}">
            <label for="inputEmail" class="control-label"><?=Lang::get('register.email')?></label>
            <input type="email" id="inputEmail" class="form-control" placeholder="" ng-model="registration.email" required>
            <span class="help-block" ng-show="errors.email">@{{errors.email.join()}}</span>
        </div>
        <div class="form-group" ng-class="{'has-error': !!errors.new_password}">
            <label for="inputPassword" class="control-label"><?=Lang::get('register.password')?></label>
            <input type="password" id="inputPassword" class="form-control" placeholder="" ng-model="registration.new_password" required>
            <span class="help-block" ng-show="errors.new_password">@{{errors.new_password.join()}}</span>
        </div>
        <div class="form-group" ng-class="{'has-error': !!errors.repeat_password}">
            <label for="inputPassword2" class="control-label"><?=Lang::get('register.repeatPassword')?></label>
            <input type="password" id="inputPassword2" class="form-control" placeholder="" ng-model="registration.repeat_password" required>
            <span class="help-block" ng-show="errors.repeat_password">@{{errors.repeat_password.join()}}</span>
        </div>
        <div class="form-group" ng-class="{'has-error': !!errors.captcha}">
            <div class="well well-sm captcha-well text-center">
                <img ng-src="@{{captchaUrl}}" alt="Captcha image" class="captcha-img"/>
                <input type="text" id="inputCaptcha" class="form-control captcha-input input-sm" placeholder="<?=Lang::get('register.captcha')?>" ng-model="registration.captcha" required>
                <div class="clearfix"></div>
                <span class="help-block" ng-show="errors.captcha">@{{errors.captcha.join()}}</span>
            </div>
        </div>
        <div class="alert alert-@{{flashStatus}}" ng-show="flash">
            <span ng-bind="flash"></span>
        </div>
        @if($showAlreadyRegistered)
        <a class="pull-right btn btn-link" href="#login"><?=Lang::get('register.iHaveUser')?></a>
        @endif
        <button type="submit" class="btn btn-md btn-danger">
            <?=Lang::get('register.registerMe')?>
        </button>
        <div class="form-group" ng-class="{'has-error': !!errors.accept}">
            <div class="checkbox">
                <label class="control-label"><input type="checkbox" name="accept" ng-model="registration.accept">
                    <!--<?=Lang::get('register.accept')?> <a href ng-click="viewTerms()"><?=Lang::get('register.termsAndConditions')?></a>-->
                    <?=Lang::get('register.accept')?> <a href="https://www.oxoawards.com/#/termsofuse" target="_blank"><?=Lang::get('register.termsAndConditions')?> de OxoAwards</a>
                </label>
                <span class="help-block" ng-show="errors.accept">@{{errors.accept.join()}}</span>
            </div>
        </div>
    </form>
    @if(Config::get('registration.oauth'))
        <div class="login-or">
            <hr class="hr-or">
        </div>
        <div class="panel panel-default">
            <div class="panel-heading"><?=Lang::get('register.also')?></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-4 col-md-4">
                        <a href="<?=URL::to('/');?>/service/login/fb/" class="btn btn-lg btn-primary btn-block btn-facebook"><i class="fa fa-facebook-square"></i> <?=Lang::get('register.facebook')?></a>
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-4">
                        <a href="<?=URL::to('/');?>/service/login/tt/" class="btn btn-lg btn-info btn-block"><i class="fa fa-twitter-square"></i> <?=Lang::get('register.twitter')?></a>
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-4">
                        <a href="<?=URL::to('/');?>/service/login/gp/" class="btn btn-lg btn-danger btn-block"><i class="fa fa-google-plus-square"></i> <?=Lang::get('register.google+')?></a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@else
    <div class="alert alert-warning">@lang('register.disabled')</div>
    <a class="pull-right btn btn-link" href="#login"><?=Lang::get('register.backToLogin')?></a>
@endif