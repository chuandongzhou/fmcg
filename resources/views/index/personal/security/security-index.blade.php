@extends('index.menu-master')

@section('subtitle', '个人中心-安全设置')
@section('top-title')
    <a href="{{ url('personal/info') }}">个人中心</a> >
    <span class="second-level">安全设置</span>
@stop
@include('includes.password')
@section('right')
    <div class="row margin-clear">
        <div class="col-sm-12 security-setting-wrap">
            <div class="item">账户上次登录时间 : {{ $user->last_login_at }}</div>
            <div class="item">
                密保手机 : {{  substr_replace($user->backup_mobile,'****',3,4) }} <span class="prompt">(用于接收短信和修改密码使用)</span>
                <a href="{{ url('personal/security/validate-phone?type=backup-phone') }}"><span class="edit"><i class="iconfont icon-xiugai"></i>修改</span></a>
            </div>
            <div class="item">
                密码 <span class="prompt">(用于登录和修改密码使用，请妥善保管)</span>
                <a data-target="#passwordModal" data-toggle="modal"><span class="edit"><i class="iconfont icon-xiugai"></i>修改</span></a>
            </div>
        </div>
    </div>
    @parent
@stop
