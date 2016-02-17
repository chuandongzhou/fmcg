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
                                <li><a href="{{ url('/') }}" class="home"><span class="fa fa-home"></span> 订百达首页</a>
                                </li>
                            @endif
                            <li><a href="{{ url('personal/info') }}"><span class="fa fa-star-o"></span> 管理中心</a></li>
                            <li>
                                <a href="{{ $user->type == cons('user.type.retailer') ? url('order-buy') : url('order-sell') }}">
                                    <span class="fa fa-file-text-o"></span> 我的订单
                                </a>
                            </li>
                            <li><a href="{{ url('help') }}"><span class="fa fa-question-circle"></span> 帮助中心</a></li>
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
                                <a href="{{ url('auth/logout') }}" class="exit"><i class="fa fa-sign-out"></i> 退出</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('body')
    @yield('container')
    <div class="msg-channel" id="alert-div">
        <p class="title"><span class="pull-left">你有新消息</span><a class="close-btn fa fa-remove pull-right"></a></p>
        <a class="check" href="#">点击查看>>>></a>
    </div>
@stop



@section('footer')
    <footer class="panel-footer">
        <div class="container text-center text-muted">
            Copyright2015成都订百达科技有限公司 蜀ICP备15031748号-1<br/>
            联系地址：成都市高新区天府大道中段1388号美年广场A座1248号&nbsp;&nbsp;联系方式：13829262065(霍女士)
        </div>
    </footer>
@stop

@section('js-lib')
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=mUrGqwp43ceCzW41YeqmwWUG"></script>
    <script src="{{ asset('js/index.js?v=1.0.0') }}"></script>
    <script src="{{ asset('js/ajax-polling.js') }}"></script>
@stop
@section('js')
    <script type="text/javascript">
        $(function () {
            if (!Cookies.get('province_id')) {
                setProvinceName();
            }
        })
    </script>
@stop