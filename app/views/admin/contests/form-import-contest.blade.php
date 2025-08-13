@extends('admin.contests.form', array('active' => 'import'))
@section('form')
    <h3 class="well well-sm">
        @lang('contest.tab.import')
        {{--@if(!$superadmin)
            <i class="fa fa-question-circle text-info" popover="@lang('contest.explain.import-contest')" popover-placement="right" popover-trigger="mouseenter" class="btn btn-default"></i>
        @endif--}}
        <div class="btn-group pull-right">
            <a href="" class="btn btn-sm btn-default btn-small" ng-repeat="(key,lang) in langs.All" ng-class="{'active':selectedLang == key}" ng-click="setLang(key)"><i class="flag-icon flag-icon-@{{key}}"></i> @{{lang}}</a>
        </div>
        <div class="clearfix"></div>
    </h3>
    <div class="btn-group">
        <div class="form-inline" ng-if="{{Auth::check()}}">
            <button type="button" class="btn btn-lg btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                @lang('contest.import-contest')
            </button>
            <ul class="dropdown-menu scrollable-menu">
                <li style="padding: 0 5px 5px;"><input type="text" ng-model="filterContest" ng-click="$event.stopPropagation();" class="form-control searchBox" style="width:100%;" placeholder="@lang('general.search')"/></li>
                <li ng-repeat="contest in contestsIds | filter: filterContest"><a type="button" ng-click="selectContest(contest)"> @{{ contest.name }} </a></li>
            </ul>
        </div>
    </div>

    <div ng-show="selected">
        <div class="h4">
            <div class="alert alert-info"> @lang('contest.importSelect') @{{ selected.name }}</div>
            <div class="alert alert-danger" ng-if="selected.templates > 0"> @lang('contest.importSelectHasTemplates')</div>
        </div>
        <h4 class="well well-sm">
        @lang('contest.selectImports')
        </h4>
        <div class="col-md-12 col-lg-12 col-sm-12">
            <div class="h4">
                <input type="checkbox" ng-model="selCat" ng-true-value="1" ng-false-value="0" ng-value="0"> @lang('contest.categories')
            </div>
            <div class="h4">
                <input type="checkbox" ng-model="selEntryForm" ng-true-value="1" ng-false-value="0" ng-value="0"> @lang('contest.entriesMetadata')
            </div>
            <div class="h4">
                <input type="checkbox" ng-model="selUserForm" ng-true-value="1" ng-false-value="0" ng-value="0"> @lang('contest.inscriptions')
            </div>
            <div class="h4">
                <input type="checkbox" ng-model="selStyle" ng-true-value="1" ng-false-value="0" ng-value="0"> @lang('contest.importStyle&Emails')
            </div>
            <div class="h4">
                <input type="checkbox" ng-model="selPayments" ng-true-value="1" ng-false-value="0" ng-value="0"> @lang('contest.contestPaymentsMethods')
            </div>
            <div class="h4">
                <input type="checkbox" ng-model="selVoting" ng-true-value="1" ng-false-value="0" ng-value="0"> @lang('contest.tab.voting')
            </div>
        </div>
        <div class="col-md-12 col-lg-12 col-sm-12 h5">
            <!--<div class="alert alert-danger" ng-if="selCat == 1 || selEntryForm == 1 || selUserForm == 1">-->
                <div class="alert alert-danger" ng-if="selCat == 1 && contest.categories.length > 0"> @lang('contest.contestHasCategories') </div>
                <div class="alert alert-danger" ng-if="selEntryForm == 1 && metadataFieldsCount > 0"> @lang('contest.contestHasInscription') </div>
                <div class="alert alert-danger" ng-if="selUserForm == 1 && contest.inscription_metadata_fields.length > 0"> @lang('contest.contestHasUserForm') </div>
            <!--</div>-->
        </div>
        <div class="col-md-12 col-lg-12 col-sm-12 h4">
            <div class="btn btn-lg btn-success" ng-click="importContest()" ng-disabled="sending"> @lang('contest.import') </div>
        </div>
        <div class="col-md-12 col-lg-12 col-sm-12 h4" ng-show="sending">
            <i class="fa fa-spin fa-2x fa-spinner"> </i> @lang('contest.waitImport')
        </div>
        <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash">
            <h4><span ng-bind-html="flash"></span></h4>
            <div class="h4 text-success"> Usted eligio importar: </div>
            <div class="h4 text-success" ng-if="FlashSelCat == 1"> <i class="fa fa-check"></i> @lang('contest.categories') </div>
            <div class="h4 text-success" ng-if="FlashSelEntryForm == 1"> <i class="fa fa-check"></i> @lang('contest.entriesMetadata') </div>
            <div class="h4 text-success" ng-if="FlashSelUserForm == 1"> <i class="fa fa-check"></i> @lang('contest.inscriptions') </div>
            <div class="h4 text-success" ng-if="FlashSelStyle == 1"> <i class="fa fa-check"></i> @lang('contest.importedStyle') </div>
            <div class="h4 text-success" ng-if="FlashSelPayments == 1"> <i class="fa fa-check"></i> @lang('contest.contestPaymentsMethods') </div>
            <div class="h4 text-success" ng-if="FlashSelVoting == 1"> <i class="fa fa-check"></i> @lang('contest.tab.voting') </div>
        </div>
    </div>
@endsection