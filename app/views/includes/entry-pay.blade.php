@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-money"></i>
    @lang('billing.payEntry')
@endsection
@section('modal-content')
    <div class="form-control-static">
    </div>
    <div class="form-horizontal">
        @if(isset($contest->billing['prepay']) && $contest->billing['prepay'] && $contest->billing['prepay'])
            <span ng-if="status == {{ Entry::FINALIZE }}  && notPayed > 0">
                @include('includes.entry-pay-form')
            </span>
        @endif
        <span ng-if="onlyPay">
            @include('includes.entry-pay-form')
        </span>
    </div>
    <br>
    <textarea style="resize:none" type="text" rows="5" class="form-control" placeholder="@lang('contest.describeMsg')" ng-disabled="sending" ng-required="status == {{ Entry::ERROR }} || tech_status == {{ ContestFile::TECH_ERROR }}" ng-model="msg">
    </textarea>
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
        <span ng-if="contest.billing ">@lang('billing.sendingBilling')</span>
    </div>
    <button type="button" class="btn btn-default" ng-disabled="sending" ng-click="cancel()">@lang('general.cancel')</button>
    <button type="button" class="btn btn-default" ng-disabled="modalForm.$invalid || sending" ng-click="accept()">@lang('general.accept')</button>
@endsection