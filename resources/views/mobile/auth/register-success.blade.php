@extends('master')

@section('title')@yield('subtitle') | 订百达 - 订货首选@stop

@section('meta')
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
@stop

@section('body')
    <div class="fixed-header fixed-item">
        注册
    </div>
    <div class="container reg-container">
        <div class="row  margin-clear">
            <ul class="reg-step">
                <li class="step-item on">创建账户 ></li>
                <li class="step-item on">设置密码 ></li>
                <li class="step-item on">填写商户信息 ></li>
                <li class="step-item on">注册成功</li>
            </ul>
        </div>
        <div class="row reg-finally">
            <h3>恭喜你注册成功</h3>
            @if($userType == cons('user.type.retailer'))
                <p>您的账号<b>{{ $userName }}</b></p>
                <p>为了防止账号被盗请妥善保管好账号密码.</p>
            @else
                <p>您的账号<b>{{ $userName }}</b>正在审核中...</p>
                <p>平台审核通过后会以短信的方式通知您</p>
                <p>请注意查收短信</p>
            @endif


        </div>
    </div>
    <div class="fixed-footer fixed-item">
        <button onclick="window.location.href='{{ url('auth/login') }}'">去登录</button>
    </div>
@stop
