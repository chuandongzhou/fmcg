@extends('auth.master')

@section('title' , '注册成功 | 订百达')

@section('body')
<nav class="navbar login-nav">
    <div class="container padding-clear register">
        <ul class="nav-title text-center">
            <li><a href="http://192.168.2.66/fmcg/public/auth/login">首页</a></li>
            <li><a class="logo-icon" href="#"><img src="http://192.168.2.66/fmcg/public/images/logo.png" alt="logo"/></a></li>
            <li><a href="#">关于我们</a></li>
        </ul>
    </div>
</nav>
<hr class="register-hr">
<div class="container">
    <div class="row vertical-offset-100">
        <div class="col-md-6 col-md-offset-3 col-sm-10 col-sm-offset-2">
            <h2 class="passed-ok"><i class="fa fa-check-circle"></i>注册成功！</h2>
            <p class="passed-item">我们将在1~3个工作日完成审核，完成后将会以短信通知您！</p>
            <p>前往<a href="{{ url('/') }}">订百达首页</a>了解更多信息</p>
        </div>
    </div>
</div>

@stop
