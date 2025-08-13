@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-send"></i> @lang('voting.sendInvitations')
@endsection
@section('modal-content')
        <div ng-if="(judges|filter:{status:{{VotingUser::PENDING_NOTIFICATION}}}).length == 0 && reSend == false">
            <div class="alert alert-warning">
                <i class="fa fa-info-circle"></i>
                @lang('voting.noJudgesPendingInvite')
            </div>
        </div>
        <div ng-if="(judges|filter:{status:{{VotingUser::PENDING_NOTIFICATION}}}).length || (((judges|filter:{status:{{VotingUser::NOTIFIED}}}).length || (judges|filter:{status:{{VotingUser::RESEND}}}).length) && reSend == true)">
            <p>@lang('voting.confirmSendInvitations') "@{{ voting.name }}"?</p>

            <h4>@lang('voting.judgesPendinginvite')</h4>
            <div ng-repeat="judge in judges" ng-if="judge.status == {{VotingUser::PENDING_NOTIFICATION}} || ((judge.status == {{VotingUser::NOTIFIED}} || judge.status == {{VotingUser::RESEND}}) && reSend == true)">
                <span user-card user-card-model="judge.inscription.user" user-show-email="true"></span>
                <span ng-if="judge.inscription.invitename">@{{ judge.inscription.invitename }}</span>
                <span ng-if="judge.inscription.email">@{{ judge.inscription.email }}</span>
            </div>
            <br>
            <h4>@lang('voting.inviteEmailPreview')</h4>
            <div style="border: 1px solid #d1d1d1; padding: 10px; max-height: 500px; overflow: auto;">
                <?
                $token = 'XXXXXXXX';
                $body = ContestAsset::where('contest_id', $contest->id)->where('type', ContestAsset::JUDGE_INVITATION_EMAIL)->select('content')->firstOrFail();
                $body->content = str_replace([':contest', ':link',':rejectlink', ':firstname', ':lastname', ':name'], [$contest->name, URL::to('/'.$contest->code.'/invite/'.$token),URL::to('/'.$contest->code.'/invite/'.$token)."#reject",Lang::get("user.firstName"),Lang::get("user.lastName"),Lang::get("user.name")], $body->content);
                echo $body->content;
                ?>
            </div>
        </div>
@endsection
@section('modal-actions')
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !modalForm.$dirty">
        <span ng-bind-html="flash"></span>
    </div>
    <button type="button" class="btn btn-success" ng-click="send()">@lang('voting.send')</button>
    <button type="button" class="btn btn-success" ng-click="send(true)">@lang('voting.sendCodes')</button>
@endsection