<h4 ng-if="entry.length ? ent.billings.length : entry.billings.length">@lang('billing.paymentsDone')</h4>
<div ng-repeat="bill in entry.length ? ent.billings : entry.billings | unique : 'id'" type="@{{catMan.getBillingAlertType(bill.status)}}" class="well well-sm bill">
    @include('includes.entry-single-payment')
</div>