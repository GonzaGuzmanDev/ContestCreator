<div class="modal-content">
    <div class="loading-alert" ng-if="sending == true" style="z-index: 10;">
        <div  class="alert alert-danger">
            <i class="fa fa-circle-o-notch fa-spin"></i> @lang('general.downloading')
        </div>
    </div>
    <div class="modal-header">
        <button type="button" class="close" ng-click="cancel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title"> @lang('voting.exportResults') </h4>
    </div>
    <form role="form" name="modalForm" class="form-horizontal" data-ng-submit="@yield('modal-submit', '')">
        <div class="modal-body modal-fixed">
            <div class="well well-sm">
                <h4 class="modal-title"> @lang('voting.selectExportType') </h4>
            </div>
            <div>
                <input type="radio" ng-model="export.type" value="excel" ng-change="type = {{ExportResult::TYPE_EXCEL}}"> @lang('voting.excel')
            </div>
            <div>
                <input type="radio" ng-model="export.type" value="jsonExport" ng-change="type = {{ExportResult::TYPE_JSON}}"> @lang('voting.json')
            </div>
            <div>
                <input type="radio" ng-model="export.type" value="doc" ng-change="type = {{ExportResult::TYPE_DOC}}"> @lang('voting.document')
            </div>
            <div class="clearfix"></div>
            <br>
            <div class="well well-sm">
                <h4 class="modal-title">
                    @lang('voting.hideNoVotes') <input type="checkbox" ng-model="hideEntryNotVoted.enable">
                </h4>
            </div>
            <div class="clearfix"></div>
            <br>
            <div class="well well-sm">
                <h4 class="modal-title"> @lang('voting.selectExportFields') </h4>
            </div>
            <input checklist-model="groupBulks" checklist-value="allMetadataFields" ng-change="addAllBulkMetadata(allMetadataFields)" type="checkbox"> @lang('general.selectAll')
            <div ng-repeat="field in metadataFields">
                <input type="checkbox" checklist-model="bulks[type]" checklist-value="field.id" ng-change="addBulkMetadata(field.id)"> @{{ field.label }} <span ng-if="field.type == 10"> <i type="icon" class="fa fa-file-archive-o"></i> ( FILES ) </span>
            </div>
        </div>
        <div class="modal-footer">
            <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash">
                <span ng-bind-html="flash"></span>
            </div>
            <button type="button" class="btn btn-default" ng-disabled="sending" ng-click="cancel()">@lang('general.cancel')</button>
            <button type="button" class="btn btn-default" ng-disabled="modalForm.$invalid || sending" ng-click="accept()">@lang('general.accept')</button>
        </div>
    </form>
</div>

