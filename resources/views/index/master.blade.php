@extends('master')



@section('title')@yield('subtitle') | 订百达@stop



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
                                    class="city-value">{{  $provinces[\Request::cookie('province_id')] or '' }}</span> <span
                                    class="fa fa-angle-down up-down"></span></a>
                    </div>
                    <div class="city-list clearfix">
                        <div class="list-wrap">
                            @foreach($provinces as $provinceId => $province)
                                <div class="item">
                                    <a title="{{ $province }}"
                                       class="{{ \Request::cookie('province_id') == $provinceId ? 'selected' : '' }}"
                                       href="javascript:void(0)" data-id="{{ $provinceId }}">{{ $province }}</a>
                                </div>
                            @endforeach
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
                            @if($user->type <= cons('user.type.wholesaler'))
                                <li><a href="{{ url('/') }}" class="home"><span class="fa fa-home"></span> 订百达首页</a></li>
                            @endif
                            <li><a href="{{ url('personal/info') }}"><span class="fa fa-star-o"></span> 管理中心</a></li>
                            <li>
                                <a href="{{ $user->type == cons('user.type.retailer') ? url('order-buy') : url('order-sell') }}">
                                    <span class="fa fa-file-text-o"></span> 我的订单
                                </a>
                            </li>
                            @if($user->type < cons('user.type.supplier'))
                                <li class="collect-select">
                                    <a class="collect-selected"><span class="selected">收藏夹</span> <span
                                                class="fa fa-angle-down"></span></a>
                                    <ul class="select-list">
                                        <li><a href="{{ url('like/shops') }}">店铺收藏</a></li>
                                        <li><a href="{{ url('like/goods') }}">商品收藏</a></li>
                                    </ul>
                                </li>
                            @endif
                            <li class="user-name-wrap">
                                <a href="{{ url('personal/shop') }}" class="name-panel"><span
                                            class="user-name">{{ $user->shop->name }}</span>( {{ cons()->valueLang('user.type' , $user->type) }}
                                    )</a>
                                <a href="{{ url('auth/logout') }}" class="exit"><span class="fa fa-ban"></span> 退出</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container wholesalers-top-header">
        <div class="col-sm-4 logo">
            <a href="{{ url('/') }}" class="logo-icon"><img src="{{ asset('images/logo.png') }}"/></a>
        </div>
        @if ($shop->id == $user->shop->id)
            <div class="col-sm-4 col-sm-push-4 right-search">
                <form action="{{ url('shop/' . $shop->id . '/search') }}" class="search" role="search" autocomplete="off">
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
                <form action="{{ url('shop/' . $shop->id . '/search') }}" class="search" role="search" autocomplete="off">
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
                    {{--<li class="menu-list" id="menu-list">--}}
                        {{--<a href="#" class="menu-wrap-title list-name">商品分类</a>--}}

                        {{--<div class="menu-list-wrap">--}}
                            {{--<div class="categories" id="other-page-categories">--}}
                                {{--<ul class="menu-wrap">--}}
                                    {{--@foreach($categories as $category)--}}
                                        {{--<li class="list1">--}}
                                            {{--<a class="one-title"--}}
                                               {{--href="{{ url('shop/' . $shop->id . '/search?category_id=1' . $category['id']) }}"><i></i>{{ $category['name'] }}--}}
                                            {{--</a>--}}

                                            {{--<div class="menu-down-wrap menu-down-layer">--}}
                                                {{--@foreach($category['child'] as $child)--}}
                                                    {{--<div class="item active">--}}
                                                        {{--<h3 class="title">--}}
                                                            {{--<a href="{{ url('shop/'  . $shop->id . '/search?category_id=2' . $child['id']) }}">--}}
                                                                {{--{{ $child['name'] }}--}}
                                                            {{--</a>--}}
                                                        {{--</h3>--}}
                                                        {{--@foreach($child['child'] as $grandChild)--}}
                                                            {{--<a href="{{ url('shop/'  . $shop->id . '/search?category_id=3' . $grandChild['id']) }}">--}}
                                                                {{--{{ $grandChild['name'] }}--}}
                                                            {{--</a>--}}
                                                        {{--@endforeach--}}
                                                    {{--</div>--}}
                                                {{--@endforeach--}}
                                            {{--</div>--}}
                                        {{--</li>--}}
                                    {{--@endforeach--}}
                                {{--</ul>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</li>--}}
                    <li><a href="{{ url('shop/' . $shop->id . '/detail') }}">店家信息</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    @if ($shop->id != $user->shop->id)
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
        <div class="container text-center text-muted">
            Copyright2015成都订百达科技有限公司 蜀ICP备15031748号-1<br/>
            联系地址：成都市高新区天府大道中段1388号美年广场A座1248号&nbsp;&nbsp;联系方式:13829262065(霍女士)
        </div>
    </footer>
@stop

@section('js-lib')
    <script src="{{ asset('js/index.js?v=1.0.0') }}"></script>
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=mUrGqwp43ceCzW41YeqmwWUG"></script>
@stop
@section('js')
    <script type="text/javascript">
        $(function () {
            likeFunc();

            if (!Cookies.get('province_id')) {
                var geolocation = new BMap.Geolocation();
                geolocation.getCurrentPosition(function (r) {
                    if (this.getStatus() == BMAP_STATUS_SUCCESS) {
                        setProvince(r.point.lng, r.point.lat);
                    }
                    else {
                        alert('failed' + this.getStatus());
                    }
                }, {enableHighAccuracy: true})

                function setProvince(lng, lat) {
                    var myGeo = new BMap.Geocoder();
                    myGeo.getLocation(new BMap.Point(lng, lat), function (result) {
                        $('span.city-value').html(result.addressComponents.province);
                    });
                }
            }
        })
    </script>
@stop