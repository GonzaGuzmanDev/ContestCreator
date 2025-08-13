@include('includes.header')
@include('contest.header', array('class' => 'small', 'banner' => ContestAsset::SMALL_BANNER_HTML))

<div class="container with-footer">
    <div class="row">
        <div class="col-sm-12 text-center">
            <h2>
                @{{ {'{{{Billing::STATUS_PENDING}}}' : '@lang('billing.MercadoPago.redirect-pending')','{{{Billing::STATUS_SUCCESS}}}' : '@lang('billing.MercadoPago.redirect-success')','{{{Billing::STATUS_ERROR}}}' : '@lang('billing.MercadoPago.redirect-error')','{{{Billing::STATUS_PROCESSING}}}' : '@lang('billing.MercadoPago.redirect-pending')'} | echoswitch : billingStatus }}
            </h2>
        </div>
        <div class="col-sm-6 col-sm-offset-3">
            <div class="well">
                <h4>
                    <entry-card entry="entry"></entry-card>
                </h4>
                @include('includes.entry-single-payment')
            </div>
        </div>
        <div class="col-sm-6 col-sm-offset-3 text-center">
            <a href="{{ url("/".$contest->code."/") }}#/entries/" class="btn btn-info btn-md">
                <i class="fa fa-ticket"></i> @lang('billing.MercadoPago.back')
            </a>
        </div>
    </div>
</div>
@include('includes.footer')