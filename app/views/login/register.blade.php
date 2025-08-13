<script type="text/ng-template" id="terms.html">
    @include('login.register-terms')
</script>
<div class="container" ng-hide="hide">
    <div class="row">
        <div class="login col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
            <a ng-href="<?=URL::to('/');?>/#/home" class="logo"><span></span></a>
            @include('login.register-form', ['showAlreadyRegistered'=>true])
        </div>
    </div>
</div>