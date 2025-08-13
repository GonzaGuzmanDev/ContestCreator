@extends('admin.contests.form', array('active' => 'mail'))
@section('form')
    <h4 class="well well-sm">
        @lang('contest.contestEmails')
        {{--@if(!$superadmin)
            <i class="fa fa-question-circle text-info" popover="@lang('contest.explain.style')" popover-placement="right" popover-trigger="mouseenter" class="btn btn-default"></i>
        @endif--}}
    </h4>

    <div class="row">
        <div class="col-md-2">
            <p class="text-right">@lang('contest.inscriptionEmail')</p>
        </div>
        <div id='wysiwyg' class="col-sm-12 col-md-10">
            <div text-angular name="htmlcontent@{{ inscriptionEmail.name }}" ng-model="inscriptionEmail.html" ta-disabled='disabled'></div>
            <div ng-show="errors.htmlcontent" class="help-inline text-danger form-control-static ng-binding ng-hide">@{{ errors.htmlcontent }}</div>
            <button class="btn btn-primary btn-sm pull-right wysiwyg-save-btn" ng-click='save(inscriptionEmail)' type="button">@lang('contest.saveHtml')</button>
            <div ng-show="saveHomeOk" class="saveTick pull-right">
                <i class="fa fa-check-circle text-success"></i>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <p class="text-right"> @lang('contest.inscriptorInvitationEmail') </p>
        </div>
        <div id='wysiwyg' class="col-sm-12 col-md-10">
            <div text-angular name="htmlcontent@{{ inscriptorInvitationEmail.name }}" ng-model="inscriptorInvitationEmail.html" ta-disabled='disabled'></div>
            <div ng-show="errors.htmlcontent" class="help-inline text-danger form-control-static ng-binding ng-hide">@{{ errors.htmlcontent }}</div>
            <button class="btn btn-primary btn-sm pull-right wysiwyg-save-btn" ng-click='save(inscriptorInvitationEmail)' type="button">@lang('contest.saveHtml')</button>
            <div ng-show="saveTermsOk" class="saveTick pull-right">
                <i class="fa fa-check-circle text-success"></i>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2">
            <p class="text-right"> @lang('contest.judgeInvitationEmail') </p>
            <p class="help-block text-right">@lang('contest.emailsReplaces')<br>
                :contest <strong>Contest name</strong> <br>
                :name <strong>Judge full name</strong> <br>
                <span style="color:#F00;">:link</span> <strong>Link to invite</strong><span style="color:#F00;">*</span><br>
                :rejectlink <strong>Link to reject invite</strong>
                <br>
                :firstname <strong>Judge first name (preferably use :name)</strong> <br>
                :lastname <strong>Judge first name (preferably use :name)</strong> <br>
                :code <strong> If we like to send codes </strong> <br>
                :invite <strong> Link for the codes</strong> <br>

            </p>
        </div>
        <div id='wysiwyg' class="col-sm-12 col-md-10">
            <div text-angular name="htmlcontent@{{ judgeInvitationEmail.name }}" ng-model="judgeInvitationEmail.html" ta-disabled='disabled'></div>
            <div ng-show="errors.htmlcontent" class="help-inline text-danger form-control-static ng-binding ng-hide">@{{ errors.htmlcontent }}</div>
            <button class="btn btn-primary btn-sm pull-right wysiwyg-save-btn" ng-click='save(judgeInvitationEmail)' type="button">@lang('contest.saveHtml')</button>
            <div ng-show="saveTermsOk" class="saveTick pull-right">
                <i class="fa fa-check-circle text-success"></i>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2">
            <p class="text-right"> @lang('contest.collaboratorInvitationEmail') </p>
        </div>
        <div id='wysiwyg' class="col-sm-12 col-md-10">
            <div text-angular name="htmlcontent@{{ collaboratorInvitationEmail.name }}" ng-model="collaboratorInvitationEmail.html" ta-disabled='disabled'></div>
            <div ng-show="errors.htmlcontent" class="help-inline text-danger form-control-static ng-binding ng-hide">@{{ errors.htmlcontent }}</div>
            <button class="btn btn-primary btn-sm pull-right wysiwyg-save-btn" ng-click='save(@{{ collaboratorInvitationEmail }})' type="button">@lang('contest.saveHtml')</button>
            <div ng-show="saveTermsOk" class="saveTick pull-right">
                <i class="fa fa-check-circle text-success"></i>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2">
            <p class="text-right"> @lang('contest.entryErrorEmail') </p>
        </div>
        <div id='wysiwyg' class="col-sm-12 col-md-10">
            <div text-angular name="htmlcontent@{{ entryErrorEmail.name }}" ng-model="entryErrorEmail.html" ta-disabled='disabled'></div>
            <div ng-show="errors.htmlcontent" class="help-inline text-danger form-control-static ng-binding ng-hide">@{{ errors.htmlcontent }}</div>
            <button class="btn btn-primary btn-sm pull-right wysiwyg-save-btn" ng-click='save(entryErrorEmail)' type="button">@lang('contest.saveHtml')</button>
            <div ng-show="saveTermsOk" class="saveTick pull-right">
                <i class="fa fa-check-circle text-success"></i>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2">
            <p class="text-right"> @lang('contest.entryApprovedEmail') </p>
        </div>
        <div id='wysiwyg' class="col-sm-12 col-md-10">
            <div text-angular name="htmlcontent@{{ entryApprovedEmail.name }}" ng-model="entryApprovedEmail.html" ta-disabled='disabled'></div>
            <div ng-show="errors.htmlcontent" class="help-inline text-danger form-control-static ng-binding ng-hide">@{{ errors.htmlcontent }}</div>
            <button class="btn btn-primary btn-sm pull-right wysiwyg-save-btn" ng-click='save(entryApprovedEmail)' type="button">@lang('contest.saveHtml')</button>
            <div ng-show="saveTermsOk" class="saveTick pull-right">
                <i class="fa fa-check-circle text-success"></i>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2">
            <p class="text-right"> @lang('contest.entryFinalizedEmail') </p>
            <p class="help-block text-right">@lang('contest.emailsReplaces')<br>
                :contest <strong>Contest name</strong> <br>
                :entry <strong>Entry id</strong> <br>
                :title <strong>Entry title</strong> <br>
                :name <strong>Inscriptor full name</strong> <br>
                <span style="color:#F00;">:link</span> <strong>Link to entry</strong><span style="color:#F00;">*</span><br>
                :message <strong>Message</strong>
            </p>
        </div>
        <div id='wysiwyg' class="col-sm-12 col-md-10">
            <div text-angular name="htmlcontent@{{ entryFinalizedEmail.name }}" ng-model="entryFinalizedEmail.html" ta-disabled='disabled'></div>
            <div ng-show="errors.htmlcontent" class="help-inline text-danger form-control-static ng-binding ng-hide">@{{ errors.htmlcontent }}</div>
            <button class="btn btn-primary btn-sm pull-right wysiwyg-save-btn" ng-click='save(entryFinalizedEmail)' type="button">@lang('contest.saveHtml')</button>
            <div ng-show="saveTermsOk" class="saveTick pull-right">
                <i class="fa fa-check-circle text-success"></i>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2">
            <p class="text-right"> @lang('contest.mediaErrorEmail') </p>
        </div>
        <div id='wysiwyg' class="col-sm-12 col-md-10">
            <div text-angular name="htmlcontent@{{ mediaErrorEmail.name }}" ng-model="mediaErrorEmail.html" ta-disabled='disabled'></div>
            <div ng-show="errors.htmlcontent" class="help-inline text-danger form-control-static ng-binding ng-hide">@{{ errors.htmlcontent }}</div>
            <button class="btn btn-primary btn-sm pull-right wysiwyg-save-btn" ng-click='save(mediaErrorEmail)' type="button">@lang('contest.saveHtml')</button>
            <div ng-show="saveTermsOk" class="saveTick pull-right">
                <i class="fa fa-check-circle text-success"></i>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2">
            <p class="text-right"> @lang('contest.otherPurposesEmail') </p>
        </div>
        <div id='wysiwyg' class="col-sm-12 col-md-10">
            <div text-angular name="htmlcontent@{{ otherPurposesEmail.name }}" ng-model="otherPurposesEmail.html" ta-disabled='disabled'></div>
            <div ng-show="errors.htmlcontent" class="help-inline text-danger form-control-static ng-binding ng-hide">@{{ errors.htmlcontent }}</div>
            <button class="btn btn-primary btn-sm pull-right wysiwyg-save-btn" ng-click='save(otherPurposesEmail)' type="button">@lang('contest.saveHtml')</button>
            <div ng-show="saveTermsOk" class="saveTick pull-right">
                <i class="fa fa-check-circle text-success"></i>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
@endsection

