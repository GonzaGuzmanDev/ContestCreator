@extends('admin.contests.form', array('active' => 'invitation'))
@section('form')
    <h4 class="well well-sm">
        <a href="#/contests/edit/@{{contest.code}}/invitation" class="" role="button"><i class="fa fa-arrow-left"></i> @lang('contest.invitations')</a>
        <span ng-show="!invitationId.id">@lang('contest.creatingInvitation')</span>
        <span ng-show="invitationId.id">@lang('contest.editingInvitation') @{{ invitationId.name }}</span>
    </h4>

    <div class="form-group" ng-class="{error: errors.name}">
        <label for="inputName" class="col-sm-2 control-label">@lang('general.name')</label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            @if($superadmin)
                <input type="text" class="form-control col-md-3" placeholder="@lang('general.name')" id="inputName" required ng-model="invitationId.name">
                <div class="clearfix"></div>
            @endif
            <div user-card user-card-model="invitationId.name" class="selected-user-card form-control-static"></div>
        </div>
        <div ng-show="errors.name" class="help-inline text-danger form-control-static">@{{errors.name.toString()}}</div>
    </div>

    <div class="form-group" ng-class="{error: errors.name}">
        <label for="inputSubject" class="col-sm-2 control-label">@lang('contest.invitation.subject')</label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            @if($superadmin)
                <input type="text" class="form-control col-md-3" placeholder="@lang('contest.invitation.subject')" id="inputSubject" required ng-model="invitationId.subject">
                <div class="clearfix"></div>
            @endif
            <div user-card user-card-model="invitationId.subject" class="selected-user-card form-control-static"></div>
        </div>
        <div ng-show="errors.subject" class="help-inline text-danger form-control-static">@{{errors.subject.toString()}}</div>
    </div>

    <div class="clearfix"></div><div class="form-group" ng-class="{error: errors.name}">
        <label for="inputContent" class="col-sm-2 control-label">@lang('contest.invitation.content')</label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            @if($superadmin)
                <input type="text" class="form-control col-md-3" placeholder="@lang('contest.invitation.content')" id="inputContent" required ng-model="invitationId.content">
                <div class="clearfix"></div>
            @endif
            <div user-card user-card-model="invitationId.content" class="selected-user-card form-control-static"></div>
        </div>
        <div ng-show="errors.content" class="help-inline text-danger form-control-static">@{{errors.subject.toString()}}</div>
    </div>
    <div class="clearfix"></div>

    <h4 class="well well-sm" ng-show="sent">
        <span>@lang('contest.receivers')</span>
    </h4>

@endsection