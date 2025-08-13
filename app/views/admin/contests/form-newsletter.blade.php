@extends('admin.contests.form', array('active' => 'newsletter'))
@section('form')
    <h4 class="well well-sm">
        <a href="#{{{ $superadmin ? '/contests/edit/@{{contest.code}}/newsletters' : '/admin/newsletters' }}}" class="" role="button"><i class="fa fa-arrow-left"></i> @lang('contest.tab.newsletter')</a>
        /
        <span ng-show="!newsletter.id">@lang('contest.creatingNewsletter')</span>
        <span ng-show="newsletter.id">@lang('contest.editingNewsletter')</span>
    </h4>
    <!--<uib-tabset active="active">
        <uib-tab index="0">
            @{{ newsletter }}
            <uib-tab-heading>
                <i class="fa fa-sliders"></i> @lang('contest.VoteConfig')
            </uib-tab-heading>-->

            <div class="form-group" ng-class="{error: errors.name}">
                <label for="inputName" class="col-sm-2 control-label">@lang('general.status')</label>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="alert alert-danger btn-sm text-center ng-binding" ng-if="selectedEmails.length == 0 && newsletter.status == {{Newsletter::STATUS_WAITING}}"> Esperando carga de emails </div>
                    <button type="button" class="btn btn-sm btn-success ng-binding" ng-disabled="!showThis" ng-click="sendNewsletters()" ng-if="selectedEmails.length > 0 && newsletter.status == {{Newsletter::STATUS_WAITING}}">
                        <i class="fa fa-send"></i>
                        @lang('contest.sendNewsletter')
                    </button>
                    <span ng-if="selectedEmails.length > 0 && newsletter.status == {{Newsletter::STATUS_WAITING}}">
                        <button type="button" class="btn btn-sm btn-info ng-binding" ng-disabled="!showThis || !newsletter.dummyEmail || errors.dummy" ng-click="sendNewsletters(newsletter.dummyEmail)">
                            <i class="fa fa-send"></i>
                            @lang('contest.sendDummyNewsletter')
                        </button>
                        <input type="email" placeholder="@lang('general.email')" name="dummy" ng-model="newsletter.dummyEmail" ng-if="showThis">
                    </span>
                    <div class="alert alert-warning btn-sm text-center col-sm-3 col-md-3 col-lg-3" ng-if="newsletter.status == {{Newsletter::STATUS_PROCESSING}}"> @lang('contest.processing') </div>
                    <div class="alert alert-info btn-sm text-center col-sm-3 col-md-3 col-lg-3" ng-if="newsletter.status == {{Newsletter::STATUS_SEND}}"> @lang('contest.sent') </div>
                </div>
            </div>

            <div class="form-group" ng-class="{error: errors.name}">
                <label for="inputName" class="col-sm-2 control-label">@lang('general.name')</label>
                <div class="col-sm-8 col-md-6 col-lg-4">
                    <input type="text" class="form-control col-md-3" placeholder="@lang('general.name')" ng-model="newsletter.name" required ng-if="showThis">
                    <div class="form-control-static" ng-if="!showThis">@{{ newsletter.name }}</div>
                    <div ng-show="errors.name" class="help-inline text-danger form-control-static">@{{errors.name.toString()}}</div>
                </div>
            </div>
            <div class="form-group" ng-class="{error: errors.reply_to}">
                <label for="inputReplyTo" class="col-sm-2 control-label">@lang('contest.replyTo')</label>
                <div class="col-sm-8 col-md-6 col-lg-4">
                    <input type="text" class="form-control col-md-3" placeholder="@lang('contest.replyTo')" ng-model="newsletter.reply_to" required ng-if="showThis">
                    <div class="form-control-static" ng-if="!showThis">@{{ newsletter.reply_to }}</div>
                    <div ng-show="errors.reply_to" class="help-inline text-danger form-control-static">@{{errors.reply_to.toString()}}</div>
                </div>
            </div>
            <div class="form-group" ng-class="{error: errors.subject}">
                <label for="inputSubject" class="col-sm-2 control-label">@lang('contest.subject')</label>
                <div class="col-sm-8 col-md-6 col-lg-4">
                    <input type="text" class="form-control col-md-3" placeholder="@lang('contest.subject')" ng-model="newsletter.subject" required ng-if="showThis">
                    <div class="form-control-static" ng-if="!showThis">@{{ newsletter.subject }}</div>
                    <div ng-show="errors.subject" class="help-inline text-danger form-control-static">@{{errors.subject.toString()}}</div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <p class="text-right">@lang('contest.emailBody')</p>
                </div>
                <div id='wysiwyg' class="col-sm-8 col-md-8">
                    <div text-angular ng-model="newsletter.email_body" ta-disabled="disable"></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <p class="text-right">@lang('contest.sendWhen')</p>
                </div>
                <div class="col-sm-4 col-md-2 col-lg-2">
                    @include('includes.datetimepicker', array('field'=>'newsletter.send_when', 'placeholder' => Lang::get('contest.sendWhen')))
                </div>
            </div>
        <!--</uib-tab>
        <uib-tab index="1">
            <uib-tab-heading>
                <i class="fa fa-sliders"></i> @lang('contest.emailsTo')
            </uib-tab-heading>-->
            <h4 class="well well-sm">
                <span>@lang('contest.selectEmails')</span>
            </h4>
            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <p class="text-right">@lang('contest.selectUsers')</p>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Usuarios <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-fixed">
                        <li style="padding: 0 5px 5px;"><input type="text" ng-model="filterInscriptions" class="form-control searchBox" style="width:100%;" ng-click="$event.stopPropagation();" placeholder="@lang('general.search')"/></li>
                        <li ng-repeat="inscription in inscriptions | filter: filterInscriptions"><a ng-click="addInscription(inscription, null)"><span user-card user-card-model="inscription"></span> <span>(@{{ inscription.email }})</span></a></li>
                    </ul>
                    <button type="button" class="btn btn-default" ng-click="addInscription(null, 'all')">
                        @lang('contest.allInscriptions')
                    </button>

                    <div class="btn-group">
                        <button type="button" class="btn" ng-class="{'btn-default': !inscriptor, 'btn-success': inscriptor }" ng-click="addInscription(null, {{Inscription::INSCRIPTOR}})">
                            @lang('contest.inscriptors')
                        </button>

                        <button ng-if="inscriptionTypes.length > 0 && inscriptor == 1" class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ng-class="">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                            <li ng-repeat="inscriptionType in inscriptionTypes" ng-if="inscriptionType.role == {{Inscription::INSCRIPTOR}}">
                                <a ng-click="filterByType(inscriptionType.id)">
                                    <i class="fa fa-check" ng-if="filterType.indexOf(inscriptionType.id) != -1"></i>@{{ inscriptionType.name }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn" ng-class="{'btn-default': !judge, 'btn-success': judge }" ng-click="addInscription(null, {{Inscription::JUDGE}})">
                            @lang('contest.judges')
                        </button>
                        <button ng-if="inscriptionTypes.length > 0 && judge == 1" class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ng-class="">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                            <li ng-repeat="inscriptionType in inscriptionTypes" ng-if="inscriptionType.role == {{Inscription::JUDGE}}">
                                <a ng-click="filterByType(inscriptionType.id)">
                                    <i class="fa fa-check" ng-if="filterType.indexOf(inscriptionType.id) != -1"></i>@{{ inscriptionType.name }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <button type="button" class="btn btn-default" ng-click="selectedEmails = []">
                        @lang('contest.deleteall')
                    </button>
                </div>
            </div>
            <!--<div class="form-group">
                <div class="col-sm-2 control-label">
                    <p class="text-right">@lang('contest.addEmails')</p>
                </div>
                <div class="col-sm-8 col-md-8 col-lg-8">
                    <button type="button" class="btn btn-sm btn-info ng-binding" ng-disabled="!showThis" ng-click="showAddEmails = !showAddEmails;">
                        <i class="fa fa-fw fa-plus" ng-class="{'fa-plus':!showAddJudges, 'fa-caret-up':showAddJudges}"></i>
                        Nuevos emails
                    </button>
                </div>
                <div class="col-sm-8 col-md-8 col-lg-8" ng-if="showAddEmails">
                    <textarea id="" ng-model="listOfEmails" style="width: 100%; max-width: 100%; min-width: 100%;" rows="10" class="form-control">
                </textarea>
                </div>
            </div>-->

            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <p class="text-right">@lang('contest.selectedUsers')</p>
                </div>
                <div class="col-sm-8 col-md-8 col-lg-8">
                    <table class="table table-condensed table-hover judges-table ng-scope">
                        <thead>
                        <tr>
                            <!--<th><input ng-disabled="!showThis" checklist-value="bulkUsers" ng-change="addAllBulk()" type="checkbox" checklist-model="groupBulks" class="ng-scope ng-pristine ng-untouched ng-valid" disabled="disabled"></th>-->
                            <th></th>
                            <th><a data-ng-click=""> E-mails <i ng-show="judgesPagination.sortBy == 'email'" ng-class="{'fa-chevron-down': !judgesPagination.sortInverted,'fa-chevron-up': judgesPagination.sortInverted}" class="fa fa-chevron-down"></i></a></th>
                            <th><a data-ng-click=""> Estado <i ng-show="judgesPagination.sortBy == 'status'" ng-class="{'fa-chevron-down': !judgesPagination.sortInverted,'fa-chevron-up': judgesPagination.sortInverted}" class="fa fa-chevron-down ng-hide"></i></a></th>
                            <th><a data-ng-click=""> Última vez visto <i ng-show="judgesPagination.sortBy == 'last_seen_at'" ng-class="{'fa-chevron-down': !judgesPagination.sortInverted,'fa-chevron-up': judgesPagination.sortInverted}" class="fa fa-chevron-down ng-hide"></i></a></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="selected in selectedEmails">
                                <!--<td class="ng-scope"> <input ng-disabled="!showThis" checklist-value="selected" ng-change="addBulk(selected)" type="checkbox" checklist-model="bulkUsers" class="ng-scope ng-pristine ng-untouched ng-valid" disabled="disabled"></td>-->
                                <td>@{{ $index + 1 }}</td>
                                <td>
                                    <span user-card user-card-model="selected"></span> <span> @{{ selected.email }} </span>
                                </td>
                                <td ng-switch="selected.status">
                                    <span ng-switch-when="{{NewsletterUser::PENDING_NOTIFICATION}}" class="text-default">
                                        <i class="fa fa-clock-o"></i> @lang('voting.pendingNotification')
                                    </span>
                                    <span ng-switch-when="{{NewsletterUser::NOTIFIED}}" class="text-warning">
                                        <i class="fa fa-envelope"></i> @lang('voting.notified')
                                    </span>
                                </td>
                                <td></td>
                                <td>
                                    <button type="button" class="btn btn-xs btn-danger" ng-click="delete(selected)"><i class="fa fa-trash"></i> </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <!--</uib-tab>
    </uib-tabset>-->
@endsection