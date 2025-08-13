<!--css-->
<link rel="stylesheet" href="<?php echo URL::asset('css/bootstrap.min.css'); ?>"/>
<link rel="stylesheet" href="<?php echo URL::asset('css/font-awesome.min.css'); ?>"/>
<link rel="stylesheet" href="<?php echo URL::asset('css/flag-icon.min.css'); ?>"/>
<link rel="stylesheet" href="<?php echo URL::asset('css/datetimepicker.css'); ?>"/>
<link rel="stylesheet" href="<?php echo URL::asset('js/lib/textAngular/textAngular.css'); ?>"/>
<link rel="stylesheet" href="<?php echo URL::asset('css/videogular.min.css'); ?>"/>
<link rel="stylesheet" href="<?php echo URL::asset('css/ngDialog.min.css'); ?>"/>
<link rel="stylesheet" href="<?php echo URL::asset('css/ngDialog-theme-default.css'); ?>"/>
<link rel="stylesheet" href="<?php echo URL::asset('js/lib/angular-hotkeys/build/hotkeys.css'); ?>"/>
<link rel="stylesheet" href="<?php echo URL::asset('css/angular-bootstrap-lightbox.css'); ?>"/>
<link rel="stylesheet" href="<?php echo URL::asset('css/responsive.css'); ?>"/>
<link rel="stylesheet" href="<?php echo URL::asset('js/lib/fullcalendar/dist/fullcalendar.css'); ?>"/>
@if(isset($contest))
    @if($contest->style == Contest::THEMEDARK)
        <link rel="stylesheet" href="<?php echo URL::asset('css/bootstrap.slate.css'); ?>"/>
        <link rel="stylesheet" href="<?php echo URL::asset('css/app.css'); ?>"/>
        <link rel="stylesheet" href="<?php echo URL::asset('css/app.dark.css'); ?>"/>
    @elseif($contest->style == Contest::THEMELIGHT)
        <link rel="stylesheet" href="<?php echo URL::asset('css/bootstrap.lumen.css'); ?>"/>
        <link rel="stylesheet" href="<?php echo URL::asset('css/app.css'); ?>"/>
        <link rel="stylesheet" href="<?php echo URL::asset('css/app.light.css'); ?>"/>
    @endif
@else
    <link rel="stylesheet" href="<?php echo URL::asset('css/bootstrap.slate.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo URL::asset('css/app.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo URL::asset('css/app.dark.css'); ?>"/>
@endif
@if(isset($contest) && $contest->custom_style != null)
<style><?=$contest->custom_style?></style>
@endif