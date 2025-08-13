@extends('layouts.default')
@section('content')
    @include('includes.header')
    <div class="main-block contents">
        @yield('content')
    </div>
@overwrite