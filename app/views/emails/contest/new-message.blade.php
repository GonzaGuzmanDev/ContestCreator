@extends('emails.template')

@section('content')
	<h2 style="margin-top: 0">@lang('contest.newMessage', ['contest'=>$contest])</h2>

	<div>
		@lang('contest.newMessage.details', ['user'=>$user, 'entry'=>$entry, 'contest'=>$contest])
		<br>
		<br>
		<div>{{$message}}</div>
		<br>
		@lang('contest.newMessage.link', ['link'=>$link])
	</div>
@endsection