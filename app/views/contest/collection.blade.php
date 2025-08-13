@include('includes.header')
@include('contest.header', array('class' => 'small', 'banner' => ContestAsset::SMALL_BANNER_HTML))

<div class="loading-alert" show-during-resolve>
    <div  class="alert alert-danger">
        <i class="fa fa-circle-o-notch fa-spin"></i> @lang('general.loading')
    </div>
</div>
<div class="container-fluid with-footer entry" hotkey="{d: hideDescriptions, m: showLog}">
    <div class="row">
        <div class="col-sm-3 col-lg-2 col-sm-3"></div>
        <div class="col-sm-9 col-lg-8 col-sm-9">
            <div class="row">
                <div class="col-sm-12">
                    <h2 class="entry-form-title">
                        <span ng-if="entry.id">
                            <span> @{{ entry.name }}</span>
                        </span>
                    </h2>
                        <script type="text/ng-template" id="entry-metadata-field.html">
                            @include('metadata.field', array('model'=>'field.model.value', 'filemodel'=>'field.model.files', 'mainField'=>'field.model', 'allValues'=>'field.allmodels', 'disabled'=>false))
                        </script>
                        <div>
                            <div ng-if="firstTabIndex != -1">
                                <uib-tabset>
                                    <div class="form-group" ng-repeat="field in getPreTabMetadata()">
                                        <div ng-include="'entry-metadata-field.html'"></div>
                                    </div>
                                    <uib-tab ng-repeat="tab in getTabs() track by $index" active="activeTab" index="$index">
                                        <uib-tab-heading><trans ng-model='tab' trans-prop="'label'"></trans></uib-tab-heading>
                                        <div ng-if="activeTab">
                                            <div class="help-block" ng-if="tab.description != null"><trans ng-model='tab' trans-prop="'description'"></trans></div>
                                            <div class="form-group" ng-repeat="field in getTabMetadata(tab)">
                                                <div ng-include="'entry-metadata-field.html'"></div>
                                            </div>
                                        </div>
                                    </uib-tab>
                                </uib-tabset>
                            </div>
                            <div ng-if="firstTabIndex == -1">
                                <div class="form-group" ng-repeat="field in entry_metadata_fields" ng-if="!isFieldHidden(field) && !isTab(field)">
                                    <div ng-include="'entry-metadata-field.html'"></div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>