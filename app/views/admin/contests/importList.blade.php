@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-mail-reply"></i> @lang('contest.loadList')
@endsection
@section('modal-content')
    <div flow-object="existingFlowObject" flow-init flow-files-submitted="$flow.upload()" flow-file-added="!!{xlsx:1}[$file.getExtension()]" flow-file-added="test( $file, $event, $flow )">
        <h4 class="text-danger"> @lang('contest.important') </h4>
        <h5> @lang('contest.importList.howTo') </h5>
        <h5> @lang('contest.importList.howToName') </h5>
        <h5> @lang('contest.importList.howToLastName') </h5>
        <h5> @lang('contest.importList.howToEmail') </h5>
        <hr>
        <span class="btn" flow-btn>@lang('contest.upload')</span>
        <div class="checkbox">
            <label>
                <input type="checkbox" ng-model="data.createPassword" ng-checked="data.createPassword == 1" ng-true-value="1" ng-false-value="0">
                @lang('contest.createPassword')
            </label>
        </div>
        <table>
            <tr ng-repeat="file in $flow.files" ng-class="{danger:file.error, success:file.isComplete()}">
                <uib-progressbar ng-show="file.isUploading()" animate="false" value="file.progress() * 100" type="success" ng-class="{active:file.isUploading()}"><b>@{{file.progress() | percentage:1}}</b></uib-progressbar>
                <div ng-show="file.error"><i class="fa fa-exclamation-circle text-danger"></i> Error @{{file.msg.flash}}</div>
                <div ng-show="file.isComplete()"><i class="fa fa-check-circle text-success"></i></div>
                <td>@{{file.name}}</td>
                <td>@{{file.msg}}</td>
            </tr>
        </table>
    </div>
@endsection
@section('modal-actions')
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !modalForm.$dirty">
        <span ng-bind-html="flash"></span>
    </div>
    <button type="button" class="btn btn-default" ng-click="close()">@lang('general.close')</button>
    <button type="button" class="btn btn-danger" ng-click="importList()">@lang('general.accept')</button>
@endsection