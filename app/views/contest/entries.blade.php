<? /** @var Contest $contest  */ ?>
@include('includes.header')
@include('contest.header', array('class' => 'small', 'banner' => ContestAsset::SMALL_BANNER_HTML))

@include('includes.categoryList')
@include('includes.categoryDropdown')
<style>
    @media (orientation: portrait) {
        .modal-dialog {
            margin-top: 50%;
            z-index:0;
        }
    }
</style>
<script type="text/ng-template" id="playVideo.html">
    <video video-fullscreen autoplay controls width='100%' poster="@{{ thumb }}" controlsList="nodownload" ng-if="fileType == 'mp4' || fileType == 'mp3'">
            <source ng-src="@{{mediaMobile}}" type="video/mp4">
        </video>
        <img ng-src="@{{mediaMobile}}" width='100%' ng-if="fileType == 'jpg' || fileType == 'png'"/>
</script>
<script type="text/ng-template" id="category.html">
    <div class="well well-sm cat-header" ng-click="toggleCat(category)" ng-hide="category.entriesCount == 0 && inscription.role == {{ Inscription::JUDGE }}">
        <h4>
            <a href="">
                <i class="fa" ng-class="{'fa-chevron-right': !category.open, 'fa-chevron-down': category.open}"></i>
            </a>
            <trans ng-model="category" trans-prop="'name'"></trans>
            <div class="label label-as-badge cat-entries-badge" ng-class="{'label-primary': !category.entriesCount, 'label-info': !category.final && category.entriesCount, 'label-success': category.final && category.entriesCount}">
                @{{ category.entriesCount || '-' }}
            </div>
            <a href="#/entry/cat/@{{ category.id }}" ng-click="$event.stopPropagation();"
               ng-if="inscription.role == {{ Inscription::INSCRIPTOR }} && category.final == 1 && inTimeForInscriptions() && !winners"
               class="" tooltip-placement="bottom" uib-tooltip="@lang('contest.newEntryInCat')">
                <i class="fa fa-plus"></i>
            </a>
            <a href="#/entry/cat/@{{ category.id }}" ng-click="$event.stopPropagation();"
               ng-if="(inscription.role == {{Inscription::OWNER}} || editPermit == true || {{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}}) && category.final == 1 && !winners"
               class="" tooltip-placement="bottom" uib-tooltip="@lang('contest.newEntryInCat')">
                <i class="fa fa-plus"></i>
            </a>
            <span ng-if="votecat.vote_config.yesPerCategory > 0" class="filter-label help-block" style="display: inline;"
                ng-repeat="votecat in voteSession.voting_categories | filter:{category_id:category.id}">
                @lang('voting.yesPerCategory') @{{ votecat.vote_config.yesPerCategory }}
            </span>
            <div class="clearfix"></div>
            <small class="cat-description" ng-if="clearView"><trans ng-model="category" trans-prop="'description'"></trans></small>
        </h4>
    </div>
    <div class="clearfix"></div>
    <div ng-show="category.open">
        <!--ng-hide="!category.entriesCount"-->
        <ul ng-model="category.children_categories" class="category-list">
            <li ng-if="(inscription.role == {{ Inscription::JUDGE }} && category.entriesCount > 0)
                || winners
                || inscription.role == {{ Inscription::INSCRIPTOR }}
                || inscription.role == {{Inscription::OWNER}}
                || inscription.role == {{Inscription::COLABORATOR}}
                || {{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}}"
                ng-repeat="category in category.children_categories track by $index" ng-include="'category.html'">
            </li>
        </ul>
        <div ng-hide="category.entriesCount == 0 && (inscription.role == {{ Inscription::JUDGE }} || winners)">
            <div class="entries" ng-if="category.final == 1 && category.open">
                <div class="alert alert-transparent" ng-if="category.totalEntries == 0">
                    <span>@lang('contest.noEntriesInCategory')<span>
                </div>
                <span ng-repeat="eRow in category.entriesRows">
                    <span ng-repeat="entry in eRow track by $index">
                        <div class="entry" ng-include="'entry.html'" ng-class="{'col-xs-12': listView == 'list', 'col-md-3 col-sm-4 col-xs-12':listView == 'thumbs'}" onload="hideCats = true; showOnlyCat = category.id;"></div>
                        <div class="clearfix visible-md visible-lg" ng-show="($index + 1) % 4 == 0"></div>
                        <div class="clearfix visible-sm visible-xs" ng-show="($index + 1) % 3 == 0"></div>
                    </span>
                </span>
                <span in-view="$inview && inViewLoadMoreCatEntries(category)" in-view-options="{offset: -100}" ng-if="!category.lastEntryShown && !category.loading">
                </span>
                <div class="col-sm-12 text-center" ng-if="!category.lastEntryShown && category.loading">
                    <div class="spinner">
                        <div class="rect1"></div>
                        <div class="rect2"></div>
                        <div class="rect3"></div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</script>

<script type="text/ng-template" id="entryPerUser.html">
    <div class="well well-sm" ng-click="toggleUser(user)">
        <h4>
            <a href="">
                <i class="fa" ng-class="{'fa-chevron-right': !user.open, 'fa-chevron-down': user.open}"></i></a>
            <i class="fa text-muted" ng-class="{'fa-circle-o': !user.entries || user.entries.length==0, 'fa-circle text-warning': user.entries.length>0}"></i>
            <span> @{{ user.first_name }} @{{ user.last_name }} (@{{ user.email }})
                <i ng-if="user.role == {{Inscription::OWNER}}" class="fa fa-star"> @lang('user.owner')  </i>
                <i ng-if="user.role == {{Inscription::COLABORATOR}}" class="fa fa-star"> @lang('user.colaborator') </i>
            </span>
            <div class="pull-right">
                <div class="badge">@{{user.entries.length}}</div>
            </div>
            <div class="clearfix"></div>
        </h4>
    </div>
    <div class="clearfix"></div>
    <div ng-show="user.open">
        <div class="text-center cat-loading" ng-show="user.loading"><i class="fa fa-spin fa-circle-o-notch fa-2x"></i></div>
        <div class="entries">
            <span ng-repeat="entry in user.entries | entriesStatus:statusFilters | entriesSearch:pagination.query | orderBy:pagination.sortBy:pagination.sortInverted">
                <div class="entry" ng-include="'entry.html'" ng-class="{'col-xs-12': listView == 'list', 'col-md-3 col-sm-4 col-xs-12':listView == 'thumbs'}"></div>
                <div class="clearfix" ng-show="($index + 1) % 4 == 0"></div>
            </span>
            <div class="clearfix"></div>
        </div>
    </div>
</script>

<script type="text/ng-template" id="entry.html">
    <div class="panel panel-default">
        <div class="panel-heading" ng-class="{'entry-selected' : isSelected(entry) && inscription.role != {{Inscription::JUDGE}}, 'entry-not-selected' : !isSelected(entry) && inscription.role != {{Inscription::JUDGE}}}">
            <div class="row">

                <div ng-if="inscription.role != {{Inscription::JUDGE}} && !winners" class="entry-status alert"
                     ng-class="{'alert-default': entry.status == {{Entry::INCOMPLETE}}, 'alert-warning': entry.status == {{Entry::COMPLETE}}, 'alert-success': entry.status == {{Entry::FINALIZE}}, 'alert-info': entry.status == {{Entry::APPROVE}}, 'alert-danger': entry.status == {{Entry::ERROR}}}"
                     ng-click="addEntriesToBulk(entry);"
                     uib-tooltip="@{{ {'{{{Entry::INCOMPLETE}}}' : '@lang('contest.entry.incomplete')', '{{{Entry::COMPLETE}}}' : '@lang('contest.entry.complete')', '{{{Entry::FINALIZE}}}' : '@lang('contest.entry.finalized')', '{{{Entry::APPROVE}}}' : '@lang('contest.entry.approved')', '{{{Entry::ERROR}}}' : '@lang('contest.entry.error'): '+entry.error} | echoswitch : entry.status }}">
                    <i class="fa fa-fw status-icon" ng-class="{'fa-file-o': entry.status == {{Entry::INCOMPLETE}}, 'fa-file-text-o': entry.status == {{Entry::COMPLETE}}, 'fa-check': entry.status == {{Entry::FINALIZE}}, 'fa-thumbs-up': entry.status == {{Entry::APPROVE}}, 'fa-thumbs-down': entry.status == {{Entry::ERROR}}}"></i>
                    <i class="fa fa-fw check" ng-class="{'fa-square-o': !isSelected(entry), 'fa-check-square-o': isSelected(entry)}"></i>
                </div>

                <div class="entry-votes" ng-if="inscription.role == {{Inscription::JUDGE}} || (entry.votes && entry.voteSession)">
                    <div ng-repeat="(key, cats) in entry.categories_id" ng-style="{'height': '@{{getHeight((showOnlyCat == cats) ? 1 : entry.categories_id.length)}}%','ss':'@{{ showOnlyCat }}'}" ng-if="showOnlyCat == null || showOnlyCat == cats">
                        <a ng-if="inscription.role == {{Inscription::JUDGE}}"  ng-href="#/entry/vote/@{{ voteSessionCode }}/@{{ entry.id }}"
                           ng-click="$event.preventDefault(); openEntryInList(entry, $event, showOnlyCat)"
                           vote-weighted-tool inscription="inscription.role" vote-session="voteSession" my-vote="entry.votes" cat="cats" keys="{'key': key, 'length': entry.categories_id.length}"></a>
                        <!--<a ng-if="winners" vote-weighted-tool vote-session="entry.voteSession" my-vote="entry.votes" cat="cats" keys="{'key': key, 'length': entry.categories_id.length}"></a>-->
                    </div>
                </div>

                <span ng-if="voteSession.vote_type == {{ VotingSession::METAL }}">
                    <span ng-if="voteSession.config.oxoMeeting">
                        <span ng-if="meetModerator == true">
                            <div class="btn btn-default" ng-class="{'btn-success': entry.voteSelected == true}" ng-click="votingEntry(entry)"> VOTANDO </div>
                        </span>
                    </span>
                </span>

                <div class="entry-title col-xs-10" ng-class="{'col-sm-7':listView == 'list','col-sm-10':listView == 'thumbs'}">
                    <a href="" ng-if="inscription.role != {{ Inscription::JUDGE }} && {{$contest->type}} == {{Contest::TYPE_CONTEST}}"
                        ng-click="$event.preventDefault(); openEntryInList(entry)">
                        <span entry-card entry="entry" class=""></span>
                    </a>
                    @if($contest->type == Contest::TYPE_CONTEST)
                        <span ng-if="entry.incompleteFields && entry.incompleteFields != false">
                            <span class="text-warning" uib-tooltip-html="getIncompleteFields(entry.incompleteFields) | nl2br" tooltip-placement="bottom">
                                @lang('contest.entry.fieldtocomplete')
                            </span>
                        </span>
                    @endif
                    <span ng-if="{{$contest->type}} == {{Contest::TYPE_TICKET}}"> <span entry-card entry="entry"></span> </span>
                    <a ng-href="#/entry/vote/@{{ voteSessionCode }}/@{{ entry.id }}"
                       ng-click="$event.preventDefault(); openEntryInList(entry, $event, showOnlyCat)" ng-if="inscription.role == {{ Inscription::JUDGE }} ">
                        <span entry-card entry="entry"></span>
                    </a>
                    <div class="cats-list text-primary" ng-class="{'text-muted':listView == 'thumbs'}">
                        <div ng-repeat="catid in entry.categories_id" ng-include="'categoryListVoting.html'" onload="changeShortlist = inscription.role == {{Inscription::JUDGE}} && voteSession.config.shortListConfig && voteSession.config.editShortlist;category = catMan.GetCategory(catid); first=true; noVote=true;winners=winners;winnersSession=entry.voteSession; voteSession.config.showVotingTool">
                        </div>
                    </div>
                    <div ng-if="(inscription.role == {{Inscription::INSCRIPTOR}} || inscription.role == {{Inscription::OWNER}} || {{Auth::user()->isSuperAdmin()}})" class="entry-thumbs">
                        <div class="clearfix"></div>
                        <span ng-repeat="importantField in entry.important_fields" class="text-primary">
                            <b>@{{ importantField.label }}: @{{ importantField.value }}</b>
                            <div class="clearfix"></div>
                            <span ng-repeat="field in entry.files_fields" ng-if="field.entry_metadata_field_id == importantField.entry_metadata_field_id">
                                <div ng-repeat="file in field.files" class="entry-thumb" ng-click="openGallery(entry, field.files, $index);$event.stopPropagation()">
                                    <img ng-src="@{{ file.thumb }}" alt="">

                                    <div class="file-error-entries" ng-if="file.status == <?=ContestFile::ERROR;?>">
                                        <span class="text-danger" uib-tooltip="@lang('general.filesStatus.errorexplain')" tooltip-placement="bottom">
                                            <i class="fa fa-warning"></i>
                                            @lang('general.error')
                                        </span>
                                    </div>

                                    <span ng-if="file.status == <?=ContestFile::UPLOAD_INTERRUPTED;?>">
                                        <div class="file-error-entries">
                                            <span class="text-warning" uib-tooltip="@lang('general.filesStatus.uploadinterruptedexplain')" tooltip-placement="bottom">
                                                <i class="fa fa-unlink"></i>
                                                @lang('general.error')
                                            </span>
                                        </div>
                                    </span>

                                    <div class="file-error-entries" ng-if="file.status == <?=ContestFile::CANCELED;?>">
                                        <span class="text-muted" uib-tooltip="@lang('general.filesStatus.canceledexplain')" tooltip-placement="bottom">
                                            <i class="fa fa-ban"></i>
                                            @lang('general.error')
                                        </span>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </span>
                        </span>
                    </div>
                    <div ng-if="inscription.role == {{Inscription::JUDGE}}" class="entry-thumbs">
                        <div class="clearfix"></div>
                        <span ng-repeat="importantField in entry.important_fields" class="text-primary">
                            <b>@{{ importantField.label }}: @{{ importantField.value }}</b>
                            <div class="clearfix"></div>
                            <span ng-repeat="field in entry.files_fields_entries" ng-if="field.entry_metadata_field_id == importantField.entry_metadata_field_id">
                                <div ng-repeat="file in field.files" class="entry-thumb" ng-click="openGallery(entry, field.files, $index);$event.stopPropagation()">
                                    <img ng-src="@{{ file.thumb }}" alt="">
                                </div>
                                <div class="clearfix"></div>
                            </span>
                        </span>
                    </div>
                </div>
                <div class="text-right pull-right" role="group" ng-class="{'col-sm-4':listView == 'list','entry-actions-thumbs col-sm-10':listView == 'thumbs'}">
                    <span ng-include="'entryActions.html'" ng-init="labels=true;small=true;" ng-if="{{$contest->type}} == {{Contest::TYPE_CONTEST}} || inscription.role == {{ Inscription::OWNER }} || sifter || {{Auth::user()->isSuperAdmin()}}"></span>
                    <span ng-if="{{$contest->type}} == {{Contest::TYPE_TICKET}}" class="text-center">
                        <h3>@lang('oxoTicket.totalPrice') @{{ contest.billing.mainCurrency }} @{{ entry.billings[0].price }}</h3>
                    </span>
                    <br>
                    <a href="" ng-click="showForm(entry.user);$event.stopPropagation();" user-card user-card-model="entry.user" ng-if="inscription.role == {{ Inscription::OWNER }} || sifter || {{Auth::user()->isSuperAdmin()}}"></a>
                    <a user-card user-card-model="entry.user" ng-if="inscription.role == {{Inscription::COLABORATOR}}"></a>
                </div>
            </div>
        </div>
    </div>
</script>
<script type="text/ng-template" id="paymentMethod.html">
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
</script>

<div class="loading-alert" show-during-resolve>
    <div  class="alert alert-danger">
        <i class="fa fa-circle-o-notch fa-spin"></i> @lang('general.loading')
    </div>
</div>
<div ng-if="inTimeForInscriptions() && {{ !!$contest->getInscriptionNextDeadlineDate() ? 1 : 0 }} && inscription.role != {{ Inscription::JUDGE }}" class="alert alert-info alert-sm alert-box text-center">
    <span ng-if="{{$contest->type == Contest::TYPE_CONTEST}}">@lang('contest.signupendson', ['date'=>$contest->getInscriptionOpenDate()]) </span>
    <span ng-if="{{$contest->type == Contest::TYPE_TICKET}}">@lang('oxoTicket.ticketEndsOn', ['date'=>$contest->getInscriptionOpenDate()]) </span>
    <span am-time-ago="'{{$contest->getInscriptionNextDeadlineDate()}}'"></span><span> {{$contest->getWhichDeadLine()}}</span>
    <a ng-if="(inscription.role == {{Inscription::OWNER}} || editPermit == true || {{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}})" href="#/admin/deadlines"> <i class="fa fa-calendar-check-o"></i> @lang('contest.editDates')</a>
</div>
<div ng-if="(!inTimeForInscriptions() || {{ !!$contest->getInscriptionNextDeadlineDate() ? 0 : 1 }}) && inscription.role != {{ Inscription::JUDGE }} && {{$contest->type}} == {{Contest::TYPE_CONTEST}}" class="alert alert-danger alert-sm alert-box text-center">
    @lang('contest.signunended')
    <a ng-if="(inscription.role == {{Inscription::OWNER}} || editPermit == true || {{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}})" href="#/admin/deadlines"> <i class="fa fa-calendar-check-o"></i> @lang('contest.editDates')</a>
</div>

<datalist id="metadataFieldsAutoComplete">
    <option ng-repeat="fields in metadataFields track by $index"
            value="@{{fields.label}}"
            data-id="@{{ fields.id }}"
            >
        @{{ fields.templates }}
    </option>
</datalist>

<div class="container-fluid with-footer" ng-class="{'voting': inscription.role == {{ Inscription::JUDGE }}}">
    <div class="row">
        @include('contest.tabs', array('active' => 'entries-list'))
        <div class="col-sm-9 col-lg-10">
            <div class="row entries-header" id="the-sticky-div">
                <div ng-if="inscription.role == {{ Inscription::JUDGE }}" class="vote-session">
                    <div class="col-sm-6 col-lg-6">
                        <h4 class="">
                            <a class="btn btn-info btn-sm" ng-href="#/voting">
                                <i class="fa fa-angle-double-left"></i> @lang('general.back')
                            </a>
                            @{{ voteSessionName }}
                            <span ng-if="voteSession.public != 1">
                                <judge-progress judge="votingUser" style="display: inline-block; max-width: 150px;"></judge-progress>
                                <label ng-if="votingUser.progress.total == votingUser.progress.votes"> @lang('contest.finishVoting') </label>
                            </span>
                            <span ng-if="voteSession.config.shortListConfig && voteSession.config.editShortlist">
                                <button class="btn btn-sm" ng-click="toggleShortlist()" ng-class="{'btn-success':showAllEntries,'btn-default':!showAllEntries}">
                                    @{{ showAllEntries ? '@lang('voting.showOnlyShortlist')':'@lang('voting.showAllEntries')' }}
                                </button>
                                <button class="btn btn-sm btn-default" ng-click="reloadEntries()">
                                    <i class="fa fa-refresh"></i>
                                </button>
                            </span>
                            <i class="fa fa-spin fa-circle-o-notch" ng-if="entriesLoading"></i>
                        </h4>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="col-sm-12" ng-if="{{$contest->type == Contest::TYPE_TICKET}}">
                    <div class="col-sm-12 col-lg-12 well">
                        <h3> @lang('oxoTicket.hi') @{{ currentUser.first_name }}! @lang('oxoTicket.purchases')</h3>
                    </div>
                    <div class="col-sm-6 input-group">
                        <span class="input-group-addon"><i class="fa fa-search"></i></span>
                        <input ng-model-options="{debounce: 100}"
                               type="text" ng-model="pagination.query"
                               class="form-control inline"
                               placeholder="@lang('general.search')"/>
                    </div>
                    <div class="col-sm-6 text-right">
                        <span ng-if="(inscription.role == {{Inscription::OWNER}} || editPermit == true || viewer == true || {{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}})">
                            <a class="btn btn-default" ng-href="<?=url('/')?>/@{{ contest.code }}/exportEntriesData" >
                                <i class="fa fa-download"></i> @lang('contest.download.entriesList')
                            </a>
                        </span>
                    </div>
                </div>
                <div class="col-sm-12 col-lg-12" ng-if="{{$contest->type == Contest::TYPE_CONTEST}}">
                    <div class="row">
                        <div class="col-sm-12 col-lg-12" ng-if="inscription.role == {{Inscription::OWNER}} || editPermit == true || {{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}}">
                            <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <form ng-submit="searchInput()">
                                    <input
                                            type="text"
                                            ng-model="pagination.queryAdmin"
                                            class="form-control"
                                            placeholder="@lang('general.search')"
                                            list="metadataFieldsAutoComplete"
                                            id="adminSearch"
                                            focus-me="true"/>
                                </form>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-10 col-lg-10" style="margin: 5px;">
                            <div class="clearfix"></div>
                            <span ng-repeat="filterMetdata in pagination.filterMetadata track by $index">
                                <span class="label label-default label-as-badge" style="font-size: 15px;">
                                    @{{ filterMetdata.label }}
                                    <i class="fa fa-close" ng-click="unselectFilterMetadata($index)"></i>
                                </span>
                            </span>
                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                        <!--<a ng-if="!inTimeForInscriptions() && inscription.role == {{ Inscription::INSCRIPTOR }}" class="btn btn-danger btn-default" ng-disabled="true"> @lang('contest.closedInscriptions')</a>-->
                        <a ng-if="reachedMaxEntries() && inscription.role == {{ Inscription::INSCRIPTOR }}" class="btn btn-danger btn-default" ng-disabled="true"> <?=Lang::get('contest.reachedMaxEntries', ["number"=>$contest->max_entries]);?></a>

                        <!--<a ng-if="inTimeForInscriptions() && !reachedMaxEntries() && inscription.role == {{ Inscription::INSCRIPTOR }}" class="btn btn-danger btn-default" href="#/entry/"><i class="fa fa-plus"> </i> @lang('contest.newEntry')</a>
                        <a ng-if="inscription.role == {{Inscription::OWNER}} || editPermit == true || {{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}}" class="btn btn-danger btn-default" href="#/entry/"><i class="fa fa-plus"> </i> @lang('contest.newEntry')</a>-->

                        <input
                            ng-if="inscription.role != {{Inscription::OWNER}} && editPermit != true && {{Auth::check() && Auth::user()->isSuperAdmin() ? 0 : 1}}"
                            ng-model-options="{debounce: 500}"
                            type="text" ng-model="pagination.query"
                            class="form-control inline" placeholder="@lang('general.search')"/>
                        <span ng-if="voteSession.config.yesPerCategory > 0" class="filter-label">
                            @lang('voting.yesPerCategory') @{{ voteSession.config.yesPerCategory }}
                        </span>
                        <form class="form-inline"  ng-if="(inscription.role == {{Inscription::OWNER}} || editPermit == true || {{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}})">
                            <div class="btn-group">
                                <button type="button" ng-disabled="loggedUser" ng-class="{'btn-default':!entryPerUser,'btn-success':entryPerUser}" class="btn btn-default" ng-click="toggleEntryPerUser()">
                                    <span uib-tooltip="@lang('general.entryPerUser')" tooltip-placement="bottom">
                                    <i class="fa fa-user"></i>
                                    </span>
                                </button>
                                <button type="button" ng-if="entryPerUser" class="btn btn-default" ng-click="expandAllUserEntries()" tooltip-placement="bottom" uib-tooltip="@lang('contest.expandAll')"><i class="fa fa-angle-double-down"></i></button>
                                <button type="button" ng-if="entryPerUser" class="btn btn-default" ng-click="collapseAllUserEntries()" tooltip-placement="bottom" uib-tooltip="@lang('contest.collapseAll')"><i class="fa fa-angle-double-up"></i></button>
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li style="padding: 0 5px 5px;"><input type="text" ng-model="filterEntriesPerUser" class="form-control searchBox" style="width:100%;" placeholder="@lang('general.search')"/></li>
                                    <li ng-repeat="user in entriesPerUser | filter: filterEntriesPerUser"><a href="#/entries/@{{ user.id }}"><span user-card user-card-model="user"></span> <span> (@{{ user.email }}) </span></a></li>
                                </ul>
                            </div>
                        </form>

                        <div class="btn-group" role="group">
                            <button ng-if="inscription.role == {{ Inscription::JUDGE }}" type="button" class="btn btn-default" ng-click="setClearView()" tooltip-placement="bottom">
                                <span ng-if="clearView"> @lang('voting.clearView') </span>
                                <span ng-if="!clearView"> @lang('voting.detailedView') </span>
                            </button>
                            <button type="button" class="btn btn-default" ng-click="toggleListGrouped()" uib-tooltip="@lang('general.toggleGroupedView')" tooltip-placement="bottom">
                            <i class="fa fa-1x" ng-class="{'fa-eye': showGrouped, 'fa-eye-slash': !showGrouped}"></i>
                            <i class="fa fa-1x fa-folder"></i>
                            <!--<i class="fa" ng-class="{'fa-bars': showGrouped, 'fa-folder': !showGrouped}"></i>
                                <span ng-show="!showGrouped">@lang('general.toggleGroupedViewCategories')</span>
                                <span ng-show="showGrouped">@lang('general.toggleGroupedViewEntries')</span>-->
                            </button>
                            <button type="button" ng-if="showGrouped && !entryPerUser" class="btn btn-default" ng-click="expandAll()" tooltip-placement="bottom" uib-tooltip="@lang('contest.expandAll')"><i class="fa fa-angle-double-down"></i></button>
                            <button type="button" ng-if="showGrouped && !entryPerUser" class="btn btn-default" ng-click="collapseAll()" tooltip-placement="bottom" uib-tooltip="@lang('contest.collapseAll')"><i class="fa fa-angle-double-up"></i></button>
                        </div>
                        <!--<button class="btn btn-default" ng-click="openMetadataFilters()">
                            <i class="fa fa-cog"></i>
                        </button>-->
                        <button ng-click="showFilters = !showFilters" class="btn btn-default" ng-class="{'btn-info': showFilters}">
                            <i class="fa fa-filter"></i> @lang('general.filters')
                            <span class="label label-primary label-as-badge"> @{{ totalEntryCategory }}</span>
                        </button>
                        <!--<button ng-if="inscription.role == {{ Inscription::JUDGE }} && voteSession.config.oxoMeeting && inLobby === false" type="button" class="btn btn-danger" ng-click="goToLobby()">
                            Wait in Lobby
                        </button>-->
                        <div class="btn-group">
                            <button ng-if="inscription.role == {{ Inscription::JUDGE }}
                                && voteSession.config.oxoMeeting
                                && meetModerator === true && inLobby === false"
                                type="button"
                                class="btn btn-success dropdown-toggle"
                                data-toggle="dropdown"
                                ng-click="judgesInLobby()">
                                Jueces
                            </button>
                            <ul class="dropdown-menu" ng-if="usersInLobby.length > 0">
                                <li style="cursor: pointer;" ng-repeat="lobbyJudge in usersInLobby">
                                    <a ng-click="goToLobby(lobbyJudge)">
                                        * @{{ lobbyJudge.first_name }} @{{ lobbyJudge.last_name }}
                                        <span class="label label-danger" ng-if="lobbyJudge.status == {{VotingUser::IN_LOBBY}}"> EN LOBBY </span>
                                    </a>
                                </li>
                            </ul>
                            <ul class="dropdown-menu" ng-if="usersInLobby.length === 0">
                                <li style="cursor: pointer;">
                                    NO HAY JUECES EN LOBBY
                                </li>
                            </ul>
                        </div>
                        <button ng-click="filterByUnread()" class="btn btn-default" ng-class="{'btn-success': unreadIds}"
                                ng-if="(inscription.permits.admin || sifter || viewer || inscription.role == {{Inscription::OWNER}} ||
                                ( inscription.role == {{Inscription::INSCRIPTOR}} && inTimeForInscriptions() )
                                || {{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}})">
                            <span class="label label-success label-as-badge"> @{{ totalUnread }}</span>
                            <i class="fa fa-commenting-o"></i>
                        </button>

                        @if(isset($contest->block_finished_entry) && $contest->block_finished_entry == 1)
                        <button ng-click="filterByCheck()" class="btn btn-default"
                                ng-class="{'btn-success': pagination.checkFilters}" ng-if="sifter || inscription.role == {{Inscription::OWNER}} || ({{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}})">
                            <span class="label label-success label-as-badge"> @{{ totalCheck }}</span>
                            <i class="fa fa-filter"></i> @lang('contest.checks')
                        </button>
                        @endif

                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 text-right hidden-xs">
                        <span ng-if="(inscription.role == {{Inscription::OWNER}} || editPermit == true || viewer == true || {{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}})">
                            <a class="btn btn-default" ng-href="<?=url('/')?>/@{{ contest.code }}/exportEntriesData" uib-tooltip="@lang('contest.download.entriesList')">
                                <i class="fa fa-download"></i>
                                <i class="fa fa-file-excel-o"></i>
                            </a>
                            <a ng-if="{{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}}" class="btn btn-default" ng-href="<?=url('/')?>/@{{ contest.code }}/export-pdf" uib-tooltip="@lang('contest.download.pdfs')">
                                <i class="fa fa-download"></i>
                                <i class="fa fa-file-pdf-o"></i>
                            </a>
                            <a ng-if="{{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}}" class="btn btn-default" ng-href="<?=url('/')?>/@{{ contest.code }}/exportFiles" uib-tooltip="@lang('contest.download.files')">
                                <i class="fa fa-download"></i>
                                <i class="fa fa-file"></i>
                            </a>
                        </span>
                        <span class="dropdown" ng-if="{{$contest->id}} != 150 && (inscription.role == {{Inscription::INSCRIPTOR}} || inscription.role == {{Inscription::OWNER}} || {{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}})"><!--ng-if="inscription.role != {{ Inscription::JUDGE }}">-->
                            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" ng-class="{'btn-info': bulkEntries.length}">
                                @{{ bulkEntries.length }} @lang('general.selected')
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                <li><a ng-click="selectAll()">@lang('general.selectAll')</a></li>
                                <li><a ng-click="deselectAll()">@lang('general.deselectAll')</a></li>
                                <li role="separator" class="divider"></li>
                                <li ng-class="{'disabled': bulkEntries.length == 0}"><a ng-click="entriesBulkActions(bulkEntries, {{Entry::FINALIZE}})"><i class="fa fa-check"></i><span> @lang('contest.finalizeEntry')</span></a></li>
                                <li ng-class="{'disabled': bulkEntries.length == 0}"><a ng-click="entriesBulkActions(bulkEntries, {{Entry::COMPLETE}})"><i class="fa fa-hand-stop-o"></i><span> @lang('contest.noFinalizeEntry')</span></a></li>
                                <li ng-class="{'disabled': bulkEntries.length == 0}"><a ng-if="inscription.role == {{Inscription::OWNER}} || {{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}}" ng-click="entriesBulkActions(bulkEntries, {{Entry::APPROVE}})"><i class="fa fa-thumbs-up"></i><span> @lang('contest.adminOK')</span></a></li>
                                <li ng-class="{'disabled': bulkEntries.length == 0}"><a ng-click="entriesBulkActions(bulkEntries, {{Entry::ERROR}})"><i class="fa fa-thumbs-down"></i><span> @lang('contest.adminError')</span></a></li>
                                <li ng-if="contest.billing.methods.MercadoPago.enabled != 1" ng-class="{'disabled': bulkEntries.length == 0}"><a ng-click="entriesBulkActions(bulkEntries)"><i class="fa fa-money"></i><span> @lang('contest.payEntry')</span></a></li>
                            </ul>
                        </span>
                        <button type="button" class="btn btn-default hidden-xs" ng-click="toggleListView()" uib-tooltip="@lang('general.toggleView')" tooltip-placement="bottom">
                            <i class="fa" ng-class="{'fa-th': listView == 'list', 'fa-align-justify': listView == 'thumbs'}"></i>
                        </button>
                        <div class="btn-group hidden-xs">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false" uib-tooltip="@lang('general.sortBy')" tooltip-placement="left"><i class="fa" ng-class="{'fa-sort-alpha-desc':pagination.sortInverted,'fa-sort-alpha-asc':!pagination.sortInverted} "></i></button>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                <li ng-class="{'active': pagination.sortBy == 'name'}">
                                    <a href="" ng-click="setSortBy('name')">
                                        <i class="fa" ng-if="pagination.sortBy == 'name'" ng-class="{'fa-sort-amount-asc':pagination.sortInverted, 'fa-sort-amount-desc':!pagination.sortInverted}"></i>
                                        @lang('general.sortBy.name')
                                    </a></li>
                                <li ng-class="{'active': pagination.sortBy == 'id'}">
                                    <a href="" ng-click="setSortBy('id')">
                                        <i class="fa" ng-if="pagination.sortBy == 'id'" ng-class="{'fa-sort-amount-asc':pagination.sortInverted, 'fa-sort-amount-desc':!pagination.sortInverted}"></i>
                                        @lang('general.sortBy.id')
                                    </a>
                                </li>
                                <li ng-class="{'active': pagination.sortBy == 'id'}" ng-if="winners">
                                    <a href="" ng-click="setSortBy('votes')">
                                        <i class="fa" ng-if="pagination.sortBy == 'id'" ng-class="{'fa-sort-amount-asc':pagination.sortInverted, 'fa-sort-amount-desc':!pagination.sortInverted}"></i>
                                        @lang('general.sortBy.votes')
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    </div>
                    <div class="row entries-filters" ng-show="showFilters">
                        <div class="col-sm-12 col-lg-12 col-xs-12" ng-if="inscription.role == {{ Inscription::JUDGE }}">
                            <div class="row">
                                <div class="col-lg-4 col-md-6 col-xs-12 text-left" style="margin-top:10px;">
                                    <div class="filter-buttons">
                                        <div class="btn-group btn-group-justified" style="margin-bottom:10px;">
                                            <a href="" class="btn btn-sm col-xs-12 btn-default" ng-click="toggleFilterBy()">
                                                <span style="">@{{ countEntries() }}</span>
                                                <span class="filter-label">@lang('contest.total') </span>
                                            </a>
                                            <a href="" class="btn btn-sm" ng-class="{'btn-default': votingStatusFilters.indexOf({{ Entry::NO_VOTED }}) == -1, 'btn-primary':votingStatusFilters.indexOf({{ Entry::NO_VOTED }}) != -1}" ng-click="toggleVotingFilterBy({{ Entry::NO_VOTED }})">
                                                <span ng-class="{'label label-primary label-as-badge':votingStatusFilters.indexOf({{ Entry::NO_VOTED }}) == -1,'badge':votingStatusFilters.indexOf({{ Entry::NO_VOTED }}) != -1}">@{{ countVoteEntries({{{ Entry::NO_VOTED }}}) }}</span>
                                                <span class="filter-label">@lang('voting.notVoted')</span>
                                            </a>
                                            <a href="" class="btn btn-sm" ng-class="{'btn-default': votingStatusFilters.indexOf({{ Entry::VOTED }}) == -1, 'btn-info':votingStatusFilters.indexOf({{ Entry::VOTED }}) != -1}" ng-click="toggleVotingFilterBy({{ Entry::VOTED }})">
                                                <span ng-class="{'label label-info label-as-badge':votingStatusFilters.indexOf({{ Entry::VOTED }}) == -1,'badge':votingStatusFilters.indexOf({{ Entry::VOTED }}) != -1}">@{{ countVoteEntries({{{ Entry::VOTED }}}) }}</span>
                                                <span class="filter-label">@lang('voting.voted')</span>
                                            </a>
                                            <a href="" ng-if="voteSession.vote_type == {{ VotingSession::AVERAGE }} || voteSession.vote_type == {{ VotingSession::VERITRON }}" type="button" class="btn btn-sm" ng-class="{'btn-default': votingStatusFilters.indexOf({{ Entry::ABSTAIN }}) == -1, 'btn-warning':votingStatusFilters.indexOf({{ Entry::ABSTAIN }}) != -1}" ng-click="toggleVotingFilterBy({{ Entry::ABSTAIN }})">
                                                <span ng-class="{'label label-warning label-as-badge':votingStatusFilters.indexOf({{ Entry::ABSTAIN }}) == -1,'badge':votingStatusFilters.indexOf({{ Entry::ABSTAIN }}) != -1}">@{{ countVoteEntries({{{ Entry::ABSTAIN }}}) }}</span>
                                                <span class="filter-label">@lang('voting.judgesabstentions')</span>
                                            </a>
                                        </div>
                                        <div class="btn-group" ng-if="voteSession.vote_type == {{ VotingSession::METAL }}">
                                            <a href="" class="btn btn-sm btn-default" ng-repeat="metal in voteSession.config.extra"
                                               style="background:@{{ dinamicEntriesFilters.indexOf(metal.name) != -1 ? metal.color : red}};font-weight:bold;" ng-click="dinamicEntriesFilter(metal.name)">
                                                <span class="badge">@{{ countVoteEntries(metal.name, true) }}</span>
                                                <span class="filter-label">@{{ metal.name }}</span>
                                            </a>
                                        </div>
                                        <div class="btn-group" ng-if="voteSession.vote_type == {{ VotingSession::YESNO }}">
                                            <a href="" class="btn btn-sm" ng-click="yesNoFilters(true)" ng-class="{'btn-default':yesNoEntriesFilters.indexOf(true) == -1, 'btn-success': yesNoEntriesFilters.indexOf(true) != -1}">
                                                <span class="label label-success label-as-badge">@{{ countYesNoEntries(true) }}</span>
                                                <span class="filter-label"> @lang('voting.yes') </span>
                                            </a>
                                            <a href="" class="btn btn-sm" ng-click="yesNoFilters(false)" ng-class="{'btn-default':yesNoEntriesFilters.indexOf(false) == -1, 'btn-danger': yesNoEntriesFilters.indexOf(false) != -1}">
                                                <span class="label label-danger label-as-badge">@{{ countYesNoEntries(false) }}</span>
                                                <span class="filter-label"> @lang('voting.no') </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="col-sm-12 col-lg-12" ng-if="inscription.role != {{ Inscription::JUDGE }}">
                            <div class="row">
                                <div class="col-lg-5 col-md-12 col-sm-12 col-xs-12 text-left">
                                    <div class="filter-buttons">
                                        <div class="btn-group btn-group-justified" ng-if="!winners">
                                            <a href="" class="btn btn-default">
                                                <span class="filter-label">@lang('contest.status') </span>
                                            </a>
                                            <a href="" class="btn" ng-class="{'btn-default': statusFilters.indexOf({{ Entry::INCOMPLETE }}) == -1, 'btn-primary':statusFilters.indexOf({{ Entry::INCOMPLETE }}) != -1}" ng-click="toggleFilterBy({{ Entry::INCOMPLETE }})" ng-disabled="entriesLoading">
                                                <span tooltip-placement="bottom" uib-tooltip="@lang('contest.incomplete')" ng-class="{'label label-primary label-as-badge':statusFilters.indexOf({{ Entry::INCOMPLETE }}) == -1,'badge':statusFilters.indexOf({{ Entry::INCOMPLETE }}) != -1}">@{{ countEntries({{{ Entry::INCOMPLETE }}}) }}
                                                    <span class="filter-label"><i class="fa fa-file-o"></i></span>
                                                </span>
                                                <div class="hidden-xs"> @lang('contest.incomplete') </div>
                                            </a>
                                            <a href="" class="btn" ng-class="{'btn-default': statusFilters.indexOf({{ Entry::COMPLETE }}) == -1, 'btn-warning':statusFilters.indexOf({{ Entry::COMPLETE }}) != -1}" ng-click="toggleFilterBy({{ Entry::COMPLETE }})" ng-disabled="entriesLoading">
                                                <span tooltip-placement="bottom" uib-tooltip="@lang('contest.complete')" ng-class="{'label label-warning label-as-badge':statusFilters.indexOf({{ Entry::COMPLETE }}) == -1,'badge':statusFilters.indexOf({{ Entry::COMPLETE }}) != -1}">@{{ countEntries({{{ Entry::COMPLETE }}}) }}
                                                    <span class="filter-label"><i class="fa fa-file-text-o"></i></span>
                                                </span>
                                                <div class="hidden-xs"> @lang('contest.complete') </div>
                                            </a>
                                            <a href="" class="btn" ng-class="{'btn-default': statusFilters.indexOf({{ Entry::FINALIZE }}) == -1, 'btn-success':statusFilters.indexOf({{ Entry::FINALIZE }}) != -1}" ng-click="toggleFilterBy({{ Entry::FINALIZE }})" ng-disabled="entriesLoading">
                                                <span tooltip-placement="bottom" uib-tooltip="@lang('contest.finalized')" ng-class="{'label label-success label-as-badge':statusFilters.indexOf({{ Entry::FINALIZE }}) == -1,'badge':statusFilters.indexOf({{ Entry::FINALIZE }}) != -1}">@{{ countEntries({{{ Entry::FINALIZE }}}) }}
                                                    <span class="filter-label"><i class="fa fa-check"></i></span>
                                                </span>
                                                <div class="hidden-xs"> @lang('contest.finalized') </div>
                                            </a>
                                            <a href="" class="btn" ng-class="{'btn-default': statusFilters.indexOf({{ Entry::APPROVE }}) == -1, 'btn-info':statusFilters.indexOf({{ Entry::APPROVE }}) != -1}" ng-click="toggleFilterBy({{ Entry::APPROVE }})" ng-disabled="entriesLoading">
                                                <span tooltip-placement="bottom" uib-tooltip="@lang('contest.approved')" ng-class="{'label label-info label-as-badge':statusFilters.indexOf({{ Entry::APPROVE }}) == -1,'badge':statusFilters.indexOf({{ Entry::APPROVE }}) != -1}">@{{ countEntries({{{ Entry::APPROVE }}}) }}
                                                    <span class="filter-label"><i class="fa fa-thumbs-up"></i></span>
                                                </span>
                                                <div class="hidden-xs"> @lang('contest.approved') </div>
                                            </a>
                                            <a href="" class="btn" ng-class="{'btn-default': statusFilters.indexOf({{ Entry::ERROR }}) == -1, 'btn-danger':statusFilters.indexOf({{ Entry::ERROR }}) != -1}" ng-click="toggleFilterBy({{ Entry::ERROR }})" ng-disabled="entriesLoading">
                                                <span tooltip-placement="bottom" uib-tooltip="@lang('contest.errors')" ng-class="{'label label-danger label-as-badge':statusFilters.indexOf({{ Entry::ERROR }}) == -1,'badge':statusFilters.indexOf({{ Entry::ERROR }}) != -1}">@{{ countEntries({{{ Entry::ERROR }}}) }}
                                                    <span class="filter-label"><i class="fa fa-thumbs-down"></i></span>
                                                </span>
                                                <div class="hidden-xs"> @lang('contest.errors') </div>
                                            </a>
                                        </div>
                                        <div class="btn-group" ng-if="winners">
                                            <button ng-if="voteSessionMetals.vote_type == {{ VotingSession::METAL }}" type="button" class="btn" ng-repeat="metal in voteSessionMetals.config.extra"
                                                    style="color:white;background-color:@{{ dinamicEntriesFilters.indexOf(metal.name) != -1 ? metal.color : '#484e55'}};font-weight:bold;" ng-click="dinamicEntriesFilter(metal.name)">
                                                <span class="badge">@{{ countVoteEntries(metal.name, true) }}</span>
                                                <div class="filter-label">@{{ metal.name }}</div>
                                            </button>
                                        </div>
                                        <div class="btn-group" ng-if="showMetals">
                                            <button type="button" class="btn btn-default" ng-click="showWinners()">
                                                <div class="filter-label" ng-if="!winners">@lang('contest.showWinners') </div>
                                                <div class="filter-label" ng-if="winners">@lang('contest.showEntries') </div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-offset-2 col-lg-5 col-md-12 text-right">
                                    <!--<span style="padding-top: 10px;"><i class="fa fa-money fa-2x"></i></span>-->
                                    <div class="filter-buttons" ng-if="contest.billing.mainPrice">
                                        <div class="btn-group btn-group-justified">
                                            <a href="" class="btn btn-default">
                                                <span class="filter-label">@lang('contest.payments') </span>
                                            </a>
                                            <a href="" class="btn" ng-class="{'btn-default': billingFilters.indexOf({{ Billing::UNPAID }}) == -1, 'btn-primary':billingFilters.indexOf({{ Billing::UNPAID }}) != -1}" ng-click="toggleBillingFilterBy({{ Billing::UNPAID }})" ng-disabled="entriesLoading">
                                                <span ng-class="{'label label-primary label-as-badge':billingFilters.indexOf({{ Billing::UNPAID }}) == -1,'badge':billingFilters.indexOf({{ Billing::UNPAID }}) != -1}">@{{ countBillingEntries({{{Billing::UNPAID}}}) }}</span>
                                                <span class="filter-label"></span>
                                                <div class="hidden-xs"> @lang('billing.unpaid') </div>
                                            </a>
                                            <a href="" class="btn" ng-class="{'btn-default': billingFilters.indexOf({{ Billing::STATUS_PROCESSING }}) == -1, 'btn-warning':billingFilters.indexOf({{ Billing::STATUS_PROCESSING }}) != -1}" ng-click="toggleBillingFilterBy({{ Billing::STATUS_PROCESSING }})" ng-disabled="entriesLoading">
                                                <span ng-class="{'label label-warning label-as-badge':billingFilters.indexOf({{ Billing::STATUS_PROCESSING }}) == -1,'badge':billingFilters.indexOf({{ Billing::STATUS_PROCESSING }}) != -1}">@{{ countBillingEntries({{{ Billing::STATUS_PROCESSING }}}) }}</span>
                                                <span class="filter-label"></span>
                                                <div class="hidden-xs"> @lang('billing.processing') </div>
                                            </a>
                                            <a href="" class="btn" ng-class="{'btn-default': billingFilters.indexOf({{ Billing::STATUS_PENDING }}) == -1, 'btn-success':billingFilters.indexOf({{ Billing::STATUS_PENDING }}) != -1}" ng-click="toggleBillingFilterBy({{ Billing::STATUS_PENDING }})" ng-disabled="entriesLoading">
                                                <span ng-class="{'label label-success label-as-badge':billingFilters.indexOf({{ Billing::STATUS_PENDING }}) == -1,'badge':billingFilters.indexOf({{ Billing::STATUS_PENDING }}) != -1}">@{{ countBillingEntries({{{ Billing::STATUS_PENDING }}}) }}</span>
                                                <span class="filter-label"></span>
                                                <div class="hidden-xs"> @lang('billing.verify') </div>
                                            </a>
                                            <a href="" class="btn" ng-class="{'btn-default': billingFilters.indexOf({{ Billing::STATUS_SUCCESS }}) == -1, 'btn-info':billingFilters.indexOf({{ Billing::STATUS_SUCCESS }}) != -1}" ng-click="toggleBillingFilterBy({{ Billing::STATUS_SUCCESS }})" ng-disabled="entriesLoading">
                                                <span ng-class="{'label label-info label-as-badge':billingFilters.indexOf({{ Billing::STATUS_SUCCESS }}) == -1,'badge':billingFilters.indexOf({{ Billing::STATUS_SUCCESS }}) != -1}">@{{ countBillingEntries({{{ Billing::STATUS_SUCCESS }}}) }}</span>
                                                <span class="filter-label"></span>
                                                <div class="hidden-xs"> @lang('billing.payed') </div>
                                            </a>
                                            <a href="" class="btn" ng-class="{'btn-default': billingFilters.indexOf({{ Billing::STATUS_ERROR }}) == -1, 'btn-danger':billingFilters.indexOf({{ Billing::STATUS_ERROR }}) != -1}" ng-click="toggleBillingFilterBy({{ Billing::STATUS_ERROR }})" ng-disabled="entriesLoading">
                                                <span ng-class="{'label label-danger label-as-badge':billingFilters.indexOf({{ Billing::STATUS_ERROR }}) == -1,'badge':billingFilters.indexOf({{ Billing::STATUS_ERROR }}) != -1}">@{{ countBillingEntries({{{ Billing::STATUS_ERROR }}}) }}</span>
                                                <span class="filter-label"></span>
                                                <div class="hidden-xs"> @lang('contest.errors') </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12 text-center" ng-if="!showGrouped && entriesLoading == true && lastEntryShown">
                    <div class="spinner">
                        <div class="rect1"></div>
                        <div class="rect2"></div>
                        <div class="rect3"></div>
                        <div class="rect4"></div>
                    </div>
                </div>
                <div class="col-sm-12 text-center logged-user" ng-if="loggedUser">
                    <span user-card user-card-model="loggedUser"></span> (@{{ loggedUser.email }}) <a href="#entries/"><i class="fa fa-close"></i></a>
                </div>
                <div class="col-sm-offset-4 col-sm-4 text-center" ng-if="totalEntries == 0 && entriesLoading == false">
                    <br>
                    <div class="alert alert-info alert-sm" role="alert" ng-if="{{$contest->type == Contest::TYPE_CONTEST}}"> <i class="fa fa-info-circle"></i> @lang('contest.noEntries')</div>
                    <div class="alert alert-info alert-sm" role="alert" ng-if="{{$contest->type == Contest::TYPE_TICKET}}"> <i class="fa fa-info-circle"></i> @lang('oxoTicket.noTickets')</div>
                </div>
            </div>
            <div class="row" ng-class="{'entries-selected' : bulkEntries.length && inscription.role != {{Inscription::JUDGE}}, 'entries-selectable': inscription.role != {{Inscription::JUDGE}}}">
            <div class="entries" ng-if="!showGrouped && !entryPerUser" ng-class="listView">
                <span ng-repeat="eRow in entriesRows">
                    <span ng-repeat="entry in eRow track by $index">
                        <div class="entry" ng-include="'entry.html'" ng-class="{'col-xs-12': listView == 'list', 'col-md-3 col-sm-4 col-xs-12':listView == 'thumbs'}"></div>
                        <div class="clearfix visible-md visible-lg" ng-show="($index + 1) % 4 == 0"></div>
                        <div class="clearfix visible-sm visible-xs" ng-show="($index + 1) % 3 == 0"></div>
                    </span>
                </span>
                <span in-view="$inview && inViewLoadMoreEntries()" in-view-options="{offset: -100}" ng-if="!lastEntryShown && !entriesLoading">
                </span>
                <div class="col-sm-12 text-center" ng-if="!lastEntryShown && entriesLoading">
                    <div class="spinner">
                        <div class="rect1"></div>
                        <div class="rect2"></div>
                        <div class="rect3"></div>
                        <div class="rect4"></div>
                        <div class="rect5"></div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="col-sm-12 entries-categories-tree" ng-if="showGrouped && !entryPerUser">
                <ul ng-model="categories" class="category-list">
                    <li ng-repeat="category in categories track by $index" ng-include="'category.html'">
                    </li>
                </ul>
            </div>
            <div ng-if="inscription.role == {{ Inscription::JUDGE }} && voteSession.config.oxoMeeting">
                <div oxo-meet ng-draggable="vm.dragOptions" class="oxomeet">
                    <span class="col-sm-12 meet-header">
                        <span class="title">
                            <i class="fa fa-video-camera"></i>
                            OxoMeet
                        </span>
                        <span class="buttons">
                            <i class="fa fa-fw fa-columns hide-in-fullscreen hide-in-columns" ng-click="columns();"></i>
                            <i class="fa fa-fw fa-window-minimize hide-in-fullscreen hide-in-minimized" ng-click="minimize();"></i>
                            <i class="fa fa-fw fa-window-restore show-in-fullscreen show-in-minimized show-in-columns" ng-click="restore();"></i>
                            <i class="fa fa-fw fa-window-maximize hide-in-fullscreen" ng-click="maximize();"></i>
                        </span>
                    </span>
                    <div id="meeting" ng-if="inLobby === false">
                        <div class="meet-resize hide-in-fullscreen hide-in-columns" modal-resizer></div>
                    </div>
                    <div ng-if="inLobby === true">
                        <div style="width: 300px; height: 300px; color: black;">
                            <h3 class="text-center"> Por favor, espere en el lobby. </h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 entries-categories-tree" ng-if="entryPerUser">
                <ul ng-model="user" class="category-list">
                    <li ng-repeat="user in entriesPerUser track by $index" ng-include="'entryPerUser.html'">
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    @include('includes.footer')
    </div>
</div>