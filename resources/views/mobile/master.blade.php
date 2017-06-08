@extends('master')

@section('title')@yield('subtitle') | 订百达 - 订货首选@stop

@section('meta')
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
@stop

@include('mobile.includes.error')

@section('js-lib')
    <script type="text/javascript" src="{{ asset('mobile/layer.js') }}"></script>
    <script type="text/javascript" src="{{ asset('mobile/mobile.js') }}"></script>
@stop
