@extends('master')

@section('css')
    <link href="{{ asset('css/admin.css?v=1.0.0') }}" rel="stylesheet">
@stop
@section('body')
    <form class="ajax-form" method="post" action="{{ url('api/v1/auth/login') }}" accept-charset="UTF-8">
        <div class="container login">
            <section class="loginBox row-fluid">
                {{--<p class="input-group logo">--}}
                    {{--<img src="imgs/logo.png" title="" alt=""/>--}}
                {{--</p>--}}

                <p class="input-group">
                    <input type="text" class="form-control input" name="username" placeholder="请输入登录名" required/>
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
