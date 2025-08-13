<? /* ESTE NO ESTA ANDANDOOOOOOOOOOO */ ?>
@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-history"></i>
    @lang('contest.entryLog')
    <span entry-card entry="entry" fields="fields"></span>
@endsection
@section('modal-content')
    <div ng-repeat="log in entryLog">
        <h5>
            <div user-card user-card-model="log" class="selected-user-card"></div>
            <div ng-if="log.status == {{Entry::COMPLETE}}" class="text-default"> @{{ log.created_at }} <i class="fa fa-check"></i> </div>
            <div ng-if="log.status == {{Entry::FINALIZE}}" class="text-success"> @{{ log.created_at }} <i class="fa fa-check"></i></div>
            <div ng-if="log.status == {{Entry::APPROVE}}" class="text-info">     @{{ log.created_at }} <i class="fa fa-thumbs-up"></i> </div>
            <div ng-if="log.status == {{Entry::ERROR}}" class="text-danger">     @{{ log.created_at }} <i class="fa fa-thumbs-down"></i> </div>
            <br>
            @{{ log.msg }}
        </h5>
        <hr>
    </div>
@endsection
@section('modal-actions')
    <button type="button" class="btn btn-default" ng-click="cancel()">@lang('general.ok')</button>
@endsection