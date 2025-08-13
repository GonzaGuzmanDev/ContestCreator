@extends('admin.contests.form', array('active' => 'pages'))
@include('includes.categoryList')
@section('form')
    <h4 class="well well-sm">
        <a href="#{{{ $superadmin ? '/contests/edit/@{{contest.code}}/pages' : '/admin/pages' }}}" class="" role="button"><i class="fa fa-arrow-left"></i> @lang('contest.pages')</a>
        /
        <span ng-show="!page.id">@lang('contest.creatingPage')</span>
        <span ng-show="page.id">@lang('contest.editingPage')</span>
    </h4>
    <div class="form-group" ng-class="{error: errors.name}">
        <label for="inputName" class="col-sm-2 control-label">@lang('general.name')</label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            <input type="text" class="form-control col-md-3" id="inputName" placeholder="@lang('general.name')" name="name" ng-model="page.name" required>
            <div ng-show="errors.name" class="help-inline text-danger form-control-static">@{{errors.name.toString()}}</div>
        </div>
    </div>
    <br>
    <div class="col-sm-offset-2">
        <a ng-if="page.url" href="@{{page.url}}" class="btn btn-primary btn-xs " target="_blank"><i class="fa fa-link"></i> @lang('metadata.preview')</a>
    </div>
    <br>
    <div class="form-group">
        <div class="col-sm-2 control-label">
            <p class="text-right">@lang('contest.content')</p>
        </div>
        <div id='wysiwyg' class="col-sm-12 col-md-10">
            <div text-angular name="htmlcontent" ng-model="page.content" ta-disabled='disabled'></div>
        </div>
    </div>
    <div class="form-group" ng-class="{error: errors.hasEntries}">
        <label for="inputHasEntries" class="col-sm-2 control-label">@lang('contest.showEntries')</label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            <select class="form-control col-md-3" id="inputHasEntries" name="hasEntries" ng-model="page.hasEntries">
                <option value="1"> SI </option>
                <option value="0"> NO </option>
            </select>
            <div ng-show="errors.name" class="help-inline text-danger form-control-static">@{{errors.name.toString()}}</div>
        </div>
    </div>

    <div class="form-group">
        <label for="inputHasEntries" class="col-sm-2 control-label">@lang('general.search')</label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            <input type="text" class="form-control form-inline" data-ng-model="pagination.query" ng-model-options="{debounce: 500}" placeholder="@lang('voting.resultsfilter')">
        </div>
    </div>
    <div class="form-group">
        <label for="inputHasEntries" class="col-sm-2 control-label">@lang('general.status')</label>
        <div ng-if="page.hasEntries == 1" class="btn-group col-sm-8 col-md-8 col-lg-8">
            <a href="" class="btn btn-default" ng-click="deselectEntries()">
                <span class="filter-label">@lang('general.deselectAll') </span>
            </a>
            <a href="" class="btn" ng-class="{'btn-default': statusFilters.indexOf({{ Entry::INCOMPLETE }}) == -1, 'btn-primary':statusFilters.indexOf({{ Entry::INCOMPLETE }}) != -1}" ng-click="toggleFilterBy({{ Entry::INCOMPLETE }})">
                <span ng-class="{'label label-primary label-as-badge':statusFilters.indexOf({{ Entry::INCOMPLETE }}) == -1,'badge':statusFilters.indexOf({{ Entry::INCOMPLETE }}) != -1}">@{{ countEntries({{{ Entry::INCOMPLETE }}}) }}</span>
                <span class="filter-label">@lang('contest.incomplete')</span>
            </a>
            <a href="" class="btn" ng-class="{'btn-default': statusFilters.indexOf({{ Entry::COMPLETE }}) == -1, 'btn-warning':statusFilters.indexOf({{ Entry::COMPLETE }}) != -1}" ng-click="toggleFilterBy({{ Entry::COMPLETE }})">
                <span ng-class="{'label label-warning label-as-badge':statusFilters.indexOf({{ Entry::COMPLETE }}) == -1,'badge':statusFilters.indexOf({{ Entry::COMPLETE }}) != -1}">@{{ countEntries({{{ Entry::COMPLETE }}}) }}</span>
                <span class="filter-label">@lang('contest.complete')</span>
            </a>
            <a href="" class="btn" ng-class="{'btn-default': statusFilters.indexOf({{ Entry::FINALIZE }}) == -1, 'btn-success':statusFilters.indexOf({{ Entry::FINALIZE }}) != -1}" ng-click="toggleFilterBy({{ Entry::FINALIZE }})">
                <span ng-class="{'label label-success label-as-badge':statusFilters.indexOf({{ Entry::FINALIZE }}) == -1,'badge':statusFilters.indexOf({{ Entry::FINALIZE }}) != -1}">@{{ countEntries({{{ Entry::FINALIZE }}}) }}</span>
                <span class="filter-label">@lang('contest.finalized')</span>
            </a>
            <a href="" class="btn" ng-class="{'btn-default': statusFilters.indexOf({{ Entry::APPROVE }}) == -1, 'btn-info':statusFilters.indexOf({{ Entry::APPROVE }}) != -1}" ng-click="toggleFilterBy({{ Entry::APPROVE }})">
                <span ng-class="{'label label-info label-as-badge':statusFilters.indexOf({{ Entry::APPROVE }}) == -1,'badge':statusFilters.indexOf({{ Entry::APPROVE }}) != -1}">@{{ countEntries({{{ Entry::APPROVE }}}) }}</span>
                <span class="filter-label">@lang('contest.approved')</span>
            </a>
            <a href="" class="btn" ng-class="{'btn-default': statusFilters.indexOf({{ Entry::ERROR }}) == -1, 'btn-danger':statusFilters.indexOf({{ Entry::ERROR }}) != -1}" ng-click="toggleFilterBy({{ Entry::ERROR }})">
                <span ng-class="{'label label-danger label-as-badge':statusFilters.indexOf({{ Entry::ERROR }}) == -1,'badge':statusFilters.indexOf({{ Entry::ERROR }}) != -1}">@{{ countEntries({{{ Entry::ERROR }}}) }}</span>
                <span class="filter-label">@lang('contest.errors')</span>
            </a>
            <button type="button" class="btn btn-primary" ng-click="selectEntries()"> Seleccionar | @lang('billing.total') @{{ filteredEntries.length }}</button>
        </div>
    </div>
    <br>
    <br>
    <div class="entries" ng-class="listView">
                <span ng-repeat="eRow in entriesRows">
                    <span ng-repeat="entry in eRow">
                        <div class="entry" ng-include="'entry.html'" ng-class="{'col-xs-12': listView == 'list', 'col-md-3 col-sm-4 col-xs-12':listView == 'thumbs'}"></div>
                        <div class="clearfix visible-md visible-lg" ng-show="($index + 1) % 4 == 0"></div>
                        <div class="clearfix visible-sm visible-xs" ng-show="($index + 1) % 3 == 0"></div>
                    </span>
                </span>

        <span in-view="$inview && inViewLoadMoreEntries()" in-view-options="{offset: -100}" ng-if="!lastEntryShown && filteredEntries.length > 0">
                    <div class="col-sm-12 text-center">
                        <div class="spinner">
                            <div class="rect1"></div>
                            <div class="rect2"></div>
                            <div class="rect3"></div>
                            <div class="rect4"></div>
                            <div class="rect5"></div>
                        </div>
                    </div>
                </span>
        <div class="clearfix"></div>
    </div>
    <script type="text/ng-template" id="entry.html">
        <div class="panel panel-default" ng-click="addEntriesToBulk(entry);">
            <div class="panel-heading" ng-class="{'entry-selected' : isSelected(entry) && inscription.role != {{Inscription::JUDGE}}, 'entry-not-selected' : !isSelected(entry) && inscription.role != {{Inscription::JUDGE}}}">
                <div class="row">
                    <div class="entry-title col-xs-12" ng-class="{'col-sm-7':listView == 'list','col-sm-10':listView == 'thumbs'}">
                        <a>
                            <span entry-card entry="entry" class=""></span>
                        </a>
                        <div class="cats-list text-primary" ng-class="{'text-muted':listView == 'thumbs'}">
                            <div ng-repeat="catid in entry.categories_id" ng-include="'categoryListVoting.html'" onload="category = catMan.GetCategory(catid); first=true; noVote=true;">
                            </div>
                        </div>
                        <div ng-if="inscription.role == {{Inscription::OWNER}} || {{Auth::user()->isSuperAdmin()}}" class="entry-thumbs">
                            <div class="clearfix"></div>
                            <span ng-repeat="importantField in entry.important_fields" class="text-primary">
                            <b>@{{ importantField.label }}: @{{ importantField.value }}</b>
                            <div class="clearfix"></div>
                            <span ng-repeat="field in entry.files_fields" ng-if="field.entry_metadata_field_id == importantField.entry_metadata_field_id">
                                <div ng-repeat="file in field.files" class="entry-thumb" ng-click="openGallery(entry, field.files, $index);$event.stopPropagation()">
                                    <img ng-src="@{{ file.thumb }}" alt="">
                                </div>
                                <div class="clearfix"></div>
                            </span>
                        </span>
                        </div>
                    </div>
                    <!--<div class="text-right pull-right" role="group" ng-class="{'col-sm-4':listView == 'list','entry-actions-thumbs col-sm-10':listView == 'thumbs'}">
                        <span ng-include="'entryActions.html'" ng-init="labels=true;small=true;" ng-if="{{$contest->type}} == {{Contest::TYPE_CONTEST}} || inscription.role == {{ Inscription::OWNER }} || sifter || {{Auth::user()->isSuperAdmin()}}"></span>
                        <span ng-if="{{$contest->type}} == {{Contest::TYPE_TICKET}}" class="text-center">
                        <h3>@lang('oxoTicket.totalPrice') @{{ contest.billing.mainCurrency }} @{{ entry.billings[0].price }}</h3>
                    </span>-->
                    </div>
                </div>
            </div>
        </div>
    </script>
@endsection