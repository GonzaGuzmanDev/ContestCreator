<div class="modal-content">
    <div class="modal-header" style="background: black;">
        <div class=lightbox-nav ng-swipe-left="Lightbox.nextImage()" ng-swipe-right="Lightbox.prevImage()">
            <button class="pull-right btn-sm btn btn-default" aria-hidden=true ng-click=$dismiss()><i class="fa fa-close fa-2x"></i></button>
            <h3 ng-prevent-drag>
                <span ng-if="Lightbox.images.tech"> @{{ Lightbox.images[Lightbox.index].name }} </span>
                <span ng-if="Lightbox.images.tech != true">@{{ getName() || getNoName() }}</span>
        <span class="text-muted">
            -
            <trans ng-model='field' ng-if="Lightbox.images.tech != true" trans-prop="'label'"></trans>
            @{{ Lightbox.index + 1 }} / @{{ Lightbox.images.length }}
        </span>
            </h3>
            <div class="pull-right col-md-2">
            </div>
        </div>
    </div>
<div class="modal-body" hotkey="{right: Lightbox.nextImage, left: Lightbox.prevImage}"><!--ng-class="{'full-height': Lightbox.images.tech != true, 'footer-height': Lightbox.images.tech == true}">-->

    <div class="lightbox-image-container">
        <span ng-if="Lightbox.isPlayable(Lightbox.image)">
            <div image-zoomer version="Lightbox.imageVersion" thumb="Lightbox.imageVersion.thumb" class="image-zoomer" ng-if="Lightbox.isImage(Lightbox.image)" ></div>
            <div ng-if=Lightbox.isAudio(Lightbox.image) class="media-player">
                <audio controls width="100%" height="50%" autoplay style="height: 50%;width: 100%;">
                    <source vsrc="@{{version.file}}" type="audio/mpeg" ng-repeat="version in Lightbox.audioOptions.sources" html5vfix>
                </audio>
            </div>
            <div ng-if=Lightbox.isVideo(Lightbox.image) class="media-player">
                <video autoplay width="100%" height="100%" controls>
                    <source vsrc="@{{version.file}}" type="video/@{{version.extension}}" ng-repeat="version in Lightbox.videoOptions.sources" html5vfix>
                </video>
            </div>
            <div ng-if="Lightbox.isDocument(Lightbox.image)" class="media-player">
                <iframe ng-src="@{{ trustSrc(version.file) }}" class="doc-frame" frameborder="0" ng-repeat="version in Lightbox.docOptions.sources"></iframe>
            </div>
        </span>
        <div ng-if="!Lightbox.isPlayable(Lightbox.image)" class="file-not-playable">
            <div ng-switch="Lightbox.image.status">
                <img ng-src=@{{Lightbox.getThumbUrl(Lightbox.image)}} />
                <div class="file-status">
                    <div ng-switch-when="0 && || Lightbox.reEncodeQueue(Lightbox.images[Lightbox.index]) != 1" class="alert alert-info alert-inline">
                        <i class="fa fa-clock-o fa-3x"></i>
                        <br>
                        @lang('metadata.filewitingencoding')
                    </div>
                    <div ng-switch-when="1" class="alert alert-transparent alert-inline">
                        @lang('metadata.fileencoding')
                        <br>
                        <uib-progressbar class="progress-striped active" value="Lightbox.image.progress" type="warning">@{{ Lightbox.image.progress }}%</uib-progressbar>
                    </div>
                    <div ng-switch-when="2">
                        <a ng-repeat="version in Lightbox.image.contest_file_versions" ng-href="@{{ version.url }}" class="btn btn-success" download target="_blank">
                            <i class="fa fa-4x @{{ getTypeIcon(Lightbox.image.type) }}"></i>
                            <h3>@{{ Lightbox.image.name }}.@{{ Lightbox.image.contest_file_versions[0].extension }}</h3>
                            <i class="fa fa-download fa-3x"></i>
                            <br>
                            @lang('metadata.filedownload')
                        </a>
                    </div>
                    <div ng-switch-when="3" class="alert alert-danger alert-inline">
                        <i class="fa fa-ban fa-3x"></i>
                        <br>
                        @lang('metadata.fileencodederror')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer" style="background-color: black;">
    <div class="btn-group btn-group-lg pull-left">
        <a class="btn btn-lg btn-default" ng-click=Lightbox.prevImage() ng-disabled="Lightbox.images.length == 1"><i class="fa fa-arrow-left"></i></a>
        <a class="btn btn-lg btn-default" ng-click=Lightbox.nextImage() ng-disabled="Lightbox.images.length == 1"><i class="fa fa-arrow-right"></i></a>
        @if(null !== Auth::user())
            <span ng-if="{{Auth::user()->isSuperAdmin()}}" ng-repeat="fileVersion in Lightbox.images[Lightbox.index].contest_file_versions">
                <a class="btn btn-sm btn-default" ng-href="@{{ fileVersion.url }}" download="@{{ Lightbox.images[Lightbox.index].name }}">
                    <i class="fa fa-download"> @{{ fileVersion.label }} </i>
                </a>
            </span>
        @endif
    </div>
    <!--<button type="button" class="pull-left btn btn-lg btn-default" ng-class="{'btn-success': Lightbox.autoNext == true}" ng-click="Lightbox.autoNext = !Lightbox.autoNext"> @lang('contest-file.autoNext') </button>-->
    <span ng-if="Lightbox.images.tech">
        <div style="font-size: 15px;margin-right:40%;" ng-if="Lightbox.images[Lightbox.index].description && Lightbox.images[Lightbox.index].status != 3" class="label label-lg label-danger"> Error: @{{ Lightbox.images[Lightbox.index].description }} </div>
        <label ng-if="Lightbox.reEncodeInQueue == true || Lightbox.reEncodeQueue(Lightbox.images[Lightbox.index]) == 1" style="font-size: 15px;" class="label label-lg label-success"> @lang('contest-file.reEcondingQueue') </label>
        <button type="button" ng-if="Lightbox.images[Lightbox.index].tech_status == {{ ContestFile::TECH_NO_STATE }}" class="btn btn-default btn-lg" ng-click="Lightbox.changeStatus(Lightbox.images[Lightbox.index], {{ContestFile::TECH_OK}}, Lightbox.autoNext)"> <i class="fa fa-thumbs-up"></i> </button>
        <button type="button" ng-if="Lightbox.images[Lightbox.index].tech_status == {{ ContestFile::TECH_OK }}" class="btn btn-success btn-lg" ng-click="Lightbox.changeStatus(Lightbox.images[Lightbox.index], {{ContestFile::TECH_NO_STATE}}, Lightbox.autoNext)"> <i class="fa fa-thumbs-up"></i> </button>
        <button type="button" ng-if="Lightbox.images[Lightbox.index].tech_status == {{ ContestFile::TECH_NO_STATE }}" class="btn btn-default btn-lg" ng-click="Lightbox.changeStatus(Lightbox.images[Lightbox.index], {{ ContestFile::TECH_ERROR }}, Lightbox.autoNext)"> <i class="fa fa-thumbs-down"></i> </button>
        <button type="button" ng-if="Lightbox.images[Lightbox.index].tech_status == {{ ContestFile::TECH_ERROR }}" class="btn btn-danger btn-lg" ng-click="Lightbox.changeStatus(Lightbox.images[Lightbox.index], {{ContestFile::TECH_NO_STATE}}, Lightbox.autoNext)"> <i class="fa fa-thumbs-down"></i> </button>
        <button ng-if="Lightbox.images[Lightbox.index].type != 4" type="button" class="btn btn-lg btn-default" ng-click="Lightbox.edit[Lightbox.index] = !Lightbox.edit[Lightbox.index]">
            <i class="fa fa-cog"></i>
        </button>
        <div ng-if="Lightbox.edit[Lightbox.index]" class="pull-right">
            <div  class="btn-group pull-right" ng-if="Lightbox.images[Lightbox.index].type == 1 || Lightbox.images[Lightbox.index].type == 3">
                <div class="btn-group text-center" role="group">
                    <button type="button" class="btn btn-lg btn-default searchBox" ng-click="Lightbox.ReEncodeFile(Lightbox.images[Lightbox.index], 270)"> <i class="fa fa-rotate-left"></i>  </button>
                    <button type="button" class="btn btn-lg btn-default searchBox" ng-click="Lightbox.ReEncodeFile(Lightbox.images[Lightbox.index], 180)"> 180 </button>
                    <button type="button" class="btn btn-lg btn-default searchBox" ng-click="Lightbox.ReEncodeFile(Lightbox.images[Lightbox.index], 90)"> <i class="fa fa-rotate-right"></i> </button>
                </div>
            </div>
            <div class="pull-right" ng-if="Lightbox.images[Lightbox.index].type == 0 || Lightbox.images[Lightbox.index].type == 2">
                <div class="input-group pull-right">
                    <div class="input-group input-group-lg">
                        <span class="input-group-addon" id="basic-addon1"> @lang('contest-file.endTime') </span>
                        <input ng-click="$event.stopPropagation();" type="text" ng-model="Lightbox.reEncode.end" class="form-control searchBox" placeholder="HH:MM:SS"/>
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-success" ng-click="Lightbox.ReEncodeFile(Lightbox.images[Lightbox.index], Lightbox.reEncode)"> @lang('general.accept') </button>
                        </div>
                    </div>
                </div>
                <div class="input-group pull-right">
                    <div class="input-group input-group-lg">
                        <span class="input-group-addon" id="basic-addon1"> @lang('contest-file.startTime') </span>
                        <input ng-click="$event.stopPropagation();" type="text" ng-model="Lightbox.reEncode.start" class="form-control searchBox" placeholder="HH:MM:SS"/>
                    </div>
                </div>
            </div>
        </div>
    </span>
</div>
</div>