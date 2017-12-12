@extends('mobile.master')

@section('subtitle', '登录')

@section('body')
    <div class="fixed-header fixed-item ">
        <div class="row nav-top white-bg orders-details-header">
            <div class="col-xs-2 edit-btn pd-clear">
                <a class="iconfont icon-fanhui2 go-back"></a>
            </div>
            <div class="col-xs-10 color-black">绑定账号</div>
        </div>
    </div>
    <form class="mobile-ajax-form" action="{{ url('api/v1/auth/login') }}" data-done-text="登录成功"
          data-done-url="{{ url('/') }}" method="post">
        <div class="container reg-container">
            <div class="row white-bg">
                <div class="col-xs-10 col-xs-push-1 bind-accout-msg">
                    <img class="pull-left" src="{{ $token['avatar'] }}" alt=""/>
                    <div class="pull-left msg-txt">Hi,{{ $token['nickname'] }} 欢迎来到订百达，完成绑定后可以微信账号一键登录哦~</div>
                </div>
            </div>
            <div class="row enter-wrap ">
                <div class="col-xs-12 m15">
                    <div class="enter-item role-enter">
                        <div class="icon-panel role-panel">
                            <a href="javascript:;" class="select-role">
                                <img class="active" src="{{ asset('images/mobile-images/role_1.png') }}">
                                <img src="{{ asset('images/mobile-images/role_2.png') }}">
                            </a>
                            <span class="triangle">
                                <input type="hidden" name="type" value="{{ cons('user.type.retailer') }}"/>
                                <input type="hidden" name="token[token]" value="{{ $token['token'] }}">
                                <input type="hidden" name="token[type]" value="{{ $token['type'] }}">
                            </span>
                        </div>
                        <input type="text" name="account" placeholder="请输入账户名"/>
                    </div>
                    <div class="enter-item">
                        <input type="password" name="password" placeholder="请输入密码"/>
                    </div>
                </div>
                <div class="col-xs-12 footer-wrap bind">
                    <a href="{{ url('weixin-auth/reg-socialite') }}" class="reg">没有账户,去注册</a>
                    {{--<a class="forget-pwd">忘记登录密码</a>--}}
                </div>
            </div>
        </div>
        <div class="fixed-footer fixed-item reg-fixed-item">
            <button type="submit" class="prev-next">立即绑定</button>
        </div>
    </form>

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