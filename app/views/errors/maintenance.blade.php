<!DOCTYPE html>
<html lang="en" ng-app="OxoAwards">
<head>
    <title>OxoAwards</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="img/favicon.png"/>
    @include('includes.css')
    @yield('appcss')
</head>
<body class="text-center">
<br>
<img src="<?=asset('img/logo.png')?>" alt="">
<br>
<br>
<br>
<div class="alert alert-danger alert-inline">
@lang('index.underMaintenance',['to'=>Config::get('app.maintenanceDateTo')]);
</div>
</body>
</html>