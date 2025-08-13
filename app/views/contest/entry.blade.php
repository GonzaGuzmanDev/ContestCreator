@include('includes.header')
@include('contest.header', array('class' => 'small', 'banner' => ContestAsset::SMALL_BANNER_HTML))

@include('includes.categoryList')
@include('includes.categoryDropdown')

<script type="text/ng-template" id="edit-actions.html">
    <div class="">
        <div class="col-sm-12 col-xs-12" ng-if="entriesIds && !newEntry && inscription.role == {{ Inscription::JUDGE }}">
            <div class="col-sm-2 col-xs-4">
                <a class="btn btn-block btn-primary" href="#/entry/vote/@{{voteSessionCode}}/@{{ previousEntry }}"><i class="fa fa-angle-double-left"></i> @lang('contest.previous') </a>
            </div>
            <div class="col-sm-8 col-xs-4 text-center">
                <h4> @{{ currentPosition + 1 }} de @{{ entriesIds.length }} </h4>
            </div>
            <div class="col-sm-2 col-xs-4 pull-right">
                <a class="btn btn-block btn-primary col-sm-3" href="#/entry/vote/@{{voteSessionCode}}/@{{ nextEntry }}"> @lang('contest.next') <i class="fa fa-angle-double-right"></i></a>
            </div>
        </div>
        <div class="col-sm-3">
            <a href="#entries" class="btn btn-info" ng-if="inscription.role != {{Inscription::JUDGE}}">
                <i class="fa fa-angle-double-left"></i> @lang('general.back')
            </a>
        </div>
        <div class="col-sm-9" ng-show="!showStatic">
            <button type="button" class="btn btn-default" ng-show="entry.id" ng-click="reset();">@lang('general.cancel')</button>
            <button type="button" ng-if="!entry.id && reachedMaxEntries() && inscription.role == {{ Inscription::INSCRIPTOR }}" class="btn btn-danger btn-default" href="#/entry/" ng-disabled="true"> <?=Lang::get('contest.reachedMaxEntries', ["number"=>$contest->max_entries]);?></button>
            <button type="submit" ng-disabled="sending || entry.categories_id.length == 0" ng-if="( {{ Auth::user()->isSuperAdmin() }} || userInscriptions.HasRole({{ Inscription::OWNER }}) || entryPermitsEdit() == true ) || (inscription.role == {{ Inscription::INSCRIPTOR }} && inTimeForInscriptions() && (entry.id || !reachedMaxEntries()))" ng-hide="{{$contest->type == Contest::TYPE_TICKET}}" class="btn btn-success">@lang('general.save')</button>
            <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !contestForm.$dirty">
                <span ng-bind-html="flash"></span>
            </div>
            <span ng-if="getEntryTotalErrors() > 0"><a href="" ng-click="showIncomplete(entry)" class="text-warning">
                <i class="fa fa-warning" uib-tooltip="@lang('contest.entry.incomplete')"></i>
                @{{ getEntryTotalErrors() }}
                @lang('contest.entry.fieldtocomplete')
            </a></span>
        </div>
        <div class="col-sm-9" ng-show="showStatic" ng-if="inscription.role != {{Inscription::JUDGE}}">
            @if($contest->block_finished_entry == null || isset($contest->block_finished_entry) && $contest->block_finished_entry == 0)
                <a type="button" ng-click="edit()" ng-if="entry.status != {{Entry::APPROVE }} && inscription.role == {{ Inscription::INSCRIPTOR }} && inTimeForInscriptions()" class="btn btn-warning">
                    <i class="fa fa-edit"></i> @lang('contest.completeEntry')
                </a>
            @endif
            @if(isset($contest->block_finished_entry) && $contest->block_finished_entry == 1)
                <a type="button" ng-click="edit()" ng-if="entry.status != {{Entry::APPROVE }} && entry.status != {{Entry::FINALIZE }} && inscription.role == {{ Inscription::INSCRIPTOR }} && inTimeForInscriptions()" class="btn btn-warning">
                    <i class="fa fa-edit"></i> @lang('contest.completeEntry')
                </a>
            @endif
            <a type="button" ng-click="edit()" ng-if="entry.status != {{Entry::APPROVE }} && ( {{ Auth::user()->isSuperAdmin() }} || inscription.role == {{ Inscription::OWNER }} || entryPermitsEdit() == true )" class="btn btn-warning">
                <i class="fa fa-edit"></i> @lang('contest.completeEntry')
            </a>

            <span class="dropup">
                <a role="button" data-toggle="dropdown" class="btn btn-info btn-md" data-target="#" href="">
                    <i class="fa fa-copy"></i>
                    @lang('contest.duplicate')
                </a>
                <ul class="dropdown-menu multi-level">
                    <li ng-repeat="category in contest.children_categories track by $index" ng-include="'categoryDropDownDuplicate.html'" ng-class="{'dropdown-submenu':category.children_categories.length>0,'disabled':entry.categories_id.indexOf(category.id)!=-1}"></li>
                </ul>
            </span>

            <span ng-include="'entryActions.html'" ng-if="showStatic && {{$contest->type}} == {{Contest::TYPE_CONTEST}}" ng-init="labels=true;"></span>
            <a ng-click="viewOptions.hideDescriptions = !viewOptions.hideDescriptions;" ng-if="!viewOptions.hideDescriptions" class="btn btn-info" uib-tooltip="@lang('contest.hideDescriptions')" uib-tooltip-placement="top">
                <i class="fa fa-ellipsis-h"></i>
            </a>
            <a ng-click="viewOptions.hideDescriptions = !viewOptions.hideDescriptions;" ng-if="viewOptions.hideDescriptions" class="btn btn-default" uib-tooltip="@lang('contest.showDescriptions')" uib-tooltip-placement="top">
                <i class="fa fa-ellipsis-h"></i>
            </a>
            <a ng-if="( {{ Auth::user()->isSuperAdmin() }} || userInscriptions.HasRole({{ Inscription::OWNER }}))" type="button" class="btn btn-info" ng-href="<?=url('/')?>/@{{ contest.code }}/export-pdf/@{{ entry.id }}"><i class="fa fa-print"></i> @lang('general.print') </a>

            <a type="button" ng-if="entry.status != {{Entry::APPROVE }} && inscription.role == {{ Inscription::INSCRIPTOR }} && inTimeForInscriptions()" class="btn btn-danger pull-right" ng-click="delete(entry)" tooltip-placement="bottom"> <i class="fa fa-trash"> </i></a>
            <a type="button" ng-if="entry.status != {{Entry::APPROVE }} && ( {{ Auth::user()->isSuperAdmin() }} || inscription.role == {{ Inscription::OWNER }} || entryPermitsEdit() == true || inscription.permits.admin)" class="btn btn-danger pull-right" ng-click="delete(entry)" tooltip-placement="bottom"> <i class="fa fa-trash"> </i></a>
        </div>
    </div>
</script>
<div ng-if="inTimeForInscriptions() && {{ !!$contest->getInscriptionNextDeadlineDate() ? 1 : 0 }} && inscription.role != {{ Inscription::JUDGE }} && {{$contest->type}} == {{Contest::TYPE_CONTEST}}" style="margin-bottom: 0px;" class="alert alert-info alert-sm alert-box text-center">
    @lang('contest.signupendson', ['date'=>$contest->getInscriptionOpenDate()])
    <span am-time-ago="signupEndTimeAgo"></span>
</div>
<div ng-if="(!inTimeForInscriptions() || {{ !!$contest->getInscriptionNextDeadlineDate() ? 0 : 1 }}) && inscription.role != {{ Inscription::JUDGE }} && {{$contest->type}} == {{Contest::TYPE_CONTEST}}" style="margin-bottom: 0px;" class="alert alert-danger alert-sm alert-box text-center">
    @lang('contest.signunended')
</div>
<div class="loading-alert" show-during-resolve>
    <div  class="alert alert-danger">
        <i class="fa fa-circle-o-notch fa-spin"></i> @lang('general.loading')
    </div>
</div>
<div class="container-fluid with-footer entry" hotkey="{d: hideDescriptions, m: showLog}">

    <div class="row">
        @include('contest.tabs', array('active' => 'entries-list'))
        <div class="col-sm-9 col-lg-10">
            <div ng-if="entriesIds && !newEntry && inscription.role != {{ Inscription::JUDGE }}">
                <div class="row">
                    <div class="col-sm-2">
                        <a class="btn btn-sm btn-block btn-primary" href="#/entry/@{{ previousEntry }}"> << Anterior </a>
                    </div>
                    <div class="col-sm-8 text-center">
                        <h4> Entry @{{ currentPosition + 1 }} de @{{ entriesIds.length }} </h4>
                    </div>
                    <div class="col-sm-2">
                        <a class="btn btn-sm btn-block btn-primary" href="#/entry/@{{ nextEntry }}"> Siguiente >> </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <h2 class="entry-form-title">
                        <span ng-if="!entry.id && {{$contest->type}} == {{Contest::TYPE_CONTEST}}">@lang('contest.newEntryTitle')</span>
                        <span ng-if="!entry.id && {{$contest->type}} == {{Contest::TYPE_TICKET}}">@lang('oxoTicket.newTicket')</span>
                        <span ng-if="entry.id">
                            <!--<span class="">
                                <a ng-href="#/voting/@{{voteSessionCode}}" ng-if="inscription.role == {{Inscription::JUDGE}}" class="btn btn-info btn-sm">
                                    <i class="fa fa-angle-double-left"></i> @lang('general.back')
                                </a>
                            </span>-->
                            <span ng-bind-html="getName() || getNoName()"></span>
                            <span class="entry-id" ng-class="{'text-muted': entry.status == {{Entry::INCOMPLETE}}, 'text-warning': entry.status == {{Entry::COMPLETE}}, 'text-success': entry.status == {{Entry::FINALIZE}}, 'text-info': entry.status == {{Entry::APPROVE}}, 'text-danger': entry.status == {{Entry::ERROR}}}" style="font-size: 0.75em;">#@{{ entry.id | zpad:5}}</span>
                        </span>
                    </h2>
                    <h4 ng-if="getEntryTotalErrors() > 0">
                        <a href="" ng-click="showIncomplete(entry)" class="text-warning">
                            <i class="fa fa-warning" uib-tooltip="@lang('contest.entry.incomplete')"></i> @{{ getEntryTotalErrors() }} @lang('contest.entry.fieldtocomplete')
                        </a>
                    </h4>

                    <div class="clearfix"></div>
                    <form name="entryForm" class="form-horizontal" ng-submit="save()">
                        <div class="form-group" ng-if="userInscriptions.HasRole(<?=Inscription::OWNER?>) || userInscriptions.HasRole(<?=Inscription::COLABORATOR?>) || {{Auth::user()->isSuperAdmin()}}">
                            <label for="inputUser" class="col-sm-3 control-label">@lang('contest.owner') <span ng-show="!showStatic" class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div ng-if="!entry.id" class="row">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" placeholder="@lang('contest.searchUser')" id="inputUser" required=""
                                               ng-model="entry.user" autocomplete="off"
                                               uib-typeahead="user as user.email for user in uData.getData($viewValue, contest.id) | filter:$viewValue | limitTo:10"
                                               typeahead-template-url="userTypeahead.html">
                                        <div user-card user-card-model="entry.user" class="selected-user-card"></div>
                                    </div>
                                </div>
                                <div class="form-control-static" ng-if="entry.id">
                                    <div user-card user-card-model="entry.user" ng-click="showForm(entry.user)"></div>
                                </div>
                                <div ng-show="errors.user" class="help-inline text-danger form-control-static">@{{errors.user.toString()}}</div>
                            </div>
                        </div>

                        <div class="form-group" ng-if="{{$contest->type == Contest::TYPE_CONTEST}}">
                            <label class="col-sm-3 control-label">@lang('contest.entryCategories') <span ng-show="!showStatic" class="text-danger">*</span></label>
                            <div class="col-sm-7">
                                <div class="form-control-static">
                                    <div ng-repeat="catid in entry.categories_id" ng-include="'categoryListVoting.html'" onload="category = getCategory(catid); first=true; editable=false; voteSession.config.oxoMeeting == 1 ? voteSession.config.showVotingTool =0 : voteSession.config.showVotingTool =1;" ng-if="showStatic"></div>
                                    <div ng-repeat="catid in entry.categories_id" ng-include="'categoryListVoting.html'" onload="category = getCategory(catid); first=true; editable=!checkSingleCategory();voteSession.config.showVotingTool =1;" ng-if="!showStatic"></div>
                                    <div class="dropdown" ng-show="!showStatic">
                                        <a role="button" data-toggle="dropdown" ng-if="!checkSingleCategory() && entry.categories_id.length > 0" class="btn btn-info btn-sm" data-target="#" href="" uib-tooltip="@lang("contest.addCategory")" tooltip-placement="bottom">
                                            <i class="fa fa-plus"></i>
                                            <span></span>
                                        </a>
                                        <a role="button" data-toggle="dropdown" ng-if="checkSingleCategory() && entry.categories_id.length > 0" class="btn btn-info btn-sm" data-target="#" href="" uib-tooltip="@lang("contest.changeCategory")" tooltip-placement="bottom">
                                            <i class="fa fa-edit"></i>
                                            <span></span>
                                        </a>
                                        <a role="button" data-toggle="dropdown" ng-if="entry.categories_id.length == 0" class="btn btn-info btn-sm" data-target="#" href="">
                                            <i class="fa fa-check"></i>
                                            @lang('contest.selectCategory')
                                        </a>
                                        <ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
                                            <li ng-repeat="category in contest.children_categories track by $index" ng-include="'categoryDropdown.html'" ng-class="{'dropdown-submenu':category.children_categories.length>0,'disabled':entry.categories_id.indexOf(category.id)!=-1}"></li>
                                        </ul>
                                    </div>
                                    <div ng-show="errors[field.id]" class="help-inline text-danger form-control-static">@{{errors[field.id].toString()}}</div>
                                </div>
                            </div>
                        </div>

                        <!-- TICKETS -->

                        <fieldset class="col-sm-12" ng-if="{{$contest->type == Contest::TYPE_TICKET}}" ng-disabled="entry.categories_id.length > 0">
                            <div class="col-sm-6">
                                <div class="col-sm-12" style="height: 150px; margin-bottom: 10px;" ng-repeat="category in contest.children_categories track by $index">
                                    <div class="panel panel-primary col-sm-9" style="height: 100%;">
                                        <div class="panel-body"><i class="fa fa-ticket fa-3x col-sm-2"></i><span class="col-sm-10"> <b>@{{ category.name }}</b> <br> @{{ category.description }}</span></div>
                                    </div>
                                    <button type="button" ng-class="{'btn-warning': category.selected}" ng-click="AddTicketToCart(category)" class="col-sm-3" style="height: 100%; color:black;"> <h4> @{{ contest.billing.mainCurrency }} @{{ category.price }} </h4></button>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <h4>TU COMPRA </h4>
                                <hr>
                                <table class="table table-striped table-hover table-condensed">
                                    <thead>
                                    <tr>
                                        <th> Entrada </th>
                                        <th> Precio </th>
                                        <th> Cantidad </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="category in contest.children_categories track by $index" ng-if="category.selected">
                                            <th>@{{ category.name }}</th>
                                            <th>@{{ contest.billing.mainCurrency }} @{{ category.totalPriceTickets }}</th>
                                            <th><span class="btn" ng-click="moreOrLessTickets(category, false)" ng-disabled="category.totalTickets == 1"> <i class="fa fa-minus"></i></span>
                                                @{{ category.totalTickets }}
                                                <span class="btn" ng-click="moreOrLessTickets(category, true)"> <i class="fa fa-plus"></i> </span>
                                            </th>
                                        </tr>
                                    </tbody>

                                </table>
                                <h4>TOTAL: @{{ contest.billing.mainCurrency }} @{{ categoriesTotal }}</h4>
                                <div class="btn btn-md btn-danger" ng-click="buyTickets(categories)" ng-disabled="selectedTickets.length == 0"> COMPRAR </div>
                            </div>
                        </fieldset>

                        <!-- FIN DE TICKETS -->
                        <script type="text/ng-template" id="entry-metadata-field.html">
                            @include('metadata.field', array('model'=>'field.model.value', 'filemodel'=>'field.model.files', 'mainField'=>'field.model', 'allValues'=>'field.allmodels', 'disabled'=>false, 'forceRequired' => false))
                        </script>
                        <div ng-show="entry.categories_id.length > 0">
                            <div ng-if="firstTabIndex != -1">
                                <uib-tabset>
                                    <div class="form-group" ng-repeat="field in getPreTabMetadata()" ng-if="!isFieldHidden(field)">
                                        <div ng-include="'entry-metadata-field.html'"></div>
                                    </div>
                                    <uib-tab ng-repeat="tab in getTabs() track by $index" active="activeTab" index="$index" ng-if="!isFieldHidden(tab)">
                                        <uib-tab-heading><trans ng-model='tab' trans-prop="'label'"></trans></uib-tab-heading>
                                        <div ng-if="activeTab">
                                        <div class="help-block" ng-if="tab.description != null"><trans ng-model='tab' trans-prop="'description'"></trans></div>
                                        <div class="form-group" ng-repeat="field in getTabMetadata(tab)" ng-if="!isFieldHidden(field)">
                                            <div ng-include="'entry-metadata-field.html'"></div>
                                        </div>
                                        </div>
                                    </uib-tab>
                                </uib-tabset>
                            </div>
                            <div ng-if="firstTabIndex == -1">
                                <div class="form-group" ng-repeat="field in entry_metadata_fields" ng-if="!isFieldHidden(field) && !isTab(field)">
                                    <div ng-include="'entry-metadata-field.html'"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" ng-show="!showStatic && {{$contest->type}} == {{Contest::TYPE_CONTEST}}">
                            <div class="col-sm-3 text-right">
                                <h5 class="text-muted" ng-show="!showStatic"><span class="text-danger">*</span> @lang('contest.requiredfields')</h5>
                            </div>
                        </div>

                        <footer class="navbar navbar-default navbar-fixed-bottom editor">
                            <div class="container">
                                <div class="row">
                                    <div ng-include="'edit-actions.html'"></div>
                                </div>
                            </div>
                        </footer>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
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