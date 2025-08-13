<ul class="nav nav-tabs">
    <li role="presentation" class="{{{$active == 'general' ? 'active' : ''}}}"><a href="#/users/edit/@{{user.id}}">@lang('user.tab1')</a></li>
    <li ng-show="user.id" role="presentation" class="{{{$active == 'inscriptions' ? 'active' : ''}}}"><a href="#/users/edit/@{{user.id}}/inscriptions">@lang('user.tab2')</a></li>
</ul>