@extends('emails.template')

@section('content')
	<h2 style="margin-top: 0">@lang('contest.newMessageSent', ['contest'=>$contest])</h2>

	<div>
		@lang('contest.newMessageSent.details', ['entry'=>$entry, 'contest'=>$contest])
		<br>
		<br>
		<div>{{$message}}</div>
		<br>
		@lang('contest.newMessage.link', ['link'=>$link])
	</div>
@endsection