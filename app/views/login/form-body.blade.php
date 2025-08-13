<h2><?=Lang::get('login.signIn')?></h2>
<form role="form" method="post" data-ng-submit="login()">
    <div class="form-group">
        <label for="inputEmail"><?=Lang::get('login.email')?></label>
        <input type="email" id="inputEmail" class="form-control" placeholder="" ng-model="loginForm.email" required focus-me="true">
    </div>
    <div class="form-group">
        <label for="inputPassword"><?=Lang::get('login.password')?></label>
        <input type="password" id="inputPassword" class="form-control" placeholder="" ng-model="loginForm.password" required>
        <div class="checkbox" style="margin-bottom: 0;">
            <label><input type="checkbox" ng-model="loginForm.remember"> <?=Lang::get("login.remindMe")?></label>
        </div>
        <a class="pull-right form-control-static" href ng-click="rememberPass();"><?=Lang::get("login.passwordForget")?></a>
        <div class="clearfix"></div>
    </div>
    <div class="alert alert-@{{flashStatus}}" ng-show="flash">
        <span ng-bind-html="flash"></span>
    </div>
    <button type="submit" class="btn btn-md btn-success btn-block" style="font-size: 18px">
        <?=Lang::get('login.signIn')?>
    </button>
</form>
@if(Config::get('registration.enabled') && Config::get('registration.oauth'))
    <div class="login-or">
        <hr class="hr-or">
        <div class="hr-or-text"></div>
    </div>
    <div class="btn-group btn-group-vertical btn-group-justified" role="group">
        <a href="<?=URL::to('/');?>/service/login/fb/" class="btn btn-sm btn-primary btn-block btn-facebook"><i class="fa fa-facebook-official"></i> <?=Lang::get('login.facebook')?></a>
        <a href="<?=URL::to('/');?>/service/login/tt/" class="btn btn-sm btn-info btn-block"><i class="fa fa-twitter-square"></i> <?=Lang::get('login.twitter')?></a>
        <a href="<?=URL::to('/');?>/service/login/gp/" class="btn btn-sm btn-danger btn-block"><i class="fa fa-google-plus-square"></i> <?=Lang::get('login.google+')?></a>
    </div>
@endif
<script type="text/ng-template" id="rememberPass.html">
    @include('login.resetpassword-modal')
</script>