@extends('admin.contests.form', array('active' => 'payments'))
@section('form')
    <span ng-if="{{ $contest->wizard_status >= Contest::WIZARD_PAYMENT_FORM && $contest->wizard_status != Contest::WIZARD_FINISHED}}">
    @include('includes.wizardProgress', array('active' => Contest::WIZARD_PAYMENT_FORM))
        <br><div class="clearfix"></div><br>
    <h3 class="text-center"> @lang('contest.wizard.billingFormTitle') </h3>
    <br>
    <h4 class="text-center"> @lang('contest.wizard.hasBilling')
    <div class="btn-group">
        <button type="button" class="btn" ng-class="{'btn-default': wizardHasPayment == false, 'btn-success': wizardHasPayment == true}" ng-click="changeWizardHasPayment(true)">
            <span> Si</span>
        </button>
        <button type="button" class="btn" ng-class="{'btn-default': wizardHasPayment == true, 'btn-danger': wizardHasPayment == false}" ng-click="changeWizardHasPayment(false)">
            <span> No </span>
        </button>
    </div>
        <i class="fa fa-question-circle text-info" popover="@lang('contest.wizard.billingHelp')" popover-placement="right" popover-trigger="mouseenter"></i>
    </h4>
        <br><br>
        <h4 ng-if="wizardHasPayment == false" class="alert alert-info alert-sm alert-box text-center">
                @lang('contest.wizard.noBilling')
        </h4>
    </span>

    <span ng-hide="wizardHasPayment == false && {{isset($contest->wizard_status) ? ($contest->wizard_status != Contest::WIZARD_FINISHED ? 1 : 0) : 0}}">
    <h4 class="well well-sm">
        @lang('contest.paymentsPrice')
       {{-- @if(!$superadmin)
            <i class="fa fa-question-circle text-info" popover="@lang('contest.explain.paymentsPrice')" popover-placement="right" popover-trigger="mouseenter" class="btn btn-default"></i>
        @endif--}}
        <div class="clearfix"></div>
    </h4>
    <div ng-disabled="!contest.billing.methods.transfer.enabled && !contest.billing.methods.check.enabled && !contest.billing.methods.creditcard.enabled && !contest.billing.methods.other.enabled && !contest.billing.methods.TCO.enabled && !contest.billing.methods.MercadoPago.enabled && !contest.billing.methods.ClicPago.enabled && !contest.billing.methods.customApi.enabled">
        <div class="form-group">
            <label class="col-sm-2 control-label">
                @lang('billing.mainPrice')
            </label>
            <div class="col-sm-8 col-md-6 col-lg-4">
                <div class="form-inline">
                <span ng-if="showThis">
                    <input type="text" ng-model="contest.billing.mainPrice" placeholder="@lang('billing.fillMainPrice')" class="form-control input-group-inline text-right" size="6"
                           ng-disabled="!contest.billing.methods.transfer.enabled && !contest.billing.methods.check.enabled && !contest.billing.methods.creditcard.enabled && !contest.billing.methods.other.enabled && !contest.billing.methods.TCO.enabled && !contest.billing.methods.MercadoPago.enabled && !contest.billing.methods.ClicPago.enabled && !contest.billing.methods.customApi.enabled"/>
                    <select ng-model="contest.billing.mainCurrency" id="" class="form-control"
                            ng-disabled="!contest.billing.methods.transfer.enabled && !contest.billing.methods.check.enabled && !contest.billing.methods.creditcard.enabled && !contest.billing.methods.other.enabled && !contest.billing.methods.TCO.enabled && !contest.billing.methods.MercadoPago.enabled && !contest.billing.methods.ClicPago.enabled && !contest.billing.methods.customApi.enabled">
                        @foreach(Config::get('billing.currency') as $currency)
                            <option>{{$currency}}</option>
                        @endforeach
                    </select>
                </span>
                    <span ng-if="!showThis" class="form-control-static">
                    @{{ contest.billing.mainPrice || '-'}} @{{ contest.billing.mainCurrency }}
                </span>
                </div>
                <div ng-show="contestForm.billing.mainPrice.$error.required && !contestForm.billing.mainPrice.$pristine" class="help-inline text-danger form-control-static">@lang('contest.completemainPrice')</div>
                <div ng-show="contestForm.billing.mainCurrency.$error.required && !contestForm.billing.mainCurrency.$pristine" class="help-inline text-danger form-control-static">@lang('contest.completemainPriceCurrency')</div>
                <div ng-show="errors['billing.mainPrice']" class="help-inline text-danger form-control-static">@{{errors['billing.mainPrice'].toString()}}</div>
                <div ng-show="errors['billing.mainCurrency']" class="help-inline text-danger form-control-static">@{{errors['billing.mainCurrency'].toString()}}</div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                @lang('billing.forcePaymentOnCreate')
            </label>
            <div class="col-sm-8 col-md-6 col-lg-4">
                <div class="checkbox">
                    <label ng-if="showThis">
                        <input type="checkbox" ng-model="contest.billing.prepay" ng-checked="contest.billing.prepay == 1" ng-true-value="1" ng-false-value="0" id=""
                               ng-disabled="!contest.billing.methods.transfer.enabled && !contest.billing.methods.check.enabled && !contest.billing.methods.creditcard.enabled && !contest.billing.methods.other.enabled && !contest.billing.methods.TCO.enabled"
                        />
                        @lang('general.enable')
                    </label>
                    <span ng-if="!showThis" >
                    <i class="fa" ng-class="{'fa-check-square-o': contest.billing.prepay == 1,'fa-square-o': contest.billing.prepay != 1 }"></i>
                        @lang('general.enable')
                </span>
                    <br>
                    <span class="text-muted">@lang('billing.forcePaymentOnCreateExplain')</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                @lang('billing.discounts')
            </label>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <div class="form-inline">
                    <div ng-repeat="discount in contest.discounts track by $index" style="margin-bottom: 3px;">
                        <input type="hidden" ng-model="discount.id" ng-if="discount.id">
                        <div ng-if="!showThis">
                            @{{ discount.value+(discount.change == {{{Discount::CHANGE_PERCENTAGE}}} ? '%' : ' '+contest.billing.mainCurrency) }}
                            @lang('billing.discount.from_entries')
                            @{{ discount.min_entries }}
                            @lang('billing.discount.to_entries')
                            @{{ discount.max_entries }}
                            @lang('billing.discount.entries'):
                            @{{ discount.name }}
                            <br>
                            @lang('billing.discount.start_at')
                            @{{ discount.start_at || '-' }}
                            @lang('billing.discount.end_at')
                            @{{ discount.end_at || '-' }}
                        </div>
                        <div ng-if="showThis">
                            <input type="number" ng-model="discount.value" class="form-control text-right input-price" min="1" maxlength="3">
                            <select ng-model="discount.change" id="" class="form-control">
                                <option value="<?=Discount::CHANGE_PERCENTAGE?>">%</option>
                                <option value="<?=Discount::CHANGE_PRICE?>">@{{ contest.billing.mainCurrency }}</option>
                            </select>
                            @lang('billing.discount.from_entries')
                            <input type="number" ng-model="discount.min_entries" class="form-control input-group-inline" maxlength="3" max="100" min="1" />
                            @lang('billing.discount.to_entries')
                            <input type="number" ng-model="discount.max_entries" class="form-control input-group-inline" maxlength="3" max="1000" min="1" />
                            @lang('billing.discount.entries')
                            <input type="text" ng-model="discount.name" placeholder="@lang('billing.discount.name')"
                                   class="form-control input-group-inline" />
                            @include('includes.datetimepicker', array('field'=>'discount.start_at', 'placeholder' => Lang::get('billing.discount.start_at'), 'inline'=>true))
                            @include('includes.datetimepicker', array('field'=>'discount.end_at', 'placeholder' => Lang::get('billing.discount.end_at'), 'inline'=>true))
                            <button class="btn btn-xs btn-primary" ng-if="showThis" type="button" ng-click="removeDiscount($index)"><i class="fa fa-remove"></i></button>
                            <br>
                            <div ng-show="errors[$index+'.value']" class="text-danger form-control-static">
                                @{{errors[$index+'.value'].toString()}}
                            </div>
                            <div ng-show="errors[$index+'.change']" class="text-danger form-control-static">
                                @{{errors[$index+'.change'].toString()}}
                            </div>
                            <div ng-show="errors[$index+'.min_entries']" class="text-danger form-control-static">
                                @{{errors[$index+'.min_entries'].toString()}}
                            </div>
                            <div ng-show="errors[$index+'.max_entries']" class="text-danger form-control-static">
                                @{{errors[$index+'.max_entries'].toString()}}
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-xs btn-primary" ng-if="showThis" type="button" ng-click="addDiscount()"><i class="fa fa-plus"></i></button>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
    <hr class="fhr">

    <h4 class="well well-sm">
        @lang('contest.contestPaymentsMethods')
        {{--@if(!$superadmin)
            <i class="fa fa-question-circle text-info" popover="@lang('contest.explain.paymentsMethods')" popover-placement="right" popover-trigger="mouseenter" class="btn btn-default"></i>
        @endif--}}
        <div class="btn-group pull-right">
            <a href="" class="btn btn-sm btn-default btn-small" ng-repeat="(key,lang) in langs.All" ng-class="{'active':selectedLang == key}" ng-click="setLang(key)"><i class="flag-icon flag-icon-@{{key}}"></i> @{{lang}}</a>
        </div>
        <div class="clearfix"></div>
    </h4>
    <div class="form-group">
        <label class="col-sm-2 control-label">
            @lang('billing.MercadoPago')
        </label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            <div class="checkbox">
                <label ng-if="showThis">
                    <input type="checkbox" ng-model="contest.billing.methods.MercadoPago.enabled" ng-checked="contest.billing.methods.MercadoPago.enabled == 1" ng-true-value="1" ng-false-value="0" id=""/>
                    @lang('general.enable')
                </label>
                <span ng-if="!showThis" >
                <i class="fa" ng-class="{'fa-check-square-o': contest.billing.methods.MercadoPago.enabled == 1,'fa-square-o': contest.billing.methods.MercadoPago.enabled != 1 }"></i>
                    @lang('general.enable')
            </span>
            </div>
        </div>
    </div>
    <div ng-if="contest.billing.methods.MercadoPago.enabled">
        <div class="form-group">
            <label class="col-sm-2 control-label">
            </label>
            <div class="col-sm-8 col-md-6 col-lg-4">
                <a href='https://www.mercadopago.com/mla/account/credentials' target='_blank'><i class="fa fa-question-circle"></i>
                    @lang('billing.MercadoPago.wherearekeys')
                </a>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                @lang('billing.MercadoPago.accessToken')
            </label>
            <div class="col-sm-8 col-md-6 col-lg-4">
                <input type="text" ng-if="showThis" ng-model="contest.billing.methods.MercadoPago.data.accessToken" placeholder="@lang('billing.MercadoPago.tokenAccess')" class="form-control" />
                <div class="form-control-static" ng-if="!showThis">
                    @{{ contest.billing.methods.MercadoPago.data.accessToken | nl2br:true }}
                </div>
                <div ng-show="errors['billing.methods.MercadoPago.data.accessToken']" class="help-inline text-danger form-control-static">@{{errors['billing.methods.MercadoPago.data.accessToken'].toString()}}</div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                @lang('billing.MercadoPago.notificationsUrl')
            </label>
            <div class="col-sm-8 col-md-6 col-lg-4">
                <input type="text" class="form-control" readonly onclick="this.select();" value="<?=url("/api/contest/".$contest->code."/report-payment/")?>" />
                <i class="fa fa-info-circle"></i> @lang('billing.MercadoPago.notificationsUrlTodo')
            </div>
        </div>
    </div>
    <hr class="fhr">
    <div class="form-group">
        <label class="col-sm-2 control-label">
            @lang('billing.transfer')
        </label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            <div class="checkbox">
                <label ng-if="showThis">
                    <input type="checkbox" ng-model="contest.billing.methods.transfer.enabled" ng-checked="contest.billing.methods.transfer.enabled == 1" ng-true-value="1" ng-false-value="0" id=""/>
                    @lang('general.enable')
                </label>
                <span ng-if="!showThis" >
                <i class="fa" ng-class="{'fa-check-square-o': contest.billing.methods.transfer.enabled == 1,'fa-square-o': contest.billing.methods.transfer.enabled != 1 }"></i>
                    @lang('general.enable')
            </span>
            </div>
        </div>
    </div>
    <div class="form-group" ng-if="contest.billing.methods.transfer.enabled">
        <label class="col-sm-2 control-label">
            @lang('billing.transfer.data')
        </label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            <div class="form-control-static" ng-if="!showThis">
                @{{ contest.billing.methods.transfer.data | nl2br:true }}
            </div>
            <textarea ng-if="showThis && selectedLang == langs.Default" ng-model="contest.billing.methods.transfer.data" id="" cols="30" rows="3" placeholder="@lang('billing.filltransfer')" class="form-control"></textarea>
            <textarea ng-if="showThis && selectedLang == key" ng-repeat="(key,lang) in langs.Editables" ng-model="contest.billing.methods.transfer.trans[key]" id="" cols="30" rows="3" placeholder="@lang('billing.filltransfer')" class="form-control"></textarea>
            <div ng-show="contestForm.billing.methods.transfer.data.$error.required && !contestForm.billing.methods.transfer.data.$pristine" class="help-inline text-danger form-control-static">@lang('contest.completeBilling')</div>
            <div ng-show="errors['billing.methods.transfer.data']" class="help-inline text-danger form-control-static">@{{errors['billing.methods.transfer.data'].toString()}}</div>
        </div>
    </div>
    <hr class="fhr">
    <div class="form-group">
        <label class="col-sm-2 control-label">
            @lang('billing.check')
        </label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            <div class="checkbox">
                <label ng-if="showThis">
                    <input type="checkbox" ng-model="contest.billing.methods.check.enabled" ng-checked="contest.billing.methods.check.enabled == 1" ng-true-value="1" ng-false-value="0" id=""/>
                    @lang('general.enable')
                </label>
                <span ng-if="!showThis" >
                <i class="fa" ng-class="{'fa-check-square-o': contest.billing.methods.check.enabled == 1,'fa-square-o': contest.billing.methods.check.enabled != 1 }"></i>
                    @lang('general.enable')
            </span>
            </div>
        </div>
    </div>
    <div class="form-group" ng-if="contest.billing.methods.check.enabled">
        <label class="col-sm-2 control-label">
            @lang('billing.check.data')
        </label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            <div class="form-control-static" ng-if="!showThis">
                @{{ contest.billing.methods.check.data | nl2br:true }}
            </div>
            <textarea ng-if="showThis && selectedLang == langs.Default" ng-model="contest.billing.methods.check.data" id="" cols="30" rows="3" placeholder="@lang('billing.fillcheck')" class="form-control"></textarea>
            <textarea ng-if="showThis && selectedLang == key" ng-repeat="(key,lang) in langs.Editables" ng-model="contest.billing.methods.check.trans[key]" id="" cols="30" rows="3" placeholder="@lang('billing.fillcheck')" class="form-control"></textarea>
            <div ng-show="contestForm.billing.methods.check.data.$error.required && !contestForm.billing.methods.check.data.$pristine" class="help-inline text-danger form-control-static">@lang('contest.completeBilling')</div>
            <div ng-show="errors['billing.methods.check.data']" class="help-inline text-danger form-control-static">@{{errors['billing.methods.check.data'].toString()}}</div>
        </div>
    </div>
    <hr class="fhr">
    <div class="form-group">
        <label class="col-sm-2 control-label">
            @lang('billing.creditcard')
        </label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            <div class="checkbox">
                <label ng-if="showThis">
                    <input type="checkbox" ng-model="contest.billing.methods.creditcard.enabled" ng-checked="contest.billing.methods.creditcard.enabled == 1" ng-true-value="1" ng-false-value="0" id=""/>
                    @lang('general.enable')
                </label>
                <span ng-if="!showThis" >
                <i class="fa" ng-class="{'fa-check-square-o': contest.billing.methods.creditcard.enabled == 1,'fa-square-o': contest.billing.methods.creditcard.enabled != 1 }"></i>
                    @lang('general.enable')
            </span>
            </div>
        </div>
    </div>
    <div class="form-group" ng-if="contest.billing.methods.creditcard.enabled">
        <label class="col-sm-2 control-label">
            @lang('billing.creditcard.data')
        </label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            <div class="form-control-static" ng-if="!showThis">
                @{{ contest.billing.methods.creditcard.data | nl2br:true }}
            </div>
            <textarea ng-if="showThis && selectedLang == langs.Default" ng-model="contest.billing.methods.creditcard.data" id="" cols="30" rows="3" placeholder="@lang('billing.fillcreditcard')" class="form-control"></textarea>
            <textarea ng-if="showThis && selectedLang == key" ng-repeat="(key,lang) in langs.Editables" ng-model="contest.billing.methods.creditcard.trans[key]" id="" cols="30" rows="3" placeholder="@lang('billing.fillcreditcard')" class="form-control"></textarea>
            <div ng-show="contestForm.billing.methods.creditcard.data.$error.required && !contestForm.billing.methods.creditcard.data.$pristine" class="help-inline text-danger form-control-static">@lang('contest.completeBilling')</div>
            <div ng-show="errors['billing.methods.creditcard.data']" class="help-inline text-danger form-control-static">@{{errors['billing.methods.creditcard.data'].toString()}}</div>
        </div>
    </div>
    <hr class="fhr">
    <!--<div class="form-group">
        <label class="col-sm-2 control-label">
            @lang('billing.other')
        </label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            <div class="checkbox">
                <label ng-if="showThis">
                    <input type="checkbox" ng-model="contest.billing.methods.other.enabled" ng-checked="contest.billing.methods.other.enabled == 1" ng-true-value="1" ng-false-value="0" id=""/>
                    @lang('general.enable')
                </label>
                <span ng-if="!showThis" >
                <i class="fa" ng-class="{'fa-check-square-o': contest.billing.methods.other.enabled == 1,'fa-square-o': contest.billing.methods.other.enabled != 1 }"></i>
                    @lang('general.enable')
            </span>
            </div>
        </div>
    </div>
    <div class="form-group" ng-if="contest.billing.methods.other.enabled">
        <label class="col-sm-2 control-label">
            @lang('billing.other.data')
        </label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            <div class="form-control-static" ng-if="!showThis">
                @{{ contest.billing.methods.other.data | nl2br:true }}
            </div>
            <textarea ng-if="showThis && selectedLang == langs.default" ng-model="contest.billing.methods.other.data" id="" cols="30" rows="3" placeholder="@lang('billing.fillother')" class="form-control"></textarea>
            <textarea ng-if="showThis && selectedLang == key" ng-repeat="(key,lang) in langs.Editables" ng-model="contest.billing.methods.other.trans[key]" id="" cols="30" rows="3" placeholder="@lang('billing.fillother')" class="form-control"></textarea>
            <div ng-show="contestForm.billing.methods.other.data.$error.required && !contestForm.billing.methods.other.data.$pristine" class="help-inline text-danger form-control-static">@lang('contest.completeBilling')</div>
            <div ng-show="errors['billing.methods.other.data']" class="help-inline text-danger form-control-static">@{{errors['billing.methods.other.data'].toString()}}</div>
        </div>
    </div>-->
    <div class="form-group">
        <label class="col-sm-2 control-label">
            @lang('billing.stripe')
        </label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            <div class="checkbox">
                <label ng-if="showThis">
                    <input type="checkbox" ng-model="contest.billing.methods.stripe.enabled" ng-checked="contest.billing.methods.stripe.enabled == 1" ng-true-value="1" ng-false-value="0" id=""/>
                    @lang('general.enable')
                </label>
                <span ng-if="!showThis" >
                <i class="fa" ng-class="{'fa-check-square-o': contest.billing.methods.stripe.enabled == 1,'fa-square-o': contest.billing.methods.stripe.enabled != 1 }"></i>
                    @lang('general.enable')
            </span>
            </div>
        </div>
    </div>
    <div ng-if="contest.billing.methods.stripe.enabled">
        <div class="form-group">
            <label class="col-sm-2 control-label">
            </label>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                @lang('billing.stripe.accessToken')
            </label>
            <div class="col-sm-8 col-md-6 col-lg-4">
                <input type="text" ng-if="showThis" ng-model="contest.billing.methods.stripe.data.accessToken" placeholder="@lang('billing.stripe.tokenAccess')" class="form-control" />
                <div class="form-control-static" ng-if="!showThis">
                    @{{ contest.billing.methods.stripe.data.accessToken | nl2br:true }}
                </div>
                <div ng-show="contestForm.billing.methods.stripe.data.accessToken.$error.required && !contestForm.billing.stripe.data.accessToken.$pristine" class="help-inline text-danger form-control-static">@lang('contest.errorStripeSetup')</div>
                <div ng-show="errors['billing.methods.stripe.data.accessToken']" class="help-inline text-danger form-control-static">@{{errors['billing.methods.stripe.data.accessToken'].toString()}}</div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                @lang('billing.stripe.notificationsUrl')
            </label>
            <div class="col-sm-8 col-md-6 col-lg-4">
                <input type="text" class="form-control" readonly onclick="this.select();" value="<?=url("/api/contest/".$contest->code."/report-payment/stripe")?>" />
                <i class="fa fa-info-circle"></i> @lang('billing.stripe.notificationsUrlTodo')
            </div>
        </div>
    </div>

    <hr class="fhr">

    <div class="form-group" ng-hide="true">
        <label class="col-sm-2 control-label">
            @lang('billing.customApi')
        </label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            <div class="checkbox">
                <label ng-if="showThis">
                    <input type="checkbox" ng-model="contest.billing.methods.customApi.enabled" ng-checked="contest.billing.methods.customApi.enabled == 1" ng-true-value="1" ng-false-value="0" id=""/>
                    @lang('general.enable')
                </label>
                <span ng-if="!showThis" >
                <i class="fa" ng-class="{'fa-check-square-o': contest.billing.methods.customApi.enabled == 1,'fa-square-o': contest.billing.methods.customApi.enabled != 1 }"></i>
                    @lang('general.enable')
            </span>
            </div>
        </div>
    </div>
    <div ng-if="contest.billing.methods.customApi.enabled" ng-hide="true">
        <div class="form-group">
            <label class="col-sm-2 control-label">
            </label>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                @lang('billing.customApi.postURL')
            </label>
            <div class="col-sm-8 col-md-6 col-lg-4">
                <input type="text" ng-if="showThis" ng-model="contest.billing.methods.customApi.data.postURL" placeholder="@lang('billing.customApi.fillPostURL')" class="form-control" />
                <div class="form-control-static" ng-if="!showThis">
                    @{{ contest.billing.methods.customApi.data.postURL | nl2br:true }}
                </div>
                <div ng-show="contestForm.billing.methods.customApi.data.postURL.$error.required && !contestForm.billing.customApi.data.postURL.$pristine" class="help-inline text-danger form-control-static">@lang('contest.customApi')</div>
                <div ng-show="errors['billing.methods.customApi.data.postURL']" class="help-inline text-danger form-control-static">@{{errors['billing.methods.customApi.data.postURL'].toString()}}</div>
            </div>
        </div>
        <h4>@lang('billing.customApi.params')</h4>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                @lang('billing.customApi.billingId')
            </label>
            <div class="col-sm-8 col-md-6 col-lg-4">
                <input type="text" ng-if="showThis" ng-model="contest.billing.methods.customApi.data.billingId" placeholder="@lang('billing.customApi.fillbillingId')" class="form-control" />
                <div class="form-control-static" ng-if="!showThis">
                    @{{ contest.billing.methods.customApi.data.billingId | nl2br:true }}
                </div>
                <div ng-show="contestForm.billing.methods.customApi.data.billingId.$error.required && !contestForm.billing.customApi.data.billingId.$pristine" class="help-inline text-danger form-control-static">@lang('contest.customApi')</div>
                <div ng-show="errors['billing.methods.customApi.data.billingId']" class="help-inline text-danger form-control-static">@{{errors['billing.methods.customApi.data.billingId'].toString()}}</div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                @lang('billing.customApi.numberOfEntries')
            </label>
            <div class="col-sm-8 col-md-6 col-lg-4">
                <input type="text" ng-if="showThis" ng-model="contest.billing.methods.customApi.data.numberOfEntries" placeholder="@lang('billing.customApi.fillnumberOfEntries')" class="form-control" />
                <div class="form-control-static" ng-if="!showThis">
                    @{{ contest.billing.methods.customApi.data.numberOfEntries | nl2br:true }}
                </div>
                <div ng-show="contestForm.billing.methods.customApi.data.numberOfEntries.$error.required && !contestForm.billing.customApi.data.numberOfEntries.$pristine" class="help-inline text-danger form-control-static">@lang('contest.customApi')</div>
                <div ng-show="errors['billing.methods.customApi.data.numberOfEntries']" class="help-inline text-danger form-control-static">@{{errors['billing.methods.customApi.data.numberOfEntries'].toString()}}</div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">
                @lang('billing.customApi.paymentStatus')
            </label>
            <div class="col-sm-8 col-md-6 col-lg-4">
                <input type="text" ng-if="showThis" ng-model="contest.billing.methods.customApi.data.paymentStatus" placeholder="@lang('billing.customApi.fillpaymentStatus')" class="form-control" />
                <div class="form-control-static" ng-if="!showThis">
                    @{{ contest.billing.methods.customApi.data.paymentStatus | nl2br:true }}
                </div>
                <div ng-show="contestForm.billing.methods.customApi.data.paymentStatus.$error.required && !contestForm.billing.customApi.data.paymentStatus.$pristine" class="help-inline text-danger form-control-static">@lang('contest.customApi')</div>
                <div ng-show="errors['billing.methods.customApi.data.paymentStatus']" class="help-inline text-danger form-control-static">@{{errors['billing.methods.customApi.data.paymentStatus'].toString()}}</div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">
                @lang('billing.customApi.price')
            </label>
            <div class="col-sm-8 col-md-6 col-lg-4">
                <input type="text" ng-if="showThis" ng-model="contest.billing.methods.customApi.data.price" placeholder="@lang('billing.customApi.fillprice')" class="form-control" />
                <div class="form-control-static" ng-if="!showThis">
                    @{{ contest.billing.methods.customApi.data.price | nl2br:true }}
                </div>
                <div ng-show="contestForm.billing.methods.customApi.data.price.$error.required && !contestForm.billing.customApi.data.price.$pristine" class="help-inline text-danger form-control-static">@lang('contest.customApi')</div>
                <div ng-show="errors['billing.methods.customApi.data.price']" class="help-inline text-danger form-control-static">@{{errors['billing.methods.customApi.data.price'].toString()}}</div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">
                @lang('billing.notificationsUrl')
            </label>
            <div class="col-sm-8 col-md-6 col-lg-4">
                <input type="text" class="form-control" readonly onclick="this.select();" value="<?=url("/api/contest/".$contest->code."/report-payment/")?>" />
                <div ng-show="contestForm.billing.methods.customApi.data.clientSecret.$error.required && !contestForm.billing.customApi.data.clientSecret.$pristine" class="help-inline text-danger form-control-static">@lang('contest.customApi')</div>
                <div ng-show="errors['billing.methods.customApi.data.clientSecret']" class="help-inline text-danger form-control-static">@{{errors['billing.methods.customApi.data.clientSecret'].toString()}}</div>
            </div>
        </div>
    </div>
    </span>
@endsection

