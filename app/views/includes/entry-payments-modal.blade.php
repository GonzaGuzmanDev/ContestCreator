@extends('layouts.modal', ['hideModalForm' => true])
@section('modal-title')
    <i class="fa fa-money"></i>
    @lang('billing.entryPayments')
@endsection
@section('modal-content')
    <div class="form-control-static" >
        <span entry-card entry="entry" fields="fields"></span>
    </div>
    <div class="form-horizontal">

        @include('includes.entry-payments', ['large'=>true])

    </div>
    <div class="clearfix"></div>
@endsection
@section('modal-actions')
    <button type="button" class="btn btn-default" ng-click="close()">@lang('general.close')</button>
@endsection