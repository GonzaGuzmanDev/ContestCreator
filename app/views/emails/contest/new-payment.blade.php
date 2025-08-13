@extends('emails.template')

@section('content')
	<div>
		@if($type == Contest::TYPE_CONTEST)
			@lang('contest.entryPayedBody', ['entry'=>$entry])
		@endif
		@if($type == Contest::TYPE_TICKET)
			@lang('oxoTicket.ticketPayedBody', ['entry'=>$entry])
		@endif
		<br/>
		{{$userInfo}}
		<br/>
		<br/>
		{{$billCatsPricesInfo}}
		<br/>
		<br/>
		@if($type == Contest::TYPE_CONTEST)
			<strong><u>@lang('contest.inscription')</u></strong>
			<br>
			{{$entryInfo}}
			<br/>
		@endif
		<br/>
		@lang('contest.newBilling.viewmore')
		<br/>
		<a href="{{$link}}">{{$link}}</a>
		<br/>
		<br/>
		@lang('contest.newInscription.thanks')
	</div>
@endsection