@include('includes.header')
@include('contest.header', array('class' => 'small', 'banner' => ContestAsset::SMALL_BANNER_HTML))

<div class="container with-footer">
    <div class="row">
        <div class="col-sm-12">
            <h2>@lang('voting.inviteWelcome', ['name'=>$contest->name])</h2>
        </div>
        <div class="col-sm-6 col-sm-offset-3">
            <div class="well text-center">
                <div ng-if="!rejected">
                    <h4>@lang('voting.inviteRejectQuestion')</h4>
                    <div class="row">
                        <div class="col-xs-6">
                            <button type="button" data-ng-click="reject()" class="btn btn-danger btn-block">
                                @lang('voting.inviteRejectAnsYes')
                            </button>
                        </div>
                        <div class="col-xs-6">
                            <a href="#/invite" class="btn btn-success btn-block">
                                @lang('voting.inviteRejectAnsNo')
                            </a>
                        </div>
                    </div>
                </div>
                <div ng-if="rejected">
                    <div class="alert alert-info">
                    @lang('voting.inviteRejected')
                    </div>
                    <a href="#/invite" class="btn btn-success btn-sm">
                        @lang('voting.inviteRejectAnsNo')
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@include('includes.footer')