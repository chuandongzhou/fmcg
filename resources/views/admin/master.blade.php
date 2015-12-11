@extends('master')


@section('title')@yield('subtitle') | 订百达后台管理@stop


@section('css')
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
@stop


@section('header')
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                        aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">切换导航</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ url('admin') }}">订百达</a>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li class="right"><a href="{{ url('admin/auth/logout') }}"><i class="fa fa-sign-out"></i> 退出</a></li>
            </ul>
        </div>
    </nav>
@stop


@section('body')
    <div class="container-fluid admin-container">
        <div class="row">
            <div class="col-sm-2">
                <div class="row left-container">
                    <div class="panel-group text-center" id="accordion">

                        @foreach($nodes as $key => $node)
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion"
                                           href="#collapse-{{ $key }}">
                                            {{ $node['name'] }}
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapse-{{ $key }}" class="panel-collapse collapse
                                {{ path_active(array_filter(array_merge(array_pluck($node['child'], 'manage_url'), array_pluck($node['child'], 'url'))) , 'in') }}">
                                    @if( isset($node['child']))
                                        <div class="panel-body">
                                            <ul>
                                                @foreach($node['child'] as $childNode)
                                                    <li>
                                                        <a href="{{ url($childNode['url']) }}">{{ $childNode['name'] }}</a>
                                                        @if($childNode['manage_url'])
                                                            <a href="{{ url($childNode['manage_url']) }}"
                                                               class="manger">管理</a>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-sm-10">
                <div class="right-container">
                    @yield('right-container')
                </div>
            </div>
        </div>
    </div><!--/.container-->
@stop


@section('js')
    <script src="{{ asset('js/admin.js') }}"></script>
@stop