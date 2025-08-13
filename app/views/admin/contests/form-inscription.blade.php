@extends('admin.contests.form', array('active' => 'inscriptions-list'))
@section('form')
    <script type="text/ng-template" id="customTemplate.html">
        @include('includes.user-typeahead')
    </script>
    <h4 class="well well-sm">
        <a href="#{{{ $superadmin ? '/contests/edit/@{{contest.code}}/inscriptions-list' : '/admin/inscriptions-list' }}}" class="" role="button"><i class="fa fa-arrow-left"></i> @lang('contest.inscriptions-list')</a>
        <span ng-show="!inscription.id">/@lang('contest.creatingInscription')</span>
        @if($superadmin)
            <div class="btn-group">
                <button ng-show="!inscription.id" type="button"  class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" uib-tooltip="@lang('contest.options')">
                    <i class="fa fa-cog"></i>
                </button>
                <ul class="dropdown-menu">
                    <li> <a href="" ng-click="importList()">@lang('contest.loadList')</a> </li>
                </ul>
            </div>
        @endif
        <span ng-show="inscription.id">/@lang('contest.editingInscription') @{{ inscription.user.first_name }} @{{ inscription.user.last_name }} (@{{ inscription.user.email }})</span>
    </h4>
    <div class="form-group" ng-class="{error: errors.user}">
        <label for="inputUser" class="col-sm-2 control-label">@lang('general.user')</label>
        <div class="col-sm-8 col-md-6 col-lg-4">

            <div ng-if="showThis">
            <input type="text" class="form-control col-md-3" placeholder="@lang('contest.searchUser')" id="inputUser" required
                   ng-model="inscription.user" autocomplete="off"
                   uib-typeahead="user as user.email for user in uData.getData($viewValue) | filter:$viewValue | limitTo:10"
                   typeahead-template-url="userTypeahead.html">
            <div class="clearfix"></div>
            </div>

            <div user-card user-card-model="inscription.user" class="selected-user-card form-control-static"></div>
        </div>
        <div ng-show="errors.user" class="help-inline text-danger form-control-static">@{{errors.user.toString()}}</div>
    </div>


    <!-- Campos de registro para crear nuevos usuarios con invitacion -->
    <div ng-if="inscription.user != null && exist(inscription.user)">
        <div class="form-group" ng-class="{error: errors.name}">
            <label for="inputRegisterFirstName" class="col-sm-2 control-label">@lang('general.name')</label>
            <div class="col-sm-8 col-md-6 col-lg-4">

                    <input type="text" class="form-control col-md-3" placeholder="@lang('general.name')" id="inputRegisterFirstName" required ng-model="user.firstName">
                    <div class="clearfix"></div>

            </div>
            <div ng-show="errors.name" class="help-inline text-danger form-control-static">@{{errors.name.toString()}}</div>
        </div>

        <div class="form-group" ng-class="{error: errors.lastName}">
            <label for="inputRegisterLastName" class="col-sm-2 control-label">@lang('general.lastName')</label>
            <div class="col-sm-8 col-md-6 col-lg-4">

                    <input type="text" class="form-control col-md-3" placeholder="@lang('general.lastName')" id="inputRegisterLastName" required ng-model="user.lastName">
                    <div class="clearfix"></div>

            </div>
            <div ng-show="errors.lastName" class="help-inline text-danger form-control-static">@{{errors.lastName.toString()}}</div>
        </div>

        <div class="form-group" ng-class="{error: user.password.$invalid && !user.password.$pristine}">
            <label for="password" class="col-sm-2 control-label"><?=Lang::get('user.password')?></label>
            <div class="col-sm-8 col-md-6 col-lg-4">
                <input type="password" class="form-control" id="password" placeholder="" name="password" ng-model="user.password" ng-required="!user.id">
            </div>
        </div>
        <div class="form-group" ng-class="{error: user.repeat_password.$invalid && !user.repeat_password.$pristine}">
            <label for="repeat_password" class="col-sm-2 control-label"><?=Lang::get('user.repeatPassword')?></label>
            <div class="col-sm-8 col-md-6 col-lg-4">
                <input type="password" class="form-control" id="repeat_password" placeholder="" name="repeat_password" ng-model="user.repeat_password" ng-required="!user.id">
                <div ng-show="user.password != user.repeat_password" class="help-inline text-danger form-control-static"><?=Lang::get('user.passwordMismatch')?></div>
                <div ng-show="errors.password" class="help-inline text-danger form-control-static">@{{errors.password.toString()}}</div>
            </div>
        </div>
    </div>
    <!----------------------------------------------------------------------------------------------->
    <div class="form-group"  ng-class="{error: errors.role}">
        <label for="inputRole" class="col-sm-2 control-label">@lang('contest.inscriptionRole')</label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            <div ng-if="!showThis || viewer">
                <div class="form-control-static">
                    <span ng-repeat="role in allRoles" ng-if="role.id == inscription.role">@{{role.label}}</span>
                </div>
            </div>
            <select ng-model="inscription.role" id="inputRole" class="form-control" ng-change="setTypeNull()" ng-if="showThis && !viewer">
                <option ng-repeat="role in allRoles" value="@{{role.id}}" ng-selected="role.id == inscription.role">@{{role.label}}</option>
            </select>
        </div>
        <div ng-show="errors.role" class="help-inline text-danger form-control-static">@{{errors.role.toString()}}</div>
    </div>
    <div class="form-group"  ng-class="{error: errors.inscription_type_id}" ng-if="allRoles[inscription.role].types.length">
        <label for="inputRole" class="col-sm-2 control-label">@lang('contest.inscriptionType')</label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            <div ng-if="!showThis">
                <div class="form-control-static">
                    <span ng-repeat="type in allRoles[inscription.role].types" ng-if="type.id == inscription.inscription_type.id">@{{type.name}}</span>
                </div>
            </div>
            <select ng-model="inscription.inscription_type.id" id="inputinscription_type_id" class="form-control" ng-if="showThis">
                <option ng-repeat="type in allRoles[inscription.role].types" value="@{{ type.id }}" ng-selected="type.id == inscription.inscription_type.id">@{{type.name}}</option>
            </select>
        </div>
        <div ng-show="errors.inscription_type_id" class="help-inline text-danger form-control-static">@{{errors.inscription_type_id.toString()}}</div>
    </div>
    <div class="form-group" ng-if="inscription.role == showPermits && !viewer">
    <label class="col-sm-2 control-label">@lang('contest.permits')</label>
    <div class="col-sm-8">
        <div class="checkbox">
            <label>
                <input type="checkbox" ng-model="inscription.permits.admin" ng-checked="inscription.permits.admin" ng-if="showThis" />
                <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': inscription.permits.admin,'fa-square-o': !inscription.permits.admin}"></i>
                @lang('contest.contestAdmin')
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" ng-model="inscription.permits.viewer" ng-checked="inscription.permits.viewer" ng-if="showThis" />
                <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': inscription.permits.viewer,'fa-square-o': !inscription.permits.viewer}"></i>
                @lang('contest.permitViewEntries')
            </label>
        </div>
        <div class="checkbox" ng-if="inscription.permits.viewer">
            <label>
                <input type="checkbox" ng-model="inscription.permits.edit" ng-checked="inscription.permits.edit" ng-if="showThis" />
                <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': inscription.permits.edit,'fa-square-o': !inscription.permits.edit}"></i>
                @lang('contest.permitEdit')
            </label>
        </div>
        <div class="checkbox" ng-if="inscription.permits.viewer">
            <label>
                <input type="checkbox" ng-model="inscription.permits.sifter" ng-checked="inscription.permits.sifter" ng-if="showThis" />
                <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': inscription.permits.sifter,'fa-square-o': !inscription.permits.sifter}"></i>
                @lang('contest.permitSifter')
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" ng-model="inscription.permits.voting" ng-checked="inscription.permits.voting" ng-if="showThis" />
                <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': inscription.permits.voting,'fa-square-o': !inscription.permits.voting}"></i>
                @lang('contest.tab.voting')
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" ng-model="inscription.permits.billing" ng-checked="inscription.permits.billing" ng-if="showThis" />
                <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': inscription.permits.billing,'fa-square-o': !inscription.permits.billing}"></i>
                @lang('contest.permitBilling')
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" ng-model="inscription.permits.tech" ng-checked="inscription.permits.tech" ng-if="showThis" />
                <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': inscription.permits.tech,'fa-square-o': !inscription.permits.tech}"></i>
                @lang('contest.permitTech')
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" ng-model="inscription.permits.design" ng-checked="inscription.permits.design" ng-if="showThis" />
                <i class="fa" ng-if="!showThis" ng-class="{'fa-check-square-o': inscription.permits.design,'fa-square-o': !inscription.permits.design}"></i>
                @lang('contest.permitDesign')
            </label>
        </div>
    </div>
    </div>
    @if(isset($contest->admin_reset_password) && $contest->admin_reset_password == true)
    <div class="form-group"  ng-class="{error: errors.role}">
        <label for="inputRole" class="col-sm-2 control-label">@lang('contest.resetPassword')</label>
        <div class="col-sm-8 col-md-6 col-lg-4">
            <btn class="btn btn-danger btn-md" ng-click="resetPassword(inscription)"> @lang('contest.resetPassword') </btn>
            <span ng-if="msg !== null"> <b> @{{ msg }} </b></span>
        </div>
        <div ng-show="errors.role" class="help-inline text-danger form-control-static">@{{errors.role.toString()}}</div>
    </div>
    @endif
    <div class="clearfix"></div>

    <h4 class="well well-sm">@lang('contest.dates')</h4>
    @include('admin.contests.deadlines', array('start' => 'inscription.start_at', 'deadline1' => 'inscription.deadline1_at', 'deadline2' => 'inscription.deadline2_at', 'errstart' => 'start_at', 'errdeadline1' => 'deadline1_at', 'errdeadline2' => 'deadline2_at'))

    <h4 class="well well-sm">@lang('contest.registerData')</h4>

    <script type="text/ng-template" id="update-signup-metadata-field.html">
        <div class="col-sm-8">
            @include('metadata.field', array('model'=>'field.value','allValues'=>'void', 'disabled'=>true, 'forceRequired' => true))
        </div>
    </script>
    <div>
        <div class="form-group" ng-repeat="field in metadata track by $index | filter:{role:role}">
            <div ng-include="'update-signup-metadata-field.html'"></div>
        </div>
    </div>
@endsection