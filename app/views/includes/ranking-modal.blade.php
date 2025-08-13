<datalist id="metadataFieldsAutoComplete">
    <option ng-repeat="field in metadataFields track by $index"
            value="@{{field.label}}"
            data-id="@{{ field.id }}"
    >
        @{{ fields.templates }}
    </option>
</datalist>
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" ng-click="cancel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title"> @lang('voting.ranking') </h4>
    </div>
    <form role="form" name="modalForm" class="form-horizontal" data-ng-submit="@yield('modal-submit', '')">
        <div class="modal-body modal-fixed">
            <div class="clearfix"></div>
            <h4 class="well well-sm"> @lang('voting.selectRankingMetadata') </h4>
            <div class="clearfix"></div>
            <div class="form-group col-lg-12 col-md-12 col-sm-12"  ng-class="{error: errors.name}">
                <label for="inputType" class="col-sm-2 control-label">@lang('voting.metadata')</label>
                <div class="col-sm-8 col-md-6 col-lg-4">
                    <select ng-model="selectedMetadata" ng-change="addMetadataRanking()" id="rankingDropDown" class="form-control form-inline">
                        <option ng-repeat="field in metadataFields"
                                value="@{{ field.id }},@{{ field.label }}">
                            @{{field.label}}
                        </option>
                    </select>
                </div>
            </div>
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th> Campo</th>
                    <!--<th> Chequear </th>-->
                    <th> Borrar </th>
                </tr>
                </thead>
                <tr ng-repeat="selected in selectedMetadataArray track by $index">
                    <td> @{{ selected }} </td>
                    <!--<td> <button class="btn btn-sm btn-default" ng-click="checkMetadata()"> chequear </button></td>-->
                    <td>
                        <a class="btn btn-xs btn-danger float-right" ng-click="unselectMetadata($index)">
                            <i class="fa fa-close"></i>
                        </a>
                    </td>
                </tr>
            </table>
            <div class="clearfix"></div>
            <h4 class="well well-sm">@lang('contest.entryCategories')</h4>
            <div class="form-group">
                <label class="col-sm-2 control-label">@lang('contest.entryCategories')</label>
                <div class="col-sm-8">
                    <div class="form-control-static">
                        <ul class="category-tree">
                            <li ng-repeat="category in categories track by $index" ng-include="'categoryTreeRankingConfig.html'" onload="selectable=true;modelList = selectedCategories;"></li>
                        </ul>
                    </div>
                </div>
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

