@extends('index.index-master')

@section('container')
    <div class="container public-personal">
        <div class="row">
            @if(auth()->user()->type > cons('user.type.retailer'))
                <div class="col-sm-2 menu">
                    <a class="go-back" href="#">< 返回首页</a>
                    <ul class="menu-list">
                        <li><a href="#"><span class=""></span>订单管理</a></li>
                        <li><a href="#">我的商品</a></li>
                        {{--TODO:批发商的订单统计需要分角色--}}
                        @if(auth()->user()->type == cons('user.type.wholesaler'))
                            <li><a href="{{ url('order/statistics?obj_type=1&pay_type=1') }}">终端商订单统计</a></li>
                            <li><a href="{{ url('order/statistics?obj_type=3&pay_type=1') }}">供应商订单统计</a></li>
                        @else
                            <li><a href="{{ url('order/statistics') }}">订单统计</a></li>
                        @endif
                        <li><a href="{{ url('personal/shop') }}">个人中心</a></li>
                    </ul>
                </div>
            @else
                <div class="col-sm-2 menu">
                    <ul class="name" href="#">
                        <li><img class="avatar" src="{{ auth()->user()->shop->logo->url }}"></li>
                        <li>终端商名称</li>
                    </ul>
                    <ul class="menu-list dealer-menu-list">
                        <li>
                            <a href="#" class="list-item"><i class="fa fa-star-o"></i> 我的收藏</a>
                            <ul class="menu-wrap">
                                <li><a href="{{ url('like/shops') }}">店铺收藏</a></li>
                                <li><a href="{{ url('like/goods') }}">商品收藏</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="{{ url('order-buy') }}" class="list-item"><i class="fa fa-file-text-o"></i>
                                我的订单</a>
                        </li>
                        <li><a href="{{ url('order-buy/statistics') }}" class="list-item active"><i
                                        class="fa fa-file-o"></i>
                                统计报表</a></li>
                        <li><a href="{{ url('personal/shop') }}" class="list-item"><i class="fa fa-heart-o"></i> 个人中心</a></li>
                    </ul>
                </div>
            @endif
            <div class="col-sm-10">
                @yield('right')
            </div>
        </div>
    </div>

@stop
