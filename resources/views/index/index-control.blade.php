@extends('master')



@section('title')@yield('subtitle') | 订百达@stop



@section('css')
    <link href="{{ asset('css/index.css?v=1.0.0') }}" rel="stylesheet">
@stop



@section('header')
    <nav class="navbar personal-header">
        <div class="container" >
            <div class="navbar-header" >
                <button type="button" class="navbar-toggle collapsed navbar-button" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    </button>
                        <a class="logo-img"><img src="{{ asset('images/personal-logo.png') }}" ></a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav items-item">
                    @if($user->type < cons('user.type.wholesaler'))
                        <li class="item"><a href="{{ url('/') }}">首页</a></li>
                        <li class="item"><a href="{{ url('shop?type=wholesaler') }}">批发商</a></li>
                        <li class="item"><a href="{{ url('shop?type=supplier') }}">供应商</a></li>
                    @else
                        {{--<li class="item">--}}
                            {{--<a href="{{ url('shop/' . $user->shop->id) }}">--}}
                                {{--<i class="fa fa-angle-left"></i>--}}
                                {{--商店首页--}}
                            {{--</a>--}}
                        {{--</li>--}}
                    @endif
                </ul>
                <ul class="nav navbar-right items-item" >
                    <li class="item"><a href="{{ url('auth/logout') }}"><i class="fa fa-sign-out"></i>退出</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <hr class="personal-hr" />

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
            联系地址：成都市高新区天府大道中段1388号美年广场A座1248号&nbsp;&nbsp;联系方式 : 13829262065(霍女士)
        </div>
    </footer>
@stop

@section('js')
    <script src="{{ asset('js/index.js?v=1.0.0') }}"></script>
    <script src="{{ asset('js/ajax-polling.js') }}"></script>
@stop
