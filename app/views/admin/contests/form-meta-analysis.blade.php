@extends('admin.contests.form', array('active' => 'meta-analysis'))
@section('form')
    <h4 class="well well-sm">
        @lang('contest.meta-analysis')
        {{--@if(!$superadmin)
            <i class="fa fa-question-circle text-info" popover="@lang('contest.explain.meta-analysis')" popover-placement="right" popover-trigger="mouseenter" class="btn btn-default"></i>
        @endif--}}
        <div class="clearfix"></div>
    </h4>

    <h3 class="well well-sm">
        Inscripciones
    </h3>

    <div class="col-lg-12 col-md-12">
        <div ng-repeat="types in metadataAnalytics.inscriptionTypes">
            <h4 class="well well-sm">
                @{{ types.name }}
            <div ng-repeat="data in types[0]">
                <h5 class="well well-sm">
                    Categoria: @{{ data.name }}
                </h5>
                <h5>
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th class="col-lg-3 col-md-3"> entry ID </th>
                        <th class="col-lg-3 col-md-3"> Status </th>
                        <th class="col-lg-3 col-md-3"> Inscriptor </th>
                        <th class="col-lg-3 col-md-3"> Pais </th>
                    </tr>
                    </thead>
                    <tr ng-repeat="entry in data.entries">
                        <td>@{{ entry.id }}</td>
                        <td  ng-switch="entry.status">
                            <span ng-switch-when="0"> Incompleto </span>
                            <span ng-switch-when="1"> Completo </span>
                            <span ng-switch-when="2"> Finalizado </span>
                            <span ng-switch-when="3"> Aprobado </span>
                            <span ng-switch-when="4"> Error </span>
                        </td>
                        <td>@{{ entry.email }}</td>
                        <td>@{{ entry.country ? entry.country : "-" }}</td>
                    </tr>
                </table>
                </h5>
            </div>
            </h4>
            <br>
            <br>
        </div>
    </div>
    <div class="clearfix"></div>
    <br>
    <h4 class="well well-sm">
        Metadata de las inscripciones
    </h4>
    <div class="col-lg-12 col-md-12">
        <div ng-repeat="metaField in metadataAnalytics.metadataFields"
             ng-if="metaField.type == {{MetadataField::SELECT}} ||
             metaField.type == {{MetadataField::MULTIPLE}}"
             class="col-lg-4 col-md-4">
            <h4 class="well well-sm">
            @lang('metadata.field') @{{metaField.label | limitTo: 30}}@{{metaField.label.length > 20 ? '...' : ''}}
            </h4>
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th> Opcion </th>
                    <th> Total </th>
                </tr>
                </thead>
                <tr ng-repeat="item in metaField.values">
                    <td class="col-lg-6 col-md-6">
                        @{{ item.value }}
                    </td>
                    <td class="col-lg-6 col-md-6">
                        @{{ item.total }}
                    </td>
                </tr>
            </table>
            <br>
        </div>
    </div>
@endsection