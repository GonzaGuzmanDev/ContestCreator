@extends('layouts.modal')
@section('modal-title')
    <i class="fa fa-check-square-o"></i> @lang('login.termstitle')
@endsection

@section('modal-content')
    <?php echo isset($contest) ? $contest->getAsset(ContestAsset::TERMS)->content : null; ?>

@endsection

@section('modal-actions')
<button type="button" class="btn btn-danger" ng-click="close()" focus-me="true">@lang('general.close')</button>
@endsection
