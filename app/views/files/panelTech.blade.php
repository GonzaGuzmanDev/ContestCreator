
<script type="text/ng-template" id="editError.html">
        @include('includes.tech-sifter')
</script>

<div flow-init="flowObj"
     flow-files-submitted="$flow.upload()"
     flow-file-success="$file.msg = $message; updateFiles($file, $message, $flow);"
     flow-file-error="$file.msg = $message"
     flow-name="uploader.flow"
     class="file-uploader">
    <div class="row">
        <div ng-class="{'col-sm-12':toggleable,'col-sm-4 col-sm-push-8':!toggleable}">
            <button type="button" class="btn btn-success btn-block" flow-btn ng-if="tech != true">
                <i class="fa fa-upload"></i> @lang('metadata.uploadFile')
            </button>
            <div class="uploads">
                <div ng-repeat="file in $flow.files" ng-class="{danger:file.error, success:file.isComplete()}" class="file-upload-item">
                    <div class="progress">
                        <div class="progress-bar" ng-class="{'progress-bar-warning progress-bar-striped active':file.isUploading(),'progress-bar-success':file.isComplete(),'progress-bar-danger':file.error}" role="progressbar" aria-valuenow="@{{ file.progress() * 100 }}" aria-valuemin="0" aria-valuemax="100" style="width: @{{ file.progress() * 100 }}%;">
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
                                ng-click="file.cancel()" title="@lang('metadata.cancelUpload')"><i class="fa fa-stop"></i></button>
                        <button type="button" class="btn btn-info btn-xs" ng-show="file.isComplete() && file.error"
                                ng-click="file.retry()" title="@lang('metadata.retryUpload')"><i class="fa fa-refresh"></i></button>
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

        <div class="my-files" ng-class="{'col-sm-12':toggleable,'col-sm-12':!toggleable}">
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
                    <span ng-show="showMyFiles && filesLoading != true">
                    <input type="text" ng-model-options="{debounce: 1000}" name="" class="form-control inline input-sm" ng-model="pagination.query" id="" placeholder="@lang('contest.filterFiles')" focus-me="true" autofocus/>
                    <div class="filter-buttons">
                        <div class="text-right">
                            <div class="btn-group">
                                <a class="btn btn-default" ng-href="<?=url('/')?>/@{{ contest.code }}/exportFiles" >
                                    <i class="fa fa-download"></i> @lang('contest.download.entriesList')
                                </a>
                                <button type="button" class="btn btn-default" ng-click="OpenDownloadFiles(filteredFiles)" >
                                    <i class="fa fa-download"></i> @lang('contest.downloadFiles')
                                </button>

                                <button type="button" class="btn" ng-click="toggleFilterBy({{ ContestFile::TECH_NO_STATE }})"
                                        tooltip-placement="bottom" uib-tooltip="@lang('contest.techWaiting')" ng-class="{'btn-default':pagination.statusFilters.indexOf({{ ContestFile::TECH_NO_STATE }}) == -1,'btn-warning':pagination.statusFilters.indexOf({{ ContestFile::TECH_NO_STATE }}) != -1}">
                                    <i class="fa fa-fw fa-clock-o"></i>
                                    <span ng-class="{'label label-warning label-as-badge':pagination.statusFilters.indexOf({{ ContestFile::TECH_NO_STATE }}) == -1,'badge':pagination.statusFilters.indexOf({{ ContestFile::TECH_NO_STATE }}) != -1}">@{{ countEntries({{{ ContestFile::TECH_NO_STATE }}}) }}</span>
                                </button>
                                <button type="button" class="btn" ng-click="toggleFilterBy({{ ContestFile::TECH_OK }})"
                                        tooltip-placement="bottom" uib-tooltip="@lang('contest.techOk')" ng-class="{'btn-default':pagination.statusFilters.indexOf({{ ContestFile::TECH_OK }}) == -1,'btn-success':pagination.statusFilters.indexOf({{ ContestFile::TECH_OK }}) != -1}">
                                    <i class="fa fa-fw fa-thumbs-up"></i>
                                    <span ng-class="{'label label-success label-as-badge':pagination.statusFilters.indexOf({{ ContestFile::TECH_OK }}) == -1,'badge':pagination.statusFilters.indexOf({{ ContestFile::TECH_OK }}) != -1}">@{{ countEntries({{{ ContestFile::TECH_OK }}}) }}</span>
                                </button>
                                <button type="button" class="btn" ng-click="toggleFilterBy({{ ContestFile::TECH_ERROR }})"
                                        tooltip-placement="bottom" uib-tooltip="@lang('contest.techError')" ng-class="{'btn-default':pagination.statusFilters.indexOf({{ ContestFile::TECH_ERROR }}) == -1,'btn-danger':pagination.statusFilters.indexOf({{ ContestFile::TECH_ERROR }}) != -1}">
                                    <i class="fa fa-fw fa-thumbs-down"></i>
                                    <span ng-class="{'label label-danger label-as-badge':pagination.statusFilters.indexOf({{ ContestFile::TECH_ERROR }}) == -1,'badge':pagination.statusFilters.indexOf({{ ContestFile::TECH_ERROR }}) != -1}">@{{ countEntries({{{ ContestFile::TECH_ERROR }}}) }}</span>
                                </button></div>
                            @lang('contest.total')  <span> @{{ pagination.total }} </span>
                        </div>
                    </div>
                    <br>
                    <div class="btn-group" role="group" aria-label="Small button group">
                        @foreach(Format::getAllTypesData() as $id => $label)
                            <button type="button" class="btn btn-default" ng-click="toggleType({{$id}})" ng-class="{active: typeSelected({{$id}})}" uib-tooltip="{{$label}}" tooltip-placement="bottom">
                                <i class="fa @{{ getTypeIcon({{{$id}}}) }} @{{ getTypeTextStyle({{{$id}}}) }}"></i>
                            </button>
                        @endforeach

                        <button type="button" ng-class="{'btn-success':pagination.inEntries == true}" class="btn btn-default" ng-click="toggleFilesInEntry()" uib-tooltip="" tooltip-placement="bottom">
                            @lang('contest-file.inEntry')
                        </button>
                        <button type="button" ng-class="{'btn-danger':delFiles}" class="btn btn-default" ng-click="toggleDeletedFiles()" ng-class="{active: typeSelected({{$id}})}" uib-tooltip="" tooltip-placement="bottom">
                            @lang('contest-file.deleted')
                        </button>
                        <button type="button" ng-class="{'btn-danger':pagination.encodeErrorFiles}" class="btn btn-default" ng-click="toggleEncodeErrorFiles()" ng-class="{active: typeSelected({{$id}})}" uib-tooltip="" tooltip-placement="bottom">
                            @lang('general.filesStatus.error')
                        </button>
                    </div>
                    <div class="pull-right">
                        <button type="button" class="btn btn-default btn-sm" ng-click="filterFiles(null, true)">
                            <i class="fa fa-refresh"></i>
                            @lang('contest.updateInscription')
                        </button>
                        <button type="button" class="btn btn-sm btn-default" ng-click="toggleView()" uib-tooltip="@lang('general.toggleView')" tooltip-placement="bottom">
                            <i class="fa" ng-class="{'fa-th': viewList, 'fa-align-justify': !viewList}"></i>
                        </button>
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

                <div class="clearfix"></div>
                <br>
                <div class="col-sm-12 " ng-if="!filesLoading">
                    <div class="btn-group btn-group-sm">
                        <button ng-repeat="metadataValueIndex in fileMetadataIdIndex" class="btn btn-sm" ng-click="toggleMetadataValue(metadataValueIndex)"
                                ng-class="{'btn-default':pagination.metadataFields.indexOf(metadataValueIndex) == -1, 'btn-success':pagination.metadataFields.indexOf(metadataValueIndex) != -1}"> @{{ metadataValueIndex }} </button>
                    </div>
                </div>
                    <div class="col-md-12 float-right">
                        <uib-pagination boundary-links="true" total-items="pagination.total" items-per-page="pagination.perPage" max-size="7" rotate="false" ng-model="pagination.page" class="pagination-sm" ng-show="pagination.total > pagination.perPage" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></uib-pagination>
                    </div>
                </div>
            </div>


            <files-gallery class="my-files-list" ng-show="showMyFiles" ng-class="{'list': viewList, 'thumbs': !viewList}">
                <div class="row">
                    <div ng-if="filteredFiles.length == 0 && filesLoading != true" class="text-muted text-center">
                        @lang('general.nofiles')
                    </div>
                    <span ng-repeat="files in filesRows" ng-if="file.deleted_at == null  && filesLoading != true" ng-init="outerIndex=$index"> <!--ng-if="file.entry_metadata_values[0].entry_id">-->
                        <span ng-repeat="file in files track by $index" ng-if="delFiles == false" ng-init="innerIndex=$index">
                            <fieldset data-ng-disabled="file.deleted" class="file-item" ng-class="{'file-deleted':file.deleted ==1, 'col-md-12':viewList,'col-md-6':!viewList,'col-md-6':!viewList && !toggleable,'selected':showSelection && isSelected(file, field), 'error': file.status == 3}">
                                <div class="thumbnail col-md-10 col-sm-9 col-xs-7">
                                    <div class="img-holder" ng-click="openGallery(filteredFiles, innerIndex, outerIndex, true)">
                                        <span class="helper"></span>
                                        <img ng-src="@{{ file.thumb }}" alt=""/>
                                    </div>
                                    <span class="title">
                                        <i class="fa @{{ getTypeIcon(file.type) }} @{{ getTypeTextStyle(file.type) }}"></i>
                                        <span ng-if="!file.editable" ng-click="toggleFile(file, field);" class="uneditable">@{{ file.name }}</span>
                                        <div class="input-group input-group-sm input-rename" ng-if="file.editable">
                                            <input type="text" ng-model="file.name" class="form-control">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button" ng-click="saveFilename(file);"><i class="fa fa-check"></i></button>
                                            </span>
                                        </div>
                                        <div class="btn-group-sm pull-right">
                                            <button ng-click="deleteFile(file, field)" class="btn btn-sm btn-danger btn-remove"><i class="fa fa-trash"></i></button>
                                            <button ng-click="file.editable = true;" class="btn btn-info btn-sm btn-rename" ng-if="!file.editable"><i class="fa fa-edit"></i></button>
                                            <button ng-if="file.type != 4" type="button" class="btn btn-sm btn-default dropdown-toggle btn-remove" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-cog"></i>
                                            </button>
                                            <ul class="dropdown-menu" style="top: 0%; width: 50%;">
                                                <li style="padding: 0 5px 5px;" ng-if="file.type == 1 || file.type == 3" class="text-center">
                                                    <span class="h4">@lang('contest-file.rotate')</span>
                                                    <div class="btn-group text-center" role="group">
                                                        <button type="button" class="btn btn-default searchBox" ng-click="ReEncodeFile(file, 270)"> -90&deg; <i class="fa fa-rotate-left"></i>  </button>
                                                        <button type="button" class="btn btn-default searchBox" ng-click="ReEncodeFile(file, 180)"> 180&deg; </button>
                                                        <button type="button" class="btn btn-default searchBox" ng-click="ReEncodeFile(file, 90)"> 90&deg;<i class="fa fa-rotate-right"></i> </button>
                                                        <button type="button" class="btn btn-default searchBox" ng-click="ReEncodeFile(file, 0)"> 0&deg; </button>
                                                    </div>
                                                    <!--<select class="form-control searchBox" id="input@{{$index}}" ng-model="reEncode.rotate">
                                                    <option class="searchBox"></option>
                                                    <option class="searchBox"> 90 </option>
                                                    <option class="searchBox"> 180 </option>
                                                    <option class="searchBox"> 270 </option>
                                                </select>-->
                                                </li>
                                                <li style="padding: 0 5px 5px;" ng-if="file.type == 0 || file.type == 2">
                                                    <div class="input-group">
                                                        <span class="input-group-addon" id="basic-addon1"> @lang('contest-file.startTime') </span>
                                                        <input ng-click="$event.stopPropagation();" type="text" ng-model="reEncode.start" class="form-control searchBox"
                                                               placeholder="@{{ file.contest_file_versions[0].config ? encodeValues(file.contest_file_versions[0].config, 'start').start : 'HH:MM:SS'}}"/>
                                                    </div>
                                                </li>
                                                <li style="padding: 0 5px 5px;" ng-if="file.type == 0 || file.type == 2">
                                                    <div class="input-group">
                                                        <span class="input-group-addon" id="basic-addon1"> @lang('contest-file.endTime') </span>
                                                        <input ng-click="$event.stopPropagation();" type="text" ng-model="reEncode.end" class="form-control searchBox"
                                                               placeholder="@{{ file.contest_file_versions[0].config ? encodeValues(file.contest_file_versions[0].config, 'end').end : 'HH:MM:SS'}}"/>
                                                    </div>
                                                </li>
                                                <button ng-if="file.type == 0 || file.type == 2" class="btn btn-default btn-block" ng-click="ReEncodeFile(file, reEncode)"> Aceptar </button>
                                                <label class="label label-success" ng-if="showReencode[file.id]"> @{{ showReencode[file.id] }} </label>
                                                <button type="button" class="btn btn-default btn-block" ng-click="ReEncodeFile(file)">
                                                    @lang('contest.encode')
                                                </button>
                                                <button type="button" class="btn btn-default btn-block" ng-click="RemakeThumbs(file)">
                                                    @lang('contest.makethumbs')
                                                </button>
                                            </ul>
                                        </div>
                                    </span>
                                    <div class="col-md-6">
                                        <span ng-repeat="fileVersion in file.contest_file_versions">
                                            <span ng-if="fileVersion.source == 1">
                                                <a class="label label-default label-fileversion label-as-badge" ng-href="@{{ fileVersion.url }}" download="@{{ file.name }}"
                                                   ng-class="{'label-info': fileVersion.status == {{ContestFileVersion::UPLOADING}},'label-default': fileVersion.status == {{ContestFileVersion::AVAILABLE}},'label-danger': fileVersion.status == {{ContestFileVersion::ERROR}},'label-warning': fileVersion.status == {{ContestFileVersion::UPLOAD_INTERRUPTED}} }">
                                                    <i class="fa fa-lg" ng-class="{'fa-cloud-upload': fileVersion.status == {{ContestFileVersion::UPLOADING}},'fa-check': fileVersion.status == {{ContestFileVersion::AVAILABLE}},'fa-remove': fileVersion.status == {{ContestFileVersion::ERROR}},'fa-unlink': fileVersion.status == {{ContestFileVersion::UPLOAD_INTERRUPTED}} }"></i>
                                                    @{{ fileVersion.label }} @{{ fileVersion.extension }} | @{{ formatBytes(fileVersion.size)}}
                                                    <span ng-if="fileVersion.status == {{ContestFileVersion::UPLOADING}}">| @{{ fileVersion.percentage }}%</span>
                                                    <span ng-if="superAdmin"> | id: @{{ fileVersion.id }}</span>
                                                </a>
                                                <span ng-if="fileVersion.duration > 0" class="label label-primary label-fileversion-info label-as-badge">
                                                    @{{ secondsToHms(fileVersion.duration) }}
                                                </span>
                                                <span ng-if="fileVersion.sizes != null && fileVersion.sizes.length > 0" class="label label-primary label-fileversion-info label-as-badge">
                                                    @{{ fileVersion.sizes }}
                                                </span>
                                                <br>
                                            </span>
                                            <span ng-if="fileVersion.source != 1">
                                                <a class="label label-fileversion label-as-badge" ng-href="@{{ fileVersion.url }}" download="@{{ file.name }}"
                                                   ng-class="{'label-primary': fileVersion.status == {{ContestFileVersion::QUEUED}},'label-warning': fileVersion.status == {{ContestFileVersion::ENCODING}},'label-success': fileVersion.status == {{ContestFileVersion::AVAILABLE}},'label-danger': fileVersion.status == {{ContestFileVersion::ERROR}} }">
                                                    <i class="fa fa-lg" ng-class="{'fa-clock-o': fileVersion.status == {{ContestFileVersion::QUEUED}},'fa-cog fa-spin': fileVersion.status == {{ContestFileVersion::ENCODING}},'fa-check': fileVersion.status == {{ContestFileVersion::AVAILABLE}},'fa-remove': fileVersion.status == {{ContestFileVersion::ERROR}} }"></i>
                                                    @{{ fileVersion.label }}
                                                    <span ng-if="fileVersion.status == {{ContestFileVersion::ENCODING}}">| @{{ fileVersion.percentage }}%</span>
                                                    <span ng-if="superAdmin">| id: @{{ fileVersion.id }}</span>
                                                </a>
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-md-6">
                                        <span user-card user-card-model="file.user"></span>
                                        <span ng-repeat="entry in file.entry_metadata_values track by $index">
                                            <div>
                                                <a href="#/entry/@{{file.entry_metadata_values[0].id}}">
                                                    <span entry-card entry="entry" class=""></span>
                                                </a>
                                                @lang('contest.field') @{{ entry.label }}
                                            </div>
                                        </span>
                                    </div>
                                    <label class="label label-warning" ng-if="reEncodeQueue(file) == 'reEncodeQueue'"> @lang('contest-file.reEcondingQueue') </label>
                                    <label class="label label-success" ng-if="reEncodeQueue(file) == 'reEncoding'"> @lang('contest-file.reEncoding') </label>
                                    <label class="label label-info" ng-if="reEncodeQueue(file) == 'reEncoded'"> @lang('contest-file.reEncoded') </label>

                                    <span style="font-size: 15px;" ng-if="file.description && file.status != 3" class="label label-md label-danger"> Error: @{{ file.description }} </span>

                                    <input type="checkbox" name="" class="btn-add" ng-checked="isSelected(file, field);" ng-click="toggleFile(file, field);" id="" ng-if="showSelection && file.status!=3"/>
                                    <div class="my-progress" ng-if="file.status != 2">
                                        <span ng-if="file.status == 0" class="">@lang('general.filesStatus.queued')</span>
                                        <uib-progressbar class="active" value="file.progress" type="info" ng-if="file.status == 1">
                                            @{{file.progress}}%
                                        </uib-progressbar>
                                    </div>

                                </div>
                                <div class="pull-right col-md-2 col-sm-3 col-xs-5">
                                    <button type="button" ng-if="file.tech_status == {{ContestFile::TECH_NO_STATE}}" class="btn btn-default btn-md" ng-click="changeStatus(file, {{ ContestFile::TECH_OK }})"> <i class="fa fa-thumbs-up"></i> </button>
                                    <button type="button" ng-if="file.tech_status == {{ ContestFile::TECH_OK }}" class="btn btn-success btn-md" ng-click="changeStatus(file, {{ContestFile::TECH_NO_STATE}})"> <i class="fa fa-thumbs-up"></i> </button>
                                    <button type="button" ng-if="file.tech_status == {{ContestFile::TECH_NO_STATE}}" class="btn btn-default btn-md" ng-click="changeStatus(file, {{ ContestFile::TECH_ERROR }})"> <i class="fa fa-thumbs-down"></i> </button>
                                    <button type="button" ng-if="file.tech_status == {{ ContestFile::TECH_ERROR }}" class="btn btn-danger btn-md" ng-click="changeStatus(file, {{ContestFile::TECH_NO_STATE}})"> <i class="fa fa-thumbs-down"></i> </button>
                                </div>
                            </fieldset>
                            <div class="clearfix" ng-show="($parent.$index + 1) % (toggleable ? 3 : 4) == 0"></div>
                        </span>
                    </span>


                    <span ng-repeat="file in deletedFiles" ng-if="delFiles == true">
                        <fieldset data-ng-disabled="file.deleted" class="file-item" ng-class="{'file-deleted':file.deleted ==1, 'col-md-12':viewList,'col-md-6':!viewList,'col-md-6':!viewList && !toggleable,'selected':showSelection && isSelected(file, field), 'error': file.status == 3}">
                            <div class="thumbnail col-md-10">
                                <div class="img-holder" ng-click="openGallery(files, $index)">
                                    <span class="helper"></span>
                                    <img ng-src="@{{ file.thumb }}" alt=""/>
                                </div>
                                <span class="title">
                                    <i class="fa @{{ getTypeIcon(file.type) }} @{{ getTypeTextStyle(file.type) }}"></i>
                                    <span ng-if="!file.editable" ng-click="toggleFile(file, field);" class="uneditable">@{{ file.name }}</span>
                                    <div class="input-group input-group-sm input-rename" ng-if="file.editable">
                                        <input type="text" ng-model="file.name" class="form-control">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button" ng-click="saveFilename(file);"><i class="fa fa-check"></i></button>
                                        </span>
                                    </div>
                                </span>
                                <span>
                                    <div ng-repeat="entry in file.entry_metadata_values">
                                        <a href="#/entry/@{{file.entry_metadata_values[0].entry_id}}">
                                            #@{{ entry.entry_id }}
                                        </a>
                                        Campo: @{{ entry.label }}
                                    </div>
                                </span>
                                <input type="checkbox" name="" class="btn-add" ng-checked="isSelected(file, field);" ng-click="toggleFile(file, field);" id="" ng-if="showSelection && file.status!=3"/>
                            </div>
                        </fieldset>
                        <div class="clearfix" ng-show="($index + 1) % (toggleable ? 3 : 4) == 0"></div>
                    </span>



                    <label class="col-sm-12 text-center" ng-if="filesLoading">
                        <div class="clearfix"></div>
                        <i class="fa fa-circle-o-notch fa-spin fa-3x"></i>
                        <div class="text-center">@lang('general.loading')</div>
                    </label>
                    <div class="col-sm-offset-4 col-sm-4 text-center" ng-if="filteredEntries.length == 0 && entriesLoading == false">
                        <div class="clearfix"></div>
                        <div class="alert alert-info alert-sm" role="alert"> <i class="fa fa-info-circle"></i> @lang('contest.noEntries')</div>
                    </div>

                    <!--<div class="text-center" ng-if="!lastFileShown && delFiles != true">
                        <div class="clearfix"></div>
                        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
                    </div>-->
                    <div class="clearfix"></div>
                </div>
            </files-gallery>
            <div class="clearfix"></div>
        </div>

        <div class="col-md-12 float-right">
            <uib-pagination boundary-links="true" total-items="pagination.total" items-per-page="pagination.perPage" max-size="7" rotate="false" ng-model="pagination.page" class="pagination-sm" ng-show="pagination.total > pagination.perPage" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></uib-pagination>
        </div>
    </div>
    <div class="clearfix"></div>
</div>