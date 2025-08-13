@extends('layouts.default')

@section('content')
    <div class="" ng-view></div>
@stop

@section('appjs')
    <script src="<?php echo asset('js/app/admin.js'); ?>"></script>
@stop
@section('app-config')
    <script>
        OxoAwards.constant('inAdmin', true);
        OxoAwards.constant('default_storage_sources_bucket', '<?=Config::get('cloud.default_storage_sources_bucket');?>');
        OxoAwards.constant('contest', null);
    </script>
@stop
@section('app-controllersjs')
    <script src="<?php echo asset('js/controllers/admin/main.js'); ?>"></script>
    <script src="<?php echo asset('js/admin-services.js'); ?>"></script>
    @if(Auth::check() && Auth::user()->isSuperAdmin())
    <script src="<?php echo asset('js/controllers/admin/home.js'); ?>"></script>
    <script src="<?php echo asset('js/controllers/admin/users.js'); ?>"></script>
    <script src="<?php echo asset('js/controllers/admin/formats.js'); ?>"></script>
    <script src="<?php echo asset('js/controllers/admin/contest-files.js'); ?>"></script>
    <script src="<?php echo asset('js/controllers/admin/contests-routes.js'); ?>"></script>
    <script src="<?php echo asset('js/controllers/admin/contests-controllers.js'); ?>"></script>
    <script src="<?php echo asset('js/controllers/contest/admin/controllers.js'); ?>"></script>
    @endif
@stop