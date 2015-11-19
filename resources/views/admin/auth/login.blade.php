@extends('master')

@section('title', '后台 | 登录')

@section('css')
    <link href="{{ asset('css/admin.css?v=1.0.0') }}" rel="stylesheet">
@stop
@section('body')
    <form class="form-signin" method="post" action="{{ url('admin/auth/login') }}" accept-charset="UTF-8">
        <div class="container login">
            <section class="loginBox row-fluid">
               {{ csrf_field() }}

                <p class="input-group">
                    <input type="text" class="form-control input" name="account" placeholder="请输入登录名" required/>
                </p>

                <p class="input-group">
                    <input type="password" class="form-control input" name="password" placeholder="请输入密码" required/>
                </p>

                <p class="input-group">
                    <button type="submit" class="btn btn-primary submit">登录</button>
                </p>
            </section>
        </div>
    </form>
@stop
