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
                <div class="col-sm-2 text-center top-title">
                    管理中心
                </div>
                <div class="col-sm-10">
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
                            <li>
                                <a href="{{ url('/') }}" class="home"><span class="fa fa-home"></span> 订百达首页</a>
                            </li>
                            @if($user->type <= cons('user.type.wholesaler'))
                                <li><a href="{{ url('cart') }}"><span class="fa fa-shopping-cart"></span> 购物车</a></li>
                            @endif
                            <li><a href="{{ url('help') }}"><span class="fa fa-question-circle"></span> 帮助中心</a></li>
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
    <div class="footer">
        <footer class="panel-footer">
            <div class="container text-center text-muted">
                <div class="row">
                    <div class="col-sm-5 col-sm-push-2 text-left">
                        <p>Copyright {!! cons('system.company_name') !!}</p>

                        <p>{!! cons('system.company_record') !!}</p>
                    </div>
                    <div class="col-sm-6 text-left">
                        <p>联系方式：{{ cons('system.company_tel') . ' ' . cons('system.company_mobile') }}</p>

                        <p>联系地址：{{ cons('system.company_addr') }}</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
@stop

@section('js-lib')
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=mUrGqwp43ceCzW41YeqmwWUG"></script>
    <script src="{{ asset('js/index.js?v=1.0.0') }}"></script>
    <script src="{{ asset('js/ajax-polling.js') }}"></script>
@stop
