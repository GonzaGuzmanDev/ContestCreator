@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-ticket"></i>
    @lang('contest.sifter.changeto')
    <span ng-class="{'text-warning': status == {{Entry::COMPLETE}}, 'text-success': status == {{Entry::FINALIZE}}, 'text-info': status == {{Entry::APPROVE}}, 'text-danger': status == {{Entry::ERROR}}}">
    @{{ {'{{{Entry::COMPLETE}}}' : '@lang('contest.entry.complete')', '{{{Entry::FINALIZE}}}' : '@lang('contest.entry.finalized')', '{{{Entry::APPROVE}}}' : '@lang('contest.entry.approved')', '{{{Entry::ERROR}}}' : '@lang('contest.entry.error')'} | echoswitch : status }}
    </span>
    <span class="label label-primary label-as-badge"> @{{ entry.length }} </span>
@endsection
@section('modal-content')
    @if(isset($contest->billing['prepay']) && $contest->billing['prepay'])
        <span ng-if="status == {{Entry::FINALIZE}} && notPayed > 0">
            @include('includes.entry-pay-form')
            </span>
    @endif
    <div class="form-control-static" ng-if="entry.length">
        <span ng-repeat="ent in entry">
            <span entry-card entry="ent" fields="fields"></span><br>
        </span>

    </div>
    <br>
    <textarea style="resize:none" type="text" rows="5" class="form-control" placeholder="@lang('contest.describeMsg')" ng-disabled="sending" ng-required="status == {{ Entry::ERROR }} || tech_status == {{ ContestFile::TECH_ERROR }}" ng-model="msg"/>
    <div ng-show="status == {{ Entry::ERROR }} || tech_status == {{ ContestFile::TECH_ERROR }}" class="form-control-static">
        <span class="text-danger">*</span> @lang('contest.comment.required')
    </div>
    <div class="clearfix"></div>
@endsection
@section('modal-actions')
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash">
        <span ng-bind-html="flash"></span>
    </div>
    <div class="alert alert-tight alert-inline alert-transparent text-warning" ng-if="sending">
        <i class="fa fa-circle-o-notch fa-spin"></i>
        <span ng-if="contest.billing && status == {{Entry::FINALIZE}} && entry.status == {{Entry::COMPLETE}}">@lang('billing.sendingBilling')</span>
        <span ng-if="!contest.billing || (status != {{Entry::FINALIZE}} && entry.status != {{Entry::COMPLETE}})">@lang('billing.sending')</span>
    </div>
    <button type="button" class="btn btn-default" ng-disabled="sending" ng-click="cancel()">@lang('general.cancel')</button>
    <button type="button" class="btn btn-default" ng-disabled="modalForm.$invalid || sending" ng-click="accept()">@lang('general.accept')</button>
@endsection