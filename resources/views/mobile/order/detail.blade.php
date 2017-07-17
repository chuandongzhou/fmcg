@extends('mobile.master')

@section('subtitle', '订单列表')

@include('includes.order-refund')

@section('header')
    <div class="fixed-header fixed-item white-bg orders-details-header">
        <div class="row nav-top">
            <div class="col-xs-12">订单详情</div>
        </div>
    </div>
@stop

@section('body')
    @parent
    <div class="container-fluid m60 p65">
        <div class="row white-bg orders-details-wrap">
            <div class="col-xs-12">
                <div class="order-msg clearfix">
                    <div class="pull-left">
                        <span class="prompt">订单号 </span>{{ $order->id }}
                    </div>
                    <div class="pull-right">
                        <span class="red">{{ $order->status_name }}</span>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 receiver">
                <div class="clearfix">
                    <div class="pull-left prompt">收货信息</div>
                    <div class="pull-right">
                        <span class="name">{{  $order->shippingAddress->consigner ?? ''}}</span>
                        <span class="num">{{ $order->shippingAddress->phone ?? ''}} </span>
                    </div>
                </div>
                <div class="address">
                    {{  isset($order->shippingAddress->address) ? $order->shippingAddress->address->address_name : '' }}
                </div>
            </div>
        </div>
        <div class="row all-orders-list">
            <div class="col-xs-12 list-item">
                <div class="item">
                    <a class="pull-left shop-name">{{ $order->shop_name }} <i
                                class="iconfont icon-jiantouyoujiantou"></i></a>
                </div>
            </div>
            <div class="col-xs-12 list-item">
                <div class="row bordered">
                    @foreach($orderGoods as $goods)
                        <a href="{{ url('goods/' . $goods->id) }}">
                            <div class="col-xs-12 item">
                                <img src="{{ $goods->image_url }}" class="pull-left commodity-img"/>
                                <div class="commodity-name pull-left">{{ $goods->name }}</div>
                                <div class="right-num-panel pull-right">
                                    <div>x{{ $goods->pivot->num }}</div>
                                    <div>
                                        {{ '¥'.$goods->pivot->price }}
                                        /{{ cons()->valueLang('goods.pieces', $goods->pivot->pieces)  }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="col-xs-12 list-item">
                <div class="item">
                    <div class="pull-left">
                        <div class="item-col">{{ $order->type == cons('order.type.business') ? '陈列费' :  '优惠券' }}</div>
                        <div class="item-col">实付</div>
                        <div class="item-col">共 <b>{{ $orderGoods->count() }}</b> 件商品</div>
                    </div>
                    <div class="pull-right text-right">
                        <div class="item-col">-¥{{ bcsub($order->price, $order->after_rebates_price, 2) }}</div>
                        <div class="item-col red">¥{{ $order->after_rebates_price }}</div>
                    </div>
                </div>
            </div>
        </div>
        @if ($order->pay_type==cons('pay_type.cod') && !$mortgageGoods->isEmpty())
            <div class="row all-orders-list">
                <div class="col-xs-12 list-item">
                    <div class="item">
                        <a class="pull-left shop-name">陈列费抵费商品 <i class="iconfont icon-jiantouyoujiantou"></i></a>
                    </div>
                </div>
                @foreach($mortgageGoods as $goods)
                    <div class="col-xs-12 list-item other-item">
                        <div class="row bordered">
                            <a href="{{ url('goods/' . $goods->id) }}">
                                <div class="col-xs-12 item">
                                    <img src="{{ $goods->image_url }}" class="pull-left commodity-img"/>
                                    <div class="commodity-name pull-left">{{ $goods->name }}</div>
                                    <div class="right-num-panel pull-right">
                                        <div>
                                            x{{ $goods['pivot']['num'] .cons()->valueLang('goods.pieces', $goods['pivot']['pieces']) }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        <div class="row all-orders-list">
            <div class="col-xs-12 clearfix other-panel">
                <div class="prompt pull-left label-name">备注：</div>
                <div class="content pull-left"> {{ object_get($order, 'remark') ? : '--' }}</div>
            </div>
        </div>
        @if($order['pay_status']==cons('order.pay_status.refund_success'))
            <div class="row all-orders-list">
                <div class="col-xs-12 clearfix other-panel">
                    <div class="prompt pull-left label-name">退款<br>原因：</div>
                    <div class="content pull-left"> {{ $order->refund_reason['reason'] }}</div>
                </div>
            </div>
        @endif
        <div class="row all-orders-list">
            <div class="col-xs-12 clearfix other-panel">
                <ul>
                    <li class="clearfix">
                        <span class="pull-left prompt">送货人</span>
                        <span class="pull-right">李泽-18098786543</span>
                    </li>
                    <li class="clearfix">
                        <span class="pull-left prompt">创建时间</span>
                        <span class="pull-right">{{ $order->created_at }}</span>
                    </li>
                    @if((int)$order->send_at)
                        <li class="clearfix">
                            <span class="pull-left prompt">发货时间</span>
                            <span class="pull-right">{{ $order->send_at }}</span>
                        </li>
                    @endif
                    @if((int)$order->paid_at)
                        <li class="clearfix">
                            <span class="pull-left prompt">支付时间</span>
                            <span class="pull-right">{{ $order->paid_at }}</span>
                        </li>
                    @endif
                    @if($order->pay_status == cons('order.pay_status.refund')  || $order->pay_status == cons('order.pay_status.refund_success'))
                        <li class="clearfix">
                            <span class="pull-left prompt">申请退款</span>
                            <span class="pull-right">{{ $order->refund_reason['time'] }}</span>
                        </li>
                    @endif
                    @if($order->pay_status == cons('order.pay_status.refund_success'))
                        <li class="clearfix">
                            <span class="pull-left prompt">退款成功</span>
                            <span class="pull-right">{{ $order->refund_at }}</span>
                        </li>
                    @endif
                    @if($order['is_cancel'])
                        <li class="clearfix">
                            <span class="pull-left prompt">取消订单</span>
                            <span class="pull-right">{{ $order->cancel_at }}</span>
                        </li>
                    @endif
                    @if($order['status']==cons('order.status.invalid'))
                        <li class="clearfix">
                            <span class="pull-left prompt">作废订单</span>
                            <span class="pull-right">{{ $order->updated_at }}</span>
                        </li>
                    @endif
                    @if((int)$order->finished_at)
                        <li class="clearfix">
                            <span class="pull-left prompt">完成时间</span>
                            <span class="pull-right">{{ $order->finished_at }}</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="fixed-footer fixed-item white-bg order-details-footer">
        <div class="text-right">
            @if ($order->can_refund)
                <button class="btn btn-danger refund" data-target="#refund"
                        data-toggle="modal"
                        data-url="{{ url('api/v1/pay/refund/' . $order->id) }}">退款
                </button>

            @elseif($order->can_cancel)
                <button type="button" class="btn btn-primary mobile-ajax"
                        data-url="{{ url('api/v1/order/cancel-sure') }}"
                        data-method="put"
                        data-danger="真的要取消该订单吗？"
                        data-data='{"order_id":{{ $order['id'] }}}'>取消
                </button>
            @endif

            @if($order->can_payment)
                <a type="button" class="btn btn-success" href="{{ url('pay/' . $order->id)}}">
                    去付款
                </a>
            @elseif($order->can_confirm_arrived)
                <button type="button" class="btn btn-danger mobile-ajax"
                        data-url="{{ url('api/v1/order/batch-finish-of-buy') }}"
                        data-method="put"
                        data-data='{"order_id":{{ $order['id'] }}}'>确认收货
                </button>
            @endif
        </div>
    </div>
@stop