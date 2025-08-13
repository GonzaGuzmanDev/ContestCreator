<div class="col-sm-3 col-lg-2 admin-menu" ng-if="{{!$contest->wizard_status || $contest->wizard_status == Contest::WIZARD_FINISHED}}">
    <div>
        @if($contest->hasCategories($contest->id))
        <a
            ng-if="inTimeForInscriptions() && !reachedMaxEntries() && inscription.role == {{ Inscription::INSCRIPTOR }}"
            class="list-group-item title new-entry"
            href="#/entry/">
            <i class="fa fa-plus"> </i>
            @lang('contest.newEntry')
        </a>
        <a
            ng-if="inscription.role == {{Inscription::OWNER}} || editPermit == true || {{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}}"
            class="list-group-item title new-entry"
            href="#/entry/">
            <i class="fa fa-plus"> </i>
            @lang('contest.newEntry')
        </a>
        @endif
    </div>
    <div class="list-subgroup">
        <a href="#/home/" role="presentation" class="list-group-item title"><i class="fa fa-home fa-fw"></i> @lang('contest.tab.home')</a>
    </div>
    <div class="list-group">
        <span ng-if="{{$contest->type}} == {{Contest::TYPE_TICKET}}">
            <a href="#/entries" ng-if="{{$contest->isInscriptor() ? 1 : 0}}" role="presentation" class="list-group-item {{$active == 'entries-list' ? 'active' : ''}}">
                <i class="fa fa-ticket fa-fw"></i>
                @lang('oxoTicket.viewTickets')
            </a>
        </span>
        <span ng-if="{{$contest->type}} == {{Contest::TYPE_CONTEST}}">
            <a href="#/admin/" role="presentation" ng-if="{{$contest->isColaborator($permits,Contest::ADMIN)}}" class="list-group-item title"><i class="fa fa-info-circle fa-fw">
                </i>
                @lang('contest.tab.info')
            </a>
            <a href="#/admin/inscriptions-list" ng-if="{{$contest->isColaborator($permits,Contest::ADMIN)}}" role="presentation" class="list-group-item {{$active == 'inscriptions-list' ? 'active' : ''}}">
                <i class="fa fa-users fa-fw"></i>
                @lang('contest.tab.inscriptions-list')
            </a>
            <a href="#/signup/{{Inscription::INSCRIPTOR}}" ng-if="{{$contest->isInscriptor() ? 1 : 0}}" role="presentation" class="list-group-item {{$active == 'registration' ? 'active' : ''}}">
                <i class="fa fa-user fa-fw"></i>
                @lang('contest.tab.my-registration')
            </a>
            <a href="#/signup/{{Inscription::JUDGE}}" ng-if="{{$contest->isJudge() ? 1 : 0}}" role="presentation" class="list-group-item {{$active == 'registration' ? 'active' : ''}}">
                <i class="fa fa-user-o fa-fw">
                </i> @lang('contest.tab.my-registration')
            </a>
            @if($contest->hasCategories($contest->id))
                <a href="#/entries" ng-if="{{$contest->isColaborator($permits,Contest::ADMIN)}} || {{$contest->isInscriptor() ? 1 : 0}}" role="presentation" class="list-group-item {{$active == 'entries-list' ? 'active' : ''}}">
                    <i class="fa fa-ticket fa-fw"></i>
                    @lang('contest.tab.entries-list')
                </a>
            @endif
            <a href="#/admin/voting-sessions" ng-if="{{$contest->isColaborator($permits,Contest::ADMIN)}} || {{$contest->isJudge() ? 1 : 0}}" role="presentation" class="list-group-item {{$active == 'voting-sessions' ? 'active' : ''}}">
                <i class="fa fa-gavel fa-fw"></i>
                @lang('contest.tab.voting')
            </a>
            <a href="#/files" ng-if="{{$contest->isInscriptor() ? 1 : 0}}" role="presentation" class="list-group-item {{$active == 'files' ? 'active' : ''}}">
                <i class="fa fa-files-o fa-fw"></i>
                @lang('contest.tab.myfiles')
            </a>
            <a href="#/tech" ng-if="{{$contest->isAdmin() ? 1 : 0}} || {{$contest->isColaborator($permits, Contest::TECH)}} || {{$contest->isColaborator($permits, Contest::ADMIN)}}" role="presentation" class="list-group-item {{$active == 'files' ? 'active' : ''}}">
                <i class="fa fa-files-o fa-fw"></i>
                @lang('contest.tab.files')
            </a>
            <a href="#/admin/billing" ng-if="{{$contest->isColaborator($permits,Contest::BILLING)}} || {{$contest->isColaborator($permits,Contest::ADMIN)}}" role="presentation" class="list-group-item {{$active == 'billing' ? 'active' : ''}}">
                <i class="fa fa-money fa-fw"></i>
                @lang('contest.tab.billing')
            </a>
            <a href="#/payments" ng-if="{{$contest->isInscriptor() ? 1 : 0}} && {{$contest->billing !== null}}" role="presentation" class="list-group-item {{$active == 'payments' ? 'active' : ''}}">
                <i class="fa fa-money fa-fw"></i>
                @lang('contest.tab.payments')
            </a>
        </span>
        <div class="list-subgroup" ng-if="({{$contest->isColaborator($permits,Contest::ADMIN)}} || {{$contest->isColaborator($permits)}}) && {{$contest->type}} == {{Contest::TYPE_CONTEST}}">
            <a href="" role="presentation" class="list-group-item title">
                <i class="fa fa-sliders fa-fw"></i>
                @lang('contest.tab.config')
            </a>
            <a href="#/admin/import-contest" ng-if="{{$contest->isColaborator($permits,Contest::ADMIN)}} && {{$contest->type}} == {{Contest::TYPE_CONTEST}}" role="presentation" class="list-group-item {{$active == 'import' ? 'active' : ''}}">
                <i class="fa fa-copy fa-fw"></i>
                @lang('contest.tab.import')
            </a>
            <a href="#/admin/deadlines" ng-if="{{$contest->isColaborator($permits,Contest::ADMIN)}}" role="presentation" class="list-group-item {{$active == 'deadlines' ? 'active' : ''}}">
                <i class="fa fa-calendar fa-fw"></i>
                @lang('contest.tab.deadlines')
            </a>
            <a href="#/admin/categories" ng-if="{{$contest->isColaborator($permits,Contest::ADMIN)}}" role="presentation" class="list-group-item {{$active == 'categories' ? 'active' : ''}}">
                <i class="fa fa-list fa-fw"></i>
                @lang('contest.tab.categories')
            </a>
            <a href="#/admin/inscriptions" ng-if="{{$contest->isColaborator($permits,Contest::ADMIN)}}" role="presentation" class="list-group-item {{$active == 'inscriptions' ? 'active' : ''}}">
                <i class="fa fa-file-o fa-fw"></i>
                @lang('contest.tab.inscriptions')
            </a>
            <a href="#/admin/billingsetup" ng-if="{{$contest->isColaborator($permits,Contest::BILLING)}}" role="presentation" class="list-group-item {{$active == 'payments' ? 'active' : ''}}">
                <i class="fa fa-credit-card fa-fw"></i>
                @lang('contest.tab.paymentMethods')
            </a>
            <a href="#/admin/entries" ng-if="{{$contest->isColaborator($permits,Contest::ADMIN)}}" role="presentation" class="list-group-item {{$active == 'entries' ? 'active' : ''}}">
                <i class="fa fa-file fa-fw"></i>
                @lang('contest.tab.entries')
            </a>
            <a href="#/admin/style" ng-if="{{$contest->isColaborator($permits,Contest::DESIGN)}} || {{$contest->isColaborator($permits,Contest::ADMIN)}}" role="presentation" class="list-group-item {{$active == 'style' ? 'active' : ''}}">
                <i class="fa fa-edit fa-fw"></i>
                @lang('contest.tab.style')
            </a>
            <a href="#/admin/mail" ng-if="{{$contest->isColaborator($permits,Contest::DESIGN)}} || {{$contest->isColaborator($permits,Contest::ADMIN)}}" role="presentation" class="list-group-item {{$active == 'mail' ? 'active' : ''}}">
                <i class="fa fa-envelope fa-fw"></i>
                @lang('contest.tab.mails')
            </a>
        </div>
        <div class="list-subgroup" ng-if="({{$contest->isColaborator($permits,Contest::ADMIN)}} || {{$contest->isColaborator($permits)}}) && {{$contest->type}} == {{Contest::TYPE_TICKET}}">
            <a href="#/admin/" role="presentation" class="list-group-item title"><i class="fa fa-info-circle fa-fw">
                </i>
                @lang('contest.tab.info')
            </a>
            <a href="" role="presentation" class="list-group-item title">
                <i class="fa fa-sliders fa-fw"></i>
                @lang('contest.tab.config')
            </a>
            <a href="#/admin/deadlines" ng-if="{{$contest->isColaborator($permits,Contest::ADMIN)}}" role="presentation" class="list-group-item {{$active == 'deadlines' ? 'active' : ''}}">
                <i class="fa fa-calendar fa-fw"></i>
                @lang('contest.tab.deadlines')
            </a>
            <a href="#/admin/categories" ng-if="{{$contest->isColaborator($permits,Contest::ADMIN)}}" role="presentation" class="list-group-item {{$active == 'categories' ? 'active' : ''}}">
                <i class="fa fa-list fa-fw"></i>
                @lang('oxoTicket.configTickets')
            </a>
            <a href="#/admin/inscriptions-list" ng-if="{{$contest->isColaborator($permits,Contest::ADMIN)}}" role="presentation" class="list-group-item {{$active == 'inscriptions-list' ? 'active' : ''}}">
                <i class="fa fa-users fa-fw"></i>
                @lang('contest.tab.inscriptions-list')
            </a>
            <a href="#/admin/inscriptions" ng-if="{{$contest->isColaborator($permits,Contest::ADMIN)}}" role="presentation" class="list-group-item {{$active == 'inscriptions' ? 'active' : ''}}">
                <i class="fa fa-file-o fa-fw"></i>
                @lang('contest.tab.inscriptions')
            </a>
            <a href="#/admin/billing" ng-if="{{$contest->isColaborator($permits,Contest::BILLING)}} || {{$contest->isColaborator($permits,Contest::ADMIN)}}" role="presentation" class="list-group-item {{$active == 'billing' ? 'active' : ''}}">
                <i class="fa fa-money fa-fw"></i>
                @lang('contest.tab.billing')
            </a>
            <a href="#/admin/billingsetup" ng-if="{{$contest->isColaborator($permits,Contest::BILLING)}}" role="presentation" class="list-group-item {{$active == 'payments' ? 'active' : ''}}">
                <i class="fa fa-credit-card fa-fw"></i>
                @lang('contest.tab.paymentMethods')
            </a>
            <a href="#/admin/entries" ng-if="{{$contest->isColaborator($permits,Contest::ADMIN)}}" role="presentation" class="list-group-item {{$active == 'entries' ? 'active' : ''}}">
                <i class="fa fa-file fa-fw"></i>
                @lang('contest.tab.entries')
            </a>
            <a href="#/admin/style" ng-if="{{$contest->isColaborator($permits,Contest::DESIGN)}} || {{$contest->isColaborator($permits,Contest::ADMIN)}}" role="presentation" class="list-group-item {{$active == 'style' ? 'active' : ''}}">
                <i class="fa fa-edit fa-fw"></i>
                @lang('contest.tab.style')
            </a>
            <!--<a href="#/admin/mail" ng-if="{{$contest->isColaborator($permits,Contest::DESIGN)}} || {{$contest->isColaborator($permits,Contest::ADMIN)}}" role="presentation" class="list-group-item {{$active == 'mail' ? 'active' : ''}}">
                <i class="fa fa-envelope fa-fw"></i>
                @lang('contest.tab.mails')
            </a>-->
        </div>

        <div class="list-subgroup" ng-if="{{$contest->isColaborator($permits,Contest::ADMIN)}} && {{$contest->type}} == {{Contest::TYPE_CONTEST}}">
            <a href="" role="presentation" class="list-group-item title"><i class="fa fa-wrench fa-fw"></i> @lang('contest.tab.tools')</a>
            <a href="#/admin/pages" ng-if="{{$contest->isColaborator($permits,Contest::DESIGN)}} || {{$contest->isColaborator($permits,Contest::ADMIN)}}" role="presentation" class="list-group-item {{$active == 'pages' ? 'active' : ''}}"><i class="fa fa-link fa-fw"></i> @lang('contest.tab.pages')</a>
            <a href="#/admin/assets" ng-if="{{$contest->isColaborator($permits,Contest::DESIGN)}} || {{$contest->isColaborator($permits,Contest::ADMIN)}}" role="presentation" class="list-group-item {{$active == 'assets' ? 'active' : ''}}"><i class="fa fa-copy fa-fw"></i> @lang('contest.tab.assets')</a>
            <a href="#/admin/newsletters" ng-if="{{$contest->isColaborator($permits,Contest::ADMIN)}}" role="presentation" class="list-group-item {{{$active == 'newsletters' ? 'active' : ''}}}"><i class="fa fa-newspaper-o fa-fw"></i> @lang('contest.tab.newsletter')</a>
            <a href="#/admin/collections" ng-if="{{$contest->isColaborator($permits,Contest::ADMIN)}}" role="presentation" class="list-group-item {{{$active == 'collections' ? 'active' : ''}}}"><i class="fa fa-archive fa-fw"></i> @lang('contest.tab.collections')</a>
            <a href="#/admin/meta-analysis" ng-if="{{$contest->isColaborator($permits,Contest::ADMIN)}} && {{$contest->id == 301}}" role="presentation" class="list-group-item {{{$active == 'collections' ? 'active' : ''}}}"><i class="fa fa-bar-chart fa-fw"></i> @lang('contest.tab.metaAnalysis')</a>
        </div>
    </div>
</div>