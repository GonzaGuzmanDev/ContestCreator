@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-money"></i>
    @lang('oxoTicket.payTicket')
@endsection
@section('modal-content')
    <div class="form-control-static">
    </div>
    <div class="form-horizontal">
        @include('includes.ticket-pay-form')
    </div>
@endsection
@section('modal-actions')
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash">
        <span ng-bind-html="flash"></span>
    </div>
    <div class="alert alert-tight alert-inline alert-transparent text-warning" ng-if="sending">
        <i class="fa fa-circle-o-notch fa-spin"></i>
        <span ng-if="contest.billing ">@lang('billing.sendingBilling')</span>
    </div>
    <button type="button" class="btn btn-default" ng-if="transaction == false" ng-disabled="sending" ng-click="cancel()">@lang('general.cancel')</button>
    <button type="button" class="btn btn-default" ng-if="transaction == false" ng-disabled="modalForm.$invalid || sending" ng-click="accept()">@lang('general.accept')</button>
    <div><buttton ng-if="transaction == true" type="button" class="btn btn-danger pull-right" ng-click="reloadRoute()">@lang('oxoTicket.continueShopping')</buttton></div>
    <div><a ng-if="transaction == true" type="button" class="btn btn-success pull-right" href="#/tickets">@lang('oxoTicket.goToviewTickets')</a></div>
    <div class="clearfix"></div>
    <div ng-if="transaction == true">
        <div class="text-center well well-sm">
            <b>@lang('oxoTicket.buyTicketsOk')</b>
        </div>
        <b>
            <div class="col-sm-12">
                <div ng-repeat="code in buyCode">
                    <span class="col-sm-6 text-center">
                        <h4> @{{ code.name }} </h4>
                        <h4>@lang('oxoTicket.paymentCode') <br>@{{ code.code }}</h4><br>
                        <img ng-src="@{{ code.qr }}"/>
                    </span>
                </div>
            </div>
        </b>
    </div>
@endsection