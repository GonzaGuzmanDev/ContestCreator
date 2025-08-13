<div class="col-sm-3 col-lg-2">
    <div class="list-group">
        <a href="#/home" class="list-group-item {{$section == 'home' ? 'active' : ''}}"><i class="fa fa-home"></i> @lang('menu.init')</a>
        <a href="#/users" class="list-group-item {{$section == 'users' ? 'active' : ''}}"><i class="fa fa-users"></i> @lang('menu.users')</a>
        <a href="#/contests" class="list-group-item {{$section == 'contests' ? 'active' : ''}}"><i class="fa fa-trophy"></i> @lang('menu.contests')</a>
        <a href="#/formats" class="list-group-item {{$section == 'formats' ? 'active' : ''}}"><i class="fa fa-sitemap"></i> @lang('menu.formats')</a>
        <a href="#/contest-files" class="list-group-item {{$section == 'contest-files' ? 'active' : ''}}"><i class="fa fa-archive"></i> @lang('menu.contestFiles')</a>
    </div>
</div>