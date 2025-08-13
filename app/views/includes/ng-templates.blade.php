<script type="text/ng-template" id="userCard.html">
    @include('includes.user')
</script>
<script type="text/ng-template" id="userTypeahead.html">
    @include('includes.user-typeahead')
</script>
<script type="text/ng-template" id="entryCard.html">
    @include('includes.entry')
</script>
<script type="text/ng-template" id="entryActions.html">
    @include('includes.entry-actions')
</script>
<script type="text/ng-template" id="entrySifter.html">
    @include('includes.entry-sifter')
</script>
<script type="text/ng-template" id="judgeProgress.html">
    @include('includes.judge-progress')
</script>
<script type="text/ng-template" id="votingUserEntryCategory.html">
    @include('includes.voting-user-entry-categories')
</script>
<script type="text/ng-template" id="editVotingGroup.html">
    @include('includes.edit-voting-group')
</script>
<script type="text/ng-template" id="deleteVotingGroup.html">
    @include('includes.delete-voting-group')
</script>
<script type="text/ng-template" id="exportResultsManager.html">
    @include('includes.export-results-manager')
</script>
<script type="text/ng-template" id="autoAbstainsModal.html">
    @include('includes.auto-abstains-modal')
</script>
<script type="text/ng-template" id="rankingModal.html">
    @include('includes.ranking-modal')
</script>
@include('includes.vote-tool')
<script type="text/ng-template" id="entryLog.html">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" ng-click="close()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title">
                <i class="fa fa-history"></i>
                @lang('contest.entryLog')
                <span entry-card entry="entry" fields="fields"></span>
            </h4>
        </div>
        <form role="form" name="modalForm" class="form-horizontal">
            <div class="modal-body modal-fixed entry-log">
                <div ng-repeat="log in entryLog track by $index">
                    <hr ng-if="$index != 0">
                    <h5 class="well well-sm" ng-class="{'text-right': !isResponse(log)}">
                        <span class="text-primary">
                            <div user-card user-card-model="log.user" class="selected-user-card"></div>
                        </span>
                        <span ng-if="log.status != {{Entry::ENTRY_MESSAGE}}" ng-class="{'text-warning': log.status == {{Entry::COMPLETE}}, 'text-success': log.status == {{Entry::FINALIZE}}, 'text-info': log.status == {{Entry::APPROVE}}, 'text-danger': log.status == {{Entry::ERROR}}, 'text-default': log.status == {{Entry::INCOMPLETE}}, 'text-primary': log.status == {{Entry::ENTRY_MESSAGE}}}">
                        <i class="fa fa-fw" ng-class="{'fa-clock-o': log.status == {{Entry::COMPLETE}}, 'fa-check': log.status == {{Entry::FINALIZE}}, 'fa-thumbs-up': log.status == {{Entry::APPROVE}}, 'fa-thumbs-down': log.status == {{Entry::ERROR}}, 'fa-plus': log.status == {{Entry::INCOMPLETE }}, 'fa-comment-o': log.status == {{Entry::ENTRY_MESSAGE }}}"></i>
                        @{{ {'{{{Entry::COMPLETE}}}' : '@lang('contest.entry.complete')', '{{{Entry::FINALIZE}}}' : '@lang('contest.entry.finalized')', '{{{Entry::ENTRY_MESSAGE}}}' : '@lang('contest.entry.message')', '{{{Entry::APPROVE}}}' : '@lang('contest.entry.approved')', '{{{Entry::INCOMPLETE}}}' : '@lang('contest.entry.new')', '{{{Entry::ERROR}}}' : '@lang('contest.entry.error'): '+entry.error} | echoswitch : log.status }}
                        </span>
                        <div class="" ng-if="log.msg" ng-class="{'pull-left': isResponse(log), 'pull-right': !isResponse(log)}"><i class="fa fa-fw fa-comment-o"></i> @{{ log.msg }}</div>
                        <div class="help-block pull-right" ng-class="{'pull-left': !isResponse(log), 'pull-right': isResponse(log)}">@{{ log.created_at }}</div>
                        <div class="clearfix"></div>
                    </h5>
                </div>
                <a id="bottom"></a>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-12" style="margin-bottom: 10px;">
                        <textarea cols="30" rows="2" class="form-control col-sm-10" focus-me="$index == 0" ng-model="entryMessage"></textarea>
                        <input ng-disabled="!entryMessage" type="button" class="btn btn-sm btn-success btn-block" ng-click="message(entryMessage, entry.id)" value="@lang('contest.leaveMessage')" />
                        <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash && !contestForm.$dirty">
                            <span ng-bind-html="flash"></span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</script>
<script type="text/ng-template" id="entryIncomplete.html">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" ng-click="close()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title">
                <i class="fa fa-warning"></i>
                @lang('contest.entry.incomplete')
                <span entry-card entry="entry" fields="fields"></span>
            </h4>
        </div>
        <form role="form" name="modalForm" class="form-horizontal">
            <div class="modal-body entry-log">
                @lang('contest.entry.incomplete.explain')
                <br>
                <br>
                <ul>
                    <span ng-repeat="error in entry.errors">
                        <li ng-repeat="err in error">@{{ err }}</li>
                    </span>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" ng-click="cancel()">@lang('general.accept')</button>
            </div>
        </form>
    </div>
</script>
<script type="text/ng-template" id="categoryList.html">
    <span ng-if="category.parent_id">
        <span ng-include="'categoryList.html'" onload="category = category.parent; first = false;"></span>
        <i class="fa fa-angle-double-right"></i>
    </span>
    <span ng-if="!category.parent_id"><i class="fa fa-caret-right"></i></span>
    @{{ category.name }}
    @if(isset($contest) && $contest->type == Contest::TYPE_TICKET)
        <span ng-if="category.final" class="label label-default label-fileversion label-as-badge">
            @{{ category.price ? category.price : contest.billing.mainPrice }} @{{ contest.billing.mainCurrency }}
            <span ng-if="entryCat.tickets > 1"> x @{{ entryCat.tickets }} </span>
        </span>
    @endif
</script>
<script type="text/ng-template" id="categoryTree.html">
    <label class="label-normal" ng-class="{'text-muted': modelList.indexOf(category.id) == -1}">
        <a href="" ng-click="category.expanded = !category.expanded;" ng-if="!category.final"><i class="fa fa-fw" ng-class="{'fa-chevron-circle-down': category.expanded, 'fa-chevron-circle-right':!category.expanded}"></i></a>
        <i class="fa fa-fw" ng-if="category.final"></i>
        <span ng-if="selectable">
            <input type="checkbox" name="" checklist-model="modelList" checklist-value="category.id" id="" ng-change="toggleThis(category)"/>
        </span>
        @{{ category.name }}
    </label>
    <div class="clearfix"></div>
    <ul class="category-tree" ng-if="category.expanded">
        <li ng-repeat="category in category.children_categories track by $index" ng-include="'categoryTree.html'"></li>
    </ul>
</script>
<script type="text/ng-template" id="categoryTreeVoteConfig.html">
    <label class="label-normal" ng-class="{'text-muted': modelList.indexOf(category.id) == -1}">
        <a href="" ng-click="category.expanded = !category.expanded;" ng-if="!category.final"><i class="fa fa-fw" ng-class="{'fa-chevron-circle-down': category.expanded, 'fa-chevron-circle-right':!category.expanded}"></i></a>
        <i class="fa fa-fw" ng-if="category.final"></i>
        <span ng-if="selectable">
            <input type="checkbox" name="" checklist-model="modelList" checklist-value="category.id" id="" ng-change="toggleThis(category)"/>
        </span>
        @{{ category.name }}
        <div ng-switch="voting.vote_type" ng-if="modelList.indexOf(category.id) != -1">
            <div ng-switch-when="{{VotingSession::YESNO}}">
                <div class="form-group"  ng-repeat="votecat in voting.voting_categories | filter:{category_id:category.id}">
                    <div class="col-sm-2 control-label">
                        <label>@lang('voting.yesPerCategory')</label>
                    </div>
                    <div class="col-sm-3">
                        <div ng-if="showThis">
                            <input type="number" class="form-control" ng-model="votecat.vote_config.yesPerCategory" min="0">
                        </div>
                        <div ng-if="!showThis">
                            @{{ votecat.vote_config.yesPerCategory }}
                        </div>
                        <span ng-if="showThis">@lang('voting.ceroForAll')</span>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </label>
    <div class="clearfix"></div>
    <ul class="category-tree" ng-if="category.expanded">
        <li ng-repeat="category in category.children_categories track by $index" ng-include="'categoryTree.html'"></li>
    </ul>
</script>
<script type="text/ng-template" id="categoryTreeRankingConfig.html">
    <label class="label-normal" ng-class="{'text-muted': modelList.indexOf(category.id) == -1}">
        <a href="" ng-click="category.expanded = !category.expanded;" ng-if="!category.final"><i class="fa fa-fw" ng-class="{'fa-chevron-circle-down': category.expanded, 'fa-chevron-circle-right':!category.expanded}"></i></a>
        <i class="fa fa-fw" ng-if="category.final"></i>
        <span ng-if="selectable">
            <input type="checkbox" name="" checklist-model="modelList" checklist-value="category.id" id="" ng-change="toggleThis(category)"/>
        </span>
        @{{ category.name }}
    </label>
    <div class="clearfix"></div>
    <ul class="category-tree" ng-if="category.expanded">
        <li ng-repeat="category in category.children_categories track by $index" ng-include="'categoryTree.html'"></li>
    </ul>
</script>

<script type="text/ng-template" id="selectedCategoryTree.html">
    <label class="label-normal" ng-class="{'text-muted': modelList.indexOf(category.id) == -1}" ng-if="modelList.indexOf(category.id) != -1 || modelList.length == 0">
        <a href="" ng-click="category.expanded = !category.expanded;" ng-if="!category.final"><i class="fa fa-fw" ng-class="{'fa-chevron-circle-down': category.expanded, 'fa-chevron-circle-right':!category.expanded}"></i></a>
        <i class="fa fa-fw" ng-if="category.final"></i>
        <span ng-if="selectable">
            <input type="checkbox" name="" checklist-model="modelList" checklist-value="category.id" id="" ng-change="toggleThis(category)"/>
        </span>
        @{{ category.name }}
    </label>
    <div class="clearfix"></div>
    <ul class="category-tree" ng-if="category.expanded">
        <li ng-repeat="category in category.children_categories track by $index" ng-include="'categoryTree.html'"></li>
    </ul>
</script>
<script type="text/ng-template" id="categoryTreeMDConfig.html">
    <div ng-init="conf = getConfig(category)"></div>
    <label class="label-normal" ng-class="{'text-muted': conf.hidden}">
        <a href="" ng-click="category.expanded = !category.expanded;" ng-if="!category.final"><i class="fa fa-fw" ng-class="{'fa-chevron-circle-down': category.expanded, 'fa-chevron-circle-right':!category.expanded}"></i></a>
        <i class="fa fa-fw" ng-if="category.final"></i>
        <span ng-if="selectable">
            <i class="fa fa-fw">
                <input type="checkbox" name="" ng-model="conf.hidden" value="@{{ category.id }}" ng-click="toggleThis(category,'hidden')" uib-tooltip="@lang('metadata.hidden')" />
            </i>
            <i class="fa fa-fw" ng-if="field.type != {{MetadataField::TITLE}} && field.type != {{MetadataField::DESCRIPTION}}">
                <input type="checkbox" name="" ng-model="conf.required" ng-disabled="conf.hidden" value="@{{ category.id }}" ng-click="toggleThis(category,'required')"  uib-tooltip="@lang('metadata.required')"/>
            </i>
        </span>
        @{{ category.name }}
        <span ng-show="conf.required && !conf.hidden" class="text-danger">*</span>
    </label>
    <div class="clearfix"></div>
    <ul class="category-tree" ng-if="category.expanded">
        <li ng-repeat="category in category.children_categories track by $index" ng-include="'categoryTreeMDConfig.html'"></li>
    </ul>
</script>
<script type="text/ng-template" id="alert-modal.html">
    <div class="modal-content" ng-class="'modal-'+status">
        <div class="modal-header">
            <button type="button" class="close" ng-click="close()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title">
                <i class="fa fa-@{{ icon }}"></i>
                @{{ title }}
            </h4>
        </div>
        <div class="modal-body">
            <uib-alert type="@{{ status }}" bind-unsafe-html="message"></uib-alert>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" ng-click="close()">@lang('general.accept')</button>
        </div>
    </div>
</script>


<script type="text/ng-template" id="categoryListPrint.html">
    <span ng-if="category.parent_id">
        <span ng-include="'categoryListPrint.html'" onload="category = getCategory(category.parent_id); first = false;"></span>
        <i class="fa fa-angle-double-right"></i>
    </span>
    <span ng-if="!category.parent_id"></span>
    <trans ng-model='category' trans-prop="'name'"></trans>
</script>
<script type="text/ng-template" id="entryPrint.html">
        <style>
            @media print {  body * {
                    visibility: hidden;
                }
                #print-content * {
                    visibility: visible;
                }
                .modal {
                    position: absolute;
                    left: 0;
                    top: 0;
                    margin: 0;
                    padding: 0;
                    visibility: visible;
                    /**Remove scrollbar for printing.**/
                    overflow: visible !important;
                }
                .modal-dialog {
                    visibility: visible !important;
                    /**Remove scrollbar for printing.**/
                    overflow: visible !important;
                }
            }
        </style>

        <div class="modal-content printEntry">
            <div class="modal-header">
                <button type="button" class="close" ng-click="close()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">
                    <span entry-card entry="entry" fields="entry_metadata_fields"></span>
                    <button type="button" class="btn btn-sm btn-info" ng-click="print()">
                        <i class="fa fa-print"></i>
                        @lang('general.print')
                        (@{{ getPages() }} @lang('general.printPagesAprox'))
                    </button>
                </h4>
            </div>
            <div id="print-content">
                @if(isset($contest))
                    {{ $contest->getAsset(ContestAsset::SMALL_BANNER_HTML)->content }}
                @endif
                <h5 class="col-sm-12"> @lang('general.entryId') : @{{ entry.id }} </h5>
                <h4 ng-repeat="catid in entry.categories_id" class="col-sm-12" ng-include="'categoryListPrint.html'" onload="category = getCategory(catid); first=true; editable=false;" ng-if="showStatic"></h4>
            </div>
            <hr>
            <form role="form" name="modalForm" class="form-horizontal">
                <div class="modal-body entry-print" id="print-content">
                <div class="form-group" ng-repeat="field in entry_metadata_fields" ng-if="!isFieldHidden(field) && field.private == 0 && field.type != {{MetadataField::TAB}}">
                        @include('metadata.print', array('model'=>'field.model.value', 'filemodel'=>'field.model.files', 'mainField'=>'field.model', 'allValues'=>'field.allmodels', 'disabled'=>false))
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" ng-click="cancel()">@lang('general.close')</button>
                <button type="button" class="btn btn-info" ng-click="print()">
                    <i class="fa fa-print"></i>
                    @lang('general.print')
                </button>
            </div>
        </div>
</script>
