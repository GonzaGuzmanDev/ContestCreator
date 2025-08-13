<div class="btn-group dropup">
    <button type="button" class="btn btn-info" ng-click="addMetadataField(roleId)"><i class="fa fa-plus"></i> @lang('metadata.addField')</button>
    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu" role="menu">
        @foreach(InscriptionMetadataField::getAllTypesData() as $typeId => $typeLabel)
            <li><a href="" ng-click="addMetadataField(roleId,{{$typeId}})">{{$typeLabel}}</a></li>
        @endforeach
    </ul>
</div>