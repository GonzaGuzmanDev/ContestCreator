@extends('admin.formats.form', array('section' => 'formats'))
@section('tabs')
@include('admin.formats.form-tabs', array('active' => 'contests'))
@endsection
@section('form')
    <div class="form-group">
        <div class="col-sm-10">
            <div class="checkbox" ng-repeat="contest in contests">
                <label>
                    <input type="checkbox" ng-model="contest.selected" ng-checked="contest.selected" class="ng-pristine ng-untouched ng-valid"> @{{ contest.name + ' (' + contest.code + ')' }}
                </label>
            </div>
            <div ng-show="errors.super" class="help-inline text-danger form-control-static ng-binding ng-hide"></div>
        </div>
    </div>
@endsection