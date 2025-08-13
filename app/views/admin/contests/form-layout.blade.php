@include('includes.header')
@include('contest.header', array('class' => 'small', 'banner' => ContestAsset::SMALL_BANNER_HTML))
<div class="loading-alert" show-during-resolve>
    <div  class="alert alert-danger">
        <i class="fa fa-circle-o-notch fa-spin"></i> @lang('general.loading')
    </div>
</div>

<div class="container-fluid with-footer">

    <form name="contestForm" class="form-horizontal form-hover" novalidate ng-submit="save()" >
        @yield('tabs')
        <div class="col-sm-9 col-lg-10">
            @yield('form')
            <div class="clearfix"></div>
            <div ng-show="!hideSaveFooter">
                <footer class="navbar navbar-default navbar-fixed-bottom editor">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                                <button type="button" class="btn btn-default" ng-click="showThis = !showThis">
                                    <span ng-if="!showThis"><i class="fa fa-edit"></i> @lang('contest.inscriptionsToggleForm')</span>
                                    <span ng-if="showThis"><i class="fa fa-chevron-circle-up"></i> @lang('contest.inscriptionsHideForm')</span>
                                </button>

                                <button type="submit" ng-if="showThis" ng-disabled="contestForm.$invalid" class="btn btn-primary"><i class="fa fa-save"></i> @lang('general.save')</button>
                                <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !contestForm.$dirty">
                                    <span ng-bind-html="flash"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </form>

</div>