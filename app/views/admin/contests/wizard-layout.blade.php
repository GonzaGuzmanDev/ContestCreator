@include('includes.header')
@include('contest.header', array('class' => 'small', 'banner' => ContestAsset::SMALL_BANNER_HTML))
<div class="loading-alert" show-during-resolve>
    <div  class="alert alert-danger">
        <i class="fa fa-circle-o-notch fa-spin"></i> @lang('general.loading')
    </div>
</div>

<div class="container-fluid with-footer">
    <form name="contestForm" class="form-horizontal form-hover" novalidate ng-submit="save()">
        @yield('tabs')
        <div class="col-sm-offset-1 col-sm-10 col-lg-offset-1 col-lg-10">
            @yield('form')
            <div class="clearfix"></div>
            <div ng-show="!hideSaveFooter">
                <footer class="navbar navbar-default navbar-fixed-bottom editor">
                    <div class="container-fluid">
                        <div class="row" ng-if="'{{ $contest->wizard_status }}'">
                            <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                                <button type="button" ng-if="{{ $contest->wizard_status > Contest::WIZARD_REGISTER_FORM}}" ng-click="previous()" class="btn btn-primary"> <i class="fa fa-arrow-circle-left"></i> @lang('contest.previous') </button>
                                <!--<button type="submit" ng-disabled="contestForm.$invalid" class="btn btn-primary">@lang('contest.next') <i class="fa fa-arrow-circle-right"></i></button>-->
                                <button type="submit" class="btn btn-primary">@lang('contest.next') <i class="fa fa-arrow-circle-right"></i></button>
                                <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash">
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