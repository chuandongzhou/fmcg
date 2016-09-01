@extends('index.menu-master')

@section('subtitle' , '订单详情')
@section('top-title')
    <a href="{{ url('order-buy') }}">进货管理</a> &rarr;
    订单详情
@stop
@include('includes.stepBar')
@include('includes.shipping-address-map')

@section('right')
    <div class="order-detail row">
        <div class="col-sm-12 go-history">
            <a class="go-back" href="{{ url('order-buy') }}"><i class="fa fa-reply"></i> 返回</a>
        </div>
        <div class="col-sm-12 order-panel">
            <ul>
                <li><span class="title-name">订单号 : </span> {{ $order['id'] }}</li>
                <li><span class="title-name">订单金额 : </span><span class="red">¥{{ $order['price'] }}</span></li>
                <li><span class="title-name">支付方式 : </span>{{ $order['payment_type'] }}</li>
                <li><span class="title-name">订单状态 : </span><span class="red">{{ $order['status_name'] }}</span></li>
                <li><span class="title-name">备注 : </span>
                    <p class="remarks-content">{{ $order['remark'] }}</p>
                </li>
            </ul>
        </div>

        <div class="col-sm-12 store-msg">
            <div class="clearfix item">
                <label class="pull-left title-name">商家信息</label>
                <ul class="pull-left">
                    <li>
                        <span>商家名称 :</span>
                        <span><a href="{{ url('shop/' . $order['shop']['id']) }}"
                                 target="_blank">{{ $order['shop']['name'] }}</a></span>
                    </li>
                    <li>
                        <span>联系人 :</span>
                        <span>{{ $order['shop']['contact_person'] }}</span>
                    </li>
                    <li>
                        <span>联系电话 :</span>
                        <span>{{ $order['shop']['contact_info'] }}</span>
                    </li>

                    <li>
                        <span>提货地址 : </span>
                        {{  $order->shop ? $order->shop->address : '' }}
                        <a href="javascript:" data-target="#shippingAddressMapModal" data-toggle="modal"
                           data-x-lng="{{ $order->shop ?  $order->shop->x_lng : 0 }}"
                           data-y-lat="{{ $order->shop ?  $order->shop->y_lat : 0}}"
                           data-address="{{ $order->shop ? $order->shop->address : '' }}"
                           data-consigner="{{ $order->shop ? $order->shop->contact_person : ''  }}"
                           data-phone= {{  $order->shop ? $order->shop->contact_info : '' }}
                        >
                            <i class="fa fa-map-marker"></i> 查看地图</a>

                    </li>
                </ul>
            </div>
            <div class="table-responsive order-table clearfix item">
                <label class="pull-left title-name">商品清单</label>
                <table class=" table table-bordered table-center">
                    <thead>
                    <tr>
                        <td>商品编号</td>
                        <td>商品图片</td>
                        <td>商品名称</td>
                        <td>商品单价</td>
                        <td>商品数量</td>
                        <td>金额</td>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($order['goods'] as $goods)
                        <tr>
                            <td>{{ $goods['id'] }}</td>
                            <td><img class="store-img" src="{{ $goods['image_url'] }}"></td>
                            <td>
                                <div class="product-panel">
                                    <a class="product-name" href="{{ url('goods/'. $goods['id']) }}"
                                       target="_blank">{{ $goods->name }}</a>
                                    {!! $goods->is_promotion ? '<p class="promotions">(<span class="ellipsis"> ' . $goods->promotion_info . '</span>)</p>' : '' !!}
                                </div>
                            </td>
                            <td>¥{{ $goods['pivot']['price'] . ' / ' . cons()->valueLang('goods.pieces', $goods->pivot->pieces)  }}</td>
                            <td>{{ $goods['pivot']['num'] }}</td>
                            <td>¥{{ $goods['pivot']['total_price'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-sm-12 text-right bottom-content">
            <p>
                总额：<b class="red">¥{{ $order->price }}</b>
                @if($order->coupon_id)
                    <br/> 优惠：<b class="red">¥{{ bcsub($order->price, $order->after_rebates_price, 2) }}</b>
                    <br><span class="prompt-coupon">(满{{ $order->coupon->full }}减 {{ $order->coupon->discount }})</span>
                    <br/>  应付金额：<b class="red">¥{{ $order->after_rebates_price }}</b>
                @endif
            </p>

            <p>
                @if(!$order['is_cancel'])
                    @if($order['can_cancel'])
                        <a class="btn btn-danger ajax" data-url="{{ url('api/v1/order/cancel-sure') }}"
                           data-method="put" data-data='{"order_id":{{ $order['id'] }}}'>取消</a>
                    @endif
                @endif
            </p>
        </div>
    </div>
@stop