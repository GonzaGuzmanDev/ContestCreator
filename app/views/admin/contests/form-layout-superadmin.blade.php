@include('admin.header')
<div class="main-block with-footer contents">
    <h3>
        <a href="#/home"><i class="fa fa-wrench"></i> @lang('general.admin')</a>
        /
        <a href="#/contests"><i class="fa fa-trophy"></i> @lang('general.contests')</a>
        /
        <span ng-show="contest.id"><i class="fa fa-edit"></i> @lang('contest.editContest') @{{contest.name}} [@{{contest.id}}]
            <a href="{{url('/')}}/@{{contest.code}}" class="" target="_blank"><i class="fa fa-link"></i></a>
        </span>
        <span ng-show="!contest.id"><i class="fa fa-plus"></i> @lang('contest.createContest')</span>
    </h3>
    <div class="row">
        @include('admin.menu', array('section' => $section))
        <div class="col-sm-9 col-lg-10">
            <form name="contestForm" class="form-horizontal form-hover" novalidate ng-submit="save()" >
                @yield('tabs')
                @yield('form')
                <div ng-show="!hideSaveFooter">
                    <hr class=""/>
                    <footer class="navbar navbar-default navbar-fixed-bottom editor">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                                    <a href="#/contests/edit/@{{contest.code}}/@{{ activeMenu }}" class="btn btn-default"><i class="fa fa-ban"></i> @lang('general.cancel')</a>
                                    <button type="submit" ng-disabled="contestForm.$invalid" class="btn btn-primary"><i class="fa fa-save"></i> @lang('general.save')</button>
                                    <button type="button" ng-click="delete()" ng-show="enableDelete" class="btn btn-danger"><i class="fa fa-trash"></i> @lang('general.delete')</button>
                                    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !contestForm.$dirty">
                                        <span ng-bind-html="flash"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </footer>
                    <div class="form-group no-hover">
                        <div class="col-sm-10">
                            <ul>
                                <li ng-repeat="(key, errors) in contestForm.$error track by $index"> <strong>@{{ key }}</strong> errors
                                    <ul>
                                        <li ng-repeat="e in errors">@{{ e.$name }} has an error: <strong>@{{ key }}</strong>.</li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <input type="hidden" ng-model="contest.user_id">
                </div>
            </form>
        </div>
    </div>
</div>