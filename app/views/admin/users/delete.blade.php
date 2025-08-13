@extends('layouts.modal')

@section('modal-title')
    <i class="fa fa-trash"></i> <?=Lang::get('user.deleteUser')?>
@endsection

@section('modal-content')
    <p><?=Lang::get('user.sure')?> @{{user.id}}: @{{user.name}} (@{{user.email}})?</p>

    <div class="form-group" ng-class="{'has-error': !!errors.captcha}">
        <div class="well well-sm captcha-well text-center">
            <img ng-src="@{{captchaUrl}}" alt="Captcha image" class="captcha-img"/>
            <input type="text" id="inputCaptcha" class="form-control captcha-input input-sm" placeholder="<?=Lang::get('register.captcha')?>" ng-model="captcha" required>
            <div class="clearfix"></div>
            <span class="help-block" ng-show="errors.captcha">@{{errors.captcha.join()}}</span>
        </div>
    </div>
@endsection

@section('modal-actions')
    <button type="button" class="btn btn-default" ng-click="close()"><?=Lang::get('user.close')?></button>
    <button type="button" class="btn btn-danger" ng-click="destroy()"><?=Lang::get('user.delete')?></button>
@endsection