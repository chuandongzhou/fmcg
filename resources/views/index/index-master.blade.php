@extends('master')



@section('title')@yield('subtitle') | 快销平台@stop



@section('css')
    <link href="{{ asset('css/index.css?v=1.0.0') }}" rel="stylesheet">
    @stop



@section('header')
    @include('includes.index-top')
    <nav class="navbar navbar-default wholesalers-header">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                        aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="navbar">
                <ul class="nav navbar-nav">
                    <li class="menu-list" id="menu-list">
                        <a href="#" class="menu-wrap-title list-name">商品分类</a>

                        <div class="menu-list-wrap">
                            <div class="categories" id="other-page-categories">
                                <ul class="menu-wrap">
                                    @foreach($categories as $category)
                                        <li><a class="one-title" href="{{ url('search?category_id=1'. $category['id']) }}"><i></i>{{ $category['name'] }}</a>

                                            <div class="menu-down-wrap menu-down-layer">
                                                @foreach($category['child'] as $child)
                                                    <div class="item active">
                                                        <h3 class="title"><a href="{{ url('search?category_id=2'. $child['id']) }}">{{ $child['name'] }}</a></h3>
                                                        @foreach($child['child'] as $grandChild)
                                                            <a href="{{ url('search?category_id=3'. $grandChild['id']) }}">{{ $grandChild['name'] }}</a>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="active"><a class="list-name" href="{{ url('/') }}">首页</a></li>
                    <li><a class="list-name" href="{{ url('shop') }}">商家</a></li>
                </ul>
            </div>
        </div>
    </nav>

@stop

@section('body')
    @yield('container')
    <a href="" id="alert-div" style="width: 300px;height:100px;background-color:rgb(76,185,254);position:fixed;right:0;bottom:0;text-align:center;line-height:100px;display:none;z-index: 99;color:black; font-size: 20px;">
        你有新消息了
    </a>
@stop



@section('footer')
    <footer class="panel-footer">
        <div class="container text-center text-muted">&copy;2003-2015 版权所有</div>
    </footer>
@stop

@section('js')
    <script src="{{ asset('js/index.js?v=1.0.0') }}"></script>
    @include('includes.ajaxPolling')
@stop
