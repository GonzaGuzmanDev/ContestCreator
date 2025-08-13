<span ng-if="inscription.permits.admin || sifter || inscription.role == {{Inscription::OWNER}} ||
            (inscription.role == {{Inscription::INSCRIPTOR}} && inTimeForInscriptions()) ||
            {{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}}">
    <button class="btn btn-default" ng-class="{'btn-sm': !!small}" ng-if="unreadMessages(entry.entry_log) != -1 || viewer" ng-click="showLog(entry)" uib-tooltip="@lang('contest.entryLog')" tooltip-placement="top">
        <i class="fa fa-commenting-o"></i>
        <span ng-if="unreadMessages(entry.entry_log) > 0" class="label label-danger label-as-badge">
                @{{ result }}
        </span>
    </button>
    @if(isset($contest->type) && $contest->type == Contest::TYPE_CONTEST)
    <button class="btn btn-success" ng-if="entry.status == {{Entry::ERROR}}"  ng-click="changeStatus(entry, {{Entry::FINALIZE}});$event.stopPropagation();"
            tooltip-placement="bottom" ng-class="{'btn-sm': !!small}" uib-tooltip="@lang('contest.finalizeEntry')" tooltip-enable="!labels">
        <i class="fa fa-check"></i>
        <span ng-if="!!labels">@lang('contest.resubmitEntry')</span>
    </button>
    @endif
    <button class="btn btn-success" ng-if="entry.status == {{Entry::COMPLETE}} && !entry.errorInFiles"  ng-click="changeStatus(entry, {{Entry::FINALIZE}});$event.stopPropagation();"
            tooltip-placement="bottom" ng-class="{'btn-sm': !!small}" uib-tooltip="@lang('contest.finalizeEntry')" tooltip-enable="!labels">
        <i class="fa fa-check"></i>
        <span ng-if="!!labels">@lang('contest.finalizeEntry')</span>
    </button>
    <span uib-tooltip="@lang('general.filesStatus.fileWithError')" tooltip-placement="top" ng-if="entry.status == {{Entry::COMPLETE}} && entry.errorInFiles">
        <button class="btn btn-default"  ng-click="" ng-disabled="true" ng-class="{'btn-sm': !!small}" tooltip-enable="!labels">
            <i class="fa fa-check"></i>
            <span ng-if="!!labels">@lang('contest.finalizeEntry')</span>
        </button>
    </span>
    @if(isset($contest->block_finished_entry) && $contest->block_finished_entry == 0)
        <button class="btn btn-default" ng-if="entry.status == {{Entry::FINALIZE}}"  ng-click="changeStatus(entry, {{Entry::COMPLETE}});$event.stopPropagation();"
                tooltip-placement="bottom" ng-class="{'btn-sm': !!small}" uib-tooltip="@lang('contest.noFinalizeEntry')" tooltip-enable="!labels">
            <i class="fa fa-hand-stop-o"></i>
            <span ng-if="!!labels">@lang('contest.noFinalizeEntry')</span>
        </button>
    @endif
</span>
<span ng-if="(inscription.permits.admin || sifter || inscription.role == {{Inscription::OWNER}} || {{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}}) && entry.status != {{Entry::COMPLETE}} && entry.status != {{Entry::INCOMPLETE}}">
    @if(isset($contest->type) && $contest->type == Contest::TYPE_CONTEST)
    <button class="btn btn-default" ng-class="{'btn-sm': !!small}" ng-if="entry.status == {{Entry::APPROVE}}" ng-click="changeStatus(entry, {{Entry::FINALIZE}});$event.stopPropagation();"
            tooltip-placement="bottom" uib-tooltip="@lang('contest.adminNotOk')" tooltip-enable="!labels">
        <i class="fa fa-hand-stop-o"></i>
        <span ng-if="!!labels">@lang('contest.adminNotOk')</span>
    </button>
    @endif
    @if(isset($contest->block_finished_entry) && $contest->block_finished_entry == 1)
        <button class="btn btn-default" ng-if="entry.status == {{Entry::FINALIZE}}"  ng-click="checkEntry(entry.id, entry);$event.stopPropagation();"
                tooltip-placement="bottom" ng-class="{'btn-sm': !!small, 'btn-success': entry.check == true, 'btn-danger': !entry.check}" uib-tooltip="@lang('contest.checked')" tooltip-enable="!labels">
        <i ng-if="!entry.check" class="fa fa-close"></i>
        <i ng-if="entry.check" class="fa fa-check"></i>
        <span ng-if="!!labels">
            <span ng-if="!entry.check">
                NO
            </span>
            @lang('contest.check')
        </span>
    </button>
    @endif
    <button class="btn btn-default" ng-class="{'btn-sm': !!small}" ng-if="entry.status != {{Entry::APPROVE}}" ng-click="changeStatus(entry, {{Entry::APPROVE}});$event.stopPropagation();"
            tooltip-placement="bottom" uib-tooltip="@lang('contest.adminOK')" tooltip-enable="!labels">
        <i class="fa fa-thumbs-up"></i>
        <span ng-if="!!labels">@lang('contest.adminOK')</span>
    </button>
    <button class="btn btn-default" ng-class="{'btn-sm': !!small}" ng-if="entry.status != {{Entry::ERROR}}" ng-click="changeStatus(entry, {{Entry::ERROR}});$event.stopPropagation();"
            tooltip-placement="bottom" uib-tooltip="@lang('contest.adminError')" tooltip-enable="!labels">
        <i class="fa fa-thumbs-down"></i>
        <span ng-if="!!labels">@lang('contest.adminError')</span>
    </button>
</span>
@if(isset($contest->type) && $contest->type == Contest::TYPE_CONTEST)
<div ng-if="isPayable(entry) == 1 && contest.billing.mainPrice && ((inscription.role == {{Inscription::INSCRIPTOR}} && inTimeForInscriptions() && (entry.status != {{Entry::INCOMPLETE}} || contest.billing.prepay == 1))
    || {{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}} || inscription.permits.billing == true || inscription.permits.admin || inscription.role == {{Inscription::OWNER}})">
    <button class="btn btn-primary" ng-class="{'btn-sm': !!small}" ng-if="catMan.mustPayEntry(entry) && catMan.getBillingsStatus(entry) != {{Billing::STATUS_ERROR}}" ng-click="changeStatus(entry);$event.stopPropagation();"
            tooltip-placement="bottom" uib-tooltip="@lang('contest.payEntry')" tooltip-enable="!labels">
        <i class="fa fa-money"></i>
        <span ng-if="!!labels">@lang('contest.payEntry')</span>
        <span class="badge">@{{ contest.billing.mainCurrency }} @{{ inscriptionType.price ? inscriptionType.price : catMan.getDuePayment(entry) }}</span>
    </button>
    <button class="btn btn-@{{catMan.getBillingsAlertType(entry)}}" ng-class="{'btn-sm': !!small}" ng-if="(!catMan.mustPayEntry(entry) || catMan.getBillingsStatus(entry) == {{Billing::STATUS_ERROR}}) && entry.billings.length > 0" ng-click="showPayments(entry);$event.stopPropagation();" ng-init="bStatus = catMan.getBillingsStatus(entry)">
        <i class="fa fa-money"></i>
        @{{ {'{{{Billing::STATUS_PENDING}}}' : '@lang('billing.status.pending')','{{{Billing::STATUS_SUCCESS}}}' : '@lang('billing.status.success')','{{{Billing::STATUS_PARTIALLY_PAID}}}' : '@lang('billing.status.partiallypaid')','{{{Billing::STATUS_ERROR}}}' : '@lang('billing.status.error')','{{{Billing::STATUS_PROCESSING}}}' : '@lang('billing.status.processing')'} | echoswitch : catMan.getBillingsStatus(entry) }}
        <span class="badge">@{{ entry.billings[0].currency }} @{{ inscriptionType.price ? inscriptionType.price : catMan.getBillingsPrice(entry.billings, entry.id) }}</span>
    </button>
</div>
@endif
