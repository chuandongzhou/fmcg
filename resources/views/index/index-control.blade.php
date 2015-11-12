@extends('master')



@section('title')@yield('subtitle') | 订百达@stop



@section('css')
    <link href="{{ asset('css/index.css?v=1.0.0') }}" rel="stylesheet">
@stop



@section('header')
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
                    <li class="active"><a class="list-name" href="{{ url('/') }}">首页</a></li>
                    @if(auth()->user()->type <= cons('user.type.wholesaler'))
                        <li><a class="list-name" href="{{ url('shop') }}">商家</a></li>
                    @endif
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="right"><a href="{{ url('auth/logout') }}"><i class="fa fa-sign-out"></i> 退出</a></li>
                </ul>
            </div>
        </div>
    </nav>

@stop

@section('body')
    @yield('container')
    <a href="" id="alert-div"
       style="width: 300px;height:100px;background-color:rgb(76,185,254);position:fixed;right:0;bottom:0;text-align:center;line-height:100px;display:none;z-index: 99;color:black; font-size: 20px;">
        你有新消息了
    </a>
@stop



@section('footer')
    <footer class="panel-footer">
        <div class="container text-center content">
            Copyright2015成都订百达科技有限公司<br />
            联系地址：成都市高新区天府大道中段1388号美年广场A座1248号&nbsp;&nbsp;联系方式:13829262065(霍女士)
        </div>
    </footer>
@stop

@section('js')
    <script src="{{ asset('js/index.js?v=1.0.0') }}"></script>
    @include('includes.ajaxPolling')
@stop
