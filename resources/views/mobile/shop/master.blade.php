@extends('mobile.master')

@section('header')
    <div class="fixed-header fixed-item">
        <div class="row nav-top margin-clear">
            <div class="col-xs-12 search-item">
                <div class="panel">
                    <i class="iconfont icon-search"></i>
                    <input type="text" onclick="window.location.href='{{ url('search/' . $shop->id . '/shop-goods') }}'"
                           class="search" placeholder="查找商品"/>
                </div>
            </div>
        </div>
        <div class="row shop-panel">
            <div class="col-xs-12 shop-item">
                <img class="shop-img" src="{{ $shop->logo_url }}">
                <div class="shop-msg-content">
                    <div class="shop-name"><b>{{ $shop->name }}</b><span
                                class="prompt">({{ cons()->valueLang('user.type', $shop->user_type)   }})</span></div>
                    <div class="amount"><span class="prompt">配送额 :</span> ¥{{ $shop->min_money }}</div>
                    <div class="sales"><span class="prompt">商铺销量 :</span> {{ $shop->sales_volume }} <span
                                class="prompt">共</span> {{ $shop->goods_count }}<span
                                class="prompt">件商品</span></div>
                </div>
            </div>
            <div class="col-xs-12 shop-list-wrap">
                <a class="{{ request()->is('shop/'. $shop->id) ? 'on' : '' }}" href="{{ url('shop/' . $shop->id) }}">所有商品</a>
                <a class="{{ request()->is('shop/*/coupons') ? 'on' : '' }}"
                   href="{{ url('shop/' . $shop->id . '/coupons') }}">优惠券</a>
                <a class="{{ request()->is('shop/*/delivery-area') ? 'on' : '' }}"
                   href="{{ url('shop/' . $shop->id . '/delivery-area') }}">配送地区</a>
                <a class="{{ request()->is('shop/*/qr-code') ? 'on' : '' }}"
                   href="{{ url('shop/' . $shop->id . '/qr-code') }}">二维码</a>
            </div>
        </div>
    </div>

@stop

@section('footer')
    <div class="fixed-footer fixed-item shops-nav-bottom nav-bottom">
        <div class="bottom-menu-item">
            <a href="javascript:" data-type="shops" data-method="post"
               class="bottom-menu-item btn btn-like list-name like-shops"
               data-id="{{ $shop->id }}" style="cursor:pointer">
                @if($shop->is_like)
                    <i class="fa fa-star"></i> 已收藏
                @else
                    <i class="fa fa-star-o"></i> 加入收藏夹
                @endif
            </a>
        </div>
    </div>
@stop