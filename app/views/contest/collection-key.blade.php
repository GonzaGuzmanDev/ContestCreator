@include('includes.header')
@include('contest.header', array('class' => 'small', 'banner' => ContestAsset::SMALL_BANNER_HTML))
<script type="text/ng-template" id="terms.html">
    @include('login.register-terms')
</script>
<div class="container with-footer">
    <div class="row">
        <div class="col-sm-12">
            <h2>@lang('collection.inviteWelcome', ['name'=>$contest->name])</h2>
        </div>
        <br>
        <div class="col-sm-6 col-sm-offset-3">
            <div class="well">
                <p><i class="fa fa-info-circle"></i> @lang('collection.keyInviteExplain')</p>
                <p>
                    <input type="text" class="form-control" data-ng-model="key" placeholder="@lang('collection.keyInviteCode')">
                </p>
                <p>
                    <button type="button" ng-click="login()" class="btn btn-success btn-block" ng-disabled="sending || key.length < 5">
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