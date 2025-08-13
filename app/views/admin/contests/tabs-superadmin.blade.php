<br><br><br>
<ul class="nav nav-tabs">
    <li role="presentation" class="{{{$active == 'general' ? 'active' : ''}}}"><a href="#/contests/edit/@{{contest.code}}">@lang('contest.tab.general')</a></li>
    <li role="presentation" ng-show="contest.id"><a href="<?=url('/');?>/@{{contest.code}}#/admin/categories">@lang('contest.tab.categories')</a></li>
    <li role="presentation" ng-show="contest.id"><a href="<?=url('/');?>/@{{contest.code}}#/admin/inscriptions">@lang('contest.tab.inscriptions')</a></li>
    <li role="presentation" ng-show="contest.id"><a href="<?=url('/');?>/@{{contest.code}}#/admin/entries">@lang('contest.tab.entries')</a></li>
    <li role="presentation" ng-show="contest.id"><a href="<?=url('/');?>/@{{contest.code}}#/admin/billingsetup">@lang('contest.tab.paymentMethods')</a></li>
    <li role="presentation" ng-show="contest.id"><a href="<?=url('/');?>/@{{contest.code}}#/admin/inscriptions-list">@lang('contest.tab.inscriptions-list')</a></li>
    <li role="presentation" ng-show="contest.id"><a href="<?=url('/');?>/@{{contest.code}}#/admin/style">@lang('contest.tab.style')</a></li>
    <li role="presentation" ng-show="contest.id"><a href="<?=url('/');?>/@{{contest.code}}#/admin/pages">@lang('contest.tab.pages')</a></li>
    <li role="presentation" ng-show="contest.id"><a href="<?=url('/');?>/@{{contest.code}}#/admin/voting-sessions">@lang('contest.tab.voting')</a></li>
    <li role="presentation" ng-show="contest.id"><a href="<?=url('/');?>/@{{contest.code}}#/admin/billing">@lang('contest.tab.billing')</a></li>
    <li role="presentation" ng-show="contest.id"><a href="<?=url('/');?>/@{{contest.code}}#/admin/newsletters">@lang('contest.tab.newsletter')</a></li>
    <!--<li role="presentation" class="{{{$active == 'invitation' ? 'active' : ''}}}" ng-show="contest.id"><a href="#/contests/edit/@{{contest.code}}/invitation">@lang('contest.tab.invitation')</a></li>-->
</ul>