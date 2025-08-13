<span class="badge pull-right">@lang('billing.total') @{{ bill.currency }} @{{ bill.price }}</span>
<uib-alert type="@{{catMan.getBillingAlertType(bill.status)}}" class="alert-sm alert-inline">
    <i class="fa" ng-class="{'fa-clock-o': bill.status == {{{Billing::STATUS_PENDING}}}, 'fa-check': bill.status == {{{Billing::STATUS_SUCCESS}}}, 'fa-ban': bill.status == {{{Billing::STATUS_ERROR}}}, 'fa-cog': bill.status == {{{Billing::STATUS_PROCESSING}}}, 'fa-clock-o': bill.status == {{{Billing::STATUS_PARTIALLY_PAID}}}}"></i>
    @{{ {'{{{Billing::STATUS_PENDING}}}' : '@lang('billing.status.pending')','{{{Billing::STATUS_PARTIALLY_PAID}}}' : '@lang('billing.status.partiallypaid')','{{{Billing::STATUS_SUCCESS}}}' : '@lang('billing.status.success')','{{{Billing::STATUS_ERROR}}}' : '@lang('billing.status.error')','{{{Billing::STATUS_PROCESSING}}}' : '@lang('billing.status.processing')'} | echoswitch : bill.status }}
    @{{ bill.paid_at | amCalendar }}
</uib-alert>
@lang('billing.id') <a href="#/admin/billing/bill/@{{ bill.id }}">#@{{ bill.id | zpad:8 }}</a>
<div>
    <span ng-switch="bill.method">
        <span ng-switch-when="{{ Billing::METHOD_TRANSFER }}">
            @lang('billing.transfer')
        </span>
        <span ng-switch-when="{{ Billing::METHOD_CHECK }}">
            @lang('billing.check')
        </span>
        <span ng-switch-when="{{ Billing::METHOD_TCO  }}">
            <i class="fa fa-credit-card"></i> @lang('billing.TCO')
            <br>
            <span ng-if="!!bill.transaction_id">
                @lang('billing.transactionid'): @{{ bill.transaction_id }}
                <br>
            </span>
            <span ng-if="bill.payment_data">
                @lang('billing.paymentData')
                <ul class="list-group">
                    <li ng-repeat="(key,val) in bill.payment_data" class="list-group-item"><strong>@{{ key }}</strong> <div class="pull-right"> @{{ val }}</div></li>
                </ul>
            </span>
        </span>
        <span ng-switch-when="{{ Billing::METHOD_MP  }}">
            <i class="fa fa-credit-card"></i> @lang('billing.MercadoPago')
            <span ng-if="bill.status == {{Billing::STATUS_PENDING}}">
                <p>
                <a href="@{{bill.payment_data.url}}" target="_blank" class="btn btn-success btn-md">
                    <i class="fa fa-external-link"></i> @lang('billing.MercadoPago.continueprocess')
                </a>
                </p>
                <p ng-if="!!bill.transaction_id">
                    @lang('billing.transactionid'): @{{ bill.transaction_id }}
                    <br>
                </p>
            </span>

        </span>
        <span ng-switch-when="{{ Billing::METHOD_CLICPAGO  }}">
            <i class="fa fa-credit-card"></i> @lang('billing.ClicPago')
            <span ng-if="bill.status == {{Billing::STATUS_PENDING}}">
                <p>
                @lang('billing.ClicPago.continueprocess', [
                    'productlink' => "@{{bill.payment_data.productlink}}",
                    'backURL' => "@{{bill.payment_data.backURL}}",
                    'transactionBackURL' => "@{{bill.payment_data.transactionBackURL}}",
                    //'precioVariable' => "@{{bill.payment_data.precioVariable}}",
                    'codigoTransaccionAdherente' => "@{{bill.payment_data.codigoTransaccionAdherente}}"])
                </p>
                <p ng-if="!!bill.transaction_id">
                    @lang('billing.transactionid'): @{{ bill.transaction_id }}
                    <br>
                </p>
            </span>
        </span>

        <span ng-switch-when="{{ Billing::CUSTOM_API  }}">
            <i class="fa fa-credit-card"></i> @lang('billing.customApi')
            <span ng-if="bill.status == {{Billing::STATUS_PENDING}}">
                <p>
                    @lang('billing.customApi.continueprocess', [
                        'postURL' => "@{{bill.payment_data.postURL}}",
                        'billingIdName' => "@{{bill.payment_data.billingIdName}}",
                        'billingId' => "@{{bill.payment_data.billingId}}",
                        'numberEntriesName' => "@{{bill.payment_data.numberEntriesName}}",
                        'numberOfEntries' => "@{{bill.payment_data.numberOfEntries}}",
                        'price' => "@{{bill.payment_data.price}}",
                        'paymentStatus' => "@{{bill.payment_data.paymentStatus}}",
                        'paymentStatusName' => "@{{bill.payment_data.paymentStatusName}}"])
                </p>
            </span>
        </span>
        <span ng-if="bill.status != {{Billing::STATUS_SUCCESS}} && bill.status != {{Billing::UNPAID}} && (inscription.permits.admin || inscription.role == {{Inscription::OWNER}} || {{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}})">
            <br><br>
            <button type="button" class="btn btn-sm btn-danger" ng-click="cancelPayment(bill.id)"> @lang('contest.cancelPayment')</button>
            <br>
            <span>@lang('contest.cancelPaymentIrreversible')</span>
            <br><br>
        </span>
    </span>
</div>
<table class="table table-condensed">
    <thead>
    <tr>
        <th>@lang('contest.inscription')</th>
        <th>@lang('contest.billingCat')</th>
        <th class="text-right" style="white-space: nowrap;">@lang('contest.billingPrice') (@{{ contest.billing.mainCurrency }})</th>
    </tr>
    </thead>
    <tbody ng-repeat="cat in bill.billing_entry_categories">
        <td>#@{{ cat.entry_id }}</td>
        <td ng-include="'categoryList.html'" onload="category = getCategory(cat.category_id); first=true;"></td>
        <td class="pull-right"> <span class="stroke"> @{{ cat.original_price }} </span> @{{ inscriptionType.price ? inscriptionType.price : cat.price }}</td>
    </tbody>
</table>
<div class="clearfix"></div>