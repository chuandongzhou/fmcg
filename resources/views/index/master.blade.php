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
            <a href="{{ url('/') }}" class="logo-icon">LOGO</a>
        </div>
        @if ($shop->id == auth()->user()->shop->id)
            <div class="col-sm-4 col-sm-push-4 right-search">
                <form action="{{ url('shop/' . $shop->id . '/search') }}" class="search" role="search">
                    <div class="input-group">
                        <input type="text" name="name" class="form-control" aria-describedby="course-search">
                <span class="input-group-btn btn-primary">
                    <button class="btn btn-primary" type="submit">搜本店</button>
                </span>
                    </div>
                </form>
            </div>
        @else
            <div class="col-sm-4  right-search">
                <form action="{{ url('shop/' . $shop->id . '/search') }}" class="search" role="search">
                    <div class="input-group">
                        <input type="text" class="form-control" aria-describedby="course-search">
                <span class="input-group-btn btn-primary">
                    <button class="btn btn-primary" type="submit">搜本店</button>
                </span>
                    </div>
                </form>
            </div>
            <div class="col-sm-4 text-right shopping-car">
                <a href="{{ url('cart') }}" class="btn btn-primary"><i class="fa fa-shopping-cart"></i> 购物车</a>
            </div>
        @endif
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
                    <li class="active"><a class="list-name" href="{{ url('shop/' . $shop->id) }}">商店首页</a></li>
                    <li class="menu-list" id="menu-list">
                        <a href="#" class="menu-wrap-title list-name">商品分类</a>

                        <div class="menu-list-wrap">
                            <div class="categories" id="other-page-categories">
                                <ul class="menu-wrap">
                                    @foreach($categories as $category)
                                        <li class="list1">
                                            <a class="one-title"
                                               href="{{ url('shop/' . $shop->id . '/search?category_id=1' . $category['id']) }}"><i></i>{{ $category['name'] }}
                                            </a>

                                            <div class="menu-down-wrap menu-down-layer">
                                                @foreach($category['child'] as $child)
                                                    <div class="item active">
                                                        <h3 class="title">
                                                            <a href="{{ url('shop/'  . $shop->id . '/search?category_id=2' . $child['id']) }}">
                                                                {{ $child['name'] }}
                                                            </a>
                                                        </h3>
                                                        @foreach($child['child'] as $grandChild)
                                                            <a href="{{ url('shop/'  . $shop->id . '/search?category_id=3' . $grandChild['id']) }}">
                                                                {{ $grandChild['name'] }}
                                                            </a>
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
                    <li><a href="{{ url('shop/' . $shop->id . '/detail') }}">店家信息</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    @if ($shop->id == auth()->user()->shop->id)
                        <li class="right"><a href="{{ url('order-sell') }}">控制台</a></li>
                    @else
                        <li class="right">
                            <a href="javascript:void(0)" data-type="shops" data-method="post"
                               class="btn btn-like" data-id="{{ $shop->id }}">
                                @if(is_null($isLike))
                                    <i class="fa fa-star-o"></i> 加入收藏夹
                                @else
                                    <i class="fa fa-star"></i> 已收藏
                                @endif
                            </a>
                        </li>
                    @endif
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

@section('js-lib')
    <script src="{{ asset('js/index.js?v=1.0.0') }}"></script>
@stop
@section('js')
    <script type="text/javascript">
        $(function () {
            likeFunc();
        })
    </script>
@stop