@extends('master')

@section('css')
    <link href="{{ asset('css/index.css?v=1.0.0') }}" rel="stylesheet">
@stop
@section('body')
    <nav class="navbar login-nav">
        <div class="container padding-clear">
            <ul class="nav-title text-center">
                <li><a href="#">首页</a></li>
                <li><a class="logo-icon" href="#"><img src="{{ asset('images/logo.png') }}" alt="logo" /></a></li>
                <li><a href="#">关于我们</a></li>
            </ul>
        </div>
    </nav>
    <div class="container-fluid login-content">
        <div id="myCarousel" class="row carousel slide login-banner-slide">
            <ol class="carousel-indicators">
                <li data-target="#myCarousel" data-slide-to="0" class="active">
                <li data-target="#myCarousel" data-slide-to="1">
                    {{--<li data-target="#myCarousel" data-slide-to="2">--}}
            </ol>
            <div class="carousel-inner">
                <div class="item active">
                    <img src="{{ asset('images/banner.jpg') }}" alt="First slide">
                </div>
                <div class="item">
                    <img src="{{ asset('images/banner.jpg') }}" alt="Second slide">
                </div>
                {{--<div class="item">--}}
                {{--<img src="{{ asset('images/banner.jpg') }}" alt="Third slide">--}}
                {{--</div>--}}
            </div>
        </div>
        <div class="row login-wrap">
            <form class="ajax-form" method="post" action="{{ url('api/v1/auth/login') }}" accept-charset="UTF-8">
                <div class="col-sm-12 item">
                    <h1 class="pull-left login"><a>登录</a></h1>
                    <a class="pull-right reg" href="{{ url('auth/register') }}">注册</a>
                </div>
                <div class="col-sm-12 item">
                    <div class="form-group">
                        <div class="enter-item">
                            <label class="icon icon-name"></label>
                            <input type="text" name="account" placeholder="用户名">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="enter-item">
                            <label class="icon icon-password"></label>
                            <input type="password" name="password" placeholder="密码">
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 item clearfix">
                    {{--<select name="type" class="role pull-left">--}}
                    {{--@foreach(cons('user.type') as $val)--}}
                    {{--<option value="{{ $val }}">{{ cons()->valueLang('user.type' , $val) }}</option>--}}
                    {{--@endforeach--}}
                    {{--</select>--}}
                    <input type="hidden" name="type"
                           value="{{ array_get(cons('user.type') , \Request::input('type') ? \Request::input('type') : 'retailer' , head(cons('user.type'))) }}"/>
                    <button type="submit" class="btn btn-primary login-btn" data-loading-text="登录中..."
                            data-done-text="登录成功" data-fail-text="登录失败">登录
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop
@section('footer')
    <footer class="panel-footer login-footer">
        <div class="container text-center content">
            Copyright2015成都订百达科技有限公司<br />
            联系地址：成都市高新区天府大道中段1388号美年广场A座1248号&nbsp;&nbsp;联系方式:13829262065(霍女士)
        </div>
    </footer>
    @parent
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $(document).ready(function () {
            $('.carousel').carousel({
                interval: 2000
            })
        });
    </script>
@stop