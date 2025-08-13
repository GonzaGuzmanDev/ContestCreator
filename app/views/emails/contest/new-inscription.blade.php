@extends('emails.template')

@section('content')
	@if($role == Inscription::INSCRIPTOR)
	<h2 style="margin-top: 0">@lang('contest.newInscriptorSubject', ['contest'=>$contest])</h2>
	@elseif($role == Inscription::JUDGE)
	<h2 style="margin-top: 0">@lang('contest.newJudgeSubject', ['contest'=>$contest])</h2>
	@endif

	<div>
		@lang('contest.newInscription.info', ['contest'=>$contest])
		<br/>
		<br/>
		<strong>@lang('register.firstName')</strong>
		<br>
		{{$user->first_name}}
		<br/>
		<br/>
		<strong>@lang('register.lastName')</strong>
		<br>
		{{$user->last_name}}
		<br/>
		<br/>
		<strong>@lang('register.email')</strong>
		<br>
		{{$user->email}}
		<br/>
		<br/>
		@lang('contest.newInscription.viewmore')
		<br>
		<a href="{{$link}}">{{$link}}</a>
		<br/>
		<br/>
		@lang('contest.newInscription.thanks')
	</div>
@endsection