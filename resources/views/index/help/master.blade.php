@extends('index.index-master')

@section('container')
    <div class="container container-wrap">
        <div class="row">
            <div class="col-sm-2">
                <ul class="help-menu-list">
                    <li>
                        <span class="title">购物指南</span>
                        <ul class="menu-wrap">
                            <li class="{{ $id == 1?'active' : '' }}"><a href="{{ url('help?id=1') }}">购物流程</a></li>
                            <li class="{{ $id == 2?'active' : '' }}"><a href="{{ url('help?id=2') }}">订单查询</a></li>
                            <li class="{{ $id == 3?'active' : '' }}"><a href="{{ url('help?id=3') }}">商品收藏</a></li>
                            <li class="{{ $id == 4?'active' : '' }}"><a href="{{ url('help?id=4') }}">商铺收藏</a></li>
                            <li class="{{ $id == 5?'active' : '' }}"><a href="{{ url('help?id=5') }}">意见反馈</a></li>
                        </ul>
                    </li>
                    <li>
                        <span class="title">支付方式</span>
                        <ul class="menu-wrap">
                            <li class="{{ $id == 6?'active' : '' }}"><a href="{{ url('help?id=6') }}">在线支付</a></li>
                            <li class="{{ $id == 7?'active' : '' }}"><a href="{{ url('help?id=7') }}">货到付款</a></li>
                            <li class="{{ $id == 8?'active' : '' }}"><a href="{{ url('help?id=8') }}">申请退款</a></li>
                            <li class="{{ $id == 9?'active' : '' }}"><a href="{{ url('help?id=9') }}">取消订单</a></li>
                        </ul>
                    </li>
                    <li>
                        <span class="title">商品上架</span>
                        <ul class="menu-wrap">
                            <li class="{{ $id == 10?'active' : '' }}"><a href="{{ url('help?id=10') }}">批发商发布商品</a>
                            </li>
                            <li class="{{ $id == 11?'active' : '' }}"><a href="{{ url('help?id=11') }}">供应商发布商品</a>
                            </li>
                            <li class="{{ $id == 12?'active' : '' }}"><a href="{{ url('help?id=12') }}">下架商品</a>
                            </li>
                            <li class="{{ $id == 13?'active' : '' }}"><a href="{{ url('help?id=13') }}">商品图片</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <span class="title">常见问题</span>
                        <ul class="menu-wrap">
                            <li class="{{ $id == 14?'active' : '' }}"><a href="{{ url('help?id=14') }}">常见问题</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="col-sm-10 right-content-wrap">
                @yield('content')
            </div>
        </div>
    </div>
@stop
