@extends('index.manage-master')

@section('header')
    @parent
    <nav class="navbar personal-header">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed navbar-button" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="logo-img"><img src="{{ asset('images/personal-logo.png') }}"></a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav items-item">
                    @if($user->type < cons('user.type.wholesaler'))
                        <li class="item"><a href="{{ url('/') }}">首页</a></li>
                        <li class="item"><a href="{{ url('shop?type=wholesaler') }}">批发商</a></li>
                        <li class="item"><a href="{{ url('shop?type=supplier') }}">供应商</a></li>
                    @else
                        {{--<li class="item">--}}
                        {{--<a href="{{ url('shop/' . $user->shop->id) }}">--}}
                        {{--<i class="fa fa-angle-left"></i>--}}
                        {{--商店首页--}}
                        {{--</a>--}}
                        {{--</li>--}}
                    @endif
                </ul>
            </div>
        </div>
    </nav>
    <hr class="personal-hr"/>
@stop
@section('container')
    <div class="container public-personal contents">
        <div class="row">
            @if($user->type > cons('user.type.retailer'))
                <div class="col-sm-2 col-xs-3 menu">
                    <ul class="name">
                        <li><a href="{{ url('personal/info') }}">{{ $user->shop->name }}</a></li>
                    </ul>
                    <ul class="menu-list dealer-menu-list">
                        {{--个人中心--}}
                        <li>
                            <a href="javascript:void(0)" class="list-item">
                                <i class="fa fa-smile-o {!! path_active(['personal/shop' ,'personal/shipping-address','personal/delivery-man','personal/password']) !!}"></i>
                                个人中心
                            </a>
                            <ul class="menu-wrap">
                                <li>
                                    <a class="{{ path_active('personal/shop') }}" href="{{ url('personal/shop') }}">
                                        店铺信息
                                    </a>
                                </li>
                                @if ($user->type != cons('user.type.supplier'))
                                    <li>
                                        <a class="{{ path_active('personal/shipping-address') }}"
                                           href="{{ url('personal/shipping-address') }}">
                                            收货地址
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <a class="{{ path_active('personal/delivery-man') }}"
                                       href="{{ url('personal/delivery-man') }}">
                                        配送人员
                                    </a>
                                </li>
                                <li>
                                    <a class="{{ path_active('personal/password') }}"
                                       href="{{ url('personal/password') }}">
                                        修改密码
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @if($user->type == cons('user.type.wholesaler'))
                            {{--我的收藏--}}
                            <li>
                                <a href="javascript:void(0)" class="list-item ">
                                    <i class="fa fa-star-o {!!  path_active('like/*') !!}"></i> 我的收藏
                                </a>
                                <ul class="menu-wrap">
                                    <li>
                                        <a class="{{ path_active('like/shops') }}" href="{{ url('like/shops') }}">
                                            店铺收藏
                                        </a>
                                    </li>
                                    <li>
                                        <a class="{{ path_active('like/goods') }}" href="{{ url('like/goods') }}">
                                            商品收藏
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                        {{--商品管理--}}
                        <li>
                            <a href="javascript:void(0)" class="list-item">
                                <i class="fa fa-heart-o {!! path_active(['my-goods','my-goods/*']) !!}"></i> 商品管理
                            </a>
                            <ul class="menu-wrap">
                                <li>
                                    <a class="{{ path_active('my-goods') }}"
                                       href="{{ url('my-goods') }}">
                                        我的商品
                                    </a>
                                </li>
                                <li>
                                    <a class="{{ path_active('my-goods/create') }}" href="{{ url('my-goods/create') }}">
                                        新增商品
                                    </a>
                                </li>
                                <li>
                                    <a class="{{ path_active('my-goods/batch-create') }}"
                                       href="{{ url('my-goods/batch-create') }}">
                                        批量导入
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @if($user->type == cons('user.type.wholesaler'))
                            {{--进货中心--}}
                            <li>
                                <a class="list-item" href="{{ url('/') }}" target="_blank">
                                    <i class="fa fa-shopping-cart"></i> 进货中心
                                </a>
                            </li>
                            {{--进货管理--}}
                            <li>
                                <a href="javascript:void(0)" class="list-item">
                                    <i class="fa fa-file-text-o {!!  request()->is('order-buy', 'order-buy/*') || request()->input('obj_type') == 3 ? 'active' : '' !!}"></i>
                                    进货管理
                                </a>
                                <ul class="menu-wrap">
                                    <li>
                                        <a href="{{ url('order-buy') }}"
                                           class="{{ path_active(['order-buy' , 'order-buy/*']) }}">
                                            <span class=""></span>订单列表
                                        </a>
                                    </li>
                                    <li>
                                        <a class="{{ request()->input('obj_type') == 3 && request()->is('order/statistics') ? 'active' : '' }}"
                                           href="{{ url('order/statistics?obj_type=3') }}">
                                            订单统计
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                        {{--订单管理--}}
                        <li>
                            <a href="javascript:void(0)" class="list-item">
                                <i class="fa fa-edit {!!  request()->is('order-sell', 'order-sell/*') || (request()->is('order/statistics') && request()->input('obj_type') < 3) ? 'active' : '' !!}"></i>
                                订单管理
                            </a>
                            <ul class="menu-wrap">
                                <li>
                                    <a href="{{ url('order-sell') }}"
                                       class=" {{ path_active(['order-sell' ,'order-sell/*' ]) }}">
                                        <span></span>订单列表</a>
                                </li>
                                <li>
                                    <a class="{{  request()->is('order/statistics') && request()->input('obj_type') < 3 ? 'active' : '' }}"
                                       href="{{ url('order/statistics') }}">
                                        订单统计
                                    </a>
                                </li>
                            </ul>
                        </li>
                        {{--财务管理--}}
                        <li>
                            <a href="javascript:void(0)" class="list-item">
                                <i class="fa fa-money {!!  path_active(['personal/balance','personal/bank'] ) !!}"></i>
                                财务管理
                            </a>
                            <ul class="menu-wrap">
                                <li>
                                    <a class="{{ path_active('personal/balance') }}"
                                       href="{{ url('personal/balance') }}">
                                        账户余额
                                    </a>
                                </li>
                                <li>
                                    <a class="{{ path_active('personal/bank') }}" href="{{ url('personal/bank') }}">
                                        提现账号
                                    </a>
                                </li>
                            </ul>
                        </li>
                        {{--客户列表--}}
                        <li>
                            <a href="javascript:void(0)" class="list-item">
                                <i class="fa fa-users {!! path_active('personal/customer/*') !!}"></i> 客户列表
                            </a>
                            <ul class="menu-wrap" >
                                <li>
                                    <a class="{{ path_active('personal/customer/retailer') }}"
                                       href="{{ url('personal/customer/retailer') }}">
                                        终端客户
                                    </a>
                                </li>
                                @if ($user->type == cons('user.type.supplier'))
                                    <li>
                                        <a class="{{ path_active('personal/customer/wholesaler') }}"
                                           href="{{ url('personal/customer/wholesaler') }}">
                                            批发客户
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    </ul>
                </div>
            @else
                <div class="col-sm-2 col-xs-3 menu">
                    <ul class="name" href="#">
                        <li><a href="{{ url('personal/info') }}">{{ $user->shop->name }}</a></li>
                    </ul>
                    <ul class="menu-list dealer-menu-list">
                        <li>
                            <a href="javascript:void(0)" class="list-item">
                                <i class="fa fa-smile-o {!!  path_active(['personal/shop' ,'personal/shipping-address' ,'personal/password']) !!}"></i>
                                个人中心
                            </a>
                            <ul class="menu-wrap">
                                <li>
                                    <a class="{{ path_active('personal/shop') }}" href="{{ url('personal/shop') }}">
                                        店铺信息
                                    </a>
                                </li>
                                <li>
                                    <a class="{{ path_active('personal/shipping-address') }}"
                                       href="{{ url('personal/shipping-address') }}">
                                        收货地址
                                    </a>
                                </li>
                                <li>
                                    <a class="{{ path_active('personal/password') }}"
                                       href="{{ url('personal/password') }}">
                                        修改密码
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="list-item ">
                                <i class="fa fa-star-o {!!  path_active('like/*') !!}"></i> 我的收藏
                            </a>
                            <ul class="menu-wrap">
                                <li>
                                    <a class="{{ path_active('like/shops') }}" href="{{ url('like/shops') }}">
                                        店铺收藏
                                    </a>
                                </li>
                                <li>
                                    <a class="{{ path_active('like/goods') }}" href="{{ url('like/goods') }}">
                                        商品收藏
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="list-item">
                                <i class="fa fa-file-text-o {!!  request()->is('order-buy', 'order-buy/*') || (is_null(request()->input('obj_type')) && request()->is('order/statistics')) ||  request()->input('obj_type') > 1 ? 'active' : '' !!}"></i>
                                订单管理
                            </a>
                            <ul class="menu-wrap">
                                <li>
                                    <a href="{{ url('order-buy') }}"
                                       class="{{ path_active(['order-buy' , 'order-buy/*']) }}">
                                        <span class=""></span>订单列表
                                    </a>
                                </li>
                                <li>
                                    <a class="{{ (is_null(request()->input('obj_type')) && request()->is('order/statistics')) ||  request()->input('obj_type') > 1  ? 'active' : '' }}"
                                       href="{{ url('order/statistics') }}">
                                        订单统计
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            @endif
            <div class="col-sm-10  col-xs-9">
                @yield('right')
            </div>
        </div>
    </div>
@stop
