<div class="container" ng-hide="hide">
    <div class="row">
        <div class="login col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
            <a ng-href="<?=URL::to('/');?>/#/home" class="logo"><span></span></a>
            @if(Config::get('registration.enabled'))
                <a class="btn btn-danger btn-md pull-right" href="#register" style="font-size: 18px"><?=Lang::get('login.register')?></a>
            @endif
            @include('login.form-body')
        </div>
    </div>
</div>