@extends('layouts.modal')

@section('modal-title')
    <i class="fa fa-trash"></i> <?=Lang::get('contest.deleteInscription')?>
@endsection

@section('modal-content')
    <p><?=Lang::get('contest.deleteInscription.sure')?></p>
    <span class="text-info" ng-if="inscription.role == <?=Inscription::INSCRIPTOR?>"><i class="fa fa-info-circle"></i> @lang('contest.deleteInscription.description')</span>
    <span class="text-info" ng-if="inscription.role == <?=Inscription::JUDGE?>"><i class="fa fa-info-circle"></i> @lang('contest.deleteInscription.descriptionJudge')</span>
    <br/>
    <span class="text-danger">@lang('contest.deleteInscription.warn')</span>
@endsection

@section('modal-actions')
    <button type="button" class="btn btn-default" ng-click="close()"><?=Lang::get('general.close')?></button>
    <button type="button" class="btn btn-danger" ng-click="destroy()"><?=Lang::get('general.delete')?></button>
@endsection