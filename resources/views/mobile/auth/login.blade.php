@extends('mobile.master')

@section('subtitle', '登录')

@section('body')
    <div class="container">
        <div class="row">
            <div class="col-xs-4 col-xs-offset-4">
                <div class="login-logo">
                    <img src="{{ asset('images/mobile-images/logo.png') }}">
                </div>
            </div>
        </div>
        <div class="row enter-wrap login-content">
            <form class="mobile-ajax-form" action="{{ url('api/v1/auth/login') }}" data-done-text="登录成功" data-done-url="{{ url('/') }}" method="post">
                <div class="col-xs-12 ">
                    <div class="enter-item">
                        <div class="icon-panel role-panel">
                            <a href="javascript:;" class="select-role">
                                <img class="active" src="{{ asset('images/mobile-images/role_1.png') }}">
                                <img src="{{ asset('images/mobile-images/role_2.png') }}">
                                <img src="{{ asset('images/mobile-images/role_3.png') }}">
                            </a>
                            <span class="triangle">
                                <input type="hidden" name="type" value="{{ cons('user.type.retailer') }}"/>
                            </span>
                        </div>
                        <input type="text" name="account" placeholder="请输入账户名"/>
                    </div>
                    <div class="enter-item">
                        <div class="icon-panel">
                            <i class="iconfont icon-mima"></i>
                        </div>
                        <input type="password" name="password" placeholder="请输入密码"/>
                    </div>
                </div>
                <div class="col-xs-12  submit-panel">
                    <button type="submit" class="btn btn-primary submit">登录</button>
                </div>
            </form>
            <div class="col-xs-12 footer-wrap">
                <a class="reg" href="{{ url('auth/register-account') }}">注册</a>
                <a class="forget-pwd" href="{{ url('auth/forget-password') }}">忘记密码</a>
            </div>
        </div>
    </div>
    @include('mobile.includes.role-select')
    @parent
@stop

@section('js')
    <script type="text/javascript">
        $(function () {
            roleSelect();
        })
    </script>
@stop