@extends('admin.contests.form', array('active' => $admin ? 'billing':'payments'))
@section('form')
    <script type="text/ng-template" id="bill-statusForm.html">
        @include('admin.contests.bill-statusForm')
    </script>
    <h4 class="well well-sm">
        @if($admin)
            @lang('contest.billing')
        @else
            @lang('contest.tab.payments')
        @endif
        {{--@if(!$superadmin)
            <i class="fa fa-question-circle text-info" popover="@lang('contest.explain.billing')" popover-placement="right" popover-trigger="mouseenter" class="btn btn-default"></i>
        @endif--}}
    </h4>
    <div class="row">
    <div class="tab-pagination col-sm-8">
        <input ng-model-options="{debounce: 500}" type="text" ng-model="pagination.query" class="form-control inline" placeholder="@lang('general.search')">
        <div class="filter-buttons billing">
            <div class="text-right">
                <div class="btn-group">
                    <button type="button" class="btn" ng-click="toggleFilterBy({{ Billing::STATUS_PENDING}})"
                            tooltip-placement="bottom" ng-class="{'btn-primary':statusFilters.indexOf({{ Billing::STATUS_PENDING }}) == -1,'btn-warning':statusFilters.indexOf({{ Billing::STATUS_PENDING }}) != -1}">
                        <i class="fa fa-fw fa-clock-o"></i> @lang('billing.status.pending') @{{ billingIncomplete }}
                        <br> @{{ currency }} @{{ incompleteMoney }}
                    </button>
                    <button type="button" class="btn" ng-click="toggleFilterBy({{ Billing::STATUS_PROCESSING }})"
                            tooltip-placement="bottom" ng-class="{'btn-primary':statusFilters.indexOf({{ Billing::STATUS_PROCESSING }}) == -1,'btn-success':statusFilters.indexOf({{ Billing::STATUS_PROCESSING }}) != -1}">
                        <i class="fa fa-fw fa-cog"></i> @lang('billing.status.processing') @{{ billingProcessing }}
                        <br> @{{ currency }} @{{ processingMoney }}
                    </button>
                    <button type="button" class="btn" ng-click="toggleFilterBy({{ Billing::STATUS_SUCCESS }})"
                            tooltip-placement="bottom" ng-class="{'btn-primary':statusFilters.indexOf({{ Billing::STATUS_SUCCESS }}) == -1,'btn-success':statusFilters.indexOf({{ Billing::STATUS_SUCCESS }}) != -1}">
                        <i class="fa fa-fw fa-check"></i> @lang('billing.status.success') @{{ billingComplete }}
                        <br> @{{ currency }} @{{ completeMoney }}
                    </button>
                    <button type="button" class="btn" ng-click="toggleFilterBy({{ Billing::STATUS_ERROR }})"
                            tooltip-placement="bottom" ng-class="{'btn-primary':statusFilters.indexOf({{ Billing::STATUS_ERROR }}) == -1,'btn-danger':statusFilters.indexOf({{ Billing::STATUS_ERROR }}) != -1}">
                        <i class="fa fa-fw fa-thumbs-down"></i> @lang('billing.status.error') @{{ billingError }}
                        <br> @{{ currency }} @{{ errorMoney }}
                    </button>
                </div>
                @lang('contest.total')  @{{ pagination.total }}
                <span class="label label-success" style="font-size: 100%;" tooltip-placement="bottom" uib-tooltip="@lang('billing.total_billing')"> @{{ currency }} @{{ totalBilling }} </span>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <uib-pagination style="float: right; !important" boundary-links="true" total-items="pagination.total" items-per-page="pagination.perPage" max-size="7" rotate="false" ng-model="pagination.page" class="pagination-sm" ng-show="pagination.total/pagination.perPage > 1" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></uib-pagination><br>
        <br>
        <span style="float: right; !important" class="pagination-data" ng-show="dataLoaded">@{{((pagination.page-1) * pagination.perPage)+1}} @lang('general.to') @{{ pagination.shownMax }} @lang('general.of') @{{pagination.total}} @lang('general.results')</span>
    </div>
    </div>
    <div class="clearfix"></div>

    <table class="table table-striped table-hover table-condensed">
        <thead>
        <tr>
            <th></th>
            <th><a data-ng-click="changeOrder('id')">@lang('billing.id') <i ng-show="pagination.orderBy == 'id'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
            @if($admin)
            <th>@lang('general.user')</th>
            @endif
            <th>@lang('billing.method')</th>
            @if($admin)
            <th>@lang('billing.type_of_inscriptor')</th>
            @endif
            <th>@lang('billing.entries')</th>
            <th>@lang('billing.price')</th>
            <th><a data-ng-click="changeOrder('method')">@lang('billing.method') <i ng-show="pagination.orderBy == 'method'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i></a></th>
            <th>
                <a data-ng-click="changeOrder('created_at')">
                    @lang('billing.date') <i ng-show="pagination.orderBy == 'created_at'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i>
                </a><br>
                <a data-ng-click="changeOrder('paid_at')">
                    @lang('billing.paidat') <i ng-show="pagination.orderBy == 'paid_at'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i>
                </a>
            </th>
            <th>
                <a data-ng-click="changeOrder('status')">
                    @lang('billing.status') <i ng-show="pagination.orderBy == 'status'" ng-class="{'fa-chevron-down': !pagination.orderDir,'fa-chevron-up': pagination.orderDir}" class="fa"></i>
                </a>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr ng-repeat="bill in billings">
            <td></td>
            <td><a href="#{{{ !$admin ? '/payment/@{{bill.id}}' : '/admin/billing/bill/@{{bill.id}}' }}}">@{{bill.id |zpad:8}}</a></td>
            @if($admin)
                <td><a href="#{{{ '/admin/billing/bill/@{{bill.id}}' }}}">
                    <span user-card user-card-model="bill.user"></span>
                </a>
            </td>
            @endif
            <td>
            @{{ bill.method }}
            </td>
            @if($admin)
            <td>
                @{{ bill.inscription[0].name }}
            </td>
            @endif
            <td>
                <div data-ng-repeat="entryCat in bill.billing_entry_categories">
                    <span ng-switch="entryCat.status">
                        <span ng-switch-when="{{ Billing::STATUS_PENDING }}" class="text-warning">
                            <i class="fa fa-clock-o"></i>
                        </span>
                        <span ng-switch-when="{{ Billing::STATUS_PROCESSING }}" class="text-info">
                            <i class="fa fa-cog"></i>
                        </span>
                        <span ng-switch-when="{{ Billing::STATUS_SUCCESS }}" class="text-success">
                            <i class="fa fa-check"></i>
                        </span>
                        <span ng-switch-when="{{ Billing::STATUS_ERROR  }}" class="text-danger">
                            <i class="fa fa-thumbs-down"></i>
                        </span>
                    </span>
                    @if($contest->type == Contest::TYPE_CONTEST)
                        <a ng-if="!entryCat.entry_deleted" href="{{{ $superadmin ? '@{{contest.code}}/#/entry/@{{entryCat.entry.id}}' : '#/entry/@{{entryCat.entry.id}}' }}}"><span entry-card entry="entryCat.entry"></span></a>
                    @endif
                    @if($contest->type == Contest::TYPE_TICKET)
                        <a ng-if="!entryCat.entry_deleted" href="{{{ $superadmin ? '@{{contest.code}}/#/buyTickets/@{{entryCat.entry.id}}' : '#/buyTickets/@{{entryCat.entry.id}}' }}}"><span entry-card entry="entryCat.entry"></span></a>
                    @endif
                    <a ng-if="entryCat.entry_deleted" entry-card entry="entryCat.entry"></a>
                    <a ng-if="entryCat.entry_deleted" class="label label-danger label-as-badge" tooltip-placement="bottom" uib-tooltip="@lang('billing.deleted_tooltip')">@lang('billing.deleted')</a>
                    <div ng-include="'categoryList.html'" onload="category = getCategory(entryCat.category_id); first=true; editable=false;"></div>
                </div>
            </td>
            <td>
                @{{ bill.price }} @{{ bill.currency }}
            </td>
            <td>
                <span ng-switch="bill.method">
                    <span ng-switch-when="{{ Billing::METHOD_TRANSFER }}">
                        @lang('billing.transfer')
                    </span>
                    <span ng-switch-when="{{ Billing::METHOD_CHECK }}">
                        @lang('billing.check')
                    </span>
                    <span ng-switch-when="{{ Billing::METHOD_CREDITCARD }}">
                        @lang('billing.creditcard')
                    </span>
                    <span ng-switch-when="{{ Billing::METHOD_OTHER }}">
                        @lang('billing.other')
                    </span>
                    <span ng-switch-when="{{ Billing::METHOD_TCO  }}">
                        <i class="fa fa-credit-card"></i> @lang('billing.TCO')
                    </span>
                    <span ng-switch-when="{{ Billing::METHOD_MP  }}">
                        <i class="fa fa-credit-card"></i> @lang('billing.MercadoPago')
                    </span>
                </span>
            </td>
            <td>
                @{{ bill.created_at | amDateFormat:'DD/MM/YYYY' }}
                <span ng-if="bill.paid_at"><br>@lang('billing.paid_at') @{{ bill.paid_at | amDateFormat:'DD/MM/YYYY' }}</span>
            </td>
            <td class="">
                @if($admin)
                <fieldset  class="btn-group btn-group-sm" uib-dropdown ng-switch="bill.status" style="white-space: nowrap">
                    <button type="button" ng-switch-when="{{ Billing::STATUS_PENDING }}" class="btn btn-warning">
                        <i class="fa fa-clock-o"></i> @lang('billing.status.pending')
                    </button>
                    <button type="button" ng-switch-when="{{ Billing::STATUS_PROCESSING }}" class="btn btn-info">
                        <i class="fa fa-cog"></i> @lang('billing.status.processing')
                    </button>
                    <button type="button" ng-switch-when="{{ Billing::STATUS_SUCCESS }}" class="btn btn-success">
                        <i class="fa fa-check"></i> @lang('billing.status.success')
                    </button>
                    <button type="button" ng-switch-when="{{ Billing::STATUS_ERROR }}" class="btn btn-danger">
                        <i class="fa fa-thumbs-down"></i> @lang('billing.status.error')
                    </button>

                    <button type="button" class="btn btn-primary" uib-dropdown-toggle>
                        <i class="fa fa-cog"></i> <i class="fa fa-caret-down"></i>
                    </button>
                    <ul class="dropdown-menu" uib-dropdown-menu role="menu" aria-labelledby="split-button">
                        <li role="menuitem" class="pull-left"><a href="" ng-if="bill.status != {{ Billing::STATUS_PENDING }}" ng-click="changeStatus(bill, {{ Billing::STATUS_PENDING }})">
                                <span class="text-warning"><i class="fa fa-clock-o"></i> @lang('billing.pendingBill')</span>
                            </a></li>
                        <li role="menuitem" class="pull-left"><a href="" class="text-success" ng-if="bill.status != {{ Billing::STATUS_PROCESSING }}" ng-click="changeStatus(bill, {{ Billing::STATUS_PROCESSING }})">
                                <span class="text-success"><i class="fa fa-cog"></i> @lang('billing.processingBill')</span>
                            </a></li>
                        <li role="menuitem" class="pull-left"><a href="" class="text-success" ng-if="bill.status != {{ Billing::STATUS_SUCCESS }}" ng-click="changeStatus(bill, {{ Billing::STATUS_SUCCESS }})">
                                <span class="text-success"><i class="fa fa-thumbs-up"></i> @lang('billing.approveBill')</span>
                            </a></li>
                        <li role="menuitem" class="pull-left"><a href="" class="text-danger" ng-if="bill.status != {{ Billing::STATUS_ERROR }}" ng-click="changeStatus(bill, {{ Billing::STATUS_ERROR }})">
                                <span class="text-danger"><i class="fa fa-thumbs-down"></i> @lang('billing.rejectBill')</span>
                            </a></li>
                    </ul>
                </fieldset >
                @else
                <span ng-switch="bill.status">
                    <div ng-switch-when="{{ Billing::STATUS_PENDING }}">
                        <span class="text-warning">
                            <i class="fa fa-clock-o"></i> @lang('billing.status.pending')
                        </span>
                        <br>
                    </div>
                    <div ng-switch-when="{{ Billing::STATUS_SUCCESS }}">
                        <span class="text-success">
                            <i class="fa fa-check"></i> @lang('billing.status.success')
                        </span>
                    </div>
                    <div ng-switch-when="{{ Billing::STATUS_ERROR }}">
                        <span class="text-danger">
                            <i class="fa fa-thumbs-down"></i> @lang('billing.status.error')
                        </span>
                    </div>
                    <div ng-switch-when="{{ Billing::STATUS_PROCESSING }}">
                        <span class="text-info">
                            <i class="fa fa-cog"></i> @lang('billing.status.processing')
                        </span>
                    </div>
                </span>
                @endif
            </td>
        </tr>
        </tbody>
    </table>
    <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !contestForm.$dirty">
        <span ng-bind-html="flash"></span>
    </div>
@endsection