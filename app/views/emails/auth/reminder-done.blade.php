@extends('emails.template')

@section('content')
	<h2 style="margin-top: 0">{{ Lang::get('reminders.subject-done') }}</h2>

	<div>
		{{ Lang::get('reminders.message.done') }}
		<br/>
		<br/>
		{{ Lang::get('reminders.message.warn', array('url' => URL::to('help'))) }}
		<br/>
		<br/>
		{{ Lang::get('reminders.message.thanks') }}
	</div>
@endsection