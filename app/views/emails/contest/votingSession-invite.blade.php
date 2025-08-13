@extends('emails.template')

@section('content')
	<h2 style="margin-top: 0">@lang('voting.inviteSubject')</h2>

	<div>
		@lang('voting.inviteIntro')
		<br/>
		<br/>
		<a href=":link">
			@lang('voting.inviteStart')
		</a>
		<br/>
		<br/>
		@lang('voting.inviteOrReject')
		<br/>
		<br/>
		<a href=":rejectlink">
			@lang('voting.rejectInvite')
		</a>
		<br/>
		@lang('voting.thanks')
	</div>
@endsection