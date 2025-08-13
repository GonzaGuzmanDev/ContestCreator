@include('admin.header')
<div class="main-block contents">
    <h3>
        <a href="#/home"><i class="fa fa-wrench"></i> @lang('general.admin')</a>
        /
        <i class="fa fa-trophy"></i> @lang('general.contests')
    </h3>
    <div class="row">
        @include('admin.menu', array('section' => 'contests'))
        <div class="col-sm-9 col-lg-10">
            <form class="form-inline" role="form">
                <a href="#/contests/edit/" class="btn btn-success"><i class="fa fa-plus"></i> @lang('contest.newContest')</a>
                <div class="form-group">
                    <input ng-model-options="{debounce: 500}" type="text" ng-model="pagination.query" class="form-control inline" placeholder="@lang('general.search')">
                </div>
            </form>
            <uib-pagination boundary-links="true" total-items="pagination.total" items-per-page="pagination.perPage" max-size="7" rotate="false" ng-model="pagination.page" class="pagination-sm" ng-show="contests.length > 0" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></uib-pagination>
            <span class="pagination-data" ng-show="dataLoaded">@{{((pagination.page-1) * pagination.perPage)+1}} @lang('general.to') @{{Math.min(pagination.page * pagination.perPage, pagination.total)}} @lang('general.of') @{{pagination.total}} @lang('general.results')</span>
            <div class="clearfix"></div>
            <table class="table table-striped table-hover table-condensed">
                <thead>
                    <tr>
                        <th><a data-ng-click="changeOrder('code')">@lang('general.code') <i ng-show="pagination.orderBy == 'code'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                        <th><a data-ng-click="changeOrder('name')">@lang('general.name') <i ng-show="pagination.orderBy == 'name'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                        <th><a data-ng-click="">@lang('contest.status') <i ng-show="pagination.orderBy == ''" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                        <th>@lang('contest.inscriptionStatusInscriptor')</th>
                        <th>@lang('contest.inscriptionStatusJudge')</th>
                        <th><a data-ng-click="changeOrder('users')">@lang('contest.inscriptionStatusUsers') <i ng-show="pagination.orderBy == 'users'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                        <th><a data-ng-click="changeOrder('entries')">@lang('contest.inscriptionStatusEntries') <i ng-show="pagination.orderBy == 'entries'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                        <th><a data-ng-click="">@lang('contest.invoiceStatus') <i ng-show="pagination.orderBy == ''" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                        <!--<th><a data-ng-click="changeOrder('start_at')">@lang('contest.startAt') <i ng-show="pagination.orderBy == 'start_at'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
                        <th><a data-ng-click="changeOrder('finish_at')">@lang('contest.finishAt') <i ng-show="pagination.orderBy == 'finish_at'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>-->
                        <th></th>
                    </tr>
                </thead>
                <br>
                <div class="btn-group">
                    <div class="btn" ng-class="{'btn-default':filterContest.indexOf({{Contest::STATUS_WIZARD}}) == -1,'btn-success':filterContest.indexOf({{Contest::STATUS_WIZARD}}) != -1}" ng-click="filterContestFunction({{Contest::STATUS_WIZARD}})"> @lang('contest.status.wizard') </div>
                    <div class="btn" ng-class="{'btn-default':filterContest.indexOf({{Contest::STATUS_COMPLETE}}) == -1,'btn-success':filterContest.indexOf({{Contest::STATUS_COMPLETE}}) != -1}" ng-click="filterContestFunction({{Contest::STATUS_COMPLETE}})"> @lang('contest.status.complete') </div>
                    <div class="btn" ng-class="{'btn-default':filterContest.indexOf({{Contest::STATUS_READY}}) == -1,'btn-success':filterContest.indexOf({{Contest::STATUS_READY}}) != -1}" ng-click="filterContestFunction({{Contest::STATUS_READY}})"> @lang('contest.status.ready') </div>
                    <div class="btn" ng-class="{'btn-default':filterContest.indexOf({{Contest::STATUS_PUBLIC}}) == -1,'btn-success':filterContest.indexOf({{Contest::STATUS_PUBLIC}}) != -1}" ng-click="filterContestFunction({{Contest::STATUS_PUBLIC}})"> @lang('contest.status.public') </div>
                    <div class="btn" ng-class="{'btn-default':filterContest.indexOf({{Contest::STATUS_CLOSED}}) == -1,'btn-success':filterContest.indexOf({{Contest::STATUS_CLOSED}}) != -1}" ng-click="filterContestFunction({{Contest::STATUS_CLOSED}})"> @lang('contest.status.closed') </div>
                    <div class="btn" ng-class="{'btn-default':filterContest.indexOf({{Contest::STATUS_BANNED}}) == -1,'btn-success':filterContest.indexOf({{Contest::STATUS_BANNED}}) != -1}" ng-click="filterContestFunction({{Contest::STATUS_BANNED}})"> @lang('contest.status.banned') </div>
                </div>
                <br>
                <br>
                <tbody>
                    <tr ng-repeat="contest in contests">
                        <td><a href="#/contests/edit/@{{contest.code}}">@{{contest.code}}</a></td>
                        <td>@{{contest.name}}</td>
                        <th class="text-center">
                            <span class="label" ng-class="{'label-primary': contest.status == {{Contest::STATUS_WIZARD}},
                            'label-warning': contest.status == {{Contest::STATUS_COMPLETE}},
                            'label-success': contest.status == {{Contest::STATUS_READY}},
                            'label-info': contest.status == {{Contest::STATUS_PUBLIC}},
                            'label-danger': contest.status == {{Contest::STATUS_CLOSED}} ||  contest.status == {{Contest::STATUS_BANNED}}}">
                                <span ng-if="contest.status == {{Contest::STATUS_WIZARD}}">@lang('contest.status.wizard') </span>
                                <span ng-if="contest.status == {{Contest::STATUS_COMPLETE}}">@lang('contest.status.complete') </span>
                                <span ng-if="contest.status == {{Contest::STATUS_READY}}">@lang('contest.status.ready') </span>
                                <span ng-if="contest.status == {{Contest::STATUS_PUBLIC}}">@lang('contest.status.public') </span>
                                <span ng-if="contest.status == {{Contest::STATUS_CLOSED}}">@lang('contest.status.closed') </span>
                                <span ng-if="contest.status == {{Contest::STATUS_BANNED}}">@lang('contest.status.banned') </span>
                            </span>
                            <span class="dropdown">
                                <button class="btn btn-primary btn-xs dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-cog"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li ng-if="contest.status == {{Contest::STATUS_READY}} || contest.status == {{Contest::STATUS_CLOSED}} || contest.status == {{Contest::STATUS_BANNED}} || contest.status == {{Contest::STATUS_PUBLIC}}"><a ng-click="contestRequest(contest, true, {{Contest::STATUS_COMPLETE}})" > Volver a Completo </a></li>
                                    <li ng-if="contest.status == {{Contest::STATUS_COMPLETE}}"><a ng-click="contestRequest(contest, true)"> Aprobar </a></li>
                                    <li ng-if="contest.status == {{Contest::STATUS_READY}}"><a ng-click="contestRequest(contest)" > Publicar </a></li>
                                    <li><a ng-click="contestRequest(contest, true, {{Contest::STATUS_CLOSED}})" > Cerrar </a></li>
                                    <li><a ng-click="contestRequest(contest, true, {{Contest::STATUS_BANNED}})" > Banear </a></li>
                                </ul>
                            </span>
                        </th>
                        <td class="text-center">
                            <span class="label" ng-class="{'label-warning': contest.inscriptorStatus == {{Contest::STATUS_INSCRIPTIONS_CLOSED}},'label-info': contest.inscriptorStatus == {{Contest::STATUS_INSCRIPTIONS_NEXT}},'label-success': contest.inscriptorStatus == {{Contest::STATUS_INSCRIPTIONS_OPEN}},'label-danger': contest.inscriptorStatus == {{Contest::STATUS_INSCRIPTIONS_UNKNOWN}} }">
                                <span ng-if="contest.inscriptorStatus == {{Contest::STATUS_INSCRIPTIONS_UNKNOWN}}">@lang('contest.inscriptionStatusUnknown')</span>
                                <span ng-if="contest.inscriptorStatus == {{Contest::STATUS_INSCRIPTIONS_CLOSED}}">@lang('contest.inscriptionStatusClosed')</span>
                                <span ng-if="contest.inscriptorStatus == {{Contest::STATUS_INSCRIPTIONS_NEXT}}">@lang('contest.inscriptionStatusNext')</span>
                                <span ng-if="contest.inscriptorStatus == {{Contest::STATUS_INSCRIPTIONS_OPEN}}">@lang('contest.inscriptionStatusOpen')</span>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="label" ng-class="{'label-warning': contest.judgeStatus == {{Contest::STATUS_INSCRIPTIONS_CLOSED}},'label-info': contest.judgeStatus == {{Contest::STATUS_INSCRIPTIONS_NEXT}},'label-success': contest.judgeStatus == {{Contest::STATUS_INSCRIPTIONS_OPEN}},'label-danger': contest.judgeStatus == {{Contest::STATUS_INSCRIPTIONS_UNKNOWN}} }">
                                <span ng-if="contest.judgeStatus == {{Contest::STATUS_INSCRIPTIONS_UNKNOWN}}">@lang('contest.inscriptionStatusUnknown')</span>
                                <span ng-if="contest.judgeStatus == {{Contest::STATUS_INSCRIPTIONS_CLOSED}}">@lang('contest.inscriptionStatusClosed')</span>
                                <span ng-if="contest.judgeStatus == {{Contest::STATUS_INSCRIPTIONS_NEXT}}">@lang('contest.inscriptionStatusNext')</span>
                                <span ng-if="contest.judgeStatus == {{Contest::STATUS_INSCRIPTIONS_OPEN}}">@lang('contest.inscriptionStatusOpen')</span>
                            </span>
                        </td>
                        <td class="text-center">@{{contest.users}}</td>
                        <td class="text-center">@{{contest.entries}}</td>
                        <th class="text-center">
                            <a ng-click="openInvoiceModal(contest)"
                               ng-class="{'btn-danger':contest.invoice_status == {{ContestInvoice::STATUS_NOT_INVOICED}} || !contest.invoice_status,
                               'btn-warning':contest.invoice_status == {{ContestInvoice::STATUS_INVOICED}},
                               'btn-primary':contest.invoice_status == {{ContestInvoice::STATUS_SWAP}},
                               'btn-success':contest.invoice_status == {{ContestInvoice::STATUS_PAYED}}}" class="btn btn-xs" target="_blank">
                                <span ng-if="contest.invoice_status == {{ContestInvoice::STATUS_NOT_INVOICED}} || !contest.invoice_status"> @lang('billing.not_invoiced') </span>
                                <span ng-if="contest.invoice_status == {{ContestInvoice::STATUS_INVOICED}}"> @lang('billing.invoiced') </span>
                                <span ng-if="contest.invoice_status == {{ContestInvoice::STATUS_PAYED}}"> @lang('billing.payed') </span>
                                <span ng-if="contest.invoice_status == {{ContestInvoice::STATUS_SWAP}}"> @lang('billing.swap') </span>
                            </a>
                        </th>
                        <!--<td>@{{contest.start_at}}</td>
                        <td>@{{contest.finish_at}}</td>-->
                        <td class="text-right">
                            <a href="{{url('/')}}/@{{contest.code}}" class="btn btn-info btn-xs" target="_blank"><i class="fa fa-link"></i> @lang('general.link')</a>
                            <a href="#/contests/edit/@{{contest.code}}" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> @lang('general.edit')</a>
                            <button class="btn btn-danger btn-xs" ng-click="delete(contest)"><i class="fa fa-trash"></i> @lang('general.delete')</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash">
                <span ng-bind-html="flash"></span>
            </div>
        </div>
    </div>
</div>