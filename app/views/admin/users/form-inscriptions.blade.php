@extends('admin.users.form', array('section' => 'users'))
@section('tabs')
@include('admin.users.form-tabs', array('active' => 'inscriptions'))
@endsection
@section('form')
<h4>@lang('user.inscriptionsData')</h4>
<div class="col-sm-9 col-lg-10">
    <table class="table table-striped table-hover table-condensed">
        <thead>
        <tr>
            <th></th>
            <th><?=Lang::get('user.contest')?></th>
            <th><?=Lang::get('user.role')?></th>
            <th><?=Lang::get('user.inscriptionType')?></th>
        </tr>
        </thead>
        <tbody>
        <tr ng-repeat="inscription in userInscriptions">
            <td></td>
            <td>@{{inscription.contest_name}}</td>
            <td>@{{inscription.role}}</td>
            <td>@{{inscription.type}}</td>
        </tr>
        </tbody>
    </table>
</div>
@endsection