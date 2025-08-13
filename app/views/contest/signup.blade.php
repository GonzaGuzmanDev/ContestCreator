@include('includes.header')
@include('contest.header', array('class' => 'small', 'banner' => ContestAsset::SMALL_BANNER_HTML))

<div ng-if="!currentUser">
    <script type="text/ng-template" id="terms.html">
     @include('login.register-terms')
    </script>
</div>

<div class="container<?=$registered?"-fluid":"";?> with-footer">
    <div class="row">
        @if($registered)
            @include('contest.tabs', array('active' => 'registration'))
        @endif
        <div class="<?=$registered?"col-sm-9 col-lg-10":"col-sm-8 col-sm-offset-2";?>">
            <div ng-if="role == {{Inscription::INSCRIPTOR}} ? {{(!!$contest->getInscriptionNextDeadlineDate() ? 0 : 1) }} : 0">
                <div class="alert alert-danger alert-lg text-center"> @lang('contest.closedInscriptorSignUp', ['date' => $contest->inscription_deadline2_at ? $contest->inscription_deadline2_at : $contest->inscription_deadline1_at]) </div>
            </div>

            @if(isset($contest->voters_public) && $contest->voters_public == 1)
                <div ng-if="role == {{Inscription::JUDGE}} ? {{ (!!$contest->getInscriptionNextDeadlineDate(Inscription::JUDGE) ? 0 : 1) }} : 0">
                    <div class="alert alert-danger alert-lg text-center"> @lang('contest.closedJudgeSignUp', ['date' => $contest->voters_deadline2_at ? $contest->voters_deadline2_at : $contest->voters_deadline1_at]) </div>
                </div>
            @endif

            @if($contest->isRegistrationNext(Inscription::INSCRIPTOR))
            <div ng-if="role == {{Inscription::INSCRIPTOR}}">
                <div class="alert alert-info text-center">
                    @lang('contest.signuponasinscriptor', ['date'=>$contest->getInscriptionOpenDate()])
                    <br/>
                    <span ng-bind="'{{$contest->getInscriptionOpenDate()}}' | amDateFormat:'DD/MM/YYYY HH:mm'"></span>,
                    <span am-time-ago="'{{$contest->getInscriptionOpenDate()}}'"></span>
                </div>
            </div>
            @endif

            @if($contest->isRegistrationNext(Inscription::JUDGE))
                <div ng-if="role == {{Inscription::JUDGE}}">
                    <div class="alert alert-info text-center">
                        @lang('contest.signuponasjudge', ['date'=>$contest->getInscriptionOpenDate(Inscription::JUDGE)])
                        <br/>
                        <span ng-bind="'{{$contest->getInscriptionOpenDate(Inscription::JUDGE)}}' | amDateFormat:'DD/MM/YYYY HH:mm'"></span>,
                        <span am-time-ago="'{{$contest->getInscriptionOpenDate(Inscription::JUDGE)}}'"></span>
                    </div>
                </div>
            @endif
        </div>
        <div class="<?=$registered?"col-sm-9 col-lg-10":"col-sm-8 col-sm-offset-2";?>">
            @if($registered)
                <h4 class="well well-sm">
            @else
                <h2>
            @endif
            @lang('contest.signuptitle')
                <span ng-if="inscriptionType"> - </span>
                <trans ng-if="inscriptionType" ng-model="inscriptionType" trans-prop="'name'"></trans>
                <a href="" ng-if="inscriptionType && inscriptionTypesRole.length > 1 && !sent" ng-click="setInscriptionType()" uib-tooltip="@lang('contest.changeInscriptionType')"><i class="fa fa-edit"></i></a>

            @if($registered)
                </h4>
            @else
                </h2>
            @endif
            <div class="alert alert-@{{flashStatus}}" ng-show="flash">
                <span ng-bind-html="flash"></span>
            </div>
            <div ng-show="chooseType" class="col-md-6 col-md-offset-3 btn-group-vertical text-center">
                <h4>@lang('contest.selectinsctype')</h4>
                <button type="button" ng-repeat="type in contest.inscription_types | filter:{role:role}" ng-click="setInscriptionType(type)" class="btn btn-default btn-block btn-lg"><trans ng-model="type" trans-prop="'name'"></trans></button>
            </div>
            <div class="clearfix"></div>

            <div class="text-center" ng-if="sent">
                <span ng-if="role == {{Inscription::INSCRIPTOR}}">
                    @if(isset($inscriptionMessage))
                    <div class="alert text-left">
                    {{ $inscriptionMessage->content }}
                    </div>
                    @endif
                    @if(isset($contest->type) && $contest->type == Contest::TYPE_CONTEST)
                        <a href="#/entries/" class="btn btn-warning btn-lg">
                            <i class="fa fa-ticket"></i> @lang('contest.gotoinscriptions')
                        </a>
                    @endif
                    @if(isset($contest->type) && $contest->type == Contest::TYPE_TICKET)
                        <a href="#/home" class="btn btn-warning btn-lg">
                        <i class="fa fa-ticket"></i> @lang('oxoTicket.startBuying')
                    </a>
                    @endif
                </span>
                <span ng-if="role == {{Inscription::JUDGE}}">
                    @if(isset($judgeMessage))
                    <div class="alert text-left">
                    {{ $judgeMessage->content }}
                    </div>
                    @endif
                    <a href="#/voting/" class="btn btn-warning btn-lg">
                        <i class="fa fa-legal"></i> @lang('contest.gotovotingsessions')
                    </a>C
                </span>
            </div>
            <form name="signupForm" class="form-horizontal" ng-submit="send()" ng-show="!sent">
                <div ng-show="!chooseType">
                    <h5 class="text-muted"><span class="text-danger">*</span> @lang('contest.requiredfields')</h5>
                    <div ng-if="!currentUser">
                        <div class="form-group" ng-class="{'has-error': !!errors.first_name}">
                            <label for="inputName" class="control-label col-sm-3"><?=Lang::get('register.firstName')?>
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="text" id="inputName" class="form-control" placeholder="" ng-model="formData.first_name" required focus-me="true">
                                <span class="help-block" ng-show="errors.first_name">@{{errors.first_name.join()}}</span>
                            </div>
                        </div>
                        <div class="form-group" ng-class="{'has-error': !!errors.last_name}">
                            <label for="inputName2" class="control-label col-sm-3"><?=Lang::get('register.lastName')?>
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="text" id="inputName2" class="form-control" placeholder="" ng-model="formData.last_name" required>
                                <span class="help-block" ng-show="errors.last_name">@{{errors.last_name.join()}}</span>
                            </div>
                        </div>
                        <div class="form-group" ng-class="{'has-error': !!errors.email}">
                            <label for="inputEmail" class="control-label col-sm-3"><?=Lang::get('register.email')?>
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="email" id="inputEmail" class="form-control" placeholder="" ng-model="formData.email" required>
                                <span class="help-block" ng-show="errors.email">
                                    @{{errors.email.join()}}
                                    <span ng-if="errors.email.join() == '<?=Lang::get('validation.unique', ['attribute'=>'email'])?>'"><?=Lang::get('register.emailexistslogin')?></span>
                                </span>
                            </div>
                        </div>
                        <div class="form-group" ng-class="{'has-error': !!errors.new_password}">
                            <label for="inputPassword" class="control-label col-sm-3"><?=Lang::get('register.password')?>
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="password" id="inputPassword" class="form-control" placeholder="" ng-model="formData.new_password" required>
                                <span class="help-block" ng-show="errors.new_password">@{{errors.new_password.join()}}</span>
                            </div>
                        </div>

                        <div class="form-group" ng-class="{'has-error': !!errors.repeat_password}">
                            <label for="inputPassword2" class="control-label col-sm-3"><?=Lang::get('register.repeatPassword')?>
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="password" id="inputPassword2" class="form-control" placeholder="" ng-model="formData.repeat_password" required>
                                <span class="help-block" ng-show="errors.repeat_password">@{{errors.repeat_password.join()}}</span>
                            </div>
                        </div>

                        <div class="form-group" ng-if="(role == {{Inscription::JUDGE}} && contest.voters_register_picture) || (role == {{Inscription::INSCRIPTOR}} && contest.inscription_register_picture)" ng-class="{'has-error': !!errors.repeat_password}">
                            <label for="inputPassword2" class="control-label col-sm-3"><?=Lang::get('register.profilePicture')?>
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="file" id="profilePicture" file="formData.profilePicture" class="form-control" placeholder="" required>
                                <span class="help-block" ng-show="errors.profilePicture">@{{errors.profilePicture.join()}}</span>
                            </div>
                        </div>

                        <div class="alert alert-@{{flashStatus}}" ng-show="flash">
                            <span ng-bind-html="flash"></span>
                        </div>
                        <!--<button type="submit" class="btn btn-md btn-danger">
                            <?=Lang::get('register.registerMe')?>
                        </button>-->
                    </div>
                    <div ng-if="!!currentUser">
                        <div class="form-group">
                            <label class="control-label col-sm-3"><?=Lang::get('register.firstName')?></label>
                            <div class="col-sm-9 form-control-static">
                                @{{ currentUser.first_name }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3"><?=Lang::get('register.lastName')?></label>
                            <div class="col-sm-9 form-control-static">
                                @{{ currentUser.last_name }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3"><?=Lang::get('register.email')?></label>
                            <div class="col-sm-9 form-control-static">
                                @{{ currentUser.email }}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-9 col-sm-push-3 form-control-static">
                                <a href="#/account/data" class="btn btn-primary btn-sm">@lang('register.editAccountData')</a>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <script type="text/ng-template" id="signup-metadata-field.html">
                        <span ng-if="((role == {{Inscription::INSCRIPTOR}} ? {{ !$contest->getInscriptionNextDeadlineDate() ? 0 : 1 }} : false)
                                        && (role == {{Inscription::INSCRIPTOR}} ? {{ !$contest->isRegistrationNext(Inscription::INSCRIPTOR)}} : false))
                                        || ((role == {{Inscription::JUDGE}} ? {{ !$contest->getInscriptionNextDeadlineDate(Inscription::JUDGE) ? 0 : 1 }} : false)
                                        && (role == {{Inscription::JUDGE}} ? {{ !$contest->isRegistrationNext(Inscription::JUDGE)}} : false))">
                        @include('metadata.field', array('model'=>'formData[field.id]', 'allValues'=>'void', 'disabled'=>false, 'forceRequired' => true))
                        </span>

                        <span ng-if="((role == {{Inscription::INSCRIPTOR}} ? {{ !$contest->getInscriptionNextDeadlineDate() ? 1 : 0 }} : false)
                                        && (role == {{Inscription::INSCRIPTOR}} ? {{ !$contest->isRegistrationNext(Inscription::INSCRIPTOR)}} : false))
                                        || ((role == {{Inscription::JUDGE}} ? {{ !$contest->getInscriptionNextDeadlineDate(Inscription::JUDGE) ? 1 : 0 }} : false)
                                        && (role == {{Inscription::JUDGE}} ? {{ !$contest->isRegistrationNext(Inscription::JUDGE)}} : false))">
                            @include('metadata.field', array('model'=>'formData[field.id]', 'allValues'=>'void', 'disabled'=>true, 'forceRequired' => true))
                        </span>
                    </script>
                    <div ng-if="firstTabIndex != -1">
                        <uib-tabset>
                            <div class="form-group" ng-repeat="field in getPreTabMetadata()">
                                <div ng-include="'signup-metadata-field.html'"></div>
                            </div>
                            <uib-tab ng-repeat="tab in getTabs() track by $index" index="$index">
                                <uib-tab-heading><trans ng-model='tab' trans-prop="'label'"></trans></uib-tab-heading>
                                <div class="help-block" ng-if="tab.description != null"><trans ng-model='tab' trans-prop="'description'"></trans></div>
                                <div class="form-group" ng-repeat="field in getTabMetadata(tab)">
                                    <div ng-include="'signup-metadata-field.html'"></div>
                                </div>
                            </uib-tab>
                        </uib-tabset>
                    </div>
                    <div ng-if="firstTabIndex == -1">
                        <div class="form-group" ng-repeat="field in metadata track by $index | filter:{role:role}" ng-if="!isTab(field)">
                            <div ng-include="'signup-metadata-field.html'"></div>
                        </div>
                    </div>
                    <div class="form-group" ng-if="newRecord == true">
                        <div class="col-sm-offset-3 col-sm-9">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" ng-model="formData.accept" required=""> @lang('contest.acceptterms') de {{$contest->name}} <span class="text-danger">*</span>
                                </label>
                                <span class="help-block" ng-show="errors.accept">@{{errors.accept.join()}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div ng-if="!currentUser">
                    <div class="form-group" ng-class="{'has-error': !!errors.accept}">
                        <div class="col-sm-offset-3 col-sm-9">
                            <div class="checkbox">
                                <label class="control-label"><input type="checkbox" name="accept" ng-model="formData.accept2">
                                    <!--<?=Lang::get('register.accept')?> <a href ng-click="viewTerms()"><?=Lang::get('register.termsAndConditions')?> de OxoAwards</a>-->
                                    <?=Lang::get('register.accept')?> <a href="https://www.oxoawards.com/#/termsofuse" target="_blank"><?=Lang::get('register.termsAndConditions')?> de OxoAwards</a>
                                    <span class="text-danger">*</span>
                                </label>
                                <span class="help-block" ng-show="errors.accept2">@{{errors.accept2.join()}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" ng-class="{'has-error': !!errors.captcha}">
                        <div class="well well-sm captcha-well text-center">
                            <img ng-src="@{{captchaUrl}}" alt="Captcha image" class="captcha-img"/>
                            <input type="text" id="inputCaptcha" class="form-control captcha-input input-sm" placeholder="<?=Lang::get('register.captcha')?>" ng-model="formData.captcha" required>
                            <div class="clearfix"></div>
                            <span class="help-block" ng-show="errors.captcha">@{{errors.captcha.join()}}</span>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <div class="col-sm-12" ng-if="errors">
                        <div class="alert alert-danger">
                            <ul>
                                <span ng-repeat="error in errors">
                                <li ng-repeat="err in error">
                                    @{{ err }}
                                    <span ng-if="err == '<?=Lang::get('validation.unique', ['attribute'=>'email'])?>'">
                                        <?=Lang::get('register.emailexistslogin')?>
                                    </span>
                                </li>
                                </span>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-3 text-right">
                        <a href="#home" class="btn btn-info">@lang('general.back')</a>
                    </div>
                    <div class="col-sm-9" ng-show="!chooseType">
                        <button ng-if="((role == 1 ? {{ !$contest->getInscriptionNextDeadlineDate() ? 0 : 1 }} : false)
                                        && (role == {{Inscription::INSCRIPTOR}} ? {{ !$contest->isRegistrationNext(Inscription::INSCRIPTOR)}} : false))
                                        || ((role == {{Inscription::JUDGE}} ? {{ !$contest->getInscriptionNextDeadlineDate(Inscription::JUDGE) ? 0 : 1 }} : false)
                                        && (role == {{Inscription::JUDGE}} ? {{ !$contest->isRegistrationNext(Inscription::JUDGE)}} : false))"
                                type="submit" ng-disabled="sending || userInscriptions.HasRole(<?=Inscription::OWNER?>) || userInscriptions.HasRole(<?=Inscription::COLABORATOR?>) || {{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}}" class="btn btn-success">
                            <span ng-if="newRecord == false">@lang('contest.updateInscription')</span>
                            <span ng-if="newRecord == true">@lang('contest.signup')</span>
                        </button>
                        <!--<button ng-if="newRecord != true" type="button" ng-click="delete()" class="btn btn-danger pull-right"><i class="fa fa-trash"></i> @lang('contest.DeleteSignUp')</button>-->
                        <span class="alert alert-inline alert-transparent text-warning" ng-if="userInscriptions.HasRole(<?=Inscription::OWNER?>) || userInscriptions.HasRole(<?=Inscription::COLABORATOR?>) || {{Auth::check() && Auth::user()->isSuperAdmin() ? 1 : 0}}"><i class="fa fa-warning"></i> @lang('contest.signupCantHasRole')</span>
                        <div class="alert alert-tight alert-inline alert-transparent text-@{{flashStatus}}" ng-show="flash">
                            <span ng-bind-html="flash"></span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('includes.footer')
</div>