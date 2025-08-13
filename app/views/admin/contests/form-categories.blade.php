@extends('admin.contests.form', array('active' => 'categories'))
@section('form')
    <script type="text/ng-template" id="editCategoryConfig.html">
        @include('admin.contests.config-category')
    </script>
    <span ng-if="{{ $contest->wizard_status >= Contest::WIZARD_CATEGORIES && $contest->wizard_status != Contest::WIZARD_FINISHED}}">
    @include('includes.wizardProgress', array('active' => Contest::WIZARD_CATEGORIES))
        <br><div class="clearfix"></div><br>
    <h3 class="text-center"> @lang('contest.wizard.categoriesFormTitle') </h3>
    <br>
    </span>
    <h4 class="well well-sm">
        @lang('contest.categories')
        {{--@if(!$superadmin)
            <i class="fa fa-question-circle text-info" popover="@lang('contest.explain.categories')" popover-placement="right" popover-trigger="mouseenter" class="btn btn-default"></i>
        @endif--}}
        <div class="btn-group pull-right">
            <a href="" class="btn btn-sm btn-default btn-small" ng-repeat="(key,lang) in langs.All" ng-class="{'active':selectedLang == key}" ng-click="setLang(key)"><i class="flag-icon flag-icon-@{{key}}"></i> @{{lang}}</a>
        </div>
        <div class="clearfix"></div>
    </h4>
    <!--<div class="btn-group">
        <div class="form-inline" ng-if="{{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}}">
            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                @lang('contest.importCategories')
            </button>
            <ul class="dropdown-menu scrollable-menu">
                <li style="padding: 0 5px 5px;"><input type="text" ng-model="filterContest" ng-click="$event.stopPropagation();" class="form-control searchBox" style="width:100%;" placeholder="@lang('general.search')"/></li>
                <li ng-repeat="contest in contestsIds | filter: filterContest"><a type="button" ng-click="importCategories(contest.code)"> @{{ contest.name }} </a></li>
            </ul>
        </div>
    </div>-->

    <script type="text/ng-template" id="category.html" onload="list = category.children_categories">
        <div class="row tree-row">
            <div class="cat-input" ng-class="{'col-md-4':EntryMetadataTemplates.length, 'col-md-5':!EntryMetadataTemplates.length}">
                <div class="tree-row-line"></div>
                <div ng-if="!showThis" style="margin-left: 10px;">
                    <span class="form-control-static form-inline" ng-if="selectedLang == langs.Default">@{{ category.name }}</span>
                    <span class="form-control-static form-inline input-trans" ng-if="selectedLang == key" ng-repeat="(key,lang) in langs.Editables">@{{!category.trans[key].name ? category.name : category.trans[key].name}}</span>
                </div>
                <div class="input-group" ng-if="showThis" ng-init="sublist = category.children_categories; subparent = category; subparentSublist = list;">
                    <input type="text" class="form-control form-inline" ng-model="category.name" required ng-if="selectedLang == langs.Default">
                    <input type="text" class="form-control form-inline input-trans" ng-model="category.trans[key].name" placeholder="@{{!category.trans[key].name ? category.name : ''}}" ng-if="selectedLang == key" ng-repeat="(key,lang) in langs.Editables">
                    <span class="input-group-btn">
                        <span class="btn btn-default handle" uib-tooltip="@lang('contest.categories.sort')"><i class="fa fa-arrows-v"></i></span>
                        <button type="button" ng-click="addCategory(category.children_categories, category)" uib-tooltip="@lang('contest.addCategoryTo')" class="btn btn-info"><i class="fa fa-plus"></i></button>
                        <button type="button" ng-click="configCategory(category)" class="btn btn-warning" uib-tooltip="@lang('contest.categories.config')"><i class="fa fa-cog"></i></button>
                        <button type="button" ng-click="removeCategory(category.parent ? category.parent.children_categories : categories, $index)" class="btn btn-danger" uib-tooltip="@lang('contest.categories.delete')"><i class="fa fa-remove"></i></button>
                    </span>
                </div>
                <div class="col-md-12 text-danger">
                    @{{ errors[category.errMsg] }}
                </div>
            </div>
            <div class="col-md-7 pull-right">
                <div class="row">
                    <div class="col-md-2" ng-if="EntryMetadataTemplates.length">
                        <div class="btn-group" uib-dropdown ng-if="showThis">
                            <button id="single-button" type="button" class="btn btn-primary" uib-dropdown-toggle ng-class="{'btn-info' : !!category.template_id}">
                                <span ng-bind-html="category.template_id ? GetTemplateName(category.template_id) : '@lang('metadata.noselectedtemplate')'"></span> <span class="caret"></span>
                            </button>
                            <ul uib-dropdown-menu aria-labelledby="single-button">
                                <li ng-class="{'active' : !category.template_id}"><a href="" ng-click="setCategoryMetadata(category, null)">@lang('metadata.noselectedtemplate')</a></li>
                                <li ng-repeat="(ind,template) in EntryMetadataTemplates" ng-class="{'active' : category.template_id == template.id}"><a href="" ng-click="setCategoryMetadata(category, template.id)">@{{ template.name }}</a></li>
                            </ul>
                        </div>
                        <div ng-if="!showThis">
                            <span ng-bind-html="category.template_id ? GetTemplateName(category.template_id) : '@lang('metadata.noselectedtemplate')'"></span>
                        </div>
                    </div>
                    <div class="col-md-2" ng-if="contest.billing">
                        <div ng-if="!showThis">
                            @{{(category.price != null && category.price != '') || category.price == 0 ? category.price : GetCategoryPrice(category)}}
                            @{{ contest.billing.mainCurrency }}
                        </div>
                        <div class="input-group" ng-if="showThis">
                            <input type="text" ng-model="category.price" class="form-control text-right input-price" placeholder="@{{category.price != null && category.price != '' ? category.price : GetCategoryPrice(category)}}">
                            <div class="input-group-addon price-currency">@{{ contest.billing.mainCurrency }}</div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <span ng-if="!showThis">
                            <span class="form-control-static form-inline" ng-if="selectedLang == langs.Default">@{{ category.description }}</span>
                            <span class="form-control-static form-inline input-trans" ng-if="selectedLang == key" ng-repeat="(key,lang) in langs.Editables">@{{!category.trans[key].description ? category.description : category.trans[key].description}}</span>
                        </span>
                        <span ng-if="showThis">
                        <textarea type="text" class="form-control" ng-model="category.description" ng-if="selectedLang == langs.Default"></textarea>
                        <textarea type="text" class="form-control input-trans" ng-model="category.trans[key].description"
                          placeholder="@{{!category.trans[key].description ? category.description : ''}}" ng-if="selectedLang == key" ng-repeat="(key,lang) in langs.Editables"></textarea>
                        </span>
                    </div>
                    <div class="btn-group col-md-3" role="group" ng-if="showThis">
                        <button type="button" class="btn btn-default" ng-disabled="category.parent == null" ng-click="moveCatLeft(category,$index)"><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-default" ng-disabled="$index == 0" ng-click="moveCatUp(category,$index)"><i class="fa fa-arrow-up"></i></button>
                        <button type="button" class="btn btn-default" ng-disabled="$index == category.parent.children_categories.length - 1" ng-click="moveCatDown(category,$index)"><i class="fa fa-arrow-down"></i></button>
                        <button type="button" class="btn btn-default" ng-disabled="$index == 0" ng-click="moveCatRight(category,$index)"><i class="fa fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="tree-root-line"></div>
        <div class="tree-last-root-line"></div>
        <ul ui-sortable="{handle: '.handle', cursor: 'move', connectWith: '.category-list', placeholder: 'ui-state-highlight'}" ng-model="category.children_categories" class="category-list"><li ng-repeat="category in category.children_categories track by $index" ng-include="'category.html'" onload="list = sublist; parent = subparent; parentSublist = subparentSublist; depth = depth + 1;"></li></ul>
    </script>
    <div class="row form-group no-hover categories-tree" ng-show="categories.length">
        <div ng-class="{'col-md-4':EntryMetadataTemplates.length, 'col-md-5':!EntryMetadataTemplates.length}">
            @lang('contest.categories.name')
        </div>
        <div class="col-md-7 pull-right">
            <div class="row">
                <div class="col-md-2" ng-if="EntryMetadataTemplates.length">
                    @lang('contest.categories.metadata')
                </div>
                <div class="col-md-2" ng-if="contest.billing">
                    @lang('contest.categories.price')
                </div>
                <div class="col-md-5">
                    @lang('contest.categories.description')
                </div>
                <div class="col-md-3"  ng-if="showThis">
                    @lang('contest.categories.tools')
                </div>
            </div>
        </div>
        <div class="col-sm-12 category-list-container">
            <ul ui-sortable="{handle: '.handle', cursor: 'move', connectWith: '.category-list', placeholder: 'ui-state-highlight', stop:onSortStop}" ng-model="categories" class="category-list">
                <li ng-repeat="category in categories track by $index" ng-include="'category.html'" onload="list = categories; parent = null; parentSublist = null; depth = 0;"></li>
            </ul>
        </div>
    </div>
    <div class="row form-group" ng-if="showThis">
        <div class="col-md-6">
            <a href="" ng-click="addCategory(categories)" class="btn btn-info"><i class="fa fa-plus"></i> @lang('contest.addRootCategory')</a>
        </div>
    </div>
@endsection