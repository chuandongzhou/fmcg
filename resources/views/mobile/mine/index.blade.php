@extends('mobile.master')

@section('subtitle', '我的')

@section('header')

@stop

@section('body')
    @parent
    <div class="container-fluid">
        <div class="row mine-top-name">
            <div class="col-xs-11 shop-name-panel pd-right-clear">
                <img src="{{ $shop->logo_url }}" class="shop-img pull-left" />
                <div class="pull-left shop-info">
                    <div class="shop-name">{{ $shop->name }}</div>
                    <div class="shop-address">{{ $shop->address }}</div>
                </div>
            </div>
            <div class="col-xs-1 set-up-icon">
                <a class="iconfont icon-shezhi"></a>
            </div>
        </div>
        <div class="row white-bg">
            <div class="col-xs-12">
                <a class="mine-title" href="{{ url('order') }}">
                    <span class="pull-left left-title">进货订单</span>
                    <span class="pull-right">查看全部订单 <i class="iconfont icon-jiantouyoujiantou"></i></span>
                </a>
            </div>
            <div class="col-xs-12 order-sort-panel">
                <a class="item un-sent" href="{{ url('order/un-sent') }}">
                    <div>
                        <i class="iconfont icon-wodedingdan icon-daifahuo"></i>
                        <span class="badge">0</span>
                    </div>
                    <div>待发货</div>
                </a>
                <a class="item non-payment" href="{{ url('order/non-payment') }}">
                    <div>
                        <i class="iconfont icon-daifukuan"></i>
                        <span class="badge">0</span>
                    </div>
                    <div>待付款</div>
                </a>
                <a class="item wait-confirm" href="{{ url('order/wait-confirm') }}">
                    <div>
                        <i class="iconfont icon-peisong icon-daiqueren "></i>
                        <span class="badge">0</span>
                    </div>
                    <div>待确认</div>
                </a>
                <a class="item non-arrived" href="{{ url('order/non-arrived') }}">
                    <div>
                        <i class="iconfont icon-shape icon-daishouhuo"></i>
                        <span class="badge">0</span>
                    </div>
                    <div>待收货</div>
                </a>
            </div>
        </div>
        <div class="row mine-list-wrap white-bg">
            <div class="col-xs-12">
                <a class="list-item" href="{{ url('coupon') }}">
                    <i class="iconfont icon-yhq left-icon"></i>
                    <span>我的优惠券</span>
                    <i class="iconfont icon-jiantouyoujiantou pull-right"></i>
                </a>
                <a class="list-item" href="{{ url('shipping-address') }}">
                    <i class="iconfont icon-shouhuodizhi left-icon"></i>
                    <span>收货地址</span>
                    <i class="iconfont icon-jiantouyoujiantou pull-right"></i>
                </a>
                <a class="list-item" href="{{ url('like/shops') }}">
                    <i class="iconfont icon-shoucang left-icon"></i>
                    <span>我的收藏</span>
                    <i class="iconfont icon-jiantouyoujiantou pull-right"></i>
                </a>
            </div>
        </div>
    </div>
@stop

@include('mobile.includes.footer')

@section('js')
    @parent
    <script type="text/javascript">
        $.get(site.api('order/order-count-buy'), '', function(data){
           $('.un-sent').find('.badge').html(data.waitSend);
           $('.non-payment').find('.badge').html(data.waitReceive);
           $('.wait-confirm').find('.badge').html(data.waitConfirm);
           $('.non-arrived').find('.badge').html(data.refund);
        }, 'json');
    </script>
@stop