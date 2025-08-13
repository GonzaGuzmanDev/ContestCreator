@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-send"></i> @lang('contest.sendNewsletter')
@endsection
@section('modal-content')
        <div>
            <p>@lang('contest.confirmSendNewsletter') "@{{ newsletter.name }}"?</p>
            <h4>@lang('contest.newsletterPreview')</h4>
            <div style="border: 1px solid #d1d1d1; padding: 10px; max-height: 500px; overflow: auto;" ng-bind-html="newsletter.email_body"></div>
        </div>
        <br>
        <div class="well well-sm">
            <h4>@lang('contest.emailsPendinginvite')</h4>
        </div>
        <div ng-repeat="email in emails" ng-if="email.status == {{NewsletterUser::PENDING_NOTIFICATION}} || ((email.status == {{NewsletterUser::NOTIFIED}} || email.status == {{NewsletterUser::RESEND}}) && reSend == true)">
            <span user-card user-card-model="email.inscription.user" user-show-email="true"></span>
            <span> @{{ $index + 1 }} - </span>
            <span ng-if="email.email">@{{ email.email }}</span>
        </div>
        <div ng-if="(emails|filter:{status:{{NewsletterUser::PENDING_NOTIFICATION}}}).length == 0">
            <div class="alert alert-warning">
                <i class="fa fa-info-circle"></i>
                @lang('contest.NoRemain')
            </div>
        </div>
@endsection
@section('modal-actions')
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !modalForm.$dirty">
        <span ng-bind-html="flash"></span>
    </div>
    <button type="button" class="btn btn-default" ng-click="close()">@lang('general.close')</button>
    <button type="button" class="btn btn-success" ng-click="send()">@lang('contest.sendNewsletter')</button>
@endsection
