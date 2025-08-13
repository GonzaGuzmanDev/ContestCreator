@include('admin.header')
<div class="main-block contents">
    <h3>
        <a href="#/home"><i class="fa fa-wrench"></i> <?=Lang::get('user.admin')?></a>
        /
        <i class="fa fa-users"></i> <?=Lang::get('user.users')?>
    </h3>
    <div class="row">
        @include('admin.menu', array('section' => 'users'))
        <div class="col-sm-9 col-lg-10">
            <form class="form-inline" role="form">
                <a href="#/users/new" class="btn btn-success"><i class="fa fa-plus"></i> <?=Lang::get('user.newUser')?></a>
                <div class="form-group">
                    <input type="text" ng-model="query" class="form-control inline" placeholder="<?=Lang::get('user.search')?>">
                </div>
            </form>
            <uib-pagination boundary-links="true" total-items="pagination.total" items-per-page="pagination.perPage" max-size="7" rotate="false" ng-model="pagination.page" class="pagination-sm" ng-show="users.length > 0" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></uib-pagination>
            <span class="pagination-data" ng-show="dataLoaded">@{{((pagination.page-1) * pagination.perPage)+1}} <?=Lang::get('user.to')?> @{{Math.min(pagination.page * pagination.perPage, pagination.total)}} <?=Lang::get('user.of')?> @{{pagination.total}} <?=Lang::get('user.results')?></span>
            <div class="clearfix"></div>
            <table class="table table-striped table-hover table-condensed">
                <thead>
                <tr>
                    <th></th>
                    <th><a data-ng-click="changeOrder('email')"><?=Lang::get('user.email')?> <i ng-show="pagination.orderBy == 'email'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                    <th><a data-ng-click="changeOrder('first_name')"><?=Lang::get('user.firstName')?> <i ng-show="pagination.orderBy == 'first_name'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                    <th><a data-ng-click="changeOrder('last_name')"><?=Lang::get('user.lastName')?> <i ng-show="pagination.orderBy == 'last_name'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                    <th><a data-ng-click="changeOrder('created_at')"><?=Lang::get('user.createAt')?> <i ng-show="pagination.orderBy == 'created_at'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="user in users" ng-class="{'info': user.super == 1}">
                    <td>
                        <img ng-src="@{{ user.picture  }}" alt="" style="width: 19px;height: 19px;"/>
                        <i class="fa fa-shield" ng-show="user.super == 1"></i>
                    </td>
                    <td><a href="#/users/edit/@{{user.id}}">@{{user.email}}</a></td>
                    <td>@{{user.first_name}}</td>
                    <td>@{{user.last_name}}</td>
                    <td>@{{user.created_at}}</td>
                    <td class="text-right">
                        <a class="btn btn-success btn-xs" href="<?=url('/admin/loginAs')?>/@{{ user.id }}"><i class="fa fa-user"></i> <?=Lang::get('user.loginAs')?></a>
                        <a href="#/users/edit/@{{user.id}}" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> <?=Lang::get('user.edit')?></a>
                        <button class="btn btn-danger btn-xs" ng-click="delete(user)"><i class="fa fa-trash"></i> <?=Lang::get('user.delete')?></button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>