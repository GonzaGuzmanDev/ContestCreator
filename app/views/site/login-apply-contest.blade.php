@include('includes.header')

<div class="main-block panned col-md-offset-1 col-md-10 col-sm-10">
        <div class="col-sm-12">
            <h2>@lang('contest.wizard.applyContestWelcome')</h2>
        </div>
        <div class="col-sm-6 col-md-6">
            <div class="well">
                @include('login.form-body')
            </div>
        </div>
        <div class="col-sm-6 col-md-6">
            <div class="well">
                @include('login.register-form', ['showAlreadyRegistered'=>false])
            </div>
        </div>
    </div>

@include('includes.footer')