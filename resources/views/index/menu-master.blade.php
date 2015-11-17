@extends('index.index-control')
@section('container')
    <div class="container public-personal contents">
        <div class="row">
            @if(auth()->user()->type > cons('user.type.retailer'))
                <div class="col-sm-2 menu">
                    <a class="go-back" href="{{ url('shop/' . auth()->user()->shop->id) }}"><i
                                class="fa fa-angle-left"></i> 返回首页</a>
                    <ul class="menu-list dealer-menu-list">

                        <li><a href="{{ url('my-goods') }}" class="{{ path_active(['my-goods' ,'my-goods/*']) }}"><i
                                        class="fa fa-shopping-cart"></i> 我的商品</a></li>
                        {{--TODO:批发商的订单统计需要分角色--}}
                        @if(auth()->user()->type == cons('user.type.wholesaler'))
                            <li>
                                <a href="javascript:void(0)"
                                   class="list-item">
                                    <i class="fa fa-edit"></i> 订单管理</a>
                                <ul class="menu-wrap" {!!  request()->is('order-buy', 'order-sell') ? 'style="display:block"' : '' !!}>
                                    <li>
                                        <a href="{{ url('order-sell') }}" class=" {{ path_active('order-sell') }}">
                                            <span></span>终端商</a>
                                    </li>
                                    <li>
                                        <a href="{{ url('order-buy') }}" class="{{ path_active('order-buy') }}">
                                            <span class=""></span>供应商
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0)" class="list-item">
                                    <i class="fa fa-file-text-o"></i> 订单统计
                                </a>
                                <ul class="menu-wrap" {!!  request()->is('order/statistics') ? 'style="display:block"' : '' !!}>
                                    <li>
                                        <a class="{{ request()->input('obj_type') == 1 ? 'active' : '' }}"
                                           href="{{ url('order/statistics?obj_type=1&pay_type=1') }}">
                                            终端商
                                        </a>
                                    </li>
                                    <li>
                                        <a class="{{ request()->input('obj_type') == 3 ? 'active' : '' }}"
                                           href="{{ url('order/statistics?obj_type=3&pay_type=1') }}">
                                            供应商
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0)" class="list-item "><i
                                            class="fa fa-star-o"></i> 我的收藏</a>
                                <ul class="menu-wrap" {!!  request()->is('like/*') ? 'style="display:block"' : '' !!}>
                                    <li><a class="{{ path_active('like/shops') }}" href="{{ url('like/shops') }}">店铺收藏</a></li>
                                    <li><a class="{{ path_active('like/goods') }}" href="{{ url('like/goods') }}">商品收藏</a></li>
                                </ul>
                            </li>
                        @else
                            <li><a href="{{ url('order-sell') }}" class="{{ path_active('order-sell*') }}"><i
                                            class="fa fa-edit"></i> 订单管理</a></li>
                            <li><a href="{{ url('order/statistics') }}"
                                   class="{{ path_active('order/statistics*') }}"><i
                                        class="fa fa-file-text-o"></i> 订单统计</a></li>
                        @endif
                        <li><a href="{{ url('personal/shop') }}" class="{{ path_active('personal/*') }}"><i
                                        class="fa fa-heart-o"></i> 个人中心</a></li>
                    </ul>
                </div>
            @else
                <div class="col-sm-2 menu">
                    <ul class="name" href="#">
                        <li><img class="avatar"
                                 src="{{ auth()->user()->shop->logo ? auth()->user()->shop->logo->url : '' }}"></li>
                        <li>{{ auth()->user()->shop->name }}</li>
                    </ul>
                    <ul class="menu-list dealer-menu-list">
                        <li>
                            <a href="javascript:void(0)" class="list-item {{ path_active('like/*') }}"><i
                                        class="fa fa-star-o"></i> 我的收藏</a>
                            <ul class="menu-wrap">
                                <li><a href="{{ url('like/shops') }}">店铺收藏</a></li>
                                <li><a href="{{ url('like/goods') }}">商品收藏</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="{{ url('order-buy') }}" class="list-item {{ path_active('order-buy') }}"><i
                                        class="fa fa-file-text-o"></i>
                                订单管理</a>
                        </li>
                        <li><a href="{{ url('order-buy/statistics') }}"
                               class="list-item {{ path_active(['order-buy/statistics','order/statistics*']) }}"><i
                                        class="fa fa-file-o"></i>
                                订单统计</a></li>
                        <li><a href="{{ url('personal/shop') }}" class="list-item {{ path_active('personal/*') }}"><i
                                        class="fa fa-heart-o"></i>
                                个人中心</a></li>
                    </ul>
                </div>
            @endif
            <div class="col-sm-10">
                @yield('right')
            </div>
        </div>
    </div>

@stop
