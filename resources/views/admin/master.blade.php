@extends('master')


@section('title')@yield('subtitle') | 快销后台管理@stop


@section('css')
        <!-- Bootstrap -->
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
@stop


@section('header')
        <!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-static-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">导航切换</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ url('admin/home') }}">恒草堂</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                @foreach ($menus as $menu1)
                    @if ($menu1['_children'])
                        <li class="dropdown">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">{{ $menu1['name'] }}
                                <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                @foreach ($menu1['_children'] as $index => $menu2)
                                    <li><a href="{{ route($menu2['route']) }}">{{ $menu2['name'] }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        <li class=""><a href="javascript:;">{{ $menu1['name'] }}</a></li>
                    @endif
                @endforeach
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="javascript:;">缓存清理</a></li>
            </ul>
        </div>
        <!--/.nav-collapse -->
    </div>
</nav>

<div class="container notification">
    @if ($notification1)
        <div class="alert alert-{{ $notification1['status'] ? 'success' : 'danger' }} alert-dismissable">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span
                        class="sr-only">Close</span></button>
            {{ $notification1['content'] }}
        </div>
    @endif
</div>
<div class="container tab-list">
    <ul class="nav nav-tabs" role="tablist">
        @foreach($tabs as $tab)
            <li role="presentation" class="{{ $tab['active'] ? 'active' : '' }}"><a
                        href="{{ $tab['active'] ? '#' : route($tab['route']) }}">{{ $tab['value'] }}</a></li>
        @endforeach
        @yield('tab')
    </ul>
</div>
@stop


@section('body')
    <div class="container">
        @yield('admin-container')
    </div><!--/.container-->
@stop


@section('js')
    <script src="{{ asset('js/admin.js') }}"></script>
@stop