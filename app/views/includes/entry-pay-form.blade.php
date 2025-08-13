<h4 class="well well-sm">@lang('contest.billingTitle')</h4>
@include('includes.entry-payments')
@lang('general.reference')
<h4 style="color:black;" class="badge payed"> @lang('billing.payed') @{{ countPayed }} </h4>
<h4 class="badge"> @lang('billing.notPayed') @{{ notPayed }} </h4>
<br>

<div>
    <table class="table table-condensed" ng-init="total = 0;">
        <thead>
        <tr>
            <th>@lang('contest.inscription')</th>
            <th>@lang('contest.billingCat')</th>
            <th class="text-right" style="white-space: nowrap;">@lang('contest.billingPrice') (@{{ contest.billing.mainCurrency }})</th>
        </tr>
        </thead>
        <tbody ng-repeat="ent in entry" ng-if="ent.mustPay">
            <tr ng-repeat="entryCategory in ent.categories_id track by $index" data-ng-class="{'payed' : !catMan.mustPayCategory(ent.length ? bulkBillings : ent, catid)}">
                <td entry-card entry="ent" fields="fields"></td>
                <td ng-include="'categoryList.html'" onload="category = getCategory(entryCategory); first=true;"></td>
                <td class="text-right">
                    <span ng-if="!contest.billing.discounts"> @{{ inscriptionType.price ? inscriptionType.price : catMan.GetPrice(category) }} </span>
                    <span ng-if="contest.billing.discounts && (entry.length == 1 || !hasDiscount(contest.billing.discounts))"> @{{ inscriptionType.price ? inscriptionType.price : catMan.GetPrice(category) }} </span>
                    <span ng-if="contest.billing.discounts && hasDiscount(contest.billing.discounts) && ent.mustPay == true">
                        <span class="stroke">@{{ inscriptionType.price ? inscriptionType.price : catMan.GetPrice(category) }}</span>
                        @{{ discountValue }}
                    </span>
                </td>
            </tr>
        </tbody>
        <tfoot class="billing-total">
        <tr>
            <td>@lang('contest.billingTotal')</td>
            <td><span ng-if="discountValue > 0"> Descuento </span></td>
            <td class="text-right">
                <span ng-if="discountValue == 0">
                    @{{ inscriptionType.price ? inscriptionType.price : priceTotal }} (@{{ contest.billing.mainCurrency }})
                </span>
                <span ng-if="discountValue > 0">
                    @{{ discountValue*notPayed }} (@{{ contest.billing.mainCurrency }})
                </span>
            </td>
        </tr>
        </tfoot>
    </table>

    <span ng-if="onlyPay" ng-include="'paymentMethod.html'">
    </span>
    @if(isset($contest->billing['prepay']) && $contest->billing['prepay'])
        <span ng-include="'paymentMethod.html'">
        </span>
    @endif
</div>