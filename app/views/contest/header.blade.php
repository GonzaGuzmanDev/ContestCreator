<? /** @var Contest $contest */
$canEditBanner = isset($contest) ? $contest->checkUserPermission(Contest::DESIGN) : '';
?>
<div header-banner class="header-banner" status="status" controls="controls" banner='{{$banner}}' ng-class="{'editing':status.editing}">
    <!-- $$ IAB HARDCODE FOR BANNERS -->
    @if(isset($contest) && $contest->id == 301)
        @if($banner == 19)
            <img src="@lang('header.bigBanner', array('contest' => $contest))" width="100%">
        @else
            <img src="@lang('header.smallBanner', array('contest' => $contest))" width="100%">
        @endif
    @else
    <div ng-if="!status.editing || status.advanced" ng-bind-html="status.bannerObj.html" class="banner-content"></div>
    @endif
    @if($canEditBanner)
    <div ng-if="status.editing">
        <div class="edit-separator" ng-if="status.advanced"></div>
        <textarea ng-model="status.bannerObj.html" class="advanced-editor" ng-if="status.advanced"></textarea>
        <div text-angular ng-model="status.bannerObj.html" ng-if="!status.advanced" ta-target-toolbars='toolbar1'></div>
        @if($canEditBanner)
            <div class="edit-buttons text-right">
                <i class="fa fa-spin fa-circle-o-notch text-warning" ng-if="status.saving"></i>
                <button type="button" class="btn btn-success" ng-click="controls.save()">@lang('contest.banner.save')</button>
                <button type="button" class="btn btn-default" ng-click="controls.cancel()">@lang('contest.banner.cancel')</button>
            </div>
            <div class="toggle-buttons text-right">
                <button type="button" class="btn btn-info" ng-if="!status.advanced" ng-click="controls.toggle()">@lang('contest.banner.toggleAdvanced')</button>
                <button type="button" class="btn btn-info" ng-if="status.advanced" ng-click="controls.toggle()">@lang('contest.banner.toggleSimple')</button>
            </div>
        @endif
        <text-angular-toolbar ng-if="!status.advanced" name="toolbar1" ta-toolbar="[['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'pre', 'quote'],
['bold', 'italics', 'underline', 'strikeThrough', 'ul', 'ol'],
['justifyLeft','justifyCenter','justifyRight', 'justifyFull'],
['redo', 'undo', 'clear'],
['insertImage', 'insertLink']]"></text-angular-toolbar>
        <div class="clearfix edit-end"></div>
    </div>
    <div class="banner-edit" ng-if="!status.editing">
        <span class="text-success" ng-if="status.saved">
            <i class="fa fa-check text-success"></i>
            @lang('general.saved')
        </span>
        <button type="button" class="btn btn-default" ng-click="status.editing=true">@lang('contest.banner.edit')</button>
    </div>
    @endif
</div>
