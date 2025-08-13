@extends('admin.contests.form', array('active' => $admin ? 'billing':'payments'))
@section('form')
    <script type="text/ng-template" id="bill-statusForm.html">
        @include('admin.contests.bill-statusForm')
    </script>
    <h4 class="well well-sm">
        <a href="#{{{ !$admin ? '/payments' : '/admin/billing' }}}" class="" role="button"><i class="fa fa-arrow-left"></i>
            @if($admin)
                @lang('contest.billing')
            @else
                @lang('contest.tab.payments')
            @endif
        </a>
        /
        @lang('contest.bill')
    </h4>
    <div class="form-group">
        <label class="col-sm-2 control-label">
            <p class="text-right">@lang('billing.id')</p>
        </label>
        <div class="col-sm-2 col-md-2">
            <div class="form-control-static">@{{ bill.id | zpad:8 }}</div>
        </div>
        <div class="col-sm-6 col-md-6 float-left" ng-if="bill.entry_deleted">
            <span class="label label-warning" style="font-size:130%;"> @lang('billing.entry_deleted') </span>
        </div>
    </div>
    <div class="form-group" ng-class="{error: errors.name}">
        <label class="col-sm-2 control-label">@lang('general.user')</label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            <div class="form-control-static">
                <span user-card user-card-model="bill.user" ng-click="showForm(bill.user)"></span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">
            <p class="text-right">@lang('billing.price')</p>
        </label>
        <div class="col-sm-2 col-md-2">
            <div class="input-group" ng-if="showThis">
                <input type="text" ng-model="bill.price" class="form-control text-right input-price">
                <div class="input-group-addon price-currency">@{{ bill.currency }}</div>
            </div>
            <div ng-if="!showThis" class="form-control-static">@{{ bill.price }} @{{ bill.currency }}</div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">
            <p class="text-right">@lang('billing.method')</p>
        </label>
        <div class="col-sm-12 col-md-10">
            <div class="form-control-static">
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
                    <span ng-switch-when="{{ Billing::METHOD_CLICPAGO  }}">
                        <i class="fa fa-credit-card"></i> @lang('billing.ClicPago')
                    </span>
                </span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">
            <p class="text-right">@lang('billing.status')</p>
        </label>
        <div class="col-sm-12 col-md-10">
            <div class="form-control-static">
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
            </div>
        </div>
    </div>
    @if($admin)
    <div class="form-group">
        <div class="col-sm-2 control-label">
        </div>
        <div class="col-sm-12 col-md-10">
            <button class="btn btn-warning" ng-if="bill.status != {{ Billing::STATUS_PENDING }}" ng-click="changeStatus({{ Billing::STATUS_PENDING }})">
                <i class="fa fa-clock-o"></i> @lang('billing.pendingBill')
            </button>
            <button class="btn btn-info" ng-if="newStatus == {{ Billing::STATUS_PROCESSING }}" ng-click="changeStatus({{ Billing::STATUS_PROCESSING }})">
                <i class="fa fa-cog"></i> @lang('billing.processingBill')
            </button>
            <button class="btn btn-success" ng-if="bill.status != {{ Billing::STATUS_SUCCESS }}" ng-click="changeStatus({{ Billing::STATUS_SUCCESS }})">
                <i class="fa fa-thumbs-up"></i> @lang('billing.approveBill')
            </button>
            <button class="btn btn-danger" ng-if="bill.status != {{ Billing::STATUS_ERROR }}" ng-click="changeStatus({{ Billing::STATUS_ERROR }})">
                <i class="fa fa-thumbs-down"></i> @lang('billing.rejectBill')
            </button>
        </div>
    </div>
    @endif
    <div class="form-group">
        <label class="col-sm-2 control-label">
            <p class="text-right">@lang('billing.entries')</p>
        </label>
        <div class="col-sm-12 col-md-6">
            <table class="table">
                <tr>
                    <th>@lang('billing.entryCat')</th>
                    <th class="text-right">@lang('billing.price')</th>
                </tr>
            <tr data-ng-repeat="entryCat in bill.billing_entry_categories">
                <td class="">
                    @if($contest->type == Contest::TYPE_CONTEST)
                        <a href="{{{ $superadmin ? '@{{contest.code}}/#/entry/@{{entryCat.entry.id}}' : '#/entry/@{{entryCat.entry.id}}' }}}"><span entry-card entry="entryCat.entry"></span></a>
                    @endif
                    @if($contest->type == Contest::TYPE_TICKET)
                        <a href="{{{ $superadmin ? '@{{contest.code}}/#/buyTickets/@{{entryCat.entry.id}}' : '#/buyTickets/@{{entryCat.entry.id}}' }}}"><span entry-card entry="entryCat.entry"></span></a>
                    @endif
                    <div ng-include="'categoryList.html'" onload="category = getCategory(entryCat.category_id); first=true; editable=false;"></div>
                </td>
                <td class="text-right" style="max-width: 150px;">
                    <div class="input-group" ng-if="showThis">
                        <input type="text" ng-model="entryCat.price" class="form-control text-right input-price">
                        <div class="input-group-addon price-currency">@{{ bill.currency }}</div>
                    </div>
                    <div ng-if="!showThis">
                    @{{ entryCat.price }}  @{{ bill.currency }}
                    </div>
                </td>
                <td>
                    <a ng-if="entryCat.entry_deleted" class="label label-danger label-as-badge" tooltip-placement="bottom" uib-tooltip="@lang('billing.deleted_tooltip')">@lang('billing.deleted')</a>
                </td>
            </tr>
            </table>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">
            <p class="text-right">@lang('billing.transactionid')</p>
        </label>
        <div class="col-sm-12 col-md-10">
            <div class="form-control-static">@{{ bill.transaction_id }}</div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">
            <p class="text-right">@lang('billing.paymentData')</p>
        </label>
        <div class="col-sm-12 col-md-9">
            <div class="form-control-static">
                <ul class="list-group">
                    <li ng-repeat="(key,val) in bill.payment_data" class="list-group-item">
                        <strong>@{{ key }}</strong> <div class="pull-right" style="word-break: break-all"> @{{ val }}</div>
                        <div class="clearfix"></div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">
            <p class="text-right">@lang('billing.comments')</p>
        </label>
        <div class="col-sm-12 col-md-10">
            <div class="form-control-static">@{{ bill.comments || '@lang('billing.nocomments')' | nl2br:true}}</div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">
            <p class="text-right">@lang('billing.description')</p>
        </label>
        <div class="col-sm-12 col-md-10">
            <div class="form-control-static">@{{ bill.description || '@lang('billing.nodescription')' | nl2br:true}}</div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">
            <p class="text-right">@lang('billing.date')</p>
        </label>
        <div class="col-sm-12 col-md-10">
            <div class="form-control-static">@{{ bill.created_at | amDateFormat:'DD/MM/YYYY  HH:mm:ss' }}</div>
        </div>
    </div>
@endsection