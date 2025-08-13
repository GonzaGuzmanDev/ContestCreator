<div class="navbar-header">
    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only"><?=Lang::get('header.toggle')?></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="<?=URL::to('/');?>/#/home"><span></span></a>
</div>

<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    <div ng-show="currentUser">
        <ul class="nav navbar-nav navbar-right">
            @if (Auth::check())
                @if( Session::has('orig_user') )
                    <li><a class="profile-menu" href="<?=url('/admin/backToAdmin')?>"> Volver a Admin </a></li>
                @endif
                @if(Auth::user()->isSuperAdmin())
                    <li>
                        <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            @lang('user.users')
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu pre-scrollable">
                            <li style="padding: 0 5px 5px;"><input type="text" ng-model="filterEntriesPerUser" class="form-control searchBox" style="width:100%;" placeholder="@lang('general.search')" ng-click="$event.stopPropagation();"/></li>
                            <li ng-repeat="user in contest.users | filter: filterEntriesPerUser"><a href="<?=url('/loginAs')?>/@{{ contest.code }}/@{{ user.user_id }}"> <span> @{{ user.first_name }} @{{ user.last_name }}<b>(@{{ user.email }})</b> </span></a></li>
                        </ul>
                    </li>
                @endif
                @yield('top.right', Auth::user()->isSuperAdmin() ? '<li><a href="'.url('admin').'#/home"><i class="fa fa-cogs"></i> '.Lang::get('header.admin').'</a></li>' : '')
            @endif
                <li class="dropdown profile-menu" ng-controller="userDropdown">
                    <a href class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <i class="fa fa-ticket"></i> @lang('account.myinscriptions')
                        <span class="badge">@{{ inscriptionsCount }}</span>
                    </a>
                    <ul class="dropdown-menu multi-level" role="menu">
                        <li class="disabled" ng-hide="inscriptionsCount">
                            <a>
                                @lang('account.noInscriptions')
                            </a>
                        </li>
                        <li ng-repeat="inscription in inscriptions track by $index" ng-if="contestClosed(inscription.contest.status)">
                            <a href="{{url('/')}}/@{{inscription.contest.code}}#/@{{ inscription.role == {{{Inscription::JUDGE}}} ? 'voting':'entries' }}" ng-class="{'text-warning': inscription.role == {{Inscription::OWNER}} || inscription.role == {{Inscription::COLABORATOR}} }">
                                <i class="fa" ng-class="{'fa-ticket': inscription.role == '{{Inscription::INSCRIPTOR}}', 'fa-user-circle-o': inscription.role == '{{Inscription::COLABORATOR}}', 'fa-user-circle': inscription.role == '{{Inscription::OWNER}}', 'fa-legal': inscription.role == '{{Inscription::JUDGE}}'}"></i>
                                @{{ inscription.contest.name }}
                            </a>
                        </li>
                        <li class="divider" ng-if="contests.length"></li>
                        <li ng-repeat="contest in contests track by $index">
                            <a href="{{url('/')}}/@{{contest.code}}#/">
                                <i class="fa"></i>@{{ contest.name }}</a>
                        </li>
                    </ul>
                </li>
            <li class="dropdown profile-menu">
                <a href class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                    <img ng-src="@{{currentUser.picThumbUrl}}?v=@{{currentUser.picDate}}" alt="" class="thumb">
                    @{{currentUser.first_name}} @{{currentUser.last_name}} <span class="caret"></span>
                </a>
                <ul class="dropdown-menu multi-level" role="menu">
                    <li><a href="#/account/data"><i class="fa fa-user"></i> @lang('account.data')</a></li>
                    <li><a href="#/account/security"><i class="fa fa-shield"></i> @lang('account.security')</a></li>
                    <li><a href="#/account/config"><i class="fa fa-cogs"></i> @lang('account.config')</a></li>
                    <li class="divider"></li>
                    <li><a href="#logout"><i class="fa fa-sign-out"></i> <?=Lang::get('header.logOut')?></a></li>
                </ul>
            </li>
        </ul>
    </div>
    <div ng-hide="currentUser">
        <div class="nav navbar-nav navbar-right">
            <a class="btn btn-primary navbar-btn" ng-href="#/login">@lang('login.signIn')</a>
            <a class="btn btn-danger navbar-btn" ng-href="#/register">@lang('login.register')</a>
        </div>
    </div>
</div>