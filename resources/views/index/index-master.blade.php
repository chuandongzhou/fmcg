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

    <div class="dealer-top-header">
        <div class="container ">
            <div class="row">
                <div class="col-sm-4 city-wrap">
                    <div class="location-panel">
                        <i class="fa fa-map-marker"></i> 所在地：<a href="#" class="location-text"><span
                                    class="city-value">成都</span> <span class="fa fa-angle-down up-down"></span></a>
                    </div>
                    <div class="city-list clearfix">
                        <div class="list-wrap">
                            <div class="item"><a href="javascript:void(0)" class="selected">成都</a></div>
                            <div class="item"><a href="javascript:void(0)">上海</a></div>
                            <div class="item"><a href="javascript:void(0)">天津</a></div>
                            <div class="item"><a href="javascript:void(0)">重庆</a></div>
                            <div class="item"><a href="javascript:void(0)">河北</a></div>
                            <div class="item"><a href="javascript:void(0)">山西</a></div>
                            <div class="item"><a href="javascript:void(0)">河南</a></div>
                            <div class="item"><a href="javascript:void(0)">辽宁</a></div>
                            <div class="item"><a href="javascript:void(0)">吉林</a></div>
                            <div class="item"><a href="javascript:void(0)">黑龙江</a></div>
                            <div class="item"><a href="javascript:void(0)">内蒙古</a></div>
                            <div class="item"><a href="javascript:void(0)">江苏</a></div>
                            <div class="item"><a href="javascript:void(0)">山东</a></div>
                            <div class="item"><a href="javascript:void(0)">安徽</a></div>
                            <div class="item"><a href="javascript:void(0)">浙江</a></div>
                            <div class="item"><a href="javascript:void(0)">福建</a></div>
                            <div class="item"><a href="javascript:void(0)">湖北</a></div>
                            <div class="item"><a href="javascript:void(0)">湖南</a></div>
                            <div class="item"><a href="javascript:void(0)">广东</a></div>
                            <div class="item"><a href="javascript:void(0)">广西</a></div>
                            <div class="item"><a href="javascript:void(0)">江西</a></div>
                            <div class="item"><a href="javascript:void(0)">四川</a></div>
                            <div class="item"><a href="javascript:void(0)">海南</a></div>
                            <div class="item"><a href="javascript:void(0)">贵州</a></div>
                            <div class="item"><a href="javascript:void(0)">云南</a></div>
                            <div class="item"><a href="javascript:void(0)">西藏</a></div>
                            <div class="item"><a href="javascript:void(0)">陕西</a></div>
                            <div class="item"><a href="javascript:void(0)">甘肃</a></div>
                            <div class="item"><a href="javascript:void(0)">青海</a></div>
                            <div class="item"><a href="javascript:void(0)">宁夏</a></div>
                            <div class="item"><a href="javascript:void(0)">新疆</a></div>
                            <div class="item"><a href="javascript:void(0)">台湾</a></div>
                            <div class="item"><a href="javascript:void(0)">香港</a></div>
                            <div class="item"><a href="javascript:void(0)">澳门</a></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed navbar-button" data-toggle="collapse"
                                data-target="#bs-example-navbar-collapse-9" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div class="navbar-collapse collapse top-nav-list" id="bs-example-navbar-collapse-9"
                         aria-expanded="false" style="height: 1px;">
                        <ul class="nav navbar-nav navbar-right operating-wrap">
                            <li><a href="#"><span class="fa fa-heart-o"></span> 个人中心</a></li>
                            <li><a href="#"><span class="fa fa-file-text-o"></span> 我的订单</a></li>
                            <li><a href="#"><span class="fa fa-star-o"></span> 收藏夹</a></li>
                            <li><a href="#"><span class="fa fa-ban"></span> 退出</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <nav class="navbar dealer-header">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed navbar-button" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand logo-icon" href="#">LOGO</a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <form class="navbar-form navbar-left search text-center" role="search">
                    <div class="input-group">
                        <div class="select-role pull-left">
                            <a href="#" class="selected"><span>商品1</span><i class="fa fa-angle-down"></i></a>
                            <ul class="select-list">
                                <li><a href="#">商品2</a></li>
                                <li><a href="#">经销商</a></li>
                            </ul>
                        </div>
                        <input type="text" class="control pull-right" aria-describedby="course-search">
                    <span class="input-group-btn btn-primary">
                        <button class="btn btn-primary search-btn" type="submit">搜索</button>
                    </span>
                    </div>
                </form>
                <ul class="nav navbar-nav navbar-right right-btn">
                    <li><a href="{{ url('cart') }}" class="btn btn-primary"><i class="fa fa-shopping-cart"></i> 购物车</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

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
                    <li><a class="list-name" href="#">店家信息</a></li>
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