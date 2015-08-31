@extends('master')



@section('title')@yield('subtitle') | 快销平台@stop



@section('css')
    <link href="{{ asset('css/index.css?v=1.0.0') }}" rel="stylesheet">
    @stop



@section('header')
    <!--[if lt IE 9]>
    <div class="ie-warning alert alert-warning alert-dismissable fade in">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        您的浏览器不是最新的，您正在使用 Internet Explorer 的一个<strong>老版本</strong>。 为了获得最佳的浏览体验，我们建议您选用其它浏览器。
        <a class="btn btn-primary" href="http://browsehappy.com/" target="_blank" rel="nofollow">立即升级</a>
    </div>
    <![endif]-->
    <div class="container wholesalers-top-header">
        <div class="col-sm-4 logo">
            <a class="logo-icon">LOGO</a>
        </div>
        <div class="col-sm-4 col-sm-push-4 right-search">
            <form class="search" role="search">
                <div class="input-group">
                    <input type="text" class="form-control" aria-describedby="course-search">
                <span class="input-group-btn btn-primary">
                    <button class="btn btn-primary" type="submit">搜本店</button>
                </span>
                </div>
            </form>
        </div>
    </div>

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
                    <li class="active"><a href="#">首页</a></li>
                    {{--<li class="menu-wrap">--}}
                        {{--<a href="#" class="menu-hide item menu-wrap-title">商品分类</a>--}}
                        {{--<ul class="a-menu">--}}
                            {{--@foreach($categories as $category)--}}
                                {{--<li>--}}
                                    {{--<a href="{{ url('shop/detail/' . $category['id'] . ($type ? '/' . $type : '')) }}"--}}
                                       {{--class="menu-hide item">{{ $category['name'] }}</a>--}}
                                    {{--<ul class="secondary-menu">--}}
                                        {{--@foreach($category['child'] as $child)--}}
                                            {{--<li class="second-menu-item">--}}
                                                {{--<a href="{{ url('shop/detail/' . $child['id'] . ($type ? '/' . $type : '')) }}" class="item">--}}
                                                    {{--{{ $child['name'] }}--}}
                                                {{--</a>--}}
                                                {{--<div class="three-menu">--}}
                                                    {{--@foreach($child['child'] as $grandChild)--}}
                                                        {{--<a href="{{ url('shop/detail/' . $grandChild['id'] . ($type ? '/' . $type : '')) }}">--}}
                                                            {{--{{ $grandChild['name'] }} |--}}
                                                        {{--</a>--}}
                                                    {{--@endforeach--}}
                                                {{--</div>--}}
                                            {{--</li>--}}
                                        {{--@endforeach--}}
                                    {{--</ul>--}}
                                {{--</li>--}}
                            {{--@endforeach--}}
                        {{--</ul>--}}

                    {{--</li>--}}
                    <li><a href="#">店家信息</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="right"><a href="#">控制台</a></li>
                </ul>
            </div>
        </div>
    </nav>
@stop

@section('body')
    @yield('container')
@stop



@section('footer')
    <footer class="panel-footer">
        <div class="container text-center text-muted">&copy;2003-2015 版权所有</div>
    </footer>
@stop

@section('js')
    <script src="{{ asset('js/index.js?v=1.0.0') }}"></script>
@stop