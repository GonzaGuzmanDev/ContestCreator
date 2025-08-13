@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-tag"></i> @lang('billing.changeStatus')
@endsection
@section('modal-content')
    <div class="form-group">
        <label class="col-md-4 control-label control-label">
            <p class="text-right">@lang('billing.id')</p>
        </label>
        <div class="col-sm-12 col-md-8">
            <div class="form-control-static">@{{ bill.id | zpad:8 }}</div>
        </div>
    </div>
    <div class="form-group" ng-class="{error: errors.name}">
        <label class="col-md-4 control-label">@lang('general.user')</label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            <div class="form-control-static">
                <span user-card user-card-model="bill.user"></span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label control-label">
            <p class="text-right">@lang('billing.price')</p>
        </label>
        <div class="col-sm-12 col-md-8">
            <div class="form-control-static">@{{ bill.price }} @{{ bill.currency }}</div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label control-label">
            <p class="text-right">@lang('billing.method')</p>
        </label>
        <div class="col-sm-12 col-md-8">
            <div class="form-control-static">
                <span ng-switch="bill.method">
                    <span ng-switch-when="{{ Billing::METHOD_TRANSFER }}">
                        @lang('billing.transfer')
                    </span>
                    <span ng-switch-when="{{ Billing::METHOD_CHECK }}">
                        @lang('billing.check')
                    </span>
                    <span ng-switch-when="{{ Billing::METHOD_TCO  }}">
                        <i class="fa fa-credit-card"></i> @lang('billing.TCO')
                    </span>
                    <span ng-switch-when="{{ Billing::METHOD_MP  }}">
                        <i class="fa fa-credit-card"></i> @lang('billing.MercadoPago')
                    </span>
                </span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label control-label">
            <p class="text-right">@lang('billing.status')</p>
        </label>
        <div class="col-sm-12 col-md-8">
            <div class="form-control-static">
                <span ng-switch="bill.status">
                    <div ng-switch-when="{{ Billing::STATUS_PENDING }}">
                        <span class="text-warning">
                            <i class="fa fa-clock-o"></i> @lang('billing.status.pending')
                        </span>
                        <br>
                    </div>
                    <div ng-switch-when="{{ Billing::STATUS_SUCCESS }}">
                        <span class="text-success">
                            <i class="fa fa-check"></i> @lang('billing.status.success')
                        </span>
                    </div>
                    <div ng-switch-when="{{ Billing::STATUS_ERROR }}">
                        <span class="text-danger">
                            <i class="fa fa-thumbs-down"></i> @lang('billing.status.error')
                        </span>
                    </div>
                    <div ng-switch-when="{{ Billing::STATUS_PROCESSING }}">
                        <span class="text-info">
                            <i class="fa fa-cog"></i> @lang('billing.status.processing')
                        </span>
                    </div>
                </span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label control-label">
            <p class="text-right">@lang('billing.transactionid')</p>
        </label>
        <div class="col-sm-12 col-md-8">
            <div class="form-control-static">@{{ bill.transaction_id }}</div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label control-label">
            <p class="text-right">@lang('billing.date')</p>
        </label>
        <div class="col-sm-12 col-md-8">
            <div class="form-control-static">@{{ bill.created_at | amDateFormat:'DD/MM/YYYY  HH:mm:ss' }}</div>
        </div>
    </div>
@endsection
@section('modal-actions')
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !modalForm.$dirty">
        <span ng-bind-html="flash"></span>
    </div>
    <button type="button" class="btn btn-default" ng-click="close()">@lang('general.cancel')</button>
    <button class="btn btn-warning" ng-if="newStatus == {{ Billing::STATUS_PENDING }}" ng-click="save()">
        <i class="fa fa-clock-o"></i> @lang('billing.pendingBill')
    </button>
    <button class="btn btn-info" ng-if="newStatus == {{ Billing::STATUS_PROCESSING }}" ng-click="save()">
        <i class="fa fa-cog"></i> @lang('billing.processingBill')
    </button>
    <button class="btn btn-success" ng-if="newStatus == {{ Billing::STATUS_SUCCESS }}" ng-click="save()">
        <i class="fa fa-thumbs-up"></i> @lang('billing.approveBill')
    </button>
    <button class="btn btn-danger" ng-if="newStatus == {{ Billing::STATUS_ERROR }}" ng-click="save()">
        <i class="fa fa-thumbs-down"></i> @lang('billing.rejectBill')
    </button>
@endsection