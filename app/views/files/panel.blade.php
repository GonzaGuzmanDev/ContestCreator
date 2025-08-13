<div flow-init="flowObj"
     flow-files-submitted="flowObj.OnFilesSubmitted($files,$event,$flow)"
     flow-file-success="flowObj.OnFileSuccess($file,$event,$flow,$message)"
     flow-file-progress="flowObj.OnFileProgress($file)"
     flow-file-retry="console.log('eaeaea');flowObj.OnFileRetry( $file, $flow )"
     flow-file-error="$file.msg = $message;flowObj.OnFileError($file, $message);"
     flow-name="uploader.flow"
     class="file-uploader">
    <div class="row">
        <div ng-class="{'col-sm-12':toggleable,'col-sm-4 col-sm-push-8':!toggleable}">
            <button type="button" class="btn btn-success btn-block" flow-btn flow-attrs="{accept: fileTypes}" ng-if="tech != true">
                <i class="fa fa-upload"></i> @lang('metadata.uploadFile')
            </button>
            <div class="uploads">
                <div ng-repeat="file in $flow.files" ng-class="{danger:file.error, success:file.isComplete()}" class="file-upload-item">
                    <div class="progress">
                        <div class="progress-bar"
                             ng-class="{'progress-bar-warning progress-bar-striped active':file.isUploading(),'progress-bar-success':file.isComplete(),'progress-bar-danger':file.error}" role="progressbar" aria-valuenow="@{{ file.progress() * 100 }}" aria-valuemin="0" aria-valuemax="100" style="width: @{{ file.progress() * 100 }}%;">
                            <div class="progress-bar-content">
                            <span ng-show="file.isUploading() || file.paused">@{{ (file.progress() * 100) | number:0 }}%</span>
                            <span ng-if="file.averageSpeed">@ @{{ file.averageSpeed | bytes }}/s</span>
                            <span class="upload-status" ng-show="file.error"><i class="fa fa-exclamation-circle"></i> Error</span>
                            <span class="upload-status" ng-show="file.isComplete() && !file.error"><i class="fa fa-check"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="pull-right">
                        <button type="button" class="btn btn-primary btn-xs" ng-show="!file.isComplete() && !file.paused"
                                ng-click="file.pause()" title="@lang('metadata.pauseUpload')"><i class="fa fa-pause"></i></button>
                        <button type="button" class="btn btn-info btn-xs" ng-show="!file.isComplete() && file.paused"
                                ng-click="file.resume()" title="@lang('metadata.resumeUpload')"><i class="fa fa-play"></i></button>
                        <button type="button" class="btn btn-danger btn-xs" ng-show="!file.isComplete()"
                                ng-click="cancelUpload(file)" title="@lang('metadata.cancelUpload')"><i class="fa fa-stop"></i></button>
                        <button type="button" class="btn btn-info btn-xs" ng-show="file.isComplete() && file.error"
                                ng-click="retryUpload(file)" title="@lang('metadata.retryUpload')"><i class="fa fa-refresh"></i></button>
                    </div>
                    <div class="name">@{{ file.name }}</div>
                    <div class="clearfix"></div>
                </div>
                <button type="button" class="btn btn-danger btn-xs" ng-click="$flow.cancel()" ng-show="$flow.isUploading()">
                    <span class="fa fa-ban"></span> @lang('metadata.cancelAllUploads')
                </button>
                <button type="button" class="btn btn-default btn-xs" ng-click="$flow.cancel(); $flow.files = [];" ng-show="!$flow.isUploading() && $flow.files.length">
                    <span class="fa fa-trash-o"></span> @lang('metadata.clearUploads')
                </button>
            </div>
        </div>
        <div class="my-files" ng-class="{'col-sm-12':toggleable,'col-sm-8 col-sm-pull-4':!toggleable}">
            <div class="header">
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-info btn-block" ng-click="toggle();" ng-show="toggleable">
                            <i class="fa fa-files-o"></i> @lang('contest.files.myfiles')
                            <i class="fa fa-angle-double-up" ng-show="showMyFiles"></i>
                        </button>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-sm-12 filters">
                <span ng-show="showMyFiles">
                    <input type="text" name="" class="form-control inline input-sm" ng-model="pagination.query" id="" placeholder="@lang('contest.filterFiles')"/>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                        @foreach(Format::getAllTypesData() as $id => $label)
                            <button ng-if="(filterType({{$id}}) == 1 && config.types.length > 1) || config.types.length == 0" type="button" class="btn btn-default" ng-click="toggleType({{$id}})" ng-class="{active: typeSelected({{$id}})}" uib-tooltip="{{$label}}" tooltip-placement="bottom">
                                <i class="fa @{{ getTypeIcon({{{$id}}}) }} @{{ getTypeTextStyle({{{$id}}}) }}"></i>
                            </button>
                        @endforeach
                    </div>
                    <div class="pull-right">
                        <!--<button type="button" class="btn btn-sm btn-default" ng-click="toggleView()" uib-tooltip="@lang('general.toggleView')" tooltip-placement="bottom">
                            <i class="fa" ng-class="{'fa-align-justify': viewList, 'fa-th': !viewList}"></i>
                        </button>-->
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false" uib-tooltip="@lang('general.sortBy')" tooltip-placement="left"><i class="fa" ng-class="{'fa-sort-alpha-desc':pagination.sortInverted,'fa-sort-alpha-asc':!pagination.sortInverted} "></i></button>
                            <ul class="dropdown-menu" role="menu">
                                <li ng-class="{'active': pagination.sortBy == 'name'}"><a href="" ng-click="setSortBy('name')">@lang('general.sortBy.name')</a></li>
                                <li ng-class="{'active': pagination.sortBy == 'created_at'}"><a href="" ng-click="setSortBy('created_at')">@lang('general.sortBy.date')</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </span>
                    </div>
                </div>
            </div>
            <files-gallery class="my-files-list" ng-show="showMyFiles" ng-class="{'list': viewList, 'thumbs': !viewList}">
                <div class="row">
                    <div ng-if="filteredFiles.length == 0" class="text-muted text-center">
                        @lang('general.nofiles')
                    </div>
                    <span ng-repeat="file in filteredFiles track by $index">
                        <div class="file-item-in-entry" ng-class="{'col-md-12':viewList,'col-md-4':!viewList,'col-md-3':!viewList && !toggleable,'selected':showSelection && isSelected(file, field), 'error': file.status == 3}">
                            <div class="thumbnail">
                                <a href="" class="text-default pull-left file-selection" ng-class="{'text-white': isSelected(file, field) == true}" ng-click="toggleFile(file, field);" ng-if="showSelection"
                                ><span ng-if="isSelected(file, field) == true"><i class="fa fa-fw fa-lg fa-check-square-o"></i></span><span ng-if="isSelected(file, field) == false"><i class="fa fa-fw fa-lg fa-square-o"></i></span></a>
                                <div ng-click="openGallery(filteredFiles, $index)">
                                    <div class="img-holder-in-entry" ng-if="file.type == '{{Format::VIDEO}}' || file.type == '{{Format::IMAGE}}' || file.type == '{{Format::AUDIO}}'">
                                        <span class="helper"></span>
                                        <img ng-src="@{{ file.thumb }}" alt="" />
                                    </div>
                                    <div class="img-holder-in-entry" ng-if="file.type == '{{Format::DOCUMENT}}' || file.type == '{{Format::OTHER}}'">
                                        <span class="helper"></span>
                                        <i class="fa fa-2x @{{ getTypeIcon(file.type) }} @{{ getTypeTextStyle(file.type) }}" style="margin: 10px 0;"></i>
                                    </div>
                                </div>
                                <span class="title">
                                    <i class="fa @{{ getTypeIcon(file.type) }} @{{ getTypeTextStyle(file.type) }}" ng-if="!file.editable"></i>
                                    <span ng-if="!file.editable" ng-click="toggleFile(file, field);" class="uneditable title-text">@{{ file.name }}</span>
                                    <div class="input-group input-group-sm input-rename" ng-if="file.editable">
                                        <input type="text" ng-model="file.name" class="form-control">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button" ng-click="saveFilename(file);"><i class="fa fa-check"></i></button>
                                            </span>
                                        </input>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="pull-right" ng-if="!file.editable">
                                        <a href="" ng-click="file.editable = true;" class="btn btn-info btn-xs btn-rename" title="@lang('contest-file.editFileName')"><i class="fa fa-fw fa-edit"></i></a>
                                        <a href="" ng-click="deleteFile(file, field)" class="btn btn-xs btn-danger btn-remove" title="@lang('contest.deleteFile')"><i class="fa fa-fw fa-trash"></i></a>
                                    </div>
                                    <div class="my-progress" ng-if="file.status != {{ContestFile::ENCODED}}">
                                        <span ng-if="file.status == {{ContestFile::QUEUED}}" class="">
                                            <i class="fa fa-clock-o"></i> @lang('general.filesStatus.queued')
                                        </span>
                                        <uib-progressbar class="active" value="file.progress" type="info" ng-if="file.status == <?=ContestFile::UPLOADING;?>">
                                            <div class="progress-bar-content">
                                            @lang('general.filesStatus.uploading') @{{file.progress | number : 1}}%
                                            </div>
                                        </uib-progressbar>
                                        <uib-progressbar class="active" value="file.progress" type="info" ng-if="file.status == <?=ContestFile::ENCODING;?>">
                                            @{{file.progress}}%
                                        </uib-progressbar>
                                        <div class="file-error" ng-if="file.status == <?=ContestFile::ERROR;?>">
                                            <span class="text-danger" uib-tooltip="@lang('general.filesStatus.errorexplain')"><i class="fa fa-warning"></i> @lang('general.filesStatus.error')</span>
                                        </div>
                                        <div class="file-error" ng-if="file.status == <?=ContestFile::UPLOAD_INTERRUPTED;?>">
                                            <span class="text-warning" uib-tooltip="@lang('general.filesStatus.uploadinterruptedexplain')"><i class="fa fa-unlink"></i> @lang('general.filesStatus.uploadinterrupted')</span>
                                        </div>
                                        <div class="file-error" ng-if="file.status == <?=ContestFile::CANCELED;?>">
                                            <span class="text-muted" uib-tooltip="@lang('general.filesStatus.canceledexplain')"><i class="fa fa-ban"></i> @lang('general.filesStatus.canceled')</span>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </span>

                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </span>

                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <uib-pagination boundary-links="true" total-items="pagination.total" items-per-page="pagination.perPage" max-size="7" rotate="false" ng-model="pagination.page" class="pagination-sm" ng-show="pagination.total > pagination.perPage" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></uib-pagination>
                    </div>
                </div>
            </files-gallery>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="clearfix"></div>
</div>