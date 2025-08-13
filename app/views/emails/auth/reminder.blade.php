@extends('emails.template')

@section('content')
	<h2 style="margin-top: 0">@lang('reminders.subject')</h2>

	<div>
		@lang('reminders.message.info')
		<br/>
		<br/>
		@lang('reminders.message.link', array('url' => URL::to('service/password/reset', array($token)), 'expire' => Config::get('auth.reminder.expire', 60)))
		<br/>
		<br/>
		@lang('reminders.message.change')
		<br/>
		<br/>
		@lang('reminders.message.thanks')
	</div>
@endsection