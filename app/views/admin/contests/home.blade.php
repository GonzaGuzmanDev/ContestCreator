@extends('admin.contests.form', array('active' => 'info'))
@section('form')

    <div class="row">
        <div class="col-sm-6">
            <ul class="list-group">
                <div ng-repeat="inscription in inscriptions.slice().reverse()">
                    <div ng-if="inscription['role'] == null">
                        <h4 class="well well-sm">
                            <a href="#/admin/inscriptions-list" >Inscripciones </a>
                            <span class="badge">@{{inscription['total']}}</span>
                        </h4>
                    </div>
                    <div ng-if="inscription['name'] == null && inscription['role'] != null">
                        <div class="col-sm-offset-1 col-sm-10">
                        <li class="list-group-item list-group-item-info">
                            <span class="badge">@{{inscription['total']}}</span>
                            @{{allRoles[inscription['role']]}}
                        </li>
                        </div>
                    </div>
                    <div ng-if="inscription['name'] != null && inscription['role'] != null">
                        <div class="col-sm-offset-1 col-sm-8">
                            <li class="list-group-item">
                                <span class="badge">@{{inscription['total']}}</span>
                                @{{inscription['name']}}
                            </li>
                        </div>
                    </div>
                </div>
            </ul>
        </div>
        <div class="col-sm-6">
            <h4 class="well well-sm">
                Entries
            </h4>
        </div>
        <div class="col-sm-12 col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Calendar</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12 col-lg-12">
                            <div ui-calendar="uiConfig.calendar" ng-model="contestEvents"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
