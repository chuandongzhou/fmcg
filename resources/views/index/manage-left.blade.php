@extends('index.master')

@section('container')
    <div class="container my-goods">
        <div class="row">
            <div class="col-sm-2 menu">
                <a class="go-back" href="#">< 返回首页</a>
                <ul class="menu-list">
                    <li><a href="#"><span class=""></span>订单管理</a></li>
                    <li><a href="#">我的商品</a></li>
                    <li><a href="#">订单统计</a></li>
                    <li><a href="#">个人中心</a></li>
                </ul>
            </div>
            @yield('right')
        </div>
    </div>
@stop
