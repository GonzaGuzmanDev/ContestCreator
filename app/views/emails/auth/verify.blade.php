@extends('emails.template')

@section('content')
	<h2 style="margin-top: 0">@lang('account.verify.subject')</h2>

	<div>
		@lang('account.verify.info')
		<br/>
		<br/>
		@lang('account.verify.link', array('url' => URL::to('account/verifyemail', array($token))))
		<br/>
		<br/>
		@lang('account.verify.thanks')
	</div>
@endsection