@extends('admin.contests.form', array('active' => 'collections'))
@section('form')
    <h4 class="well well-sm">
        <a href="#{{{ $superadmin ? '/contests/edit/@{{contest.code}}/collections' : '/admin/collections' }}}" class="" role="button"><i class="fa fa-arrow-left"></i> @lang('contest.tab.collections')</a>
        /
        <span ng-show="!collection.id">@lang('general.create')</span>
        <span ng-show="collection.id">@lang('general.edit')</span>
    </h4>

    <uib-tabset active="active">
        <uib-tab index="0" ng-if="colaborator == null">
            <uib-tab-heading>
                <i class="fa fa-sliders"></i> @lang('contest.VoteConfig')
            </uib-tab-heading>
            <div class="form-group" ng-class="{error: errors.name}">
                <label for="inputName" class="col-sm-2 control-label">@lang('general.name')</label>
                <div class="col-sm-8 col-md-6 col-lg-4">
                    <input type="text" class="form-control col-md-3" placeholder="@lang('general.name')" ng-model="collection.name" required ng-if="showThis">
                    <div class="form-control-static" ng-if="!showThis">@{{ collection.name }}</div>
                    <div ng-show="errors.name" class="help-inline text-danger form-control-static">@{{errors.name.toString()}}</div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <p class="text-right">@lang('collection.start')</p>
                </div>
                <div class="col-sm-4 col-md-2 col-lg-2">
                    <div ng-if="!showThis" class="form-control-static">
                        @{{collection.start_at}}
                    </div>
                    <div ng-if="showThis">
                        @include('includes.datetimepicker', array('field'=>'collection.start_at', 'placeholder' => Lang::get('collection.start')))
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <p class="text-right">@lang('collection.end')</p>
                </div>
                <div class="col-sm-4 col-md-2 col-lg-2">
                    <div ng-if="!showThis" class="form-control-static">
                        @{{collection.finish_at}}
                    </div>
                    <div ng-if="showThis">
                        @include('includes.datetimepicker', array('field'=>'collection.finish_at', 'placeholder' => Lang::get('collection.end')))
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">@lang('general.private')</label>
                <div class="col-sm-8">
                    <div class="checkbox">
                        <label ng-if="showThis">
                            <input type="checkbox" name="" ng-model="collection.private" id="" ng-true-value="1" ng-false-value="0"/>
                        </label>
                        <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': collection.private == 1,'fa-square-o': collection.private != 1 }"></i>
                    </div>
                </div>
            </div>

            <h4 class="well well-sm">
                <span>@lang('contest.entries')</span>
            </h4>

            <div class="form-group col-lg-12 col-md-12 col-sm-12"  ng-class="{error: errors.name}">
                <label for="inputType" class="col-sm-2 control-label">@lang('voting.metadata')</label>
                <div class="col-sm-8 col-md-6 col-lg-4" ng-show="showThis">
                    <select ng-model="selectedMetadata" ng-change="selectMetadata(selectedMetadata)" id="metadataDropDown" class="form-control form-inline">
                        <option ng-repeat="field in metadataFields"
                                value="@{{ field.id }},@{{ field.label }}" class="col-sm-8 col-md-6 col-lg-4">
                            @{{field.label}}
                        </option>
                    </select>
                </div>
            </div>
            <div class="form-group col-lg-12 col-md-12 col-sm-12">
            <div class="col-sm-2 control-label"></div>
            <div class="col-sm-6 col-md-6 col-lg-6">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th class="col-sm-6 col-md-6 col-lg-6"> Campo</th>
                    <!--<th> Chequear </th>-->
                    <th class="col-sm-1 col-md-1 col-lg-1 text-center" ng-show="showThis"> Borrar </th>
                </tr>
                </thead>
                <tr ng-repeat="selected in selectedMetadataArray track by $index">
                    <td> @{{ selected.split(',')[1] }} </td>
                    <!--<td> <button class="btn btn-sm btn-default" ng-click="checkMetadata()"> chequear </button></td>-->
                    <td ng-show="showThis" class="text-center">
                        <a class="btn btn-xs btn-danger" ng-click="unselectMetadata($index)">
                            <i class="fa fa-close"></i>
                        </a>
                    </td>
                </tr>
            </table>
            </div>
            </div>

            <div class="form-group col-lg-12 col-md-12 col-sm-12"  ng-class="{error: errors.name}">
                <label for="inputType" class="col-sm-2 control-label">@lang('contest.voting-session')</label>
                <div class="col-sm-6 col-md-6 col-lg-2" ng-show="showThis">
                    <select ng-model="collection.voting_session_id" id="votingSessionDropDown" class="form-control form-inline">
                        <option value=""> Ninguno </option>
                        <option ng-repeat="voteSession in votingSessions"
                                value="@{{ voteSession.id }}" ng-selected="voteSession.id == collection.voting_session_id" class="col-sm-8 col-md-6 col-lg-4">
                            @{{voteSession.name}}
                        </option>
                    </select>
                </div>
                <div ng-repeat="voteSession in votingSessions" ng-if="voteSession.id == collection.voting_session_id"
                     class="col-sm-8 col-md-6 col-lg-4 form-control-static" ng-show="!showThis">
                    @{{voteSession.name}}
                </div>
            </div>

            <div class="form-group" ng-if="collection.voting_session_id">
                <label class="col-sm-2 control-label">@lang('collection.show-prize')</label>
                <div class="col-sm-8">
                    <div class="checkbox">
                        <label ng-if="showThis">
                            <input type="checkbox" name="" ng-model="collection.show_prize" id="" ng-true-value="1" ng-false-value="0"/>
                        </label>
                        <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': collection.show_prize == 1,'fa-square-o': collection.show_prize != 1 }"></i>
                    </div>
                    <div ng-if="collection.show_prize === 1" class="btn btn-default" ng-click="selectVoteType(vote)" ng-class="{'btn-success': vote.selected == true }" ng-repeat="vote in voteConfig">
                        @{{ vote.name }}
                    </div>
                </div>
            </div>
        </uib-tab>
        <uib-tab index="1" ng-if="collection.private === 1">
            <uib-tab-heading>
                <i class="fa fa-user-secret"></i> @lang('general.private')
            </uib-tab-heading>
            <div>
                <div class="row">
                    <div class="col-md-6">
                        <p><span class="badge">1.</span> @lang('collection.inviteEmails')</p>
                        <textarea id="" ng-model="collection.newEmails" style="width: 100%; max-width: 100%; min-width: 100%;" rows="10" class="form-control"></textarea>
                        <span class="help-block">@lang('contest.VoteAddJudgesInviteEmailsFormat')</span>
                        <div class="text-right">
                            <br>
                            <i class="fa fa-spin fa-spinner" data-ng-show="collection.sending"></i>
                            @{{ collection.msg }}
                            <span class="text-danger">@{{ collection.errors }}</span>
                            <button type="button" class="btn btn-info" ng-disabled="collection.newEmails == ''" data-ng-click="inviteEmails()"><i class="fa fa-plus"></i> @lang('collection.inviteEmails')</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p><span class="badge">2.</span> @lang('contest.VoteAddJudgesInviteKeys')</p>
                        <button type="button" class="btn btn-info" data-ng-click="requestKeys()"><i class="fa fa-ticket"></i> @lang('contest.VoteInviteSimpleKeys')</button>
                        <i class="fa fa-spin fa-spinner" data-ng-show="collection.requesting"></i>
                        <br>
                        <br>
                        <div class="alert alert-success alert-sm" ng-if="collection.invitationKeys.length">
                            <i class="fa fa-info-circle"></i>
                            @lang('contest.VoteAddJudgesKeysUrl')
                            <br>
                            <a href="<?=url("/".$contest->code."/invite-key/")?>"><?=url("/".$contest->code."/invite-key/")?></a>
                        </div>
                        <div class="row">
                            <div data-ng-repeat="keysList in collection.invitationKeys" class="col-sm-2">
                                <span data-ng-repeat="key in keysList">
                                    @{{ key }}<br>
                                </span>
                            </div>
                        </div>
                        <br>
                        <br>
                    </div>
                </div>
                <br>
                <h4 class="well well-sm">
                    <span> Invitaciones </span>
                </h4>

                <table class="table table-condensed table-hover judges-table">
                    <thead>
                        <tr>
                            <th> invitaciones </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="invite in collection.invites" class="col-md-12 col-lg-12">
                        <td ng-if="invite.email"> @{{ invite.email }} </td>
                        <td ng-if="invite.key"> @{{ invite.key }} </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </uib-tab>
    </uib-tabset>
@endsection