@extends('master')

@section('title' , '子帐号登录|订百达 - 订货首选')

@section('js-lib')
    <script src="{{ asset('js/index.js') }}"></script>
@stop

@section('css')
    <link href="{{ asset('css/index.css?v=1.0.0') }}" rel="stylesheet">
    <style>
        html, body {
            background-color: #f1f1f1;
            margin-bottom: 0 !important;
        }
    </style>
@stop

@section('body')
    <div class="container-fluid login-container">
        <div class="row">
            <div class="col-xs-12 text-center login-title">
                <h3>登录系统</h3>
            </div>
        </div>
        <div class="row content-panel">
            <div class="col-xs-8 col-xs-offset-2 login-content">
                <div class="col-xs-12 logo text-right">
                    <img src="{{ asset('images/logo.png') }}">
                </div>
                <div class="col-xs-7 login-left-img">
                    <img src="{{ asset('images/login-banner.jpg') }}">
                </div>
                <div class="col-xs-5 login-right-content">
                    <div class="row login-wrap">
                        <form class="ajax-form" method="post" action="{{ url('api/v1/child-user/auth/login') }}"
                              accept-charset="UTF-8" data-help-class="error-msg text-center"
                              data-done-url="{{ url('child-user/info') }}">
                            <div class="col-xs-12 padding-clear">
                                <span class="role-title"></span>
                            </div>
                            <div class="col-xs-12 padding-clear item text-center">
                                <div class="enter-item form-group">
                                    <span class="icon icon-name"></span>
                                    <span class="line"></span>
                                    <input type="text" name="account" class="user-name" placeholder="用户名">
                                </div>
                                <div class="enter-item form-group">
                                    <span class="icon icon-password"></span>
                                    <span class="line"></span>
                                    <input type="password" class="password" placeholder="密码" name="password">
                                </div>
                                <span class="triangle-left"></span>
                                <span class="triangle-right"></span>
                            </div>

                            <div class="col-xs-12 btn-item text-center">
                                <button type="submit"
                                        class="login-btn btn btn-warning no-prompt geetest-btn">登录
                                </button>
                                <div id="mask"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xs-6 col-xs-offset-3 change-role-options">

            </div>
        </div>
    </div>
@stop
@section('footer')
    @include('includes.footer', ['class' => 'register-footer'])
@stop