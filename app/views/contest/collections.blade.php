<? /** @var Contest $contest  */ ?>
@include('includes.header')
@include('includes.categoryList')
@include('contest.header', array('class' => 'small', 'banner' => ContestAsset::SMALL_BANNER_HTML))
<div class="loading-alert" show-during-resolve>
    <div  class="alert alert-danger">
        <i class="fa fa-circle-o-notch fa-spin"></i> @lang('general.loading')
    </div>
</div>

<script type="text/ng-template" id="category.html">
    <div class="list-group-item" ng-click="toggleCat(category)" ng-class="{'active': category.open == 1}">
        <h6>
            <trans ng-model="category" trans-prop="'name'"></trans>
            <div class="clearfix"></div>
            <small class="cat-description" ng-if="clearView"><trans ng-model="category" trans-prop="'description'"></trans></small>
        </h6>
    </div>
    <div class="clearfix"></div>
    <div ng-show="category.open" ng-class="{'active': category.open == 1}">
        <!--ng-hide="!category.entriesCount"-->
        <ul ng-model="category.children_categories" class="category-list no-list-style">
            <li ng-repeat="category in category.children_categories track by $index" ng-include="'category.html'">
            </li>
        </ul>
    </div>
</script>


<div class="container-fluid with-footer">
    <div ng-bind-html="content"></div>
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="row">
        <!--<div class="col-lg-2 col-md-3 col-sm-2  hidden-xs fixed-filters"  ng-hide="showMenuBar == false">-->
        <div class="col-lg-2 col-md-3 col-sm-2  hidden-xs"  ng-hide="showMenuBar == false">
            <div class="list-subgroup">
            <h5 class="list-group-item title bg-gray" ng-click="showMenuBar = !showMenuBar">
                <i class="fa fa-arrow-left"></i>
                <span class="text-center">@lang('collection.filters')</span>
            </h5>
            </div>
            <div ng-click="showSearch = !showSearch" role="button" ng-model="search" class="list-group-item title">
                <i class="fa fa-search"></i><b> @lang('collection.search') </b>
            </div>
            <div class="input-group" ng-if="showSearch">
                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                <input ng-model-options="{debounce: 500}" type="text" ng-model="pagination.query" class="form-control" placeholder="@lang('general.search')" focus-me="true">
            </div>
            <div ng-hide="categories.length == 0" ng-model="categories" class="list-group-item title no-list-style" role="button" ng-click="showCategories = !showCategories">
                  <i class="fa fa-list"></i><b> @lang('collection.categories') </b>
            </div>
            <div class="pre-scrollable">
            <div ng-repeat="category in categories track by $index" role="button" ng-hide="showCategories == false" ng-include="'category.html'">
            </div>
            </div>

            <div ng-click="showPrizes = !showPrizes" ng-hide="prizes.length  == 0" role="button" ng-model="pagination.prizes" class="list-group-item title">
                <i class="fa fa-trophy"></i><b> @lang('collection.prizes') </b>
            </div>
            <div ng-if="showPrizes">
                <div role="button" ng-class="{'active': pagination.prizes.indexOf(prize.id) != -1}" ng-click="selectPrize(prize)" class="list-group-item" ng-repeat="prize in prizes"> @{{prize.name}} </div>
            </div>

        </div>

        <div class="col-lg-2 col-md-2 col-sm-12  hidden-xs fixed-filters" ng-click="showMenuBar = !showMenuBar" ng-hide="showMenuBar == true">
            <div class="col-lg-2 col-md-3 bg-gray">
                <h4><i class="fa fa-arrow-right"></i></h4>
                <span class="collection-filters">
                    <b>@lang('collection.filters')</b>
                </span>
            </div>
        </div>

        <div class="visible-xs col-xs-12">
            <div ng-click="showSearch = !showSearch" role="button" ng-model="search" class="list-group-item title">
                <i class="fa fa-search"></i><b> @lang('collection.search') </b>
            </div>
            <div class="input-group" ng-if="showSearch">
                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                <input ng-model-options="{debounce: 500}" type="text" ng-model="pagination.query" class="form-control" placeholder="@lang('general.search')" focus-me="true">
            </div>
            <div ng-hide="categories.length == 0" ng-model="categories" class="list-group-item title no-list-style" role="button" ng-click="showCategories = !showCategories">
                <i class="fa fa-list"></i><b> @lang('collection.categories') </b>
            </div>
            <div class="pre-scrollable">
                <div ng-repeat="category in categories track by $index" role="button" ng-hide="showCategories == false" ng-include="'category.html'">
                </div>
            </div>

            <div ng-click="showPrizes = !showPrizes" ng-hide="prizes.length  == 0" role="button" ng-model="pagination.prizes" class="list-group-item title">
                <i class="fa fa-trophy"></i><b> @lang('collection.prizes') </b>
            </div>
            <div ng-if="showPrizes">
                <div role="button" ng-class="{'active': pagination.prizes.indexOf(prize.id) != -1}" ng-click="selectPrize(prize)" class="list-group-item" ng-repeat="prize in prizes"> @{{prize.name}} </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-3 col-sm-2 hidden-xs"></div>

        <div class="col-lg-9 col-md-9 col-sm-10 col-xs-12">
            <div class="entries col-lg-12 col-md-12 col-sm-12">
                <h5 ng-if="selectedCategory != null"> <b>Categoria Seleccionada: @{{ selectedCategory }}</b></h5>
                <h5 ng-if="selectedPrizes.length > 0">
                    <b>
                        @lang('collection.selectedPrices')
                        <span ng-repeat="selectedPrize in selectedPrizes" class="label label-as-badge"
                              style="background-color:@{{ selectedPrize.color }};color:white;font-weight: bold; margin-right: 10px;">
                            @{{ selectedPrize.name }} <i role="button" class="fa fa-close" ng-click="selectPrize(selectedPrize)"></i>
                        </span>
                    </b>
                </h5>
                <div class="text-center cat-loading" ng-show="loading == true"><i class="fa fa-circle-o-notch fa-spin fa-2x"></i></div>
                <div ng-if="entries.length === 0 && category.final === 1"
                class="label label-warning col-lg-offset-4 col-lg-4">
                    <h4>@lang('collection.NoEntries')</h4>
                </div>
                <div ng-if="loading == false" ng-repeat="entry in entries" class="collection-grid panel panel-default col-lg-2 col-md-3 col-sm-6" href="" ng-click="$event.preventDefault(); openEntryInList(entry)">
                    <!--<span ng-repeat="field in entry.files_fields">
                        <div ng-repeat="(key, file) in field.files" ng-if="key == 0" ng-click="$event.preventDefault(); openEntryInList(entry)">-->
                        <div ng-click="$event.preventDefault(); openEntryInList(entry)">
                            <!--<img class="collection-img" ng-src="@{{ file.thumb }}" alt="">-->
                            <img class="collection-img" ng-src="@{{ entry.files_fields[0].files[0].thumb }}" alt="">
                        </div>
                        <div class="clearfix"></div>
                    <!--</span>-->
                    <div style="padding:10px;" role="button">
                        <div>
                        <h5> <b>@{{ entry.name }} </b></h5>
                        </div>
                        <div>
                            <span ng-repeat="category in entry.categoryName" ng-if="config.voteType.indexOf(entry.votes[category['id']].vote[0].id) !== -1">
                                <span role="button" uib-tooltip="@{{ category['name'] }}" class="label label-as-badge" style="background-color:@{{ entry.votes[category['id']].vote[0].color }};color:white;font-weight: bold; margin-right: 10px;">
                                    @{{entry.votes[category['id']].vote[0].name[0] }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    @include('includes.footer')
</div>