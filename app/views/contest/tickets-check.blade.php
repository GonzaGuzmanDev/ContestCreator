<? /** @var Contest $contest  */ ?>
        <!DOCTYPE html>
<html lang="en" ng-app="OxoAwards">
<head>
    <title>OxoAwards</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="img/favicon.png"/>
    @include('includes.css', array('contest' => isset($contest) ? $contest : null))
    @yield('appcss')
</head>
<body>

@if(Config::get('app.maintenanceScheduled'))
    <div class="alert alert-danger navbar-alert text-center">
        @lang('index.maintenanceScheduled', ['from'=>Config::get('app.maintenanceDateFrom'),'to'=>Config::get('app.maintenanceDateTo')])
    </div>
@endif
</body>
</html>
<div class="col-sm-12">
    <div class="col-sm-4 col-sm-offset-4">
        <br><br><br>
        <div>
            @if($ticketStatus == Ticket::VALID)
            <div class="text-center text-success">
                <h3>
                    <i class="fa fa-check text-center"> </i>
                    <br>
                    @lang('oxoTicket.validTicket', ['ticketInfo' => $info])
                </h3>
            </div>
            @endif
            @if($ticketStatus == Ticket::INVALID)
            <div class="text-center text-danger">
                <h3>
                    <i class="fa fa-close text-center"> </i>
                    <br>
                    @lang('oxoTicket.invalidTicket', ['ticketInfo' => $info])
                </h3>
            </div>
            @endif
            @if($ticketStatus == Ticket::ALREADY_CHECKED)
            <div class="text-center">
                <h3 class="text-warning">
                    <i class="fa fa-clock-o text-center"> </i>
                    <br>
                    @lang('oxoTicket.alreadyCheckedTicket', ['times' => $times, 'ticketInfo' => $info])
                    <table class="table table-striped table-hover table-condensed">
                        <thead>
                        <tr>
                            <th> Dia </th>
                            <th> Ultima Hora </th>
                            <th> Cantidad </th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($dateChecked as $checked)
                            <!--<div>
                                {{ $checked->created_at }} : {{ $checked->total }}
                            </div>-->
                            <tr>
                                <th>{{ $checked->created_at->format('Y-m-d') }}</th>
                                <th> {{ $checked->created_at->format('H:i:s') }} </th>
                                <th class="text-right"> {{ $checked->total }}</th>
                            </tr>
                    @endforeach
                        </tbody>
                    </table>
                </h3>
            </div>
            @endif
            @if($ticketStatus == Ticket::NOT_PAYED)
            <div class="text-center text-danger">
                <h3>
                    <i class="fa fa-close text-center"> </i>
                    <br>
                    @lang('oxoTicket.notPayedTicket', ['ticketInfo' => $info])
                    <br>
                    {{$info}}
                </h3>
            </div>
            @endif
        </div>
    </div>
</div>






