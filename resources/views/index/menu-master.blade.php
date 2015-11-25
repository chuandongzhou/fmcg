@extends('index.index-control')
@section('container')
    <div class="container public-personal contents">
        <div class="row">
            @if($user->type > cons('user.type.retailer'))
                <div class="col-sm-2 menu">
                    <ul class="name" href="#">
                        <li><a href="{{ url('personal/info') }}">{{ $user->shop->name }}</a></li>
                    </ul>
                    <ul class="menu-list dealer-menu-list">
                        <li>
                            <a href="javascript:void(0)" class="list-item">
                                <i class="fa fa-heart-o"></i> 商品管理
                            </a>
                            <ul class="menu-wrap" {!!  request()->is('my-goods','my-goods/create') ? 'style="display:block"' : '' !!}>
                                <li>
                                    <a class="{{ path_active('my-goods') }}" href="{{ url('my-goods') }}">
                                        我的商品
                                    </a>
                                </li>
                                <li>
                                    <a class="{{ path_active('my-goods/create') }}" href="{{ url('my-goods/create') }}">
                                        新增商品
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript:void(0)" class="list-item">
                                <i class="fa fa-edit"></i> 订单管理
                            </a>
                            <ul class="menu-wrap" {!!  request()->is('order-sell', 'order-sell/*') || request()->input('obj_type') == 1 ? 'style="display:block"' : '' !!}>
                                <li>
                                    <a href="{{ url('order-sell') }}"
                                       class=" {{ path_active(['order-sell' ,'order-sell/*' ]) }}">
                                        <span></span>订单列表</a>
                                </li>
                                <li>
                                    <a class="{{ request()->input('obj_type') == 1 ? 'active' : '' }}"
                                       href="{{ url('order/statistics?obj_type=1&pay_type=1') }}">
                                        订单统计
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @if($user->type == cons('user.type.wholesaler'))
                            <li>
                                <a href="javascript:void(0)" class="list-item">
                                    <i class="fa fa-file-text-o"></i> 进货管理
                                </a>
                                <ul class="menu-wrap" {!!  request()->is('order-buy', 'order-buy/*') || request()->input('obj_type') == 3 ? 'style="display:block"' : '' !!}>
                                    <li>
                                        <a href="{{ url('order-buy') }}"
                                           class="{{ path_active(['order-buy' , 'order-buy/*']) }}">
                                            <span class=""></span>订单列表
                                        </a>
                                    </li>
                                    <li>
                                        <a class="{{ request()->input('obj_type') == 3 ? 'active' : '' }}"
                                           href="{{ url('order/statistics?obj_type=3&pay_type=1') }}">
                                            订单统计
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0)" class="list-item ">
                                    <i class="fa fa-star-o"></i> 我的收藏
                                </a>
                                <ul class="menu-wrap" {!!  request()->is('like/*') ? 'style="display:block"' : '' !!}>
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
                                <a href="{{ url('/') }}" target="_blank">
                                    <i class="fa fa-shopping-cart"></i> 进货中心
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="javascript:void(0)" class="list-item">
                                <i class="fa fa-smile-o"></i> 个人中心
                            </a>
                            <ul class="menu-wrap" {!!  request()->is('personal/shop' ,'personal/shipping-address','personal/delivery-man','personal/password') ? 'style="display:block"' : '' !!}>
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

                        <li>
                            <a href="javascript:void(0)" class="list-item">
                                <i class="fa fa-money"></i> 财务管理
                            </a>
                            <ul class="menu-wrap" {!!  request()->is('personal/balance','personal/bank') ? 'style="display:block"' : '' !!}>
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


                    </ul>
                </div>
            @else
                <div class="col-sm-2 menu">
                    <ul class="name" href="#">
                        <li><a href="{{ url('personal/info') }}">{{ $user->shop->name }}</a></li>
                    </ul>
                    <ul class="menu-list dealer-menu-list">
                        <li>
                            <a href="javascript:void(0)" class="list-item ">
                                <i class="fa fa-star-o"></i> 我的收藏
                            </a>
                            <ul class="menu-wrap"  style="display:block">
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
                                <i class="fa fa-file-text-o"></i> 订单管理
                            </a>
                            <ul class="menu-wrap"  style="display:block">
                                <li>
                                    <a href="{{ url('order-buy') }}"
                                       class="{{ path_active(['order-buy' , 'order-buy/*']) }}">
                                        <span class=""></span>订单列表
                                    </a>
                                </li>
                                <li>
                                    <a class="{{ request()->input('obj_type') == 3 ? 'active' : '' }}"
                                       href="{{ url('order/statistics?obj_type=3&pay_type=1') }}">
                                        订单统计
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="list-item">
                                <i class="fa fa-smile-o"></i> 个人中心
                            </a>
                            <ul class="menu-wrap" style="display:block">
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
                        </li>
                    </ul>
                </div>
            @endif
            <div class="col-sm-10">
                @yield('right')
            </div>
        </div>
    </div>

@stop
