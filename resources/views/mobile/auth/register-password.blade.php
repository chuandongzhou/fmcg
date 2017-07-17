@extends('mobile.master')

@section('subtitle', '设置密码');

@section('body')
    @parent
    <div class="fixed-header fixed-item reg-fixed-item">
        注册
    </div>
    <form class="mobile-ajax-form" action="{{ url('api/v1/auth/set-password') }}" method="post"
          data-done-url="{{ url('auth/register-shop') }}">
        <div class="container reg-container">
            <div class="row  margin-clear">
                <ul class="reg-step">
                    <li class="step-item on">创建账户 ></li>
                    <li class="step-item on">设置密码 ></li>
                    <li class="step-item">填写商户信息 ></li>
                    <li class="step-item">注册成功</li>
                </ul>
            </div>
            <div class="row enter-wrap">
                <div class="col-xs-12 ">
                    <div class="tips">请为账号<b>{{ array_get($user, 'user_name') }}</b>设置密码</div>
                    <div class="enter-item">
                        <input type="password" name="password" placeholder="请输入密码"/>
                    </div>
                    <div class="enter-item">
                        <input type="password" name="password_confirmation" placeholder="确认密码"/>
                    </div>
                    <div class="hidden">
                        <input type="hidden" name="user_name" value="{{ $user['user_name'] }}"/>
                        <input type="hidden" name="type" value="{{ $user['type'] }}"/>
                        <input type="hidden" name="backup_mobile" value="{{ $user['backup_mobile'] }}"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="fixed-footer fixed-item reg-fixed-item">
            <button type="submit" class="prev-next"> 下一步</button>
        </div>
    </form>
@stop

