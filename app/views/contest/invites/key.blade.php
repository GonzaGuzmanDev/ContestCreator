@include('includes.header')
@include('contest.header', array('class' => 'small', 'banner' => ContestAsset::SMALL_BANNER_HTML))
<script type="text/ng-template" id="terms.html">
    @include('login.register-terms')
</script>
<div class="container with-footer">
    <div class="row">
        <div class="col-sm-12">
            <h2>@lang('voting.inviteWelcome', ['name'=>$contest->name])</h2>
        </div>
        <br>
        <div class="col-sm-6 col-sm-offset-3">
            <div class="well">
                <p><i class="fa fa-info-circle"></i> @lang('voting.keyInviteExplain')</p>
                <p>
                    <input type="text" class="form-control" data-ng-model="key" placeholder="@lang('voting.keyInviteCode')">
                </p>
                <div class="form-group" ng-class="{'has-error': !!errors.accept}">
                    <div class="checkbox" style="margin-bottom: 0;">
                        <label class="control-label"><input type="checkbox" name="accept" ng-model="registration.accept">
                            <?=Lang::get('register.accept')?> <a href="<?URL::to('/')?>/#/termsofuse" target="_blank"><?=Lang::get('register.termsAndConditions')?></a>
                            @lang('contest.acceptterms') de {{$contest->name}}
                        </label>
                    </div>
                    <div class="checkbox" style="margin-bottom: 0;">
                        <label class="control-label"><input type="checkbox" name="accept" ng-model="confidenciality.accept">
                            Acepto el <a href="https://www.oxoawards.com/{{$contest->code}}/#/page/confidencialidad" target="_blank"> Acuerdo de confidencialidad</a>
                            de {{$contest->name}}
                        </label>
                    </div>
                </div>
                <p>
                    <button type="button" ng-click="login()" class="btn btn-success btn-block" ng-disabled="sending || !registration.accept || !confidenciality.accept">
                    <i class="fa fa-spin fa-spinner" data-ng-show="sending"></i>
                    @{{ sending ? '@lang('voting.keyInviteSending')' : '@lang('voting.keyInviteSend')' }}
                    </button>
                </p>
                <div class="alert alert-close alert-@{{flashStatus}}" ng-show="flash">
                    <span ng-bind="flash"></span>
                </div>
            </div>
        </div>
    </div>
</div>
@include('includes.footer')