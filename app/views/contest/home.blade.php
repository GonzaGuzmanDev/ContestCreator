<? /** @var Contest $contest */ ?>
@include('includes.header')
<div class="container">
</div>
@include('contest.header', array('banner' => ContestAsset::BIG_BANNER_HTML))
<div class="container with-footer">
<div class="row">
    @if(!$contest->isContestClosed())
        @if($contest->status == Contest::STATUS_CLOSED)
            <div class="alert alert-danger alert-xl alert-box text-center h4">
                @lang('contest.closedContest')
            </div>
        @endif
        @if($contest->status == Contest::STATUS_COMPLETE && ($contest->isColaborator($permits, Contest::ADMIN) || $superadmin || $owner))
            <div class="alert alert-warning alert-xl alert-box text-center h4">
                @lang('contest.completeContest')
            </div>
        @endif
        @if($contest->status == Contest::STATUS_READY && ($contest->isColaborator($permits, Contest::ADMIN) || $superadmin || $owner))
            <div class="alert alert-info alert-xl alert-box text-center h4">
                @lang('contest.readyContest',['link' => '#/admin/deadlines'])
            </div>
        @endif
    @endif
    @if($contest->getAsset(ContestAsset::HOME_HTML)->content)
    <div class="col-sm-8">
        {{ $contest->getAsset(ContestAsset::HOME_HTML)->content }}
    </div>
    @endif
    @if($contest->getAsset(ContestAsset::HOME_HTML)->content)<div class="col-sm-4">
    @else <div class="col-sm-offset-4 col-sm-4">
    @endif
        <!-- REGISTRO DE USUARIO E INSCRIPCION, CUANDO EL USUARIO NO EXISTE -->
        <div ng-show="!currentUser">
            @include('login.form-small')
            <hr>
            @if($contest->inscription_public)
                @if($contest->isRegistrationOpen(Inscription::INSCRIPTOR))
                    <a class="btn btn-danger btn-lg btn-block btn-wsnormal" href="#signup/{{Inscription::INSCRIPTOR}}">
                        <span ng-if="{{$contest->type == Contest::TYPE_CONTEST}}"> @lang('contest.signupasinscriptor') </span>
                        <span ng-if="{{$contest->type == Contest::TYPE_TICKET}}"> @lang('oxoTicket.register') </span>
                    </a>
                    <br/>
                    @if($contest->hasCategories($contest->id))
                        <div class="alert alert-info alert-sm text-center">
                            <i class="fa fa-info-circle"></i>
                            @lang('contest.signupendson', ['date'=>$contest->getInscriptionOpenDate()])
                            <span am-time-ago="'{{$contest->getInscriptionNextDeadlineDate()}}'"></span><span> {{$contest->getWhichDeadLine()}}</span>
                        </div>
                    @endif
                @elseif($contest->isRegistrationNext(Inscription::INSCRIPTOR))
                    <div class="alert alert-info text-center">
                        @lang('contest.signuponasinscriptor', ['date'=>$contest->getInscriptionOpenDate()])
                        <br/>
                        <span ng-bind="'{{$contest->getInscriptionOpenDate()}}' | amDateFormat:'DD/MM/YYYY HH:mm'"></span>,
                        <span am-time-ago="'{{$contest->getInscriptionOpenDate()}}'"></span>
                    </div>
                @elseif($contest->isRegistrationOver(Inscription::INSCRIPTOR))
                    <div class="alert alert-warning text-center">@lang('contest.signupclosedasinscriptor')</div>
                @endif
            @endif

            @if($contest->inscription_public && $contest->voters_public)
                <hr>
            @endif
            @if($contest->voters_public)
                @if($contest->isRegistrationOpen(Inscription::JUDGE))
                    <a class="btn btn-danger btn-lg btn-block btn-wsnormal" href="#signup/{{Inscription::JUDGE}}">@lang('contest.signupasjudge')</a>
                    <br/>
                    <div class="alert alert-info alert-sm text-center">
                        <i class="fa fa-info-circle"></i>
                        @lang('contest.signupendson', ['date'=>$contest->getInscriptionOpenDate(Inscription::JUDGE)])
                        <span am-time-ago="'{{$contest->getInscriptionNextDeadlineDate(Inscription::JUDGE)}}'"></span>
                    </div>
                @elseif($contest->isRegistrationNext(Inscription::JUDGE))
                    <div class="alert alert-info text-center">
                        @lang('contest.signuponasjudge', ['date'=>$contest->getInscriptionOpenDate(Inscription::JUDGE)])
                        <br/>
                        <span ng-bind="'{{$contest->getInscriptionOpenDate(Inscription::JUDGE)}}' | amDateFormat:'DD/MM/YYYY HH:mm'"></span>,
                        <span am-time-ago="'{{$contest->getInscriptionOpenDate(Inscription::JUDGE)}}'"></span>
                    </div>
                @elseif($contest->isRegistrationOver(Inscription::JUDGE))
                    <div class="alert alert-warning text-center">@lang('contest.signupclosedasjudge')</div>
                @endif
            @endif
        </div>
        <!------------------------------------------------------------------------------------------------------------------------------------->
        <!-- USUARIO EXISTENTE, LOGUEADO -->
        <div ng-show="currentUser" class="home-menu" ng-if="{{ $contest->type == Contest::TYPE_CONTEST}}">
            <br>
            @if($inscription == Inscription::INSCRIPTOR && $contest->isContestClosed())
                @if($contest->hasCategories($contest->id))
                    <a class="btn btn-primary btn btn-block" href="#updateInscription/{{Inscription::INSCRIPTOR}}"><i class="fa fa-user"></i> @lang('contest.viewmyregisterInscriptor')</a>
                @else <a class="btn btn-success btn btn-block" href="#signup/{{Inscription::INSCRIPTOR}}"><i class="fa fa-user"></i> @lang('contest.updateForm')</a>
                @endif
            @elseif($contest->inscription_public && !$contest->isColaborator($permits, Contest::EDIT) && !$contest->isColaborator($permits, Contest::SIFTER)
            && !$contest->isColaborator($permits, Contest::VIEWER) && !$contest->isColaborator($permits, Contest::ADMIN) && !$superadmin && !$owner
            && !$contest->isColaborator($permits, Contest::DESIGN) && !$contest->isColaborator($permits, Contest::TECH) && !$contest->isColaborator($permits, Contest::BILLING) && !$contest->isColaborator($permits, Contest::VOTING))
                @if($contest->isRegistrationOpen(Inscription::INSCRIPTOR))
                    @if($contest->hasCategories($contest->id))
                        <a class="btn btn-primary btn-lg btn-block btn-wsnormal" href="#signup/{{Inscription::INSCRIPTOR}}">
                            @lang('contest.signupasinscriptor')
                        </a>
                        <div class="pointer-top"></div>
                        <div class="alert alert-info alert-sm text-center">
                            <i class="fa fa-info-circle"></i>
                            @lang('contest.signupendson', ['date'=>$contest->getInscriptionOpenDate()])
                            <span am-time-ago="'{{$contest->getInscriptionNextDeadlineDate()}}'"></span>
                        </div>
                    @else
                        <a class="btn btn-primary btn-lg btn-block btn-wsnormal" href="#signup/{{Inscription::INSCRIPTOR}}">
                            @lang('contest.completeForm')
                        </a>
                    @endif
                @elseif($contest->isRegistrationNext(Inscription::INSCRIPTOR))
                    <div class="alert alert-info text-center">
                        @lang('contest.signuponasinscriptor', ['date'=>$contest->getInscriptionOpenDate()])
                        <br/>
                        <span ng-bind="'{{$contest->getInscriptionOpenDate()}}' | amDateFormat:'DD/MM/YYYY HH:mm'"></span>,
                        <span am-time-ago="'{{$contest->getInscriptionOpenDate()}}'"></span>
                    </div>
                @elseif($contest->isRegistrationOver(Inscription::INSCRIPTOR) && !$judge)
                    <div class="alert alert-warning text-center">@lang('contest.signupclosedasinscriptor')</div>
                @endif
            @endif
            @if($judge != null || isset($owner) || $superadmin)
                @if(!$owner && !$superadmin && $contest->voters_public == 1)
                    <a class="btn btn-primary btn btn-block" href="#signup/{{Inscription::JUDGE}}"><i class="fa fa-user"></i> @lang('contest.viewmyregisterJudge')</a>
                @endif
            @elseif($contest->voters_public)
                @if($contest->isRegistrationOpen(Inscription::JUDGE))
                    <a class="btn btn-primary btn-lg btn-block btn-wsnormal" href="#signup/{{Inscription::JUDGE}}">@lang('contest.signupasjudge')</a>
                    <div class="pointer-top"></div>
                    <div class="alert alert-info alert-sm text-center">
                        <i class="fa fa-info-circle"></i>
                        @lang('contest.signupendson', ['date'=>$contest->getInscriptionOpenDate(Inscription::JUDGE)])
                        <span am-time-ago="'{{$contest->getInscriptionNextDeadlineDate(Inscription::JUDGE)}}'"></span>
                    </div>
                @elseif($contest->isRegistrationNext(Inscription::JUDGE))
                    <div class="alert alert-info text-center">
                        @lang('contest.signuponasjudge', ['date'=>$contest->getInscriptionOpenDate(Inscription::JUDGE)])
                        <br/>
                        <span ng-bind="'{{$contest->getInscriptionOpenDate(Inscription::JUDGE)}}' | amDateFormat:'DD/MM/YYYY HH:mm'"></span>,
                        <span am-time-ago="'{{$contest->getInscriptionOpenDate(Inscription::JUDGE)}}'"></span>
                    </div>
                @elseif($contest->isRegistrationOver(Inscription::JUDGE))
                    <div class="alert alert-warning text-center">@lang('contest.signupclosedasjudge')</div>
                @endif
            @endif
            @if(($inscription != null && $contest->isContestClosed()) || isset($owner) || $superadmin
            || $contest->isColaborator($permits, Contest::EDIT) || $contest->isColaborator($permits, Contest::SIFTER)
            || $contest->isColaborator($permits, Contest::VIEWER) || $contest->isColaborator($permits, Contest::ADMIN))
                @if($contest->hasCategories($contest->id))
                    <div class="text-center btn-group-top">
                        <a href="#entries" ng-click="selected()" class="btn btn-primary btn-lg btn-block">
                            <i class="fa fa-ticket"></i>
                            @lang('contest.viewinscriptions')
                            <span class="">@{{ countAllEntries() }}</span>
                        </a>
                    </div>
                    <div class="text-center btn-group-bottom row">
                        <div class="col-sm-12">
                            <div class="btn-group btn-group-justified">
                                <a href="#entries" type="button" class="btn btn-sm btn-primary" ng-click="selected({{ Entry::INCOMPLETE }})">
                                    <!--<span tooltip-placement="bottom" uib-tooltip="@lang('contest.incomplete')" >-->
                                    <i class="fa fa-fw fa-square-o"></i>
                                    <span ng-class="{'label label-primary label-as-badge':!filterByIncomplete,'badge':filterByIncomplete}">@{{ incomplete }}</span>
                                    <!--</span>-->
                                </a>
                                <a href="#entries" type="button" class="btn btn-sm btn-warning" ng-click="selected({{ Entry::COMPLETE }})">
                                <!--<span tooltip-placement="bottom" uib-tooltip="@lang('contest.complete')" >-->
                                    <i class="fa fa-fw fa-clock-o"></i>
                                    <span ng-class="{'label label-warning label-as-badge':!filterByComplete,'badge':filterByComplete}">@{{ complete }}</span>
                                <!--</span>-->
                                </a>
                                <a href="#entries" type="button" class="btn btn-sm btn-success" ng-click="selected({{ Entry::FINALIZE }})">
                                <!--<span tooltip-placement="bottom" uib-tooltip="@lang('contest.finalized')" >-->
                                    <i class="fa fa-fw fa-check"></i>
                                    <span ng-class="{'label label-success label-as-badge':!filterByFinalize,'badge':filterByFinalize}">@{{ finalize }}</span>
                                <!--</span>-->
                                </a>
                                <a href="#entries" type="button" class="btn btn-sm btn-info" ng-click="selected({{ Entry::APPROVE }})">
                                <!--<span tooltip-placement="bottom" uib-tooltip="@lang('contest.approved')" >-->
                                    <i class="fa fa-fw fa-thumbs-up"></i>
                                    <span ng-class="{'label label-info label-as-badge':!filterByApproved,'badge':filterByApproved}">@{{ approved }}</span>
                                <!--</span>-->
                                </a>
                                <a href="#entries" type="button" class="btn btn-sm btn-danger" ng-click="selected({{ Entry::ERROR }})">
                                <!--<span tooltip-placement="bottom" uib-tooltip="@lang('contest.error')" >-->
                                    <i class="fa fa-fw fa-thumbs-down"></i>
                                    <span ng-class="{'label label-danger label-as-badge':!filterByError,'badge':filterByError}">@{{ error }}</span>
                                <!--</span>-->
                                </a>
                            </div>
                        </div>
                    </div>
                @if($inscription)
                    <a class="btn btn-primary btn-block btn-lg" href="#/files"><i class="fa fa-files-o"></i> @lang('contest.files.myfiles')</a>
                @endif
                @endif
            @endif
            @if($judge != null || isset($owner) || $superadmin || $contest->isColaborator($permits, Contest::ADMIN) || $contest->isColaborator($permits, Contest::VOTING))
                @if(!$owner && !$superadmin && !$contest->isColaborator($permits, Contest::ADMIN) && !$contest->isColaborator($permits, Contest::VOTING))
                    <a class="btn btn-primary btn-lg btn-block" href="#/voting"><i class="fa fa-legal"></i> @lang('contest.voteinscriptions')</a>
                @else <a class="btn btn-primary btn-lg btn-block" href="#/admin/voting-sessions"><i class="fa fa-legal"></i> @lang('contest.voteinscriptions')</a>
                @endif
            @endif
            @if($contest->isColaborator($permits, Contest::ADMIN) || isset($owner) || $superadmin || $contest->isColaborator($permits, Contest::VIEWER))
                <a href="#/admin/inscriptions-list" class="btn btn-block btn-lg btn-primary">
                    <i class="fa fa-user"></i>
                    @lang('contest.tab.inscriptions-list')
                    <span class="">@{{inscriptions[inscriptions.length-1]['total']}}</span>
                </a>
            @endif
            @if($contest->isColaborator($permits, Contest::DESIGN) || isset($owner) || $superadmin)
                <a class="btn btn-primary btn-lg btn-block" href="#admin/style"><i class="fa fa-edit"></i> @lang('contest.permitDesign')</a>
            @endif
            @if($contest->isColaborator($permits, Contest::BILLING) || isset($owner) || $superadmin  || $contest->isColaborator($permits, Contest::ADMIN))
                <div class="text-center btn-group-top">
                    <a class="btn btn-primary btn-lg btn-block" href="#admin/billing"><i class="fa fa-money"></i> @lang('contest.billing') @{{countAllBillings()}}</a>
                </div>
                <div class="text-center btn-group-bottom row">
                    <div class="col-sm-12">
                        <div class="btn-group btn-group-justified">
                            <a href="#admin/billing" type="button" class="btn btn-sm btn-warning" ng-click="filterBilling({{ Billing::STATUS_PENDING}})">
                                    <span tooltip-placement="bottom" uib-tooltip="@lang('contest.complete')" >
                                    <i class="fa fa-fw fa-clock-o"></i>
                                    <span ng-class="{'label label-warning label-as-badge':!filterByComplete,'badge':filterByComplete}">@{{ billingIncomplete }}</span>
                                    </span>
                            </a>
                            <a href="#admin/billing" type="button" class="btn btn-sm btn-info" ng-click="filterBilling({{ Billing::STATUS_PROCESSING }})">
                                    <span tooltip-placement="bottom" uib-tooltip="@lang('contest.processing')" >
                                    <i class="fa fa-fw fa-cog"></i>
                                    <span ng-class="{'label label-info label-as-badge':!filterByProcessing,'badge':filterByProcessing}">@{{ billingProcessing }}</span>
                                    </span>
                            </a>
                            <a href="#admin/billing" type="button" class="btn btn-sm btn-success" ng-click="filterBilling({{ Billing::STATUS_SUCCESS }})">
                                    <span tooltip-placement="bottom" uib-tooltip="@lang('contest.finalized')" >
                                    <i class="fa fa-fw fa-check"></i>
                                    <span ng-class="{'label label-success label-as-badge':!filterByFinalize,'badge':filterByFinalize}">@{{ billingComplete }}</span>
                                    </span>
                            </a>
                            <a href="#admin/billing" type="button" class="btn btn-sm btn-danger" ng-click="filterBilling({{ Billing::STATUS_ERROR }})">
                                    <span tooltip-placement="bottom" uib-tooltip="@lang('contest.error')" >
                                    <i class="fa fa-fw fa-thumbs-down"></i>
                                    <span ng-class="{'label label-danger label-as-badge':!filterByError,'badge':filterByError}">@{{ billingError }}</span>
                                    </span>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
            @if(isset($owner) || $superadmin || $contest->isColaborator($permits, Contest::TECH)  || $contest->isColaborator($permits, Contest::ADMIN))
                <a class="btn btn-primary btn-lg btn-block" href="#/tech"><i class="fa fa-files-o"></i> @lang('contest.files.contestMedia') @{{ files }}</a>
            @endif
            @if(isset($owner) || $superadmin || $contest->isColaborator($permits, Contest::ADMIN))
                <a href="#admin" class="btn btn-primary btn-lg btn-block"><i class="fa fa-sliders"></i> @lang('contest.configuration')</a>
            @endif
        </div>

        <!-- USUARIO EXISTENTE LOGUEADO, SISTEMA DE TICKETS -->
        <div ng-show="currentUser" class="home-menu" ng-if="{{ $contest->type == Contest::TYPE_TICKET}}">
            @if($inscription == Inscription::INSCRIPTOR && $contest->isContestClosed())
                <a class="btn btn-primary btn btn-block" href="#updateInscription/{{Inscription::INSCRIPTOR}}"><i class="fa fa-user"></i> @lang('oxoTicket.viewmyregisterInscriptor')</a>
                <div class="text-center btn-group-top">
                    <a href="#tickets" ng-click="selected()" class="btn btn-primary btn-lg btn-block">
                        <i class="fa fa-ticket"></i>
                        @lang('oxoTicket.viewTickets')
                        <span class="">@{{ countAllEntries() }} (@{{tickets}} Tickets)</span>
                    </a>
                </div>
                <div class="text-center btn-group-bottom row">
                    <div class="col-sm-12">
                        <div class="btn-group btn-group-justified">
                            <a href="#entries" type="button" class="btn btn-sm btn-info" ng-click="selected({{ Entry::APPROVE }})">
                            <span tooltip-placement="bottom" uib-tooltip="@lang('contest.approved')" >
                            <i class="fa fa-fw fa-thumbs-up"></i>
                            <span ng-class="{'label label-info label-as-badge':!filterByApproved,'badge':filterByApproved}">@{{ approved }}</span>
                            </span>
                            </a>
                            <a href="#entries" type="button" class="btn btn-sm btn-danger" ng-click="selected({{ Entry::ERROR }})">
                            <span tooltip-placement="bottom" uib-tooltip="@lang('contest.error')" >
                            <i class="fa fa-fw fa-thumbs-down"></i>
                            <span ng-class="{'label label-danger label-as-badge':!filterByError,'badge':filterByError}">@{{ error }}</span>
                            </span>
                            </a>
                        </div>
                    </div>
                </div>
                <br>
                <div class="text-center btn-group-bottom">
                    <div class="btn-group btn-group-justified">
                        <a href="#buyTickets" class="btn btn-danger btn-lg btn-block">
                            <i class="fa fa-money"></i>
                            @lang('oxoTicket.buyTickets')
                        </a>
                    </div>
                </div>
                <br>
            @elseif($contest->inscription_public && !$contest->isColaborator($permits, Contest::EDIT) && !$contest->isColaborator($permits, Contest::SIFTER)
            && !$contest->isColaborator($permits, Contest::VIEWER) && !$contest->isColaborator($permits, Contest::ADMIN) && !$superadmin && !$owner
            && !$contest->isColaborator($permits, Contest::DESIGN) && !$contest->isColaborator($permits, Contest::TECH) && !$contest->isColaborator($permits, Contest::BILLING) && !$contest->isColaborator($permits, Contest::VOTING))
            @if($contest->isRegistrationOpen(Inscription::INSCRIPTOR))
                    <a class="btn btn-primary btn-lg btn-block btn-wsnormal" href="#signup/{{Inscription::INSCRIPTOR}}">
                        @lang('oxoTicket.completeRegisterForm')
                    </a>
                    <div class="pointer-top"></div>
                    <div class="alert alert-info alert-sm text-center">
                        <i class="fa fa-info-circle"></i>
                        @lang('oxoTicket.signupendson', ['date'=>$contest->getInscriptionOpenDate()])
                        <span am-time-ago="'{{$contest->getInscriptionNextDeadlineDate()}}'"></span>
                    </div>
                @else
                    <a class="btn btn-primary btn-lg btn-block btn-wsnormal" href="#signup/{{Inscription::INSCRIPTOR}}">
                        @lang('contest.completeForm')
                    </a>
                @endif
            @elseif($contest->isRegistrationNext(Inscription::INSCRIPTOR))
                <div class="alert alert-info text-center">
                    @lang('oxoTicket.signuponasinscriptor', ['date'=>$contest->getInscriptionOpenDate()])
                    <br/>
                    <span ng-bind="'{{$contest->getInscriptionOpenDate()}}' | amDateFormat:'DD/MM/YYYY HH:mm'"></span>,
                    <span am-time-ago="'{{$contest->getInscriptionOpenDate()}}'"></span>
                </div>
            @elseif($contest->isRegistrationOver(Inscription::INSCRIPTOR) && !$judge)
                <div class="alert alert-warning text-center">@lang('oxoTicket.signupclosedasinscriptor')</div>
            @endif
            @if(isset($owner) || $superadmin || $contest->isColaborator($permits, Contest::ADMIN))
                    <div class="text-center btn-group-top">
                        <a href="#tickets" ng-click="selected()" class="btn btn-primary btn-lg btn-block">
                            <i class="fa fa-ticket"></i>
                            @lang('oxoTicket.viewTicketsAdmin')
                            <span class="">@{{ countAllEntries() }} (@{{tickets}} Tickets)</span>
                        </a>
                    </div>
                    <div class="text-center btn-group-bottom row">
                        <div class="col-sm-12">
                            <div class="btn-group btn-group-justified">
                                <a href="#entries" type="button" class="btn btn-sm btn-info" ng-click="selected({{ Entry::APPROVE }})">
                            <span tooltip-placement="bottom" uib-tooltip="@lang('contest.approved')" >
                            <i class="fa fa-fw fa-thumbs-up"></i>
                            <span ng-class="{'label label-info label-as-badge':!filterByApproved,'badge':filterByApproved}">@{{ approved }}</span>
                            </span>
                                </a>
                                <a href="#entries" type="button" class="btn btn-sm btn-danger" ng-click="selected({{ Entry::ERROR }})">
                            <span tooltip-placement="bottom" uib-tooltip="@lang('contest.error')" >
                            <i class="fa fa-fw fa-thumbs-down"></i>
                            <span ng-class="{'label label-danger label-as-badge':!filterByError,'badge':filterByError}">@{{ error }}</span>
                            </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="text-center btn-group-bottom">
                        <div class="btn-group btn-group-justified">
                            <a href="#buyTickets" class="btn btn-danger btn-lg btn-block">
                                <i class="fa fa-money"></i>
                                @lang('oxoTicket.buyTickets')
                            </a>
                        </div>
                    </div>
                    <br>
                <a href="#admin" class="btn btn-primary btn-lg btn-block"><i class="fa fa-sliders"></i> @lang('contest.configuration')</a>
            @endif
        </div>
    </div>
</div>

{{ $contest->getAsset(ContestAsset::HOME_BOTTOM_HTML)->content }}

@include('includes.footer')

</div>
</div>