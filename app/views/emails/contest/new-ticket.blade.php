@extends('emails.template', array('contestTicket' => $contest))

@section('content')
    <?php
    foreach(json_decode($tickets) as $ticket){;?>
	<div class="container">
		{{ $contest->getAsset(ContestAsset::SMALL_BANNER_HTML)->content }}
		<h4>
			Gracias por participar del FIAP 2018. Te esperamos en el Faena Art Center de Puerto Madero para disfrutar del festival más prestigioso de la región.
			Para ingresar deberás mostrar el código QR que aparece debajo. También encontraras el resumen de tu compra y el detalle de las actividades de cada jornada.
			<br> <b style="text-align: center;">¡Muchas gracias por ser parte del FIAP 2018!</b>
		</h4>
		<br>
		<div style="text-align: center">
				<h4>
					<img style="float: left;" src="<?=$ticket->qr?>">
					<table>
						<tbody>
						<tr>
							<td> @lang('oxoTicket.email.reservationCode')</td>
							<td style="font-weight: bold;">{{$ticket->code}} </td>
						</tr>
						<tr>
							<td> @lang('oxoTicket.email.event') </td>
							<td style="font-weight: bold;"> FIAP 2018</td>
						</tr>
						<tr>
							<td> @lang('oxoTicket.email.place')</td>
							<td style="font-weight: bold;"> FAENA ART CENTER</td>
						</tr>
						<tr>
							<td> @lang('oxoTicket.email.direction')</td>
							<td style="font-weight: bold;"> Aime Paine 1169, Puerto Madero</td>
						</tr>
						<tr>
							<td> @lang('oxoTicket.email.date')</td>
							<td style="font-weight: bold;">
								<!--
								6986	Festival completo
								6987	Jornada Innovación
								6988	Jornada Audiencias
								6989	Jornada Producción + Ceremonia de entrega de Premios
								6990	Estudiantes
								-->
								@if($ticket->ticketId == 6986 || $ticket->ticketId == 6990)
									24, 25 y 26 de Septiembre
								@endif
								@if($ticket->ticketId == 6987)
									24 de Septiembre
								@endif
								@if($ticket->ticketId == 6988)
									25 de Septiembre
								@endif
								@if($ticket->ticketId == 6989)
									26 de Septiembre
								@endif
							</td>
						</tr>
						<tr>
							<td> @lang('oxoTicket.email.eventDay')</td>
							<td style="font-weight: bold;"> {{$ticket->name}} </td>
						</tr>
						</tbody>
					</table>
					@if($ticket->ticketId == 6990 || $ticket->ticketId ==6987 || $ticket->ticketId ==6986)
						<img src="https://www.oxoawards.com/tickets-fiap/asset/1613" alt="Innovacion" width="100%"/>
					@endif
					@if($ticket->ticketId == 6990 || $ticket->ticketId ==6988 || $ticket->ticketId ==6986)
						<img src="https://www.oxoawards.com/tickets-fiap/asset/1612" alt="Audiencias" width="100%"/>
					@endif
					@if($ticket->ticketId == 6990 || $ticket->ticketId == 6989 || $ticket->ticketId ==6986)
						<img src="https://www.oxoawards.com/tickets-fiap/asset/1614" alt="Produccion" width="100%"/>
					@endif
					<h6>
						@if($ticket->ticketId == 6986)
							<p class="text-center"> Festival Completo. Faena Art Center. Del 24 al 26 de Septiembre. Incluye ceremonia de entrega de premios. </p>
						@endif
						<p class="text-center"> La agenda de FIAP 2018 puede sufrir modificaciones sin previo aviso. </p>
					</h6>
					<hr>
				</h4>
			<div class="clearfix"></div>
		</div>
	<br>
</div>
<?php };?>
@endsection