@include('includes.header')
<script type="text/ng-template" id="delete.html">
    @include('account.delete')
</script>
<div class="main-block contents container">
    <h2><i class="fa fa-user"></i> @lang('account.title')</h2>
    <div class="row">
        <div class="col-sm-3 col-lg-3">
            @include('account.menu')
        </div>
        <div class="col-sm-9 col-lg-9">
            <h3><i class="fa fa-cogs"></i> @lang('account.config')</h3>
            <br/>
            <form name='form' data-ng-submit="saveLanguage()">
                <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-language"></i> @lang('account.changeLang')</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="lang" class="control-label">@lang('account.changeLang.description')</label>
                            <select ng-model="langForm.lang" data-ng-options="k as v for (k, v) in langOptions" id="lang" class="form-control" style="width: auto" >
                            </select>
                        </div>
                        <div class="alert alert-@{{flashStatus}}" ng-show="flash">
                            <span ng-bind="flash"></span>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <button type="submit" class="btn btn-success" ng-disabled="form.$pristine"><i class="fa fa-save"></i> @lang('general.save')</button>
                    </div>
                </div>
            </form>

            <form name='notifications' data-ng-submit="saveNotifications()">
                <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-send"></i> @lang('account.notifications')</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <span ng-if="inscriptions.CountAllInscriptions() == 0">
                                <i class="fa fa-info-circle"></i> @lang('account.notifications.noinscriptions')
                            </span>
                            <span ng-if="inscriptions.CountAllInscriptions() > 0">
                                @lang('account.notifications.description')
                                <span ng-if="inscriptions.HasRoleInAllInscriptions('<?=Inscription::OWNER?>') || inscriptions.HasRole('<?=Inscription::COLABORATOR?>')">
                                    <h4>
                                        <span ng-if="inscriptions.HasRoleInAllInscriptions('<?=Inscription::OWNER?>')">
                                            <i class="fa fa-user-circle"></i> @lang('user.owner')
                                        </span>
                                        <span ng-if="inscriptions.HasRoleInAllInscriptions('<?=Inscription::COLABORATOR?>')">
                                            <span ng-if="inscriptions.HasRoleInAllInscriptions('<?=Inscription::OWNER?>')">/
                                            </span>
                                            <i class="fa fa-user-circle-o"></i> @lang('user.colaborator')
                                        </span>
                                    </h4>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="" ng-model="currentUser.notifications.<?=User::NotificationNewUser?>" id="" ng-true-value="true" ng-false-value="false"/>
                                            @lang('account.notifications.newUser')
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="" ng-model="currentUser.notifications.<?=User::NotificationNewEntry?>" id="" ng-true-value="true" ng-false-value="false"/>
                                            @lang('account.notifications.newEntry')
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="" ng-model="currentUser.notifications.<?=User::NotificationEntryFinalized?>" id="" ng-true-value="true" ng-false-value="false"/>
                                            @lang('account.notifications.entryFinalized')
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="" ng-model="currentUser.notifications.<?=User::NotificationEntryPaid?>" id="" ng-true-value="true" ng-false-value="false"/>
                                            @lang('account.notifications.entryPaid')
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="" ng-model="currentUser.notifications.<?=User::NotificationNewMessage?>" id="" ng-true-value="true" ng-false-value="false"/>
                                            @lang('account.notifications.newMessage')
                                        </label>
                                    </div>
                                </span>
                                <span ng-if="inscriptions.HasRoleInAllInscriptions('<?=Inscription::INSCRIPTOR?>')">
                                    <h4>
                                        <i class="fa fa-ticket"></i> @lang('user.inscriptor')
                                    </h4>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="" ng-model="currentUser.notifications.<?=User::NotificationEntryApproved?>" id="" ng-true-value="true" ng-false-value="false"/>
                                            @lang('account.notifications.entryApproved')
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="" ng-model="currentUser.notifications.<?=User::NotificationEntryPaid?>" id="" ng-true-value="true" ng-false-value="false"/>
                                            @lang('account.notifications.entryPaid')
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="" ng-model="currentUser.notifications.<?=User::NotificationEntryError?>" id="" ng-true-value="true" ng-false-value="false"/>
                                            @lang('account.notifications.entryError')
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="" ng-model="currentUser.notifications.<?=User::NotificationMediaError?>" id="" ng-true-value="true" ng-false-value="false"/>
                                            @lang('account.notifications.mediaError')
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="" ng-model="currentUser.notifications.<?=User::NotificationNewMessage?>" id="" ng-true-value="true" ng-false-value="false"/>
                                            @lang('account.notifications.newMessage')
                                        </label>
                                    </div>
                                </span>
                                <span ng-if="inscriptions.HasRoleInAllInscriptions('<?=Inscription::JUDGE?>')">
                                    <h4>
                                        <i class="fa fa-legal"></i> @lang('user.judge')
                                    </h4>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="" ng-model="currentUser.notifications.<?=User::NotificationContestsNotifications?>" id="" ng-true-value="true" ng-false-value="false"/>
                                            @lang('account.notifications.contestsNotifications')
                                        </label>
                                    </div>
                                </span>
                            </span>
                        </div>
                        <div class="checkbox" ng-if="superAdmin == 1">
                            <label>
                                <input type="checkbox" name="" ng-model="currentUser.notifications.<?=User::NotificationContestsErrors?>" id="" ng-true-value="true" ng-false-value="false"/>
                                @lang('account.notifications.adminErrorReport')
                            </label>
                        </div>
                    </div>
                    <div class="panel-footer" ng-if="inscriptions.CountAllInscriptions() > 0 || superAdmin == 1">
                        <button type="submit" class="btn btn-success" ng-disabled="notifications.$pristine"><i class="fa fa-save"></i> @lang('general.save')</button>
                        <span class="text-@{{notif.flashStatus}}" ng-show="notif.flash">
                            <span ng-bind="notif.flash"></span>
                        </span>
                    </div>
                </div>
            </form>

            @if(Config::get('registration.oauth'))
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-globe"></i> @lang('account.socialNetworks')</div>
                <div class="panel-body">
                    <i class="fa fa-facebook-official fa-2x"></i> <h4 style="display: inline-block;">@lang('login.facebook')</h4>
                    <a ng-hide="social.Facebook" href="<?=URL::to('/');?>/service/login/fb/" class="btn btn-primary btn-facebook pull-right"><i class="fa fa-facebook-official"></i> @lang('account.connect-facebook')</a>
                    <a ng-show="social.Facebook" href="<?=URL::to('/');?>/service/disconnect/fb/" class="btn btn-primary btn-warning pull-right"><i class="fa fa-user-times"></i> @lang('account.disconnect-facebook')</a>
                    <hr class="hr-sm"/>
                    <i class="fa fa-twitter-square fa-2x"></i> <h4 style="display: inline-block;">@lang('login.twitter')</h4>
                    <a ng-hide="social.Twitter" href="<?=URL::to('/');?>/service/login/tt/" class="btn btn-info pull-right"><i class="fa fa-twitter-square"></i> @lang('account.connect-twitter')</a>
                    <a ng-show="social.Twitter" href="<?=URL::to('/');?>/service/disconnect/tt/" class="btn btn-primary btn-warning pull-right"><i class="fa fa-user-times"></i> @lang('account.disconnect-twitter')</a>
                    <hr class="hr-sm"/>
                    <i class="fa fa-google-plus-square fa-2x"></i> <h4 style="display: inline-block;">@lang('login.google+')</h4>
                    <a ng-hide="social.Google" href="<?=URL::to('/');?>/service/login/gp/" class="btn btn-danger pull-right"><i class="fa fa-google-plus-square"></i> @lang('account.connect-google+')</a>
                    <a ng-show="social.Google" href="<?=URL::to('/');?>/service/disconnect/gp/" class="btn btn-primary btn-warning pull-right"><i class="fa fa-user-times"></i> @lang('account.disconnect-google+')</a>
                </div>
            </div>
            @endif

            <div class="panel panel-default panel-danger" ng-if="false">
                <div class="panel-heading"><i class="fa fa-trash"></i> @lang('account.deleteAccount')</div>
                <div class="panel-body">
                    <button type="button" class="btn btn-danger pull-right" ng-click="deleteAccount()"><i class="fa fa-trash"></i> @lang('account.deleteAccount')</button>
                    @lang('account.deleteAccount.description')
                    <br/>
                    <span class="text-danger">@lang('account.deleteAccount.warn')</span>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
    @include('includes.footer')
</div>
