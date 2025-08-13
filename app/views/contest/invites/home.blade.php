@include('includes.header')
@include('contest.header', array('class' => 'small', 'banner' => ContestAsset::SMALL_BANNER_HTML))

<div class="container with-footer">
    <div class="row">
        <div class="col-sm-12">
            <h2>@lang('voting.inviteWelcome', ['name'=>$contest->name])</h2>

            <p ng-if="allowRegister"><i class="fa fa-info-circle"></i> @lang('voting.inviteExplain1')</p>
            <p ng-if="!allowRegister"><i class="fa fa-info-circle"></i> @lang('voting.inviteExplain2')</p>
        </div>
        <div class="col-sm-6" ng-class="{'col-sm-offset-3': !allowRegister}">
            <div class="well">
                @include('login.form-body')
            </div>
        </div>
        <div class="col-sm-6" ng-if="allowRegister">
            <div class="well">
                @include('login.register-form', ['showAlreadyRegistered'=>false])
            </div>
        </div>
    </div>
</div>
@include('includes.footer')