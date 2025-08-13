<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" ng-click="close()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title"> @lang('voting.selectAutoAbstains') </h4>
    </div>
    <form role="form" name="modalForm" class="form-horizontal" data-ng-submit="@yield('modal-submit', '')">
        <div class="modal-body modal-fixed">
            <!--<input checklist-model="groupBulks" checklist-value="allMetadataFields" ng-change="addAllBulkMetadata(allMetadataFields)" type="checkbox"> @lang('general.selectAll')-->
            <div ng-repeat="field in fields" style="color:black;">
                <div class="page-header">
                    <h3>@{{ field[$index].entry_metadata_field.label }} </h3>
                </div>
                <div class="checkbox" ng-repeat="fieldValue in field" style="color:black;">
                    <input type="checkbox" checklist-model="selected" checklist-value="fieldValue"> @{{ fieldValue.value }}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash">
                <span ng-bind-html="flash"></span>
            </div>
            <button type="button" class="btn btn-default" ng-disabled="sending" ng-click="close()">@lang('general.cancel')</button>
            <button type="button" class="btn btn-default" ng-disabled="modalForm.$invalid || sending" ng-click="accept()">@lang('general.accept')</button>
        </div>
    </form>
</div>

