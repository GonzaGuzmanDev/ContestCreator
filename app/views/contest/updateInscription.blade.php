@include('includes.header')
@include('contest.header', array('class' => 'small', 'banner' => ContestAsset::SMALL_BANNER_HTML))
<div class="container with-footer">
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <h2>
                @lang('contest.signuptitle')
                @{{ inscriptionType ? ' - '+inscriptionType.name : '' }}
                <a href="" ng-if="inscriptionType && inscriptionTypesRole.length > 1 && !sent" ng-click="setInscriptionType()" uib-tooltip="@lang('contest.changeInscriptionType')"><i class="fa fa-edit"></i></a>
            </h2>

            <div ng-show="chooseType" class="col-md-6 col-md-offset-3 btn-group-vertical text-center">
                <h4>@lang('contest.selectinsctype')</h4>
                <button type="button" ng-repeat="type in contest.inscription_types | filter:{role:role}" ng-click="setInscriptionType(type)" class="btn btn-default btn-block btn-lg"><trans ng-model="type" trans-prop="'name'"></trans></button>
            </div>
            <div class="clearfix"></div>

            <form name="signupForm" class="form-horizontal" ng-submit="send(true)">
                <div ng-show="!chooseType">
                <h5 class="text-muted"><span class="text-danger">*</span> @lang('contest.requiredfields')</h5>

                <div class="form-group">
                    <label class="control-label col-sm-3"><?=Lang::get('register.firstName')?></label>
                    <div class="col-sm-9 form-control-static">
                        @{{ currentUser.first_name }}
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3"><?=Lang::get('register.lastName')?></label>
                    <div class="col-sm-9 form-control-static">
                        @{{ currentUser.last_name }}
                    </div>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="control-label col-sm-3"><?=Lang::get('register.email')?></label>
                    <div class="col-sm-9 form-control-static">
                        @{{ currentUser.email }}
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-9 col-sm-push-3 form-control-static">
                        <a href="#/account/data" class="btn btn-primary btn-sm">@lang('register.editAccountData')</a>
                    </div>
                </div>
                <hr>
                <script type="text/ng-template" id="update-signup-metadata-field.html">
                    @include('metadata.field', array('model'=>'formData[field.id]', 'allValues'=>'void', 'disabled'=>false, 'forceRequired' => true))
                </script>
                <div ng-if="firstTabIndex != -1">
                    <uib-tabset>
                        <div class="form-group" ng-repeat="field in getPreTabMetadata()">
                            <div ng-include="'update-signup-metadata-field.html'"></div>
                        </div>
                        <uib-tab ng-repeat="tab in getTabs() track by $index" index="$index">
                            <uib-tab-heading><trans ng-model='tab' trans-prop="'label'"></trans></uib-tab-heading>
                            <div class="help-block" ng-if="tab.description != null"><trans ng-model='tab' trans-prop="'description'"></trans></div>
                            <div class="form-group" ng-repeat="field in getTabMetadata(tab)">
                                <div ng-include="'update-signup-metadata-field.html'"></div>
                            </div>
                        </uib-tab>
                    </uib-tabset>
                </div>
                <div ng-if="firstTabIndex == -1">
                    <div class="form-group" ng-repeat="field in metadata track by $index | filter:{role:role}" ng-if="!isTab(field)">
                        <div ng-include="'update-signup-metadata-field.html'"></div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-3 text-right">
                        <a href="#home" class="btn btn-info">@lang('general.back')</a>
                    </div>
                    <div class="col-sm-9">
                        <button type="submit" class="btn btn-success">@lang('contest.updateInscription')</button>
                        <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash">
                            <span ng-bind-html="flash"></span>
                        </div>
                        <br>
                        <br>
                        <button type="button" ng-click="delete()" class="btn btn-danger"><i class="fa fa-trash"></i> @lang('contest.DeleteSignUp')</button>
                    </div>
                </div>
                </div>
            </form>
        </div>
    </div>
    @include('includes.footer')
</div>