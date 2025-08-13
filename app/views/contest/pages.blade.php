<? /** @var Contest $contest  */ ?>
@include('includes.header')
@include('includes.categoryList')
@include('contest.header', array('class' => 'small', 'banner' => ContestAsset::SMALL_BANNER_HTML))
<div class="loading-alert" show-during-resolve>
    <div  class="alert alert-danger">
        <i class="fa fa-circle-o-notch fa-spin"></i> @lang('general.loading')
    </div>
</div>

<div class="container-fluid with-footer">
    <div ng-bind-html="content"></div>
    <div class="entries" ng-class="listView">
                <span ng-repeat="eRow in entriesRows">
                    <span ng-repeat="entry in eRow">
                        <div class="entry" ng-include="'entry.html'" ng-class="{'col-xs-12': listView == 'list', 'col-md-3 col-sm-4 col-xs-12':listView == 'thumbs'}"></div>
                        <div class="clearfix visible-md visible-lg" ng-show="($index + 1) % 4 == 0"></div>
                        <div class="clearfix visible-sm visible-xs" ng-show="($index + 1) % 3 == 0"></div>
                    </span>
                </span>

        <span in-view="$inview && inViewLoadMoreEntries()" in-view-options="{offset: -100}" ng-if="!lastEntryShown && entriesRows.length > 0">
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
        <div class="public-panel panel-default">
            <div class="public-panel-heading">
                <div class="row">
                    <div class="public-entry-title col-xs-12" ng-class="{'col-sm-7':listView == 'list','col-sm-12':listView == 'thumbs'}">
                         <span entry-card entry="entry" class=""></span>
                        <div class="cats-list text-primary" ng-class="{'text-muted':listView == 'thumbs'}">
                            <div ng-repeat="catid in entry.categories_id" ng-include="'categoryListVoting.html'" onload="category = catMan.GetCategory(catid); first=true; noVote=true;">
                            </div>
                        </div>
                        <div class="public-entry-thumbs">
                            <div class="clearfix"></div>
                            <span ng-repeat="importantField in entry.important_fields" class="text-primary">
                            <b>@{{ importantField.label }}: @{{ importantField.value }}</b>
                            <div class="clearfix"></div>
                            <span ng-repeat="field in entry.files_fields" ng-if="field.entry_metadata_field_id == importantField.entry_metadata_field_id">
                                <div ng-repeat="file in field.files" class="public-entry-thumb" ng-click="openGallery(entry, field.files, $index);$event.stopPropagation()">
                                    <img ng-src="@{{ file.thumb }}" alt="">
                                </div>
                                <div class="clearfix"></div>
                            </span>
                        </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </script>
    @include('includes.footer')

</div>