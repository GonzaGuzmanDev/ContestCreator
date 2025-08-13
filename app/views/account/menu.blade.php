<div class="thumbnail picture-holder" ng-controller="ProfilePictureController">
    <a href="{{Auth::user()->getProfilePictureURL()}}" data-lightbox="pic-image" title="{{Auth::user()->fullName()}}" class="">
        <img ng-src="{{Auth::user()->getProfilePictureURL('preview')}}?v=@{{picDate}}" alt="">
    </a>
    <div flow-init="{target: pictureUploadPath, singleFile:true, testChunks:false}"
         flow-files-submitted="$flow.upload()"
         flow-file-success="$file.msg = $message; updatePicture();"
         flow-file-error="$file.msg = $message" class="picture-uploader">

        <div class="w2">
            <div ng-repeat="file in $flow.files" ng-class="{danger:file.error, success:file.isComplete()}">
                <uib-progressbar ng-show="file.isUploading()" animate="false" value="file.progress() * 100" type="success" ng-class="{active:file.isUploading()}"><b>@{{file.progress() | percentage:1}}</b></uib-progressbar>
                <div ng-show="file.error"><i class="fa fa-exclamation-circle text-danger"></i> Error @{{file.msg.flash}}</div>
                <div ng-show="file.isComplete()"><i class="fa fa-check-circle text-success"></i></div>
            </div>
        </div>
        <div class="w2 text-right">
            <span flow-btn class="btn btn-primary btn-sm picture-uploader-btn">@lang('account.changePicture')</span>
        </div>
    </div>
</div>
<div class="list-group">
    <a href="#/account/data" class="list-group-item" ng-class="{active: activeMenu == 'data'}"><i class="fa fa-user"></i> @lang('account.data')</a>
    <a href="#/account/security" class="list-group-item" ng-class="{active: activeMenu == 'security'}"><i class="fa fa-shield"></i> @lang('account.security')</a>
    <a href="#/account/config" class="list-group-item" ng-class="{active: activeMenu == 'config'}"><i class="fa fa-cogs"></i> @lang('account.config')</a>
</div>