@extends('emails.template')

@section('content')
	<h2 style="margin-top: 0">@lang('home.contact-mail-subject')</h2>

	<div>
		<strong>@lang('home.contact-mail-name'):</strong> {{$name}}
		<br/>
		<br/>
		<strong>@lang('home.contact-mail-email'):</strong> {{$email}}
		<br/>
		<br/>
		<strong>@lang('home.contact-mail-phone'):</strong> {{$phone}}
		<br/>
		<br/>
		<strong>@lang('home.contact-mail-message'):</strong>
		<br/>
		{{$messageText}}
	</div>
@endsection