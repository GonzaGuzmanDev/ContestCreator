    @include('admin.header')
<div class="main-block with-footer contents">
    <h3>
        <a href="#/home"><i class="fa fa-wrench"></i> @lang('general.admin')</a>
        /
        <a href="#/users"><i class="fa fa-trophy"></i> @lang('general.users')</a>
        /
        <span ng-show="user.id">
            <i class="fa fa-edit"></i>
            @lang('user.editUser') @{{user.first_name}} @{{user.last_name}} (@{{user.email}})
        </span>
        <span ng-show="!user.id"><i class="fa fa-plus"></i> @lang('user.createUser')</span>
    </h3>
    <div class="row">
        @include('admin.menu', array('section' => $section))
        <div class="col-sm-9 col-lg-10">
            <form name="userForm" class="form-horizontal form-hover" novalidate ng-submit="save()" >
                @yield('tabs')
                @yield('form')
                <div ng-show="!hideSaveFooter">
                    <hr class=""/>
                    <div class="form-group no-hover">
                        <div class="col-sm-6">
                            <div class="alert alert-tight alert-@{{flashStatus}}" ng-show="flash && !userForm.$dirty">
                                <span ng-bind="flash"></span>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group no-hover">
                        <div class="col-sm-10">
                            <a href="#/users/" class="btn btn-default"><i class="fa fa-ban"></i> @lang('general.cancel')</a>
                            <button type="submit" ng-disabled="userForm.$invalid" class="btn btn-primary"><i class="fa fa-save"></i> @lang('general.save')</button>
                            <button type="button" ng-click="delete()" ng-show="enableDelete" class="btn btn-danger"><i class="fa fa-trash"></i> @lang('general.delete')</button>
                        </div>
                    </div>
                    <input type="hidden" ng-model="contest.user_id">
                </div>
            </form>
        </div>
    </div>
</div>