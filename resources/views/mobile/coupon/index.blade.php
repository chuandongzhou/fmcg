@extends('mobile.master')

@section('subtitle', '我的优惠券')

@section('header')
    <div class="fixed-header fixed-item white-bg orders-details-header">
        <div class="row nav-top">
            <div class="col-xs-12 color-black">我的优惠券</div>
        </div>
    </div>
@stop

@section('body')
    @parent
    <div class="container-fluid m60">
        <div class="row">
            @foreach($coupons as $coupon)
                <div class="col-xs-12 clearfix white-bg coupon-list-wrap">
                    <div class="pull-left money-panel">
                        <div class="price">¥<b>{{ $coupon->discount }}</b></div>
                        <div class="condition">满{{ $coupon->full }}使用</div>
                    </div>
                    <div class="pull-right shop-panel">
                        <div class="shop-name">{{ $coupon->shop  ? $coupon->shop->name : '' }}</div>
                        <div class="date">
                            <span>{{ $coupon->end_at }} 前有效</span>
                            <a href="{{ url('shop/' . $coupon->shop_id) }}" class="btn">去使用</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@stop