@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-trash"></i> @lang('contest.billing')
@endsection
@section('modal-content')
        <h3>@{{contest.name}}</h3>

        <div class="form-group">
            <label class="col-sm-2 control-label"> @lang('contest.status') </label>
            <div class="col-sm-8">
                <div class="form-check form-check-inline">
                    <label class="form-check-label">
                        <input class="form-check-input" type="radio" name="notInvoiced" id="inlineRadio1" ng-model="params.status" value="{{ContestInvoice::STATUS_NOT_INVOICED}}"> @lang('contest.notInvoiced')
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <label class="form-check-label">
                        <input class="form-check-input" type="radio" name="invoiced" id="inlineRadio2" ng-model="params.status" value="{{ContestInvoice::STATUS_INVOICED}}"> @lang('contest.invoiced')
                    </label>
                </div>
                <div class="form-check form-check-inline disabled">
                    <label class="form-check-label">
                        <input class="form-check-input" type="radio" name="payed" id="inlineRadio3" ng-model="params.status" value="{{ContestInvoice::STATUS_PAYED}}"> @lang('contest.payed')
                    </label>
                </div>
                <div class="form-check form-check-inline disabled">
                    <label class="form-check-label">
                        <input class="form-check-input" type="radio" name="payed" id="inlineRadio4" ng-model="params.status" value="{{ContestInvoice::STATUS_SWAP}}"> @lang('contest.swap')
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"> @lang('contest.billNumber') </label>
            <div class="col-sm-8">
                <input type="text" class="form-control" ng-model="params.invoice_code">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"> @lang('contest.date') </label>
            <div class="col-sm-4">
                @include('includes.datetimepicker', array('field'=> 'params.invoice_date', 'placeholder' => Lang::get('contest.date')))
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"> @lang('contest.amount') </label>
            <div class="col-sm-3">
                <input type="text" class="form-control input-price" ng-model="params.amount">
            </div>
            <label class="col-sm-2 control-label"> @lang('contest.currency') </label>
            <div class="col-sm-3">
                <select ng-model="params.currency" class="form-control form-inline">
                    @foreach(Config::get('billing.currency') as $currency)
                        <option>{{$currency}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"> @lang('contest.concept') </label>
            <div class="col-sm-8">
                <input type="text" class="form-control" ng-model="params.concept">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"> @lang('contest.businessName') </label>
            <div class="col-sm-8">
                <input type="text" class="form-control input-price" ng-model="params.business_name">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"> @lang('contest.billData') </label>
            <div class="col-sm-8">
                <textarea  rows="4" class="form-control" ng-model="params.data"> </textarea>
            </div>
        </div>
@endsection
@section('modal-actions')
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !modalForm.$dirty">
        <span ng-bind-html="flash"></span>
    </div>
    <button type="button" class="btn btn-default" ng-click="close()">@lang('general.close')</button>
    <button type="button" class="btn btn-danger" ng-click="accept()">@lang('general.save')</button>
@endsection