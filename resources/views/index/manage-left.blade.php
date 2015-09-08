@extends('index.master')

@section('container')
    <div class="container my-goods">
        <div class="row">
            <div class="col-sm-2 menu">
                <a class="go-back" href="#">< 返回首页</a>
                <ul class="menu-list">
                    <li><a href="#"><span class=""></span>订单管理</a></li>
                    <li><a href="#">我的商品</a></li>
                    {{--TODO:批发商的订单统计需要分角色--}}
                    @if(session('type') == cons('user.type.wholesaler'))
                        <li><a href="{{ url('order-sell/statistics?obj_type=1&pay_type=1') }}">终端商订单统计</a></li>
                        <li><a href="{{ url('order-buy/statistics?obj_type=3&pay_type=1') }}">供应商订单统计</a></li>
                    @else
                    <li><a href="{{ url('order/statistics') }}">订单统计</a></li>
                    @endif
                    <li><a href="#">个人中心</a></li>
                </ul>
            </div>
            @yield('right')
        </div>
    </div>
@stop
