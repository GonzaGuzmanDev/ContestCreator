@extends('layouts.modal')

@section('modal-title')
    <i class="fa fa-trash"></i> <?=Lang::get('contest.deleteFile')?>
@endsection

@section('modal-content')
    <p><?=Lang::get('contest.deleteFile.sure')?> @{{file.name}}</p>
@endsection

@section('modal-actions')
    <i class="fa fa-spin fa-circle-o-notch" ng-show="deleting"></i>
    <button type="button" class="btn btn-danger" ng-disabled="deleting" ng-click="destroy()"><?=Lang::get('general.delete')?></button>
    <button type="button" class="btn btn-default" ng-disabled="deleting" ng-click="close()"><?=Lang::get('general.close')?></button>
@endsection