@extends('layouts.modal')
@section('modal-title')
    @lang('contest.inscriptionData')
@endsection
@section('modal-content')
    <script type="text/ng-template" id="update-signup-metadata-field.html">
        @include('metadata.field', array('model'=>'field.value','allValues'=>'void', 'disabled'=>true, 'forceRequired' => true))
    </script>
    <div class="row">
        <label class="col-sm-3 control-label">
            @lang('user.name')
        </label>
        <div class="col-sm-9 form-control-static">
            @{{ user.first_name }} @{{ user.last_name }}
        </div>
    </div>
    <div class="row">
        <label class="col-sm-3 control-label">
            @lang('user.email')
        </label>
        <div class="col-sm-9 form-control-static">
            @{{ user.email }}
        </div>
    </div>
    <div class="row">
        <div ng-repeat="field in metadata track by $index | filter:{role:role}">
            <div ng-include="'update-signup-metadata-field.html'"></div>
        </div>
    </div>
    <div class="clearfix"></div>
    <h4> @lang('contest.notes') </h4>
    <div ng-repeat="oldNote in inscription.notes track by $index">
        <hr ng-if="$index != 0">
        <div class="col-sm-12">
            <div>@{{ oldNote.created_at }}</div>
            <div class="col-sm-10">
                <div class="well well-sm" ng-if="oldNote.msg && !note.editable">@{{ oldNote.msg }}</div>
                <div class="input-group input-group-sm input-rename" ng-if="note.editable">
                    <textarea type="text" ng-model="oldNote.msg" class="form-control" focus-me="note.editable"> </textarea>
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button" ng-click="leaveNote(oldNote.msg, inscription.id, 0, oldNote.id); note.editable=0;"><i class="fa fa-check"></i></button>
                    </span>
                </div>
            </div>
            <div class="col-sm-2">
                <button class="btn btn-info btn-xs" ng-click="note.editable = 1;"><i class="fa fa-edit"></i></button>
                <button class="btn btn-danger btn-xs" ng-click="leaveNote(note, inscription.id, 1, oldNote.id)"><i class="fa fa-trash"></i></button>
            </div>
        </div>
    </div>
    <div class="col-sm-12" style="margin-bottom: 10px;">
        <textarea cols="30" rows="6" class="form-control col-sm-10" focus-me="$index == 0" ng-model="note"></textarea>
        <input ng-disabled="!note" type="button" class="btn btn-sm btn-success btn-block" ng-click="leaveNote(note, inscription.id)" value="@lang('contest.leaveNote')" />
        <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !contestForm.$dirty">
            <span ng-bind-html="flash"></span>
        </div>
    </div>
    <div class="clearfix"></div>
@endsection
@section('modal-actions')
    <a class="btn btn-warning" data-ng-href="#/admin/inscription/@{{ inscription.id }}"><i class="fa fa-edit"></i> @lang('general.edit')</a>
    <button type="button" class="btn btn-default" ng-disabled="sending" ng-click="close()">@lang('general.close')</button>
@endsection