@extends('admin.contests.form', array('active' => 'style'))
@section('form')
    <span ng-if="{{ $contest->wizard_status >= Contest::WIZARD_STYLE && $contest->wizard_status != Contest::WIZARD_FINISHED}}">
    @include('includes.wizardProgress', array('active' => Contest::WIZARD_STYLE))
        <br><div class="clearfix"></div><br>
    <h3 class="text-center"> @lang('contest.wizard.styleExplain') </h3>
    <br>
    </span>
    <h4 class="well well-sm">
        @lang('contest.contestStyle')
       {{-- @if(!$superadmin)
            <i class="fa fa-question-circle text-info" popover="@lang('contest.explain.style')" popover-placement="right" popover-trigger="mouseenter" class="btn btn-default"></i>
        @endif--}}
    </h4>
    <div class="row">
        <div class="form-group">
            <div class="col-sm-2">
                <p class="text-right">@lang('contest.styleTheme')</p>
            </div>
            <div class="col-sm-10 col-md-6 col-lg-4">
                <? $themes = Contest::GetThemes(); ?>
                <select ng-model="contest.style" class="form-control">
                    @foreach($themes as $themeKey => $themeName)
                        <option value="{{$themeKey}}">{{$themeName}}</option>
                    @endforeach
                </select>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group">
            <div class="col-sm-2">
                <p class="text-right">@lang('contest.customStyle')</p>
            </div>
            <div class="col-sm-10 col-md-6 col-lg-4">
                <textarea class="form-control" required="" ng-model="contest.custom_style" style="max-width: 100%; min-width: 100%;"></textarea>
            </div>
        </div>
        <div class="col-sm-10 col-sm-push-2 col-md-6 col-lg-4 text-right">
            <i class="fa fa-circle-o-notch fa-spin text-warning" ng-show="savingStyles"></i>
            <i class="fa fa-check-circle text-success" ng-show="saveStylesOk" ></i>
            <button class="btn btn-primary btn-sm" ng-click='preview()' type="button">@lang('contest.preview')</button>
            <button class="btn btn-primary btn-sm" ng-click='saveStyle()' type="button">@lang('contest.saveHtml')</button>
        </div>
        <br>
        <br>
        <div class="clearfix"></div>
    </div>
    <h4 class="well well-sm">
        @lang('contest.htmlblocks')
    </h4>
    <div class="row">
        <div class="form-group">
            <div class="col-md-2">
                <p class="text-right">@lang('contest.homeHTML')
                    {{--<i class="fa fa-question-circle text-info" popover="@lang('contest.homeHTMLInfo')" popover-placement="right" popover-trigger="mouseenter"></i>--}}
                </p>
            </div>
            <div id='wysiwyg' class="col-sm-12 col-md-10">
                <div text-angular name="htmlcontent-homeHtml" ng-model="homeHtml.html" ta-disabled='disabled'></div>
                <div ng-show="errors.htmlcontent" class="help-inline text-danger form-control-static ng-binding ng-hide">@{{ errors.htmlcontent }}</div>
                <button class="btn btn-primary btn-sm pull-right wysiwyg-save-btn" ng-click='saveHomeHtml()' type="button">@lang('contest.saveHtml')</button>
                <div ng-show="saveHomeOk" class="saveTick pull-right">
                    <i class="fa fa-check-circle text-success"></i>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-2">
                <p class="text-right">@lang('contest.homeBottomHTML')
                    {{--<i class="fa fa-question-circle text-info" popover="@lang('contest.homeBottomHTMLInfo')" popover-placement="right" popover-trigger="mouseenter"></i>--}}
                </p>
            </div>
            <div id='wysiwyg' class="col-sm-12 col-md-10">
                <div text-angular name="htmlcontent-homeBottomHtml" ng-model="homeBottomHtml.html" ta-disabled='disabled'></div>
                <div ng-show="errors.htmlcontent" class="help-inline text-danger form-control-static ng-binding ng-hide">@{{ errors.htmlcontent }}</div>
                <button class="btn btn-primary btn-sm pull-right wysiwyg-save-btn" ng-click='saveHomeBottomHtml()' type="button">@lang('contest.saveHtml')</button>
                <div ng-show="saveHomeBottomOk" class="saveTick pull-right">
                    <i class="fa fa-check-circle text-success"></i>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-2">
                <p class="text-right">@lang('contest.votingBottomHtml')
                {{--    <i class="fa fa-question-circle text-info" popover="@lang('contest.votingBottomHTMLInfo')" popover-placement="right" popover-trigger="mouseenter"></i>--}}
                </p>
            </div>
            <div id='wysiwyg' class="col-sm-12 col-md-10">
                <div text-angular name="htmlcontent-votingBottomHtml" ng-model="votingBottomHtml.html" ta-disabled='disabled'></div>
                <div ng-show="errors.htmlcontent" class="help-inline text-danger form-control-static ng-binding ng-hide">@{{ errors.htmlcontent }}</div>
                <button class="btn btn-primary btn-sm pull-right wysiwyg-save-btn" ng-click='saveVotingBottomHtml()' type="button">@lang('contest.saveHtml')</button>
                <div ng-show="saveVotingBottomOk" class="saveTick pull-right">
                    <i class="fa fa-check-circle text-success"></i>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-2">
                <p class="text-right"> @lang('contest.termsHTML')
                {{--    <i class="fa fa-question-circle text-info" popover="@lang('contest.termsHTMLInfo')" popover-placement="right" popover-trigger="mouseenter"></i>--}}
                </p>
            </div>
            <div id='wysiwyg' class="col-sm-12 col-md-10">
                <div text-angular name="htmlcontent-termsHtml" ng-model="termsHtml.html" ta-disabled='disabled'></div>
                <div ng-show="errors.htmlcontent" class="help-inline text-danger form-control-static ng-binding ng-hide">@{{ errors.htmlcontent }}</div>
                <button class="btn btn-primary btn-sm pull-right wysiwyg-save-btn" ng-click='saveTermsHtml()' type="button">@lang('contest.saveHtml')</button>
                <div ng-show="saveTermsOk" class="saveTick pull-right">
                    <i class="fa fa-check-circle text-success"></i>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-2">
                <p class="text-right">@lang('contest.newInscriptionMessage')</p>
            </div>
            <div id='wysiwyg' class="col-sm-12 col-md-10">
                <div text-angular name="htmlcontent-newInscriptionMessage" ng-model="newInscriptionMessage.html" ta-disabled='disabled'></div>
                <div ng-show="errors.htmlcontent" class="help-inline text-danger form-control-static ng-binding ng-hide">@{{ errors.htmlcontent }}</div>
                <button class="btn btn-primary btn-sm pull-right wysiwyg-save-btn" ng-click='saveNewInscriptionMessage()' type="button">@lang('contest.saveHtml')</button>
                <div ng-show="saveNewInscriptionMessageOk" class="saveTick pull-right">
                    <i class="fa fa-check-circle text-success"></i>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-2">
                <p class="text-right">@lang('contest.newJudgeInscriptionMessage')</p>
            </div>
            <div id='wysiwyg' class="col-sm-12 col-md-10">
                <div text-angular name="htmlcontent-newJudgeInscriptionMessage" ng-model="newJudgeInscriptionMessage.html" ta-disabled='disabled'></div>
                <div ng-show="errors.htmlcontent" class="help-inline text-danger form-control-static ng-binding ng-hide">@{{ errors.htmlcontent }}</div>
                <button class="btn btn-primary btn-sm pull-right wysiwyg-save-btn" ng-click='saveNewJudgeInscriptionMessage()' type="button">@lang('contest.saveHtml')</button>
                <div ng-show="saveNewJudgeInscriptionMessageOk" class="saveTick pull-right">
                    <i class="fa fa-check-circle text-success"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
@endsection

