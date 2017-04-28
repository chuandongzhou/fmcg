@extends('index.manage-master')

@section('container')
    <div class="page-sidebar-wrapper">
        <!--左侧导航栏菜单-->
        <div class="page-sidebar navbar-collapse collapse">
            <ul class="page-sidebar-menu  page-header-fixed page-sidebar-menu-hover-submenu " data-keep-expanded="false"
                data-auto-scroll="true" data-slide-speed="200">
                <li class="nav-item start {!! path_active(['personal/shop','personal/security/*','personal/delivery-area*','personal/shipping-address*','personal/info']) !!}">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="fa fa-smile-o"></i>
                        <span class="title">个人中心</span>
                        <span class="selected"></span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item start {{ path_active('personal/info') }}">
                            <a href="{{ url('personal/info') }}" class="nav-link">
                                <span class="title">个人中心</span>
                            </a>
                        </li>
                        <li class="nav-item start {{ path_active('personal/shop') }}">
                            <a href="{{ url('personal/shop') }}" class="nav-link">
                                <span class="title">店铺信息</span>
                            </a>
                        </li>
                        @if ($user->type != cons('user.type.retailer'))
                            <li class="nav-item start {{ path_active(['personal/delivery-area*']) }}">
                                <a href="{{ url('personal/delivery-area') }}" class="nav-link ">
                                    <span class="title">配送区域</span>
                                </a>
                            </li>
                        @endif
                        @if ($user->type < cons('user.type.supplier'))
                            <li class="nav-item start {{ path_active(['personal/shipping-address*']) }}">
                                <a href="{{ url('personal/shipping-address') }}" class="nav-link ">
                                    <span class="title">收货地址</span>
                                </a>
                            </li>
                        @endif
                        <li class="nav-item start {{ path_active('personal/security/*') }}">
                            <a href="{{ url('personal/security/index') }}" class="nav-link ">
                                <span class="title">安全设置</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @if($user->type == cons('user.type.maker'))

                    <li class="nav-item  {!!  request()->is('inventory','inventory/*') ? 'active' : '' !!}">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="fa fa-edit "></i>
                            <span class="title">库存管理</span>
                            <span class="{!!  request()->is( 'inventory','inventory/*')  ? 'selected' : '' !!}"></span>
                            <span class="arrow"></span>
                        </a>
                        <ul class="sub-menu">

                            <li class="nav-item {{ path_active(['inventory']) }} ">
                                <a href="{{ url('inventory') }}" class="nav-link ">
                                    <span class="title">库存管理</span>
                                </a>
                            </li>
                            <li class="nav-item  {{ request()->is('inventory/in')? 'active' : '' }}">
                                <a href="{{ url('inventory/in') }}" class="nav-link ">
                                    <span class="title">入库</span>
                                </a>
                            </li>
                            <li class="nav-item  {{ path_active( 'inventory/out') }}">
                                <a href="{{ url('inventory/out') }}" class="nav-link ">
                                    <span class="title">出库</span>
                                </a>
                            </li>
                            <li class="nav-item  {{ path_active( 'inventory/out') }}">
                                <a href="{{ url('inventory/in-transit-goods') }}" class="nav-link ">
                                    <span class="title">在途商品</span>
                                </a>
                            </li>
                            <li class="nav-item  {{ request()->is('inventory/detail')? 'active' : '' }}">
                                <a href="{{ url('inventory/detail-list') }}" class="nav-link ">
                                    <span class="title">出入库明细</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item start  {!! path_active(['my-goods','my-goods/*']) !!}">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="fa fa-shopping-basket"></i>
                            <span class="title">商品管理</span>
                            <span class="{!! request()->is('my-goods','my-goods/*')?'selected':''  !!}"></span>
                            <span class="arrow open"></span>
                        </a>
                        <ul class="sub-menu">
                            <li class="nav-item  {{ path_active(['my-goods','my-goods/*/edit']) }} ">
                                <a href="{{ url('my-goods') }}" class="nav-link ">
                                    <span class="title">我的商品</span>
                                </a>
                            </li>
                            <li class="nav-item {{ path_active('my-goods/create') }} ">
                                <a href="{{ url('my-goods/create') }}" class="nav-link ">
                                    <span class="title">新增商品</span>
                                </a>
                            </li>
                            <li class="nav-item  {{ path_active('my-goods/batch-create') }}">
                                <a href="{{ url('my-goods/batch-create') }}" class="nav-link ">
                                    <span class="title">批量导入</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item  {!!  request()->is('order-sell*') || request()->is('order/statistics-of-sell') ? 'active' : '' !!}">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="fa fa-edit "></i>
                            <span class="title">订单管理</span>
                            <span class="{!!  request()->is( 'order-sell*') || request()->is('order/statistics-of-sell') ? 'selected' : '' !!}"></span>
                            <span class="arrow"></span>
                        </a>
                        <ul class="sub-menu">
                            <li class="nav-item {{ path_active(['order-sell' ,'order-sell/detail*' ]) }} ">
                                <a href="{{ url('order-sell') }}" class="nav-link ">
                                    <span class="title">订单列表</span>
                                </a>
                            </li>
                            <li class="nav-item  {{ path_active( 'order/statistics-of-sell') }}">
                                <a href="{{ url('order/statistics-of-sell') }}" class="nav-link ">
                                    <span class="title">订单统计</span>
                                </a>
                            </li>
                            <li class="nav-item  {{ request()->is('order-sell/templete')? 'active' : '' }}">
                                <a href="{{ url('order-sell/templete') }}" class="nav-link ">
                                    <span class="title">订单打印模版</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    @else
                    @if($user->type == cons('user.type.wholesaler'))
                        <li class="nav-item {!! request()->is('order-buy', 'order/statistics-of-buy') ? 'active' : '' !!} ">
                            <a href="javascript:;" class="nav-link nav-toggle">
                                <i class="fa fa-file-text-o "></i>
                                <span class="title">进货管理</span>
                                <span class="{!! request()->is('order-buy', 'order/statistics-of-buy')  ? 'selected' : '' !!}"></span>
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item  {{ path_active(['order-buy']) }}">
                                    <a href="{{ url('order-buy') }}" class="nav-link ">
                                        <span class="title">订单列表</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ path_active('order/statistics-of-buy') }} ">
                                    <a href="{{ url('order/statistics-of-buy') }}" class="nav-link ">
                                        <span class="title">订单统计</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    @if($user->type != cons('user.type.retailer'))
                        <li class="nav-item  {!!  request()->is('order-sell*') || request()->is('order/statistics-of-sell') ? 'active' : '' !!}">
                            <a href="javascript:;" class="nav-link nav-toggle">
                                <i class="fa fa-edit "></i>
                                <span class="title">订单管理</span>
                                <span class="{!!  request()->is( 'order-sell*') || request()->is('order/statistics-of-sell') ? 'selected' : '' !!}"></span>
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item {{ path_active(['order-sell' ,'order-sell/detail*' ]) }} ">
                                    <a href="{{ url('order-sell') }}" class="nav-link ">
                                        <span class="title">订单列表</span>
                                    </a>
                                </li>
                                <li class="nav-item  {{ path_active( 'order/statistics-of-sell') }}">
                                    <a href="{{ url('order/statistics-of-sell') }}" class="nav-link ">
                                        <span class="title">订单统计</span>
                                    </a>
                                </li>
                                <li class="nav-item  {{ request()->is('order-sell/templete')? 'active' : '' }}">
                                    <a href="{{ url('order-sell/templete') }}" class="nav-link ">
                                        <span class="title">订单打印模版</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item {!! path_active(['order-buy', 'order-buy/*', 'order/statistics']) !!}  ">
                            <a href="javascript:;" class="nav-link nav-toggle">
                                <i class="fa fa-edit "></i>
                                <span class="title">订单管理</span>
                                <span class="{!! request()->is('order-buy', 'order-buy/*', 'order/statistics')  ? 'selected' : '' !!} "></span>
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item  {{ path_active(['order-buy' , 'order-buy/*']) }}">
                                    <a href="{{ url('order-buy') }}" class="nav-link ">
                                        <span class="title">订单列表</span>
                                    </a>
                                </li>
                                <li class="nav-item  {{ request()->is('order/statistics-of-buy') ? 'active' : '' }}">
                                    <a href="{{ url('order/statistics-of-buy') }}" class="nav-link ">
                                        <span class="title">订单统计</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    @if($user->type != cons('user.type.retailer'))
                        <li class="nav-item start  {!! path_active(['my-goods','my-goods/*']) !!}">
                            <a href="javascript:;" class="nav-link nav-toggle">
                                <i class="fa fa-shopping-basket"></i>
                                <span class="title">商品管理</span>
                                <span class="{!! request()->is('my-goods','my-goods/*')?'selected':''  !!}"></span>
                                <span class="arrow open"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item  {{ path_active(['my-goods','my-goods/*/edit']) }} ">
                                    <a href="{{ url('my-goods') }}" class="nav-link ">
                                        <span class="title">我的商品</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ path_active('my-goods/create') }} ">
                                    <a href="{{ url('my-goods/create') }}" class="nav-link ">
                                        <span class="title">新增商品</span>
                                    </a>
                                </li>
                                <li class="nav-item  {{ path_active('my-goods/batch-create') }}">
                                    <a href="{{ url('my-goods/batch-create') }}" class="nav-link ">
                                        <span class="title">批量导入</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item {!!  path_active(['personal/coupon*','personal/finance/*','personal/bank*']) !!} ">
                            <a href="javascript:;" class="nav-link nav-toggle">
                                <i class="fa fa-money "></i>
                                <span class="title">财务管理</span>
                                <span class="{!! request()->is('personal/coupon*','personal/finance/*','personal/bank*')?'selected':'' !!}"></span>
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item start {{ path_active(['personal/coupon']) }}">
                                    <a href="{{ url('personal/coupon') }}" class="nav-link ">
                                        <span class="title">优惠券</span>
                                    </a>
                                </li>
                                <li class="nav-item  {{ path_active(['personal/finance/balance','personal/finance/withdraw']) }}">
                                    <a href="{{ url('personal/finance/balance') }}" class="nav-link ">
                                        <span class="title">账户余额</span>
                                    </a>
                                </li>
                                <li class="nav-item  {{ path_active(['personal/bank/*','personal/bank']) }}">
                                    <a href="{{ url('personal/bank') }}" class="nav-link ">
                                        <span class="title">提现账号</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item {!! path_active(['personal/delivery/*', 'personal/delivery','personal/delivery-man*','personal/delivery-statistical']) !!}">
                            <a href="javascript:;" class="nav-link nav-toggle">
                                <i class="fa fa-shopping-basket"></i>
                                <span class="title">配送管理</span>
                                <span class="{!! request()->is('personal/delivery*','personal/delivery-man*')?'selected':''  !!}"></span>
                                <span class="arrow open"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item start {{ path_active(['personal/delivery-man','personal/delivery-man/*']) }}">
                                    <a href="{{ url('personal/delivery-man') }}" class="nav-link ">
                                        <span class="title">配送人员</span>
                                    </a>
                                </li>
                                <li class="nav-item start {{ path_active(['personal/delivery/*','personal/delivery','personal/delivery-statistical']) }}">
                                    <a href="{{ url('personal/delivery') }}" class="nav-link ">
                                        <span class="title">配送历史</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item  {!! path_active(['business/*']) !!}">
                            <a href="javascript:;" class="nav-link nav-toggle">
                                <i class="icon-wallet"></i>
                                <span class="title">业务管理</span>
                                <span class="{!! request()->is('business/*') ? 'selected' : '' !!}"></span>
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item  {{ path_active(['business/salesman']) }}">
                                    <a href="{{ url('business/salesman') }}" class="nav-link ">
                                        <span class="title">业务员管理</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ path_active(['business/salesman-customer*']) }}">
                                    <a href="{{ url('business/salesman-customer') }}" class="nav-link ">
                                        <span class="title">客户管理</span>
                                    </a>
                                </li>

                                <li class="nav-item {{ path_active(['business/report*']) }}">
                                    <a href="{{ url('business/report') }}" class="nav-link ">
                                        <span class="title">业务报表</span>
                                    </a>
                                </li>

                                <li class="nav-item {{ path_active(['business/order/order-forms*']) }}">
                                    <a href="{{ url('business/order/order-forms') }}" class="nav-link ">
                                        <span class="title">订货单</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ path_active(['business/order/return-orders*']) }}">
                                    <a href="{{ url('business/order/return-orders') }}" class="nav-link ">
                                        <span class="title">退货单</span>
                                    </a>
                                </li>
                                <li class="nav-item  {{ path_active('business/salesman/target') }}">
                                    <a href="{{ url('business/salesman/target') }}" class="nav-link ">
                                        <span class="title">业务员目标管理</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ path_active(['business/mortgage-goods*']) }}">
                                    <a href="{{ url('business/mortgage-goods') }}" class="nav-link ">
                                        <span class="title">抵陈列费商品</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ path_active(['business/mortgage-goods*']) }}">
                                    <a href="{{ url('business/display-info') }}" class="nav-link ">
                                        <span class="title">陈列费发放情况</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    @if($user->type != cons('user.type.supplier'))
                        <li class="nav-item start {!!  path_active('like/*') !!} ">
                            <a href="javascript:;" class="nav-link nav-toggle">
                                <i class="fa fa-star-o"></i>
                                <span class="title">我的收藏</span>
                                <span class="{!!request()->is('like/*')?'selected':'' !!}"></span>
                                <span class="arrow open"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item start  {{ path_active(['like/shops','like/shops/*']) }}">
                                    <a href="{{ url('like/shops') }}" class="nav-link ">
                                        <span class="title">店铺收藏</span>
                                    </a>
                                </li>
                                <li class="nav-item start  {{ path_active(['like/goods','like/goods/*']) }}">
                                    <a href="{{ url('like/goods') }}" class="nav-link ">
                                        <span class="title">商品收藏</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    <li class="nav-item {!! path_active(['personal/chat*']) !!} ">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="fa fa-commenting-o "></i>
                            <span class="title">消息列表</span>
                            <span class="{!! request()->is('personal/chat/*','personal/chat')?'selected':'' !!}"></span>
                            <span class="arrow"></span>
                        </a>
                        <ul class="sub-menu">
                            <li class="nav-item {{ path_active('personal/chat') }} ">
                                <a href="{{ url('personal/chat') }}" class="nav-link ">
                                    <span class="title">消息列表</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    @if($user->type != cons('user.type.retailer'))
                        <li class="nav-item {!! path_active('personal/model/*') !!} ">
                            <a href="javascript:;" class="nav-link nav-toggle">
                                <i class="icon-layers"></i>
                                <span class="title">模板管理</span>
                                <span class="{!! request()->is('personal/model/*')?'selected':'' !!}"></span>
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                {{--<li class="nav-item  {{ path_active('personal/model/*') }}">--}}
                                {{--<a href="{{ url('personal/model/advert') }}" class="nav-link ">--}}
                                {{--<span class="title">首页广告</span>--}}
                                {{--</a>--}}
                                {{--</li>--}}
                                <li class="nav-item  {{ path_active('personal/model/model-edit') }}">
                                    <a href="{{ url('personal/model/model-edit') }}" class="nav-link ">
                                        <span class="title">模板设置</span>
                                    </a>
                                </li>
                                <li class="nav-item  {{ path_active('personal/model/model-choice') }}">
                                    <a href="{{ url('personal/model/model-choice') }}" class="nav-link ">
                                        <span class="title">模板选择</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item {!! path_active('personal/customer/*') !!} ">
                            <a href="javascript:;" class="nav-link nav-toggle">
                                <i class="fa fa-users "></i>
                                <span class="title">客户列表</span>
                                <span class="{!! request()->is('personal/customer/*')?'selected':'' !!}"></span>
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                @if ($user->type == cons('user.type.supplier'))
                                    <li class="nav-item {{ path_active('personal/customer/wholesaler') }}">
                                        <a class="nav-link" href="{{ url('personal/customer/wholesaler') }}">
                                            <span class="title">批发客户</span>
                                        </a>
                                    </li>
                                @endif
                                <li class="nav-item {{ path_active('personal/customer/retailer') }} ">
                                    <a href="{{ url('personal/customer/retailer') }}" class="nav-link ">
                                        <span class="title">终端客户</span>
                                    </a>
                                </li>

                            </ul>
                        </li>
                    @endif
                @endif

            </ul>
        </div>
    </div>
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">@yield('top-title')</div>
            </div>
            @yield('right')
        </div>
    </div>
@stop