@include('includes.categoryList')
<div class="clearfix"></div>
<br>

<div class="loading-alert" show-during-resolve>
    <div  class="alert alert-danger">
        <i class="fa fa-circle-o-notch fa-spin"></i> @lang('general.loading')
    </div>
</div>

<div class="container-fluid with-footer">
    <div ng-bind-html="content"></div>
    <div class="" ng-class="listView">
                <span ng-repeat="eRow in entriesRows">
                    <span ng-repeat="entry in eRow">
                        <div ng-include="'entry.html'" class="entry col-xs-6 col-sm-5 col-md-3 col-lg-3 col-lg-offset-2 col-md-offset-2"></div>
                    </span>
                </span>

        <div class="clearfix"></div>
    </div>
    <script type="text/ng-template" id="entry.html">
        <div class="public-panel panel-default">
            <div class="public-panel-heading">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-lg-12 col-md-12">
                        <div class="">
                            <div class="public-entry-thumbs" >
                                <div class="clearfix"></div>
                                <span ng-repeat="importantField in entry.important_fields" class="text-primary">
                                <div class="clearfix"></div>
                                <span ng-repeat="field in entry.files_fields_entries" ng-if="field.entry_metadata_field_id == importantField.entry_metadata_field_id">
                                    <div ng-repeat="file in field.files" class="public-entry-thumb" ng-click="openGallery(entry, field.files, $index);$event.stopPropagation()">
                                        <img ng-src="@{{ file.thumb }}" alt="">
                                    </div>
                                    <div class="clearfix"></div>
                                </span>
                            </span>
                            </div>
                            <div class="cats-list text-primary" ng-class="{'text-muted':listView == 'thumbs'}">
                                <div ng-repeat="catid in entry.categories_id" ng-include="'categoryListVotingPublic.html'" onload="changeShortlist = inscription.role == {{Inscription::JUDGE}} && voteSession.config.shortListConfig && voteSession.config.editShortlist;category = catMan.GetCategory(catid); first=true; winners=winners;winnersSession=entry.voteSession; voteSession.config.showVotingTool">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <br>
    </script>
    <a href="<?=URL::to('/');?>/#/home" target="_blank"><img src="https://www.oxoawards.com/AyV/asset/2099" class="col-xs-6 col-sm-5 col-md-3 col-lg-3 col-lg-offset-2 col-md-offset-2" style="width:150px;"></a>
</div>