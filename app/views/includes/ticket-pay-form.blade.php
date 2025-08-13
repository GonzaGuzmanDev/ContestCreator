<h4 class="well well-sm">@lang('oxoTicket.ticketTitle')</h4>
@include('includes.entry-payments')
<div>
    <table class="table table-condensed">
        <thead>
        <tr class="text-left">
            <th>@lang('oxoTicket.ticketName')</th>
            <th>@lang('oxoTicket.quantity')</th>
            <th style="white-space: nowrap;">@lang('contest.billingPrice') (@{{ contest.billing.mainCurrency }})</th>
        </tr>
        </thead>
        <tbody class="text-left">
            <tr ng-repeat="ticket in tickets">
                <td> @{{ ticket.name }}</td>
                <td> @{{ ticket.totalTickets }}</td>
                <td> @{{ ticket.totalPriceTickets }} </td>
            </tr>
        </tbody>
        <tfoot class="billing-total">
        <tr>
            <td>@lang('contest.billingTotal')</td>
            <td> @{{ totalTickets }} </td>
            <td> @{{ total }} </td>
        </tr>
        </tfoot>
    </table>

    <span ng-hide="transaction">
    <h4>@lang('contest.billingMethod')</h4>
    <select class="form-control" required="required" ng-model="payment.method" id="" ng-disabled="sending" >
        <option></option>
        <option ng-repeat="(code, data) in contest.billing.methods" ng-value="code">
            @{{ { '{{{Billing::METHOD_TRANSFER}}}' : '@lang('billing.transfer')', '{{{Billing::METHOD_CHECK}}}' : '@lang('billing.check')', '{{{Billing::METHOD_TCO}}}' : '@lang('billing.TCO')', '{{{Billing::METHOD_CREDITCARD}}}' : '@lang('billing.creditcard')', '{{{Billing::METHOD_OTHER}}}' : '@lang('billing.other')', '{{{Billing::METHOD_MP}}}' : '@lang('billing.MercadoPago')','{{{Billing::CUSTOM_API}}}' : '@lang('billing.customApi')' } | echoswitch:code}}
        </option>
    </select>
    <div class="clearfix"></div>
    <div ng-if="payment.method == '{{Billing::METHOD_TRANSFER}}'">
        <br>
        <div ng-if="language == langDefault" class="well well-sm" ng-bind-html="contest.billing.methods[payment.method]['data'] | nl2br:true "></div>
        <div ng-if="language != langDefault" class="well well-sm" ng-bind-html="contest.billing.methods[payment.method]['trans'][language] | nl2br:true "></div>
    </div>
    <div ng-if="payment.method == '{{Billing::METHOD_CHECK}}'">
        <br>
        <div ng-if="language == langDefault" class="well well-sm" ng-bind-html="contest.billing.methods[payment.method]['data'] | nl2br:true "></div>
        <div ng-if="language != langDefault" class="well well-sm" ng-bind-html="contest.billing.methods[payment.method]['trans'][language] | nl2br:true "></div>
    </div>
    <div ng-if="payment.method == '{{Billing::METHOD_CREDITCARD}}'">
        <br>
        <div ng-if="language == langDefault" class="well well-sm" ng-bind-html="contest.billing.methods[payment.method]['data'] | nl2br:true "></div>
        <div ng-if="language != langDefault" class="well well-sm" ng-bind-html="contest.billing.methods[payment.method]['trans'][language] | nl2br:true "></div>
    </div>
    <div ng-if="payment.method == '{{Billing::METHOD_OTHER}}'">
        <br>
        <div ng-if="language == langDefault" class="well well-sm" ng-bind-html="contest.billing.methods[payment.method]['data'] | nl2br:true "></div>
        <div ng-if="language != langDefault" class="well well-sm" ng-bind-html="contest.billing.methods[payment.method]['trans'][language] | nl2br:true "></div>
    </div>
    <div ng-if="payment.method == '{{Billing::METHOD_MP}}'">
        <br>
        <div class="well well-sm">@lang('billing.MercadoPago.explain')</div>
    </div>

    <div ng-if="payment.method == '{{Billing::METHOD_STRIPE}}'">
        
    </div>

    <div ng-if="payment.method == '{{Billing::CUSTOM_API}}'">
        <br>
        <div class="well well-sm">@lang('billing.customApi.explain')</div>
    </div>
    <div ng-if="payment.method == '{{Billing::METHOD_TCO}}'">
        <br>
        <div class="form-group">
            <label for="ccNo" class="col-sm-5 control-label">@lang('billing.TCO.ccNo')</label>
            <div class="col-sm-7">
                <input type="text" value="" ng-model="payment.TCO.ccNo" autocomplete="off" ng-disabled="sending" required class="form-control" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-5 control-label">
                <span>@lang('billing.TCO.expirationdate')</span>
            </label>
            <div class="col-sm-7">
                <div class="form-inline">
                    <input type="text" ng-model="payment.TCO.expMonth" size="2" maxlength="2" ng-disabled="sending" required value="" class="form-control" />
                    <span> / </span>
                    <input type="text" ng-model="payment.TCO.expYear" size="4" maxlength="4" ng-disabled="sending" required value="" class="form-control" />
                    @lang('billing.TCO.expirationformat')
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-5 control-label">
                <span>@lang('billing.TCO.cvc')</span>
            </label>
            <div class="col-sm-7">
                <input type="text" value="" ng-model="payment.TCO.cvv" autocomplete="off" ng-disabled="sending" required class="form-control" />
            </div>
        </div>
    </div>
    </span>
</div>