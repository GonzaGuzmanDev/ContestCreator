@extends('layouts.modal')

@section('modal-title')
    <i class="fa fa-download"></i> <?=Lang::get('contest.downloadFiles')?>
@endsection

@section('modal-content')

    <h4>Para descargar el material, utilice firefox + plugin "down them all" </h4>
    <h4 class="btn btn-success"> <a ng-href="https://addons.mozilla.org/es/firefox/addon/downthemall/"> Plugin </a> </h4>
    <br>
    <br>

    <div class="btn-group">
        <span type="button" class="btn btn-md btn-default" ng-click="originals = !originals" ng-class="{'btn-default':originals == 0, 'btn-success':originals == 1}"> Originales </span>
        <span type="button" class="btn btn-md btn-default" ng-click="encodes = !encodes" ng-class="{'btn-default':encodes == 0, 'btn-success':encodes == 1}"> Encodeados </span>
        <span type="button" class="btn btn-md btn-default" ng-click="descargarJSON()"> Download Json </span>
    </div>
<br>
<br>
<br>
    Download files name
    <div class="btn-group">
        <span type="button" class="btn btn-md btn-default" ng-click="addToName('entry.id')"> Entry Id </span>
        <span type="button" class="btn btn-md btn-default" ng-click="addToName('fileVersion.id')"> File Version Id </span>
        <span type="button" class="btn btn-md btn-default" ng-click="addToName('entry.label')"> Entry Label </span>
        <span type="button" class="btn btn-md btn-default" ng-click="addToName('file.name')"> File Name </span>
        <span type="button" class="btn btn-md btn-default" ng-click="addToName('fileVersion.extension')"> Extension </span>
        <span type="button" class="btn btn-md btn-default" ng-click="deleteValues()"> Borrar </span>
    </div>
    <br>
    <h4>@{{ fileName }}</h4>
    <br>
<span ng-repeat="file in filesRows track by $index" >
            <span ng-if="originals == 1" ng-repeat="fileVersion in file.contest_file_versions">
                <span ng-if="fileVersion.source == 1">
                    <span ng-repeat="entry in file.entry_metadata_values" ng-init="name = getName(entry.id, fileVersion.id, entry.label, file.name, fileVersion.extension)">
                        <a class="label label-default label-fileversion label-as-badge" ng-href="@{{ fileVersion.url }}" download="@{{ name }}"
                           ng-class="{'label-info': fileVersion.status == {{ContestFileVersion::UPLOADING}},'label-default': fileVersion.status == {{ContestFileVersion::AVAILABLE}},'label-danger': fileVersion.status == {{ContestFileVersion::ERROR}} }">
                            <i class="fa fa-lg" ng-class="{'fa-cloud-upload': fileVersion.status == {{ContestFileVersion::UPLOADING}},'fa-check': fileVersion.status == {{ContestFileVersion::AVAILABLE}},'fa-remove': fileVersion.status == {{ContestFileVersion::ERROR}} }"></i>
                            @{{ name }}
                        </a>
                        <br>
                    </span>
                </span>
            </span>

            <span ng-if="encodes == 1" ng-repeat="fileVersion in file.contest_file_versions">
                <span ng-if="fileVersion.source == 0">
                    <span ng-repeat="entry in file.entry_metadata_values" ng-init="name = getName(entry.id, fileVersion.id, entry.label, file.name, fileVersion.extension)">
                        <a class="label label-default label-fileversion label-as-badge" ng-href="@{{ fileVersion.url }}" download="@{{ name }}"
                           ng-class="{'label-info': fileVersion.status == {{ContestFileVersion::UPLOADING}},'label-default': fileVersion.status == {{ContestFileVersion::AVAILABLE}},'label-danger': fileVersion.status == {{ContestFileVersion::ERROR}} }">
                            <i class="fa fa-lg" ng-class="{'fa-cloud-upload': fileVersion.status == {{ContestFileVersion::UPLOADING}},'fa-check': fileVersion.status == {{ContestFileVersion::AVAILABLE}},'fa-remove': fileVersion.status == {{ContestFileVersion::ERROR}} }"></i>
                            @{{ name }}
                        </a>
                        <br>
                    </span>
                </span>
            </span>
</span>
@endsection

@section('modal-actions')
    <button type="button" class="btn btn-default" ng-disabled="deleting" ng-click="close()"><?=Lang::get('general.close')?></button>
@endsection