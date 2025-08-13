@include('admin.header')
<div class="main-block with-footer contents">
    <h3>
        <a href="#/home"><i class="fa fa-wrench"></i> @lang('general.admin')</a>
        /
        <a href="#/formats"><i class="fa fa-sitemap"></i> @lang('general.formats')</a>
        /
        <span ng-show="format.id"><i class="fa fa-edit"></i> @lang('format.editFormat') @{{format.id}}: @{{format.name}}</span>
        <span ng-show="!format.id"><i class="fa fa-plus"></i> @lang('format.createFormat')</span>
    </h3>
    <div class="row">
        @include('admin.menu', array('section' => $section))
        <div class="col-sm-9 col-lg-10">
            <form name="formatForm" class="form-horizontal form-hover" novalidate ng-submit="save()" >
                @yield('tabs')
                @yield('form')
                <hr class=""/>
                <div class="form-group no-hover">
                    <div class="col-sm-6">
                        <div class="alert alert-tight alert-@{{flashStatus}}" ng-show="flash && !saving">
                            <span ng-bind="flash"></span>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="form-group no-hover">
                    <div class="col-sm-10">
                        <a href="#/formats/" class="btn btn-default"><i class="fa fa-ban"></i> @lang('general.cancel')</a>
                        <button type="submit" ng-disabled="formatForm.$invalid" class="btn btn-primary"><i class="fa fa-save"></i> @lang('general.save')</button>
                        <i class="fa fa-spin fa-circle-o-notch text-warning" ng-if="saving"></i>
                        <button type="button" ng-click="delete()" ng-show="enableDelete" class="btn btn-danger"><i class="fa fa-trash"></i> @lang('general.delete')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>