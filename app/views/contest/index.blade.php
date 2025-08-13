@extends('layouts.default')

@section('content')
    <div class="" ng-view id="main-content"></div>
@stop

@section('appcss')
    <link rel="stylesheet" href="<?php echo asset('css/contest.css'); ?>"/>
@stop
@section('appjs')
    <script src="<?php echo asset('js/app/contest.js'); ?>"></script>
@stop
@section('app-config')
    <script>
        OxoAwards.constant('contest', @if(isset($contest)) {{$contest->toJson(JSON_NUMERIC_CHECK)}} @else {{'null'}} @endif);
        OxoAwards.constant('inAdmin', false);
        OxoAwards.constant('TCOSandbox', <?=Config::get('billing.TCOSandbox') ? "true":"false"?>);
        OxoAwards.constant('MPSandbox', <?=Config::get('billing.MPSandbox') ? "true":"false"?>);
        OxoAwards.constant('isLogged', @if(Auth::check()) true @else false @endif);
    </script>
@stop
@section('app-controllersjs')
    <script src="<?php echo asset('js/controllers/contest/home.js'); ?>"></script>
    @if(Auth::check())
        <? $admjs = false; ?>
        @foreach($inscriptions as $inscription)
            @if(!$admjs && ($inscription->role == Inscription::OWNER || $inscription->role == Inscription::COLABORATOR))
                <script src="<?php echo asset('js/controllers/contest/admin/routes.js'); ?>"></script>
                <script src="<?php echo asset('js/controllers/contest/admin/controllers.js'); ?>"></script>
                <? $admjs = true; ?>
            @endif
        @endforeach
        @if(Auth::user()->isSuperAdmin())
            <script src="<?php echo asset('js/controllers/contest/admin/routes.js'); ?>"></script>
            <script src="<?php echo asset('js/controllers/contest/admin/controllers.js'); ?>"></script>
        @endif
    @endif
    @if($contest->facebook_pixel_id)
        <!-- Facebook Pixel Code -->
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)}(window,document,'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '<?php echo $contest->facebook_pixel_id ?>');
            fbq('track', 'PageView');
        </script>
        <noscript>
            <img height="1" width="1"
                 src="<?php echo "https://www.facebook.com/tr?id=".$contest->facebook_pixel_id."&ev=PageView&noscript=1" ?>"/>
        </noscript>
        <!-- End Facebook Pixel Code -->
    @endif
    <!-- Google Analytics -->
    @if($contest->google_analytics_id)
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', '<?php echo $contest->google_analytics_id?>', 'auto');
        ga('send', 'pageview');
    </script>
    <!-- End Google Analytics -->
    @endif
@stop